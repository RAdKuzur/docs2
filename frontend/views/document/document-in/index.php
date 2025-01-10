<?php

use app\components\VerticalActionColumn;
use common\helpers\DateFormatter;
use common\helpers\html\HtmlCreator;
use common\helpers\StringFormatter;
use frontend\helpers\document\DocumentInHelper;
use frontend\models\work\document_in_out\DocumentInWork;
use kartik\daterange\DateRangePicker;
use kartik\export\ExportMenu;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel \frontend\models\search\SearchDocumentIn */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Входящая документация';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="document-in-index">
    <div class="substrate">
        <h1><?= Html::encode($this->title) ?></h1>

        <div class="flexx space">
            <div class="flexx">
                <?= DocumentInHelper::createGroupButton(); ?>

                <div class="export-menu">
                    <?php

                    $gridColumns = [
                        ['attribute' => 'fullNumber'],
                        ['attribute' => 'localDate', 'encodeLabel' => false],
                        ['attribute' => 'realDate', 'encodeLabel' => false],
                        ['attribute' => 'realNumber', 'encodeLabel' => false],

                        ['attribute' => 'companyName', 'encodeLabel' => false],
                        ['attribute' => 'documentTheme', 'encodeLabel' => false],
                        ['attribute' => 'sendMethodName', 'value' => 'sendMethod.name'],
                        ['attribute' => 'needAnswer', 'value' => function(DocumentInWork $model) {
                            return $model->getNeedAnswerString();
                        }, 'format' => 'raw'],

                    ];

                    echo ExportMenu::widget([
                        'dataProvider' => $dataProvider,
                        'columns' => $gridColumns,

                        'options' => [
                            'padding-bottom: 100px',
                        ],
                    ]);

                    ?>
                </div>
            </div>

            <?= HtmlCreator::filterToggle() ?>
        </div>
    </div>

    <?= $this->render('_search', ['searchModel' => $searchModel]) ?>

    <div style="margin-bottom: 20px">

        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'summary' => false,

            'columns' => [
                ['attribute' => 'fullNumber'],
                ['attribute' => 'localDate',
                    'value' => function(DocumentInWork $model){
                        return DateFormatter::format($model->local_date, DateFormatter::Ymd_dash, DateFormatter::dmy_dot);
                    },
                    'encodeLabel' => false,
                ],
                ['attribute' => 'realDate',
                    'encodeLabel' => false,
                    'value' => function(DocumentInWork $model) {
                        return DateFormatter::format($model->real_date, DateFormatter::Ymd_dash, DateFormatter::dmy_dot);
                    },
                ],
                ['attribute' => 'realNumber', 'encodeLabel' => false],

                ['attribute' => 'companyName', 'encodeLabel' => false],
                ['attribute' => 'documentTheme', 'encodeLabel' => false],
                ['attribute' => 'sendMethodName',
                    'value' => function(DocumentInWork $model) {
                        return Yii::$app->sendMethods->get($model->send_method);
                    }
                ],
                ['attribute' => 'needAnswer',
                    'value' => function(DocumentInWork $model) {
                        return $model->getNeedAnswerString(StringFormatter::FORMAT_LINK);
                    },
                    'format' => 'raw'
                ],

                ['class' => VerticalActionColumn::class],
            ],
            'rowOptions' => function ($model) {
                return ['data-href' => Url::to([Yii::$app->frontUrls::DOC_IN_VIEW, 'id' => $model->id])];
            },
        ]);

        ?>
    </div>
</div>
