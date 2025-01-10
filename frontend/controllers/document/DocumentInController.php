<?php

namespace frontend\controllers\document;

use common\components\wizards\LockWizard;
use common\controllers\DocumentController;
use common\helpers\DateFormatter;
use common\helpers\files\FilesHelper;
use common\helpers\html\HtmlBuilder;
use common\helpers\SortHelper;
use common\helpers\StringFormatter;
use common\repositories\dictionaries\CompanyRepository;
use common\repositories\dictionaries\PeopleRepository;
use common\repositories\dictionaries\PositionRepository;
use common\repositories\document_in_out\DocumentInRepository;
use common\repositories\document_in_out\InOutDocumentsRepository;
use common\repositories\general\FilesRepository;
use common\services\general\files\FileService;
use common\services\general\PeopleStampService;
use DomainException;
use frontend\events\document_in\InOutDocumentCreateEvent;
use frontend\events\document_in\InOutDocumentDeleteEvent;
use frontend\events\document_in\InOutDocumentUpdateEvent;
use frontend\models\search\SearchDocumentIn;
use frontend\models\work\document_in_out\DocumentInWork;
use frontend\services\document\DocumentInService;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class DocumentInController extends DocumentController
{
    private DocumentInRepository $repository;
    private InOutDocumentsRepository $inOutRepository;
    private PeopleRepository $peopleRepository;
    private PositionRepository $positionRepository;
    private CompanyRepository $companyRepository;
    private DocumentInService $service;
    private PeopleStampService $peopleStampService;
    private LockWizard $lockWizard;
    public function __construct(
                                 $id,
                                 $module,
        DocumentInRepository     $repository,
        InOutDocumentsRepository $inOutRepository,
        PeopleRepository         $peopleRepository,
        PositionRepository       $positionRepository,
        CompanyRepository        $companyRepository,
        DocumentInService        $service,
        PeopleStampService       $peopleStampService,
        LockWizard               $lockWizard,
                                 $config = [])
    {
        parent::__construct($id, $module, Yii::createObject(FileService::class), Yii::createObject(FilesRepository::class), $config);
        $this->repository = $repository;
        $this->inOutRepository = $inOutRepository;
        $this->peopleRepository = $peopleRepository;
        $this->positionRepository = $positionRepository;
        $this->companyRepository = $companyRepository;
        $this->service = $service;
        $this->peopleStampService = $peopleStampService;
        $this->lockWizard = $lockWizard;
    }

    public function actionIndex()
    {
        $model = new DocumentInWork();
        $searchModel = new SearchDocumentIn();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if ($model->load(Yii::$app->request->post())){
            $this->repository->createReserve($model);
            $this->repository->save($model);
        }
        return $this->render('index', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->repository->get($id)
        ]);
    }

    public function actionCreate()
    {
        $model = new DocumentInWork();
        $correspondentList = $this->peopleRepository->getOrderedList(SortHelper::ORDER_TYPE_FIO);
        $availablePositions = $this->positionRepository->getList();
        $availableCompanies = $this->companyRepository->getList();
        $mainCompanyWorkers = $this->peopleRepository->getPeopleFromMainCompany();
        if ($model->load(Yii::$app->request->post())) {
            $model->generateDocumentNumber();
            $this->service->getPeopleStamps($model);

            if (!$model->validate()) {
                throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($model->getErrors()));
            }
            $this->repository->save($model);
            if ($model->needAnswer) {
                $model->recordEvent(new InOutDocumentCreateEvent($model->id, null, $model->dateAnswer, $model->nameAnswer), DocumentInWork::class);
            }
            $this->service->getFilesInstances($model);
            $this->service->saveFilesFromModel($model);
            $model->releaseEvents();
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'correspondentList' => $correspondentList,
            'availablePositions' => $availablePositions,
            'availableCompanies' => $availableCompanies,
            'mainCompanyWorkers' => $mainCompanyWorkers,
        ]);
    }
    public function actionReserve()
    {
        $model = new DocumentInWork();
        $this->repository->createReserve($model);
        $model->generateDocumentNumber();
        $this->repository->save($model);
        return $this->redirect(['index']);
    }

    public function actionUpdate($id)
    {
        if ($this->lockWizard->lockObject($id, DocumentInWork::tableName(), Yii::$app->user->id)) {
            $model = $this->repository->get($id);
            /** @var DocumentInWork $model */
            $model->setValuesForUpdate();

            $correspondentList = $this->peopleRepository->getOrderedList(SortHelper::ORDER_TYPE_FIO);
            $availablePositions = $this->positionRepository->getList($model->correspondentWork->people_id);
            $availableCompanies = $this->companyRepository->getList($model->correspondentWork->people_id);
            $mainCompanyWorkers = $this->peopleRepository->getPeopleFromMainCompany();
            $tables = $this->service->getUploadedFilesTables($model);
            if ($model->load(Yii::$app->request->post())) {
                $this->lockWizard->unlockObject($id, StringFormatter::getLastSegmentBySlash($this->id));
                $this->service->getPeopleStamps($model);

                if (!$model->validate()) {
                    throw new DomainException('Ошибка валидации. Проблемы: ' . json_encode($model->getErrors()));
                }
                $this->repository->save($model);
                if ($model->needAnswer) {
                    if ($this->inOutRepository->getByDocumentInId($model->id)){
                        $model->recordEvent(
                            new InOutDocumentUpdateEvent(
                                $model->id,
                                null,
                                $model->dateAnswer,
                                $model->nameAnswer
                            ),
                            DocumentInWork::class
                        );
                    }
                    else {
                        $model->recordEvent(
                            new InOutDocumentCreateEvent(
                                $model->id,
                                null,
                                $model->dateAnswer,
                                $model->nameAnswer
                            ),
                            DocumentInWork::class
                        );
                    }

                }
                else {
                    $model->recordEvent(new InOutDocumentDeleteEvent($model->id), DocumentInWork::class);
                }
                $this->service->getFilesInstances($model);
                $this->service->saveFilesFromModel($model);
                $model->releaseEvents();
                return $this->redirect(['view', 'id' => $model->id]);
            }

            return $this->render('update', [
                'model' => $model,
                'correspondentList' => $correspondentList,
                'availablePositions' => $availablePositions,
                'availableCompanies' => $availableCompanies,
                'mainCompanyWorkers' => $mainCompanyWorkers,
                'scanFile' => $tables['scan'],
                'docFiles' => $tables['doc'],
                'appFiles' => $tables['app'],
            ]);
        }
        else {
            Yii::$app->session->setFlash
                ('error', "Объект редактируется пользователем {$this->lockWizard->getUserdata($id, DocumentInWork::tableName())}. Попробуйте повторить попытку позднее");
            return $this->redirect(Yii::$app->request->referrer ?: ['index']);
        }
    }

    public function actionDelete($id)
    {
        $model = $this->repository->get($id);
        $number = $model->fullNumber;
        if ($model) {
            $this->repository->delete($model);
            Yii::$app->session->setFlash('success', "Документ $number успешно удален");
            return $this->redirect(['index']);
        }
        else {
            throw new DomainException('Модель не найдена');
        }
    }

    public function actionDependencyDropdown()
    {
        $id = Yii::$app->request->post('id');
        $response = '';

        if ($id === '') {
            // Получаем позиции и компании
            $response .= HtmlBuilder::buildOptionList($this->positionRepository->getList());
            $response .= "|split|";
            $response .= HtmlBuilder::buildOptionList($this->companyRepository->getList());
        } else {
            // Получаем позиции для указанного ID
            $positions = $this->positionRepository->getList($id);
            $response .= count($positions) > 0 ? HtmlBuilder::buildOptionList($positions) : HtmlBuilder::createEmptyOption();
            $response .= "|split|";
            // Получаем компанию для указанного ID
            $companies = $this->companyRepository->getList($id);
            $response .= count($companies) > 0 ? HtmlBuilder::buildOptionList($companies) : HtmlBuilder::createEmptyOption();
        }

        echo $response;
        exit;
    }

    public function beforeAction($action)
    {
        /*if (Yii::$app->rac->isGuest() || !Yii::$app->rac->checkUserAccess(Yii::$app->rac->authId(), get_class(Yii::$app->controller), $action)) {
            Yii::$app->session->setFlash('error', 'У Вас недостаточно прав. Обратитесь к администратору для получения доступа');
            $this->redirect(Yii::$app->request->referrer);
            return false;
        }*/

        return parent::beforeAction($action); 
    }
}