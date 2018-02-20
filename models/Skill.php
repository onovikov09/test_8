<?php

namespace app\models;

use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "skill".
 *
 * @property int $id
 * @property string $title
 * @property int $sort
 * @property int $is_active
 */
class Skill extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'skill';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['sort', 'is_active'], 'default', 'value' => null],
            [['sort', 'is_active'], 'integer'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'sort' => 'Sort',
            'is_active' => 'Is Active',
        ];
    }

    /**
     * Список навыков id => title
     *
     * @return array
     */
    public static function getList()
    {
        return ArrayHelper::map(self::find()->orderBy('sort ASC, title ASC')->all(), 'id', 'title');
    }

    /**
     * Добавление нового навыка
     *
     * @param $title
     * @return mixed
     */
    public static function create($title)
    {
        $skill = new static();
        $skill->title = $title;
        $skill->save(false);
        return $skill->id;
    }
}
