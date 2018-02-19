<?php

namespace app\components;

use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;
use yii\validators\Validator;

class UploadFileBehavior extends Behavior
{
    /**
     * @var string название атрибута, хранящего в себе путьк файлу
     */
    public $fileSrcAttribute = 'src';

    /**
     * @var string название атрибута, хранящего в себе тип файла
     */
    public $fileTypeAttribute = 'type';

    /**
     * @var string название атрибута, в котором приходит файл
     */
    public $nameParam = 'files';

    /**
     * @var string название атрибута, куда сохранить ид файла
     */
    public $saveFieldName;

    /**
     * @var класс модели для сохранения файла
     */
    public $fileModel;

    /**
     * @var разрешена ли мультизагрузка
     */
    public $multiple = false;

    /**
     * @var string алиас директории, куда будем сохранять файлы
     */
    public $savePathAlias = '@app/web/uploads';

    public function getFullPath()
    {
        return \Yii::getAlias($this->savePathAlias) . DIRECTORY_SEPARATOR . $this->owner->tableName() . DIRECTORY_SEPARATOR;
    }

    public function getWebPath()
    {
        return DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $this->owner->tableName() . DIRECTORY_SEPARATOR;
    }

    public function checkPath()
    {
        $path = $this->getFullPath();
        if (!is_dir($path)) {
            if (!mkdir($path, 0777, true)) {
                return false;
            }
        }
        return true;
    }

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_VALIDATE=> 'setFiles',
            ActiveRecord::EVENT_AFTER_VALIDATE=> 'uploadFile',
        ];
    }

    public function setFiles($event)
    {
        $modelOwner = $this->owner;
        $fieldForScenario = $modelOwner->scenarios()[$modelOwner->getScenario()];
        if (!in_array($this->nameParam, $fieldForScenario) && !isset($_FILES[ucfirst($modelOwner->tableName())])) {
            return;
        }

        $files = UploadedFile::getInstances($modelOwner, $this->nameParam);
        if (empty($files)) {
            return;
        }

        if (!$this->multiple && is_array($files)) {
            $files = $files[0];
        }

        $modelOwner->{$this->nameParam} = $files;

        $fileModel = new $this->fileModel();
        if ($modelOwner->tableName() == $fileModel->tableName()) {
            $this->uploadFile();
        }
    }

    public function uploadFile($event = null)
    {
        $fileModel = new $this->fileModel();
        $modelOwner = $this->owner;

        $isSelfOwner = false;
        if ($modelOwner->tableName() == $fileModel->tableName()) {
            $isSelfOwner = true;
            $fileModel = $modelOwner;

            if ($event && 'afterValidate' == $event->name) {
                return;
            }
        }

        if (is_null($modelOwner->{$this->nameParam}) || !$modelOwner->{$this->nameParam}) {
            return;
        }

        if (!$this->checkPath()) {
            $modelOwner->addError($this->nameParam, \Yii::t('app', 'DIRECTORY_NOT_EXIST'));
            return;
        }

        $file = $modelOwner->{$this->nameParam};
        $ext = pathinfo($file->name)['extension'];
        $newFileName =  uniqid() . "." . $ext;

        $fileModel->{$this->fileSrcAttribute} = $this->getWebPath() . $newFileName;
        if (!$isSelfOwner) {
            $fileModel->{$this->fileTypeAttribute} = $modelOwner->tableName();
        }

        if ($file->saveAs($this->getFullPath() . $newFileName, false)) {
            if (!$isSelfOwner) {
                $fileModel->save(false);
                $modelOwner->{$this->saveFieldName} = $fileModel->id;
            }

        }
        $this->owner->addErrors($fileModel->errors);
    }
}