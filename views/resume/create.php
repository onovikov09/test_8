<?php $user = Yii::$app->user->getIdentity() ?>

<a href="<?= Yii::$app->request->referrer ?>">вернуться назад</a>

<h1><?= yii\helpers\Html::encode($this->context->title) ?></h1>

<div class="resume">
    <div class="wrap_field">
        <input type="text" name="Resume[title]" placeholder="Должность">
    </div>
    <div class="wrap_field">
        <textarea type="text" name="Resume[description]" placeholder="О себе"></textarea>
    </div>
    <div class="wrap_field">
    <?php

            $format = <<< SCRIPT
                function format(state) {
                    return '<span class="skill_title" data-id="' + state.id + '" data-skill_grade="0">' + state.text + '<span class="skill_grade"></span></span>';
                }
SCRIPT;

            $this->registerJs($format, \yii\web\View::POS_HEAD);
            $escape = new \yii\web\JsExpression("function(m) { return m; }");


    echo \kartik\select2\Select2::widget([
        'name' => "Resume[skill_ids]",
        'value' => [],
        'data' => \app\models\Skill::getList(),
        'showToggleAll' => false,
        'options' => ['placeholder' => 'Выберите навык из существующих или добавьте свой', 'multiple' => true],
        'pluginOptions' => [
            'tags' => true,
            'maximumInputLength' => 20,
            'templateSelection' => new \yii\web\JsExpression('format'),
            //'templateResult' => new \yii\web\JsExpression('format'),
            'escapeMarkup' => $escape,
        ],
    ]);


    ?>
    </div>
    <input type="button" class="btn btn-primary js_resume_create" value="Добавить резюме">
</div>

<?= $this->registerCssFile('@web/css/jquery.raty.css'); ?>
<?= $this->registerJsFile('@web/js/jquery.raty.js', ['depends' => [yii\web\JqueryAsset::className()]]); ?>

