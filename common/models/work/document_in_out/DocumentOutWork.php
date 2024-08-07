<?php

namespace common\models\work\document_in_out;
use common\models\scaffold\DocumentOut;
use common\repositories\document_in_out\DocumentOutRepository;

use Yii;

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
        if (date('Y') !== substr($docs[0]->local_date, 0, 4)) {
            $this->document_number = 1;
        }
        else {
            $docs = $repository->getAllDocumentsInYear();
            if (end($docs)->local_date > $this->local_date && $this->document_theme != 'Резерв') {
                $tempId = 0;
                $tempPre = 0;
                if (count($docs) == 0) {
                    $tempId = 1;
                }
                for ($i = count($docs) - 1; $i >= 0; $i--) {
                    if ($docs[$i]->local_date <= $this->local_date) {
                        $tempId = $docs[$i]->local_number;
                        if ($docs[$i]->local_postfix != null) {
                            $tempPre = $docs[$i]->local_postfix + 1;
                        }
                        else {
                            $tempPre = 1;
                        }
                        break;
                    }
                }
                $this->local_number = $tempId;
                $this->local_postfix = $tempPre;
                Yii::$app->session->addFlash('warning', 'Добавленный документ должен был быть зарегистрирован раньше. Номер документа: '.$this->local_number.'/'.$this->local_postfix);
            }
            else
            {
                if (count($docs) == 0) {
                    $this->local_number = 1;
                }
                else {
                    $this->local_number = end($docs)->local_number + 1;
                }
            }
        }
    }
}