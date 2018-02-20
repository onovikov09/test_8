<h1><?= yii\helpers\Html::encode($this->context->title) ?></h1>

<?php if (!$resume->isForeign()) { ?>
    <a href="<?= \yii\helpers\Url::to(["resume/edit","id"=>$resume->id]) ?>" class="btn btn-success">Редактировать</a>
    <a href="<?= \yii\helpers\Url::to(["resume/delete","id"=>$resume->id]) ?>" class="btn btn-danger">Удалить</a><br><br>
<?php } ?>

<img src="<?= $resume->user->avatar_or_stub ?>" alt="<?= $resume->user->full_name ?>" class="avatar_preview">
<h2><?= $resume->user->full_name ?> - <?= $resume->title ?></h2>
<ul class="list-group">
    <?php if ($resume->user->biography) { ?>
        <li class="list-group-item">Краткая биография: <?= $resume->user->biography ?></li>
    <?php } ?>
    <li class="list-group-item">Обо мне: <?= $resume->description ?></li>
    <?php if ($resume->resume2skillAsSort) { ?>
        <li class="list-group-item">Навыки:
            <ul class="list-group">
                <?php foreach($resume->resume2skillAsSort as $item) { ?>
                    <li class="list-group-item">
                        <?= $item->skill->title ?>: <span class="view_rate" data-score="<?= $item->grade ?>"></span>
                    </li>
                <?php } ?>
            </ul>
        </li>
    <?php } ?>
</ul>

<?= $this->registerCssFile('@web/css/jquery.raty.css'); ?>
<?= $this->registerJsFile('@web/js/jquery.raty.js', ['depends' => [yii\web\JqueryAsset::className()]]); ?>
<?= $this->registerJs("$('.view_rate').raty({starOff:'/images/star-off.png',starOn:'/images/star-on.png',readOnly:true});", \yii\web\View::POS_LOAD); ?>