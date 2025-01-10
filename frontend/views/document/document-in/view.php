<?php

use common\helpers\DateFormatter;
use common\helpers\files\FilesHelper;
use common\helpers\StringFormatter;
use frontend\models\work\document_in_out\DocumentInWork;
use frontend\models\work\general\PeopleWork;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model \frontend\models\work\document_in_out\DocumentInWork */

$this->title = $model->document_theme;
$this->params['breadcrumbs'][] = ['label' => 'Входящая документация', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>


<div class="document-in-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <div class="card">
        <div class="card-block-1">
            <div class="card-set">
                <div class="card-head">Основное</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Имя
                    </div>
                    <div class="field-date">
                        <?= $model->getFullName() ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Тип
                    </div>
                    <div class="field-date">
                        Входящая документация
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Тема
                    </div>
                    <div class="field-date">
                        <?= $model->getDocumentTheme() ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Способ получения
                    </div>
                    <div class="field-date">
                        <?= $model->getSendMethodName() ?>
                    </div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">От кого</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Корреспондент
                    </div>
                    <div class="field-date">
                        <?= $model->getCompanyName() ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Должность и ФИО
                    </div>
                    <div class="field-date">
                        <?= $model->getCorrespondentName() ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Дата и номер
                    </div>
                    <div class="field-date">
                        <?= $model->getRealDate() . ' № ' . $model->getRealNumber() ?>
                    </div>
                </div>
            </div>
            <?php if ($model->getNeedAnswer()) : ?>
            <div class="card-set">
                <div class="card-head">Ответ</div>
                <?php if ($model->getAnswerNotEmpty()) : ?>
                <div class="card-field flexx">
                    <div class="field-title">
                        Документ
                    </div>
                    <div class="field-date">
                        <?= $model->getAnswer() ?>
                    </div>
                </div>
                <?php else : ?>
                <div class="card-field flexx">
                    <div class="field-title">
                        Ответственный
                    </div>
                    <div class="field-date">
                        <?= $model->getResponsibleName() ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Cрок ответа
                    </div>
                    <div class="field-date">
                        <?= $model->getResponsibleDate() ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
        <div class="card-block-2">
            <div class="card-set">
                <div class="card-head">Дата и номер</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        № п/п
                    </div>
                    <div class="field-date">
                        <?= $model->getFullNumber() ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Дата
                    </div>
                    <div class="field-date">
                        <?= $model->getLocalDate() ?>
                    </div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">Ключевые слова</div>
                <div class="card-field">
                    <div class="field-date">
                        <?= $model->getKeyWords() ?>
                    </div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">Файлы</div>
                <div class="flexx" style="justify-content: space-evenly;">
                    <div style="width: 50px;"><svg version="1.1" id="Слой_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                              viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve">
<style type="text/css">
    .st0{fill:#020202;}
    .st1{fill:#060606;}
    .st2{fill:#040404;}
    .st3{fill:#0D0D0D;}
    .st4{fill:#010101;}
    .st5{fill:#030303;}
    .st6{fill:#050505;}
    .st7{fill:none;stroke:#000000;stroke-width:1.7;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;}
    .st8{fill:none;stroke:#000000;stroke-width:1.7;stroke-miterlimit:10;}
    .st9{fill:none;stroke:#000000;stroke-width:1.7;stroke-linecap:round;stroke-miterlimit:10;}
</style>
                            <g>
                                <path class="st0" d="M26.95,0.05c1.14,0.23,1.91,0.99,2.68,1.78c1.9,1.93,3.82,3.85,5.76,5.75c0.88,0.86,1.28,1.85,1.27,3.08
		c-0.03,2.92-0.01,5.85-0.01,8.77c0,0.18,0,0.35,0,0.59c0.21,0,0.38,0,0.55,0c2.78,0,5.55,0,8.33,0c2.68,0,4.42,1.74,4.43,4.41
		c0,7.03,0,14.06,0,21.09c0,2.68-1.76,4.43-4.46,4.43c-7,0-14,0-21,0c-2.29,0-3.75-1.09-4.42-3.32c-0.16,0-0.34,0-0.52,0
		c-5.26,0-10.52,0-15.78,0c-1.93,0-2.95-0.71-3.63-2.53c-0.01-0.04-0.06-0.06-0.1-0.09c0-13.78,0-27.55,0-41.33
		c0.07-0.14,0.14-0.27,0.19-0.42c0.31-0.82,0.84-1.45,1.62-1.84c0.29-0.15,0.61-0.25,0.91-0.38C10.83,0.05,18.89,0.05,26.95,0.05z
		 M20.02,44.97c0-0.22,0-0.4,0-0.57c0-6.63,0-13.25,0-19.88c0-0.36,0-0.72,0.06-1.07c0.39-2.05,2.03-3.42,4.12-3.43
		c3.41-0.01,6.82,0,10.23,0c0.17,0,0.34,0,0.5,0c0-3.37,0-6.67,0-9.99c-1.89,0-3.74,0.01-5.59,0c-1.65-0.01-2.67-1.04-2.67-2.67
		c0-1.02,0-2.05,0-3.07c0-0.84,0-1.68,0-2.57c-0.22,0-0.39,0-0.57,0c-7.47,0-14.94,0-22.41,0c-1.34,0-1.97,0.63-1.97,1.96
		c0,13.11,0,26.21,0,39.32c0,1.34,0.63,1.98,1.96,1.98c3.04,0,6.07,0,9.11,0C15.17,44.97,17.55,44.97,20.02,44.97z M35.04,21.68
		c-3.56,0-7.11,0-10.67,0c-1.68,0-2.69,1.02-2.69,2.7c0,7.07,0,14.13,0,21.2c0,1.68,1.02,2.71,2.69,2.71c7.07,0,14.13,0,21.2,0
		c1.69,0,2.72-1.04,2.72-2.74c0-7.05,0-14.1,0-21.15c0-1.71-1.02-2.72-2.72-2.72C42.06,21.68,38.55,21.68,35.04,21.68z M28.32,3.03
		c0,1.44,0,2.89,0,4.33c0,0.71,0.3,1,1.01,1c0.92,0.01,1.85,0,2.77,0c0.52,0,1.05,0,1.57,0C31.87,6.56,30.1,4.8,28.32,3.03z"/>
                                <path class="st1" d="M18.32,13.36c4.32,0,8.64,0,12.95,0c0.82,0,1.23,0.29,1.22,0.85c-0.02,0.55-0.42,0.82-1.21,0.82
		c-8.62,0-17.24,0-25.86,0c-0.21,0-0.43,0-0.63-0.06c-0.41-0.12-0.6-0.42-0.57-0.85c0.03-0.41,0.26-0.66,0.66-0.73
		c0.19-0.04,0.39-0.03,0.58-0.03C9.75,13.36,14.03,13.36,18.32,13.36z"/>
                                <path class="st2" d="M11.66,20.83c-2.08,0-4.16,0-6.24,0c-0.16,0-0.33,0.01-0.49-0.01c-0.43-0.06-0.67-0.3-0.71-0.73
		c-0.04-0.42,0.15-0.73,0.56-0.86c0.18-0.06,0.38-0.06,0.58-0.06c4.22,0,8.45,0,12.67,0c0.11,0,0.23-0.01,0.34,0.01
		c0.5,0.06,0.81,0.4,0.79,0.87c-0.02,0.45-0.33,0.76-0.82,0.78c-0.55,0.02-1.1,0.01-1.66,0.01C15.01,20.83,13.34,20.83,11.66,20.83z
		"/>
                                <path class="st3" d="M11.31,36.64c1.95,0,3.89,0,5.84,0c0.19,0,0.39,0,0.58,0.05c0.4,0.09,0.61,0.39,0.62,0.77
		c0.01,0.41-0.2,0.71-0.63,0.81c-0.16,0.04-0.32,0.04-0.48,0.04c-3.97,0-7.95,0-11.92,0c-0.45,0-0.85-0.08-1.05-0.54
		c-0.24-0.57,0.18-1.12,0.86-1.14c0.79-0.02,1.59,0,2.38,0C8.78,36.64,10.04,36.64,11.31,36.64z"/>
                                <path class="st4" d="M11.26,26.66c-2.03,0-4.06,0-6.09,0c-0.65,0-1.02-0.39-0.94-0.95c0.06-0.43,0.39-0.7,0.9-0.7
		C6.22,25,7.31,25,8.4,25c2.96,0,5.91,0,8.87,0.01c0.25,0,0.53,0.05,0.73,0.17c0.31,0.19,0.43,0.52,0.3,0.89
		c-0.13,0.38-0.4,0.58-0.8,0.58c-1.1,0.01-2.21,0-3.31,0C13.21,26.66,12.23,26.66,11.26,26.66z"/>
                                <path class="st5" d="M11.31,30.83c2,0,3.99,0,5.99,0c0.66,0,1.05,0.31,1.05,0.82c0,0.51-0.39,0.84-1.04,0.84
		c-4.03,0-8.05,0-12.08,0c-0.66,0-1.02-0.32-1.01-0.85c0.01-0.52,0.35-0.81,1-0.81C7.25,30.83,9.28,30.83,11.31,30.83z"/>
                                <path class="st6" d="M31.64,30.83c0-1.31,0-2.58,0-3.84c0-0.65-0.01-1.3,0-1.95c0.03-1.02,0.72-1.69,1.74-1.7
		c1.01-0.01,2.01,0,3.02,0c1.26,0,1.9,0.64,1.91,1.92c0,1.66,0,3.31,0,4.97c0,0.18,0,0.35,0,0.6c0.4,0,0.76,0,1.13,0
		c0.92,0,1.6,0.4,1.98,1.24c0.38,0.84,0.3,1.67-0.33,2.36c-1.54,1.67-3.09,3.33-4.68,4.96c-0.8,0.83-2.08,0.81-2.88-0.02
		c-1.56-1.61-3.09-3.24-4.61-4.89c-0.64-0.69-0.77-1.53-0.38-2.4c0.39-0.85,1.08-1.26,2.02-1.25
		C30.89,30.83,31.23,30.83,31.64,30.83z M33.31,25.02c0,2.18,0,4.31,0,6.43c0,0.75-0.29,1.03-1.02,1.04c-0.55,0-1.11-0.02-1.66,0.01
		c-0.2,0.01-0.49,0.12-0.54,0.26c-0.07,0.18-0.01,0.5,0.12,0.64c1.46,1.59,2.95,3.15,4.44,4.72c0.22,0.23,0.46,0.24,0.68,0.01
		c1.5-1.58,2.99-3.16,4.47-4.76c0.12-0.13,0.16-0.44,0.1-0.61c-0.05-0.13-0.32-0.25-0.5-0.26c-0.58-0.03-1.17-0.01-1.75-0.01
		c-0.67-0.01-0.98-0.31-0.99-0.99c-0.01-0.63,0-1.27,0-1.9c0-1.52,0-3.04,0-4.58C35.52,25.02,34.44,25.02,33.31,25.02z"/>
                                <path class="st5" d="M34.99,41.65c1.69,0,3.38-0.02,5.07,0.01c1.08,0.02,1.85,0.58,2.24,1.58c0.38,0.98,0.17,1.89-0.55,2.65
		c-0.47,0.49-1.07,0.74-1.74,0.74c-3.34,0.01-6.69,0.02-10.03,0c-1.4-0.01-2.46-1.13-2.45-2.5c0-1.38,1.05-2.46,2.47-2.47
		C31.64,41.63,33.32,41.64,34.99,41.65C34.99,41.64,34.99,41.65,34.99,41.65z M34.99,43.31c-1.61,0-3.21,0-4.82,0
		c-0.64,0-0.99,0.28-1,0.81c-0.01,0.54,0.35,0.85,1.01,0.85c3.2,0,6.39,0,9.59,0c0.65,0,1.03-0.32,1.02-0.84
		c0-0.53-0.36-0.82-1.04-0.82C38.17,43.3,36.58,43.31,34.99,43.31z"/>
                            </g>
</svg></div>
                    <div style="width: 50px;"></div>
                    <div style="width: 50px;"></div>
                </div>
                <div class="flexx" style="justify-content: space-evenly;">
                    <div>Сканы</div>
                    <div>Редактируемые</div>
                    <div>Приложения</div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">Свойства</div>
                <div class="flexx">
                <div class="card-field flexx">
                    <div class="field-title field-option">
                        Создатель карточки
                    </div>
                    <div class="field-date">
                        <?= $model->getCreatorName() ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title field-option">
                        Последний редактор
                    </div>
                    <div class="field-date">
                        <?= $model->getLastEditorName() ?>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
