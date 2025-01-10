<?php

namespace frontend\models\search;

use common\components\dictionaries\base\DocumentStatusDictionary;
use common\components\interfaces\SearchInterfaces;
use common\helpers\DateFormatter;
use frontend\models\search\abstractBase\DocumentSearch;
use frontend\models\work\document_in_out\DocumentInWork;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

/**
 * SearchDocumentIn represents the model behind the search form of `app\models\common\DocumentIn`.
 */
class SearchDocumentIn extends DocumentSearch implements SearchInterfaces
{
    public $localDate;              // дата поступления документа (используется для сортировки)
    public $realDate;               // регистрационная дата документа (используется для сортировки)


    public function rules()
    {
        return array_merge(parent::rules(), [
            [['local_number'], 'integer'],
            [['realNumber'], 'string'],
            [['localDate', 'realDate'], 'safe'],
        ]);
    }

    /**
     * Создает экземпляр DataProvider с учетом поискового запроса (фильтров или сортировки)
     *
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $this->load($params);
        $query = DocumentInWork::find()
            ->joinWith([
                'company',
                'correspondent',
                'correspondent.people' => function ($query) {
                    $query->alias('correspondentPeople');
                },
                'inOutDocument.responsible' => function ($query) {
                    $query->alias('responsible');
                },
                'inOutDocument.responsible.people' => function ($query) {
                    $query->alias('responsiblePeople');
                }
            ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['local_date' => SORT_DESC, 'local_number' => SORT_DESC, 'local_postfix' => SORT_DESC]]
        ]);

        $this->sortAttributes($dataProvider);
        $this->filterQueryParams($query);

        return $dataProvider;
    }

    /**
     * Кастомизированная сортировка по полям таблицы, с учетом родительской сортировки
     *
     * @param ActiveDataProvider $dataProvider
     * @return void
     */
    public function sortAttributes(ActiveDataProvider $dataProvider) {
        parent::sortAttributes($dataProvider);

        $dataProvider->sort->attributes['localDate'] = [
            'asc' => ['local_date' => SORT_ASC],
            'desc' => ['local_date' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['realDate'] = [
            'asc' => ['real_date' => SORT_ASC],
            'desc' => ['real_date' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['realNumber'] = [
            'asc' => ['real_number' => SORT_ASC],
            'desc' => ['real_number' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['needAnswer'] = [
            'asc' => ['need_answer' => SORT_DESC],
            'desc' => ['need_answer' => SORT_ASC],
        ];
    }


    /**
     * Вызов функций фильтров по параметрам запроса
     *
     * @param ActiveQuery $query
     * @return void
     */
    public function filterQueryParams(ActiveQuery $query) {
        $this->filterStatus($query);
        $this->filterDate($query);
        $this->filterNumber($query);
        $this->filterExecutorName($query);
        $this->filterAbstractQueryParams($query, $this->documentTheme, $this->keyWords, $this->sendMethodName, $this->correspondentName);
    }


    /**
     * Фильтрует по статусу документа
     *
     * @param ActiveQuery $query
     * @return void
     */
    private function filterStatus(ActiveQuery $query) {
        $statusConditions = [
            DocumentStatusDictionary::CURRENT => ['>=', 'local_date', date('Y') . '-01-01'],
            DocumentStatusDictionary::ARCHIVE => ['<=', 'local_date', date('Y-m-d')],
            DocumentStatusDictionary::EXPIRED => [
                'AND',
                ['<', 'date', date('Y-m-d')],
                ['IS', 'document_out_id', null]
            ],
            DocumentStatusDictionary::NEEDANSWER => ['=', 'need_answer', 1],
            DocumentStatusDictionary::RESERVED => ['like', 'LOWER(document_theme)', 'РЕЗЕРВ'],
        ];
        $query->andWhere($statusConditions[$this->status]);
    }

    /**
     * Фильтрация документов по диапазону дат
     *
     * @param ActiveQuery $query
     * @return void
     */
    private function filterDate(ActiveQuery $query) {
        if ($this->startDateSearch != '' || $this->finishDateSearch != '')
        {
            $dateFrom = $this->startDateSearch ? date('Y-m-d', strtotime($this->startDateSearch)) : DateFormatter::DEFAULT_YEAR_START;
            $dateTo =  $this->finishDateSearch ? date('Y-m-d', strtotime($this->finishDateSearch)) : date('Y-m-d');

            $query->andWhere(['or',
                ['between', 'local_date', $dateFrom, $dateTo],
                ['between', 'real_date', $dateFrom, $dateTo],
            ]);
        }
    }

    /**
     * Фильтрация документа по заданному номеру (реальному или локальному)
     *
     * @param ActiveQuery $query
     * @return void
     */
    private function filterNumber(ActiveQuery $query) {
        $query->andFilterWhere(['or',
            ['like', 'real_number', $this->number],
            ['like', "CONCAT(local_number, '/', local_postfix)", $this->number],
        ]);
    }

    /**
     * Фильтрует по исполнителю документа
     *
     * @param ActiveQuery $query
     * @return void
     */
    private function filterExecutorName(ActiveQuery $query) {
        $query->andFilterWhere(['like', 'LOWER(responsiblePeople.firstname)', mb_strtolower($this->executorName)]);
    }
}
