<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\work\document_in_out\DocumentOutWork */
/* @var $correspondentList */
/* @var $availablePositions */
/* @var $availableCompanies */
/* @var $mainCompanyWorkers */

$this->title = 'Добавить исходящий документ';
$this->params['breadcrumbs'][] = ['label' => 'исходящая документация', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
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
    ]) ?>

</div>
