<?php

namespace app\components;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class FrontController extends Controller
{
    public $title = null;
    //public $enableCsrfValidation = false;

    public function setTitle($titleChunk = '')
    {
        $this->title = ($titleChunk ? $titleChunk . ' | ' : '') . Yii::$app->id;
    }
}
