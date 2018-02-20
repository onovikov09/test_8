<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "resume2skill".
 *
 * @property int $id
 * @property string $resume_id
 * @property int $skill_id
 * @property int $grade
 */
class Resume2skill extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'resume2skill';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['resume_id', 'skill_id'], 'required'],
            [['skill_id', 'grade'], 'default', 'value' => null],
            [['skill_id', 'grade'], 'integer'],
            [['resume_id'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'resume_id' => 'Resume ID',
            'skill_id' => 'Skill ID',
            'grade' => 'Grade',
        ];
    }

    /**
     * Получение связанного навыка
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSkill()
    {
        return $this->hasOne(Skill::className(), ["id" => "skill_id"]);
    }

    /**
     * Добавление привязки навыка
     *
     * @param $resume_id
     * @param $skill_id
     * @param $grade
     * @return static
     */
    public static function create($resume_id, $skill_id, $grade)
    {
        $resume2skill = new static();
        $resume2skill->resume_id = $resume_id;
        $resume2skill->skill_id = $skill_id;
        $resume2skill->grade = $grade;
        $resume2skill->save(false);
        return $resume2skill;
    }
}
