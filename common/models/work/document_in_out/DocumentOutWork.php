<?php

namespace common\models\work\document_in_out;
use common\helpers\StringFormatter;
use common\models\scaffold\DocumentOut;
use common\models\work\general\PeopleWork;
use common\repositories\document_in_out\DocumentInRepository;
use common\repositories\document_in_out\DocumentOutRepository;

use common\repositories\document_in_out\InOutDocumentsRepository;
use Yii;
use yii\helpers\Url;

class DocumentOutWork extends DocumentOut
{
    public $needAnswer;
    public $dateAnswer;
    public $scanFile;
    public $docFiles;
    public $appFiles;
    public $nameAnswer;
    public function generateDocumentNumber(){
        $repository = Yii::createObject(DocumentOutRepository::class);
        $docs = $repository->getAllDocumentsDescDate();
        if (date('Y') !== substr($docs[0]->document_date, 0, 4)) {
            $this->document_number = 1;
        }
        else {
            $docs = $repository->getAllDocumentsInYear();
            if (end($docs)->document_date > $this->document_date && $this->document_theme != 'Резерв') {
                $tempId = 0;
                $tempPre = 0;
                if (count($docs) == 0) {
                    $tempId = 1;
                }
                for ($i = count($docs) - 1; $i >= 0; $i--) {
                    if ($docs[$i]->document_date <= $this->document_date) {
                        $tempId = $docs[$i]->document_number;
                        if ($docs[$i]->document_postfix != null) {
                            $tempPre = $docs[$i]->document_postfix + 1;
                        }
                        else {
                            $tempPre = 1;
                        }
                        break;
                    }
                }
                $this->document_number = $tempId;
                $this->document_postfix = $tempPre;
                Yii::$app->session->addFlash('warning', 'Добавленный документ должен был быть зарегистрирован раньше. Номер документа: '.$this->document_number.'/'.$this->document_postfix);
            }
            else
            {
                if (count($docs) == 0) {
                    $this->document_number = 1;
                }
                else {
                    $this->document_number = end($docs)->document_number + 1;
                }
            }
        }
    }
    public function getNeedAnswer(int $format = StringFormatter::FORMAT_RAW)
    {
        if (array_key_exists($format, StringFormatter::getFormats())) {
            $links = (Yii::createObject(InOutDocumentsRepository::class))->get($this->id);

            /** @var InOutDocumentsWork $links */
            if ($links == null) {
                return '';
            }

            if ($links->isDocumentInEmpty()) {
                if ($links->isNoPeopleTarget()) {
                    if ($links->isNoAnswerDate()) {
                        return 'Требуется ответ';
                    }
                    return 'До '.$links->date;
                }
                return 'До '.$links->date.' от '.$links->responsibleWork->getFIO(PeopleWork::FIO_SURNAME_INITIALS);
            }

            $str = 'Входящий документ "'.(Yii::createObject(DocumentInRepository::class))->get($links->document_in_id)->document_theme.'"';
            return $format == StringFormatter::FORMAT_LINK ?
                StringFormatter::stringAsLink($str, Url::to(['document-шт/view', 'id' => $links->document_in_id])) : $str;
        }
        throw new \InvalidArgumentException('Неизвестный формат строки');
    }

}