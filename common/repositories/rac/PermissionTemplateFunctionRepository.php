<?php

namespace common\repositories\rac;

use DomainException;
use frontend\models\work\rac\PermissionTemplateFunctionWork;

class PermissionTemplateFunctionRepository
{
    public function save(PermissionTemplateFunctionWork $templateFunction)
    {
        if (!$templateFunction->save()) {
            throw new DomainException('Ошибка привязки правила к шаблону. Проблемы: '.json_encode($templateFunction->getErrors()));
        }

        return $templateFunction->id;
    }
}