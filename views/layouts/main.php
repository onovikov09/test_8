<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->context->titlePage) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<?php
    if ($flashes = Yii::$app->getSession()->getAllFlashes()) {
        foreach ($flashes as $flash) {
            $this->registerJs('toaster(' . json_encode($flash, JSON_UNESCAPED_UNICODE) . ');');
        }
    }
?>

<div class="wrap">
    <?= $this->render('/layouts/nav_bar') ?>
    <div class="container">
        <?= $content ?>
        <?= $this->render('/layouts/footer') ?>
    </div>
</div>

<?= $this->render('/layouts/form_signin') ?>
<?= $this->render('/layouts/form_signup') ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
