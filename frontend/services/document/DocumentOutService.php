<?php

namespace frontend\services\document;

use common\helpers\files\filenames\DocumentOutFileNameGenerator;
use common\helpers\files\FilesHelper;
use common\helpers\html\HtmlBuilder;
use common\services\DatabaseService;
use common\services\general\files\FileService;
use common\services\general\PeopleStampService;
use frontend\controllers\document\DocumentOutController;
use frontend\events\general\FileCreateEvent;
use frontend\models\work\document_in_out\DocumentOutWork;
use PhpParser\Comment\Doc;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\UploadedFile;

class DocumentOutService implements DatabaseService
{
    private FileService $fileService;
    private PeopleStampService $peopleStampService;
    private DocumentOutFileNameGenerator $filenameGenerator;

    public function __construct(
        FileService $fileService,
        PeopleStampService $peopleStampService,
        DocumentOutFileNameGenerator $filenameGenerator
    )
    {
        $this->fileService = $fileService;
        $this->peopleStampService = $peopleStampService;
        $this->filenameGenerator = $filenameGenerator;
    }

    public function getPeopleStamps(DocumentOutWork $model)
    {
        $peopleStampId = $this->peopleStampService->createStampFromPeople($model->correspondent_id);
        $model->correspondent_id = $peopleStampId;
        $peopleStampId = $this->peopleStampService->createStampFromPeople($model->signed_id);
        $model->signed_id = $peopleStampId;
        $peopleStampId = $this->peopleStampService->createStampFromPeople($model->executor_id);
        $model->executor_id = $peopleStampId;
    }

    public function getFilesInstances(DocumentOutWork $model)
    {
        $model->scanFile = UploadedFile::getInstance($model, 'scanFile');
        $model->appFile = UploadedFile::getInstances($model, 'appFile');
        $model->docFile = UploadedFile::getInstances($model, 'docFile');
    }

    public function saveFilesFromModel(DocumentOutWork $model)
    {
        if ($model->scanFile !== null) {
            $filename = $this->filenameGenerator->generateFileName($model, FilesHelper::TYPE_SCAN);

            $this->fileService->uploadFile(
                $model->scanFile,
                $filename,
                [
                    'tableName' => DocumentOutWork::tableName(),
                    'fileType' => FilesHelper::TYPE_SCAN
                ]

            );

            $model->recordEvent(
                new FileCreateEvent(
                    $model::tableName(),
                    $model->id,
                    FilesHelper::TYPE_SCAN,
                    $filename,
                    FilesHelper::LOAD_TYPE_SINGLE
                ),
                get_class($model)
            );
        }

        for ($i = 1; $i < count($model->docFile) + 1; $i++) {
            $filename = $this->filenameGenerator->generateFileName($model, FilesHelper::TYPE_DOC, ['counter' => $i]);

            $this->fileService->uploadFile(
                $model->docFile[$i - 1],
                $filename,
                [
                    'tableName' => DocumentOutWork::tableName(),
                    'fileType' => FilesHelper::TYPE_DOC
                ]
            );

            $model->recordEvent(
                new FileCreateEvent(
                    $model::tableName(),
                    $model->id,
                    FilesHelper::TYPE_DOC,
                    $filename,
                    FilesHelper::LOAD_TYPE_MULTI
                ),
                get_class($model)
            );
        }

        for ($i = 1; $i < count($model->appFile) + 1; $i++) {
            $filename = $this->filenameGenerator->generateFileName($model, FilesHelper::TYPE_APP, ['counter' => $i]);

            $this->fileService->uploadFile(
                $model->appFile[$i - 1],
                $filename,
                [
                    'tableName' => DocumentOutWork::tableName(),
                    'fileType' => FilesHelper::TYPE_APP
                ]
            );

            $model->recordEvent(
                new FileCreateEvent(
                    $model::tableName(),
                    $model->id,
                    FilesHelper::TYPE_APP,
                    $filename,
                    FilesHelper::LOAD_TYPE_MULTI
                ),
                get_class($model)
            );
        }
    }

    public function isAvailableDelete($id)
    {
        return [];
    }

    public function getUploadedFilesTables(DocumentOutWork $model)
    {
        $scanLinks = $model->getFileLinks(FilesHelper::TYPE_SCAN);
        $scanFiles = HtmlBuilder::createTableWithActionButtons(
            [
                array_merge(['Название файла'], ArrayHelper::getColumn($scanLinks, 'link'))
            ],
            [
                HtmlBuilder::createButtonsArray(
                    'Удалить',
                    Url::to('delete-file'),
                    ['modelId' => array_fill(0, count($scanLinks), $model->id), 'fileId' => ArrayHelper::getColumn($scanLinks, 'id')])
            ]
        );

        $docLinks = $model->getFileLinks(FilesHelper::TYPE_DOC);
        $docFiles = HtmlBuilder::createTableWithActionButtons(
            [
                array_merge(['Название файла'], ArrayHelper::getColumn($docLinks, 'link'))
            ],
            [
                HtmlBuilder::createButtonsArray(
                    'Удалить',
                    Url::to('delete-file'),
                    ['modelId' => array_fill(0, count($docLinks), $model->id), 'fileId' => ArrayHelper::getColumn($docLinks, 'id')])
            ]
        );

        $appLinks = $model->getFileLinks(FilesHelper::TYPE_APP);
        $appFiles = HtmlBuilder::createTableWithActionButtons(
            [
                array_merge(['Название файла'], ArrayHelper::getColumn($appLinks, 'link'))
            ],
            [
                HtmlBuilder::createButtonsArray(
                    'Удалить',
                    Url::to('delete-file'),
                    ['modelId' => array_fill(0, count($appLinks), $model->id), 'fileId' => ArrayHelper::getColumn($appLinks, 'id')])
            ]
        );

        return ['scan' => $scanFiles, 'doc' => $docFiles, 'app' => $appFiles];
    }
}