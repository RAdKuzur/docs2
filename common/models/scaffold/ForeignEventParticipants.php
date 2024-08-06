<?php

namespace common\models\scaffold;

/**
 * This is the model class for table "foreign_event_participants".
 *
 * @property int $id
 * @property string|null $firstname
 * @property string|null $secondname
 * @property string|null $patronymic
 * @property string|null $birthdate
 * @property int|null $sex 0 - мужской, 1 - женский
 * @property int|null $is_true
 * @property int|null $guaranteed_true
 * @property string|null $email
 */
class ForeignEventParticipants extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'foreign_event_participants';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sex', 'is_true', 'guaranteed_true'], 'integer'],
            [['firstname', 'secondname', 'patronymic'], 'string', 'max' => 64],
            [['birthdate', 'email'], 'string', 'max' => 256],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'firstname' => 'Firstname',
            'secondname' => 'Secondname',
            'patronymic' => 'Patronymic',
            'birthdate' => 'Birthdate',
            'sex' => 'Sex',
            'is_true' => 'Is True',
            'guaranteed_true' => 'Guaranteed True',
            'email' => 'Email',
        ];
    }
}
