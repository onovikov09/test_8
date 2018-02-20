<?php

namespace app\components;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class FrontController extends Controller
{
    public $titlePage = null;
    public $title = null;

    public function setTitle($titleChunk = '')
    {
        $this->titlePage = ($titleChunk ? $titleChunk . ' | ' : '') . Yii::$app->id;
        $this->title = $titleChunk;
    }

    public function checkActive($controllerName)
    {
        $current = $this->id;
        if (count(explode('/', $controllerName)) > 1) {
            $current = $this->route;
        }
        return ($current == $controllerName ? 'active' : '');
    }
}
