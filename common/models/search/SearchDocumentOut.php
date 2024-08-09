<?php

namespace common\models\search;

use common\helpers\DateFormatter;
use common\models\work\document_in_out\DocumentInWork;
use common\models\work\document_in_out\DocumentOutWork;
use MongoDB\BSON\Document;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class SearchDocumentOut extends DocumentOutWork
{
    public $fullNumber;
    public $companyName;
    public $sendMethodName;
    public $localDate;
    public $realDate;
    public $realNumber;
    public $documentNumber;
    public $documentDate;
    public $sendDate;
    public $documentTheme;

    public $archive;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'document_number', 'position_id', 'company_id', 'signed_id', 'executor_id', 'creator_id', 'archive'], 'integer'],
            [['documentNumber', 'fullNumber'], 'string'],
            [['localDate', 'realDate', 'documentTheme', 'correspondentName', 'companyName', 'sendMethodName'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $this->load($params);

        $query = DocumentOutWork::find()
            ->joinWith('company');

        if ($this->localDate !== '' && $this->localDate !== null) {
            $dates = DateFormatter::splitDates($this->localDate);
            $query->andWhere(
                ['BETWEEN', 'document_date',
                    DateFormatter::format($dates[0], DateFormatter::dmy_dot, DateFormatter::Ymd_dash),
                    DateFormatter::format($dates[1], DateFormatter::dmy_dot, DateFormatter::Ymd_dash)]);
        }

        if ($this->realDate !== '' && $this->realDate !== null) {
            $dates = DateFormatter::splitDates($this->realDate);
            $query->andWhere(['BETWEEN', 'document_date', $dates[0], $dates[1]]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['document_date' => SORT_DESC, 'document_number' => SORT_DESC, 'document_postfix' => SORT_DESC]]
        ]);

        $dataProvider->sort->attributes['documentNumber'] = [
            'asc' => ['document_number' => SORT_ASC, 'document_postfix' => SORT_ASC],
            'desc' => ['document_number' => SORT_DESC, 'document_postfix' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['localDate'] = [
            'asc' => ['sent_date' => SORT_ASC],
            'desc' => ['sent_date' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['realDate'] = [
            'asc' => ['document_date' => SORT_ASC],
            'desc' => ['document_date' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['realNumber'] = [
            'asc' => ['real_number' => SORT_ASC],
            'desc' => ['real_number' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['companyName'] = [
            'asc' => ['company.name' => SORT_ASC],
            'desc' => ['company.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['documentTheme'] = [
            'asc' => ['document_theme' => SORT_ASC],
            'desc' => ['document_theme' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['sendMethodName'] = [
            'asc' => ['send_method' => SORT_ASC],
            'desc' => ['send_method' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['needAnswer'] = [
            'asc' => ['need_answer' => SORT_DESC],
            'desc' => ['need_answer' => SORT_ASC],
        ];

        //var_dump($this->realDate);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // строгие фильтры
        $query->andFilterWhere([
            'send_method' => $this->sendMethodName,
        ]);

        // гибкие фильтры Like
        $query->andFilterWhere(['like', "CONCAT(document_number, '/', document_postfix)", $this->fullNumber])
            ->andFilterWhere(['like', 'document_number', $this->realNumber])
            ->andFilterWhere(['like', 'company.name', $this->companyName])
            ->andFilterWhere(['like', 'document_theme', $this->documentTheme])
            ->andFilterWhere(['like', 'real_number', $this->realNumber]);

        return $dataProvider;
    }
}