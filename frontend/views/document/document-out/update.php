<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\work\document_in_out\DocumentOutWork */
/* @var $correspondentList */
/* @var $availablePositions */
/* @var $availableCompanies */
/* @var $mainCompanyWorkers */
/* @var $scanFile */
/* @var $docFiles */
/* @var $appFiles */

$this->title = 'Исходящий документ №' . $model->fullNumber;
$this->params['breadcrumbs'][] = ['label' => 'Исходящая документация', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="document-in-create">

    <h3><?= Html::encode($this->title) ?></h3>
    <br>

    <?= $this->render('_form', [
        'model' => $model,
        'correspondentList' => $correspondentList,
        'availablePositions' => $availablePositions,
        'availableCompanies' => $availableCompanies,
        'mainCompanyWorkers' => $mainCompanyWorkers,
        'scanFile' => $scanFile,
        'docFiles' => $docFiles,
        'appFiles' => $appFiles,
    ]) ?>

</div>
