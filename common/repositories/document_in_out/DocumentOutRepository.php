<?php

namespace common\repositories\document_in_out;

use common\models\work\document_in_out\DocumentInWork;
use common\models\work\document_in_out\DocumentOutWork;
use DomainException;

class DocumentOutRepository
{
    public function get($id)
    {
        return DocumentOutWork::find()->where(['id' => $id])->one();
    }

    public function getAllDocumentsDescDate()
    {
        return DocumentOutWork::find()->orderBy(['document_date' => SORT_DESC])->all();
    }

    public function getAllDocumentsInYear()
    {
        return DocumentOutWork::find()->where(['like', 'document_date', date('Y')])->orderBy(['document_number' => SORT_ASC, 'document_postfix' => SORT_ASC])->all();
    }

    public function save(DocumentOutWork $document)
    {
        if (!$document->save()) {
            throw new DomainException('Ошибка сохранения входящего документа. Проблемы: '.json_encode($document->getErrors()));
        }

        return $document->id;
    }
}