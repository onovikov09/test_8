<?php

namespace app\models;

use Yii;

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
            [['user_id', 'sort', 'created_at', 'skill_ids'], 'default', 'value' => null],
            [['user_id', 'sort', 'is_active', 'created_at'], 'integer'],
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
        ];
    }

    public function getResume2skill()
    {
        return $this->hasMany(Resume2skill::className(), ['resume_id' => 'id']);
    }

    public function getSkills()
    {
        return $this->hasMany(Skill::className(), ['id' => 'skill_id'])->via('resume2skill');
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function setSkills()
    {
        if (!$this->skill_ids) {
            return;
        }

        foreach ($this->resume2skill as $item)
        {
            if (isset($this->skill_ids[$item->id])) {
                $item->grade = $this->skill_ids[$item->id];
                $item->save(false);
                unset($this->skill_ids[$item->id]);
            }
        }

        $skills = Skill::getList();
        foreach ($this->skill_ids as $id_or_title=>$grade)
        {
            if (!isset($skills[$id_or_title])) {
                $id_or_title = Skill::create($id_or_title);
            }

            Resume2skill::create($this->id, $id_or_title, $grade);
        }

    }
}
