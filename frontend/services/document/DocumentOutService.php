<?php

namespace frontend\services\document;

use common\helpers\files\FilePaths;
use common\helpers\files\FilesHelper;
use common\models\work\document_in_out\DocumentInWork;
use common\models\work\document_in_out\DocumentOutWork;
use common\repositories\general\CompanyRepository;
use common\repositories\general\PositionRepository;
use common\services\general\files\FileService;
use yii\web\UploadedFile;

class DocumentOutService
{
    private PositionRepository $positionRepository;
    private CompanyRepository $companyRepository;
    private FileService $fileService;

    public function __construct(
        PositionRepository $positionRepository,
        CompanyRepository $companyRepository,
        FileService $fileService
    )
    {
        $this->positionRepository = $positionRepository;
        $this->companyRepository = $companyRepository;
        $this->fileService = $fileService;
    }

    public function getFilesInstances(DocumentOutWork $model)
    {
        $model->scanFile = UploadedFile::getInstance($model, 'scanFile');
        $model->appFiles = UploadedFile::getInstances($model, 'appFiles');
        $model->docFiles = UploadedFile::getInstances($model, 'docFiles');
    }

    public function saveFilesFromModel(DocumentOutWork $model)
    {
        $this->fileService->uploadFile(
            $model,
            $model->scanFile,
            FilesHelper::TYPE_SCAN,
            FilesHelper::LOAD_TYPE_SINGLE,
            FilePaths::DOCUMENT_OUT_SCAN
        );

        for ($i = 1; $i < count($model->docFiles) + 1; $i++) {
            $this->fileService->uploadFile(
                $model,
                $model->docFiles[$i - 1],
                FilesHelper::TYPE_DOC,
                FilesHelper::LOAD_TYPE_MULTI,
                FilePaths::DOCUMENT_OUT_DOC,
                ['counter' => $i]
            );
        }

        for ($i = 1; $i < count($model->appFiles) + 1; $i++) {
            $this->fileService->uploadFile(
                $model,
                $model->appFiles[$i - 1],
                FilesHelper::TYPE_APP,
                FilesHelper::LOAD_TYPE_MULTI,
                FilePaths::DOCUMENT_OUT_APP,
                ['counter' => $i]
            );
        }
    }
}