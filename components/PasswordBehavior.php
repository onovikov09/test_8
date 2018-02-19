<?php

namespace app\components;

use yii\base\Behavior;
use yii\db\ActiveRecord;


class PasswordBehavior extends Behavior
{
    public $attribute;

    /**
     * @return array
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_UPDATE => 'generatePassword',
            ActiveRecord::EVENT_BEFORE_INSERT => 'generatePassword',
        ];
    }

    public function generatePassword()
    {
        if ($newPassword = $this->owner->{$this->attribute}) {
            $this->owner->setPassword($newPassword);
        }
    }
}