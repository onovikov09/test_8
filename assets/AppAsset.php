<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/toastr.min.css',
        '/components/bootstrap/css/bootstrap.min.css',
        '/components/bootstrap/css/bootstrap-theme.min.css',
        'css/site.css',
    ];
    public $js = [
        'js/toastr.min.js',
        '/components/bootstrap/js/bootstrap.min.js',
        'js/site.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];

    public function init()
    {
        parent::init();
        array_walk($this->css, [$this, 'addRev']);
        array_walk($this->js, [$this, 'addRev']);
    }

    private function addRev(&$item)
    {
        $char = ((false !== strpos((is_array($item) ? $item[0] : $item), "?")) ? "&" : "?");

        if (is_array($item)) {
            $item[0] .=  ($char . 'r=' .\Yii::$app->params['rev']);
        } else {
            $item .=  ( $char . 'r=' .\Yii::$app->params['rev']);
        }
    }
}
