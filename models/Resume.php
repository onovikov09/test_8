<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * This is the model class for table "resume".
 *
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string $description
 * @property int $sort
 * @property int $is_active
 * @property int $created_at
 * @property int $update_at
 */
class Resume extends \yii\db\ActiveRecord
{
    public $skill_ids;

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'resume';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'title'], 'required'],
            [['user_id', 'created_at', 'skill_ids'], 'default', 'value' => null],
            [['user_id', 'sort', 'is_active', 'created_at', 'update_at'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['description'], 'string', 'max' => 1024],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'title' => 'Title',
            'description' => 'Description',
            'sort' => 'Sort',
            'is_active' => 'Is Active',
            'created_at' => 'Created At',
            'update_at' => 'Update At',
        ];
    }

    /**
     * Связки навыков и резюме
     *
     * @return \yii\db\ActiveQuery
     */
    public function getResume2skill()
    {
        return $this->hasMany(Resume2skill::className(), ["resume_id" => "id"]);
    }

    /**
     * Возвращает отсортированный список
     *
     * @return \yii\db\ActiveQuery
     */
    public function getResume2skillAsSort()
    {
        return $this->getResume2skill()->innerJoinWith(["skill"])->orderBy("skill.title");
    }

    /**
     * Привязанные навыки
     *
     * @return $this
     */
    public function getSkills()
    {
        return $this->hasMany(Skill::className(), ['id' => 'skill_id'])->via('resume2skill');
    }

    /**
     * Возвращает автора
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * Основная работа с навыками
     *
     */
    public function setSkills()
    {
        $deletedResume2SkillIds = [];

        //Обновление грейдов навыков
        foreach ($this->resume2skill as $item)
        {
            if (isset($this->skill_ids[$item->skill_id]))
            {
                if ($this->skill_ids[$item->skill_id] != $item->grade)
                {
                    $item->grade = $this->skill_ids[$item->skill_id];
                    $item->save(false);
                }

                unset($this->skill_ids[$item->skill_id]);
            } else {
                $deletedResume2SkillIds[] = $item->id;
            }
        }

        //Удаленные навыки
        if (!empty($deletedResume2SkillIds)) {
            Resume2skill::deleteAll(['in', 'id', $deletedResume2SkillIds]);
        }

        //Добавление новых навыков
        if ($this->skill_ids)
        {
            $skills = Skill::getList();
            foreach ($this->skill_ids as $id_or_title=>$grade)
            {
                if (!isset($skills[$id_or_title]))
                {
                    $id_or_title = Skill::create($id_or_title);
                }

                Resume2skill::create($this->id, $id_or_title, $grade);
            }
        }
    }

    /**
     * Возвращает ссылку на резюме
     *
     * @param bool $isAbsolutLink
     * @return string
     */
    public function getUrl($isAbsolutLink = false)
    {
        return Url::to(["resume/view", "id"=>$this->id], $isAbsolutLink);
    }

    /**
     * Проверка является ли пользователь автором резюме
     *
     * @return bool
     */
    public function isForeign()
    {
        return (Yii::$app->user->isGuest || $this->user_id != Yii::$app->user->getIdentity()->id);
    }

    /**
     * Список привязанных к резюме навыков
     *
     * @return array
     */
    public function getArraySkill()
    {
        return $this->getResume2skill()->select(['resume2skill.skill_id'])->column();
    }

    /**
     * Список ид навыков и грейды к ним
     *
     * @return array
     */
    public function getArraySkillGrade()
    {
        return ArrayHelper::map($this->getResume2skill()->all(), 'skill_id', 'grade');
    }
}
