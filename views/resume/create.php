<h1><?= yii\helpers\Html::encode($this->context->title) ?></h1>

<?php $skillGrade = json_encode($resume->getArraySkillGrade(), JSON_UNESCAPED_UNICODE) ?>
<?php $this->registerJs("var skillGrade = $skillGrade", \yii\web\View::POS_HEAD) ?>

<?php if ($resume->id) { ?>
    <a href="<?= \yii\helpers\Url::to(["resume/view","id"=>$resume->id]) ?>" class="btn btn-success">Просмотр</a>
    <a href="<?= \yii\helpers\Url::to(["resume/delete","id"=>$resume->id]) ?>" class="btn btn-danger">Удалить</a><br><br>
<?php } ?>

<div class="resume">
    <div class="wrap_field">
        <input type="text" name="Resume[title]" placeholder="Специализация" value="<?= $resume->title ?>">
    </div>
    <div class="wrap_field">
        <textarea type="text" name="Resume[description]" placeholder="Описание"><?= $resume->description ?></textarea>
    </div>
    <div class="wrap_field">
    <?php
        $format = <<< SCRIPT
            function format(state) {
                return '<span class="skill_title" data-id="' + state.id + '" data-skill_grade="' 
                + (skillGrade[state.id] || 0) + '">' + state.text + '<span class="skill_grade"></span></span>';
            }
SCRIPT;
        $this->registerJs($format, \yii\web\View::POS_HEAD);

        echo \kartik\select2\Select2::widget([
            'name' => "Resume[skill_ids]",
            'value' => $resume->getArraySkill(),
            'data' => \app\models\Skill::getList(),
            'showToggleAll' => false,
            'size' => \kartik\select2\Select2::LARGE,
            'options' => ['placeholder' => 'Выберите навык из существующих или добавьте свой', 'multiple' => true],
            'pluginOptions' => [
                'tags' => true,
                'maximumInputLength' => 20,
                'templateSelection' => new \yii\web\JsExpression($format),
                'escapeMarkup' => new \yii\web\JsExpression("function(m) { return m; }"),
            ],
        ]);
    ?>
    </div>
    <input type="button" class="btn btn-primary js_resume_create" value="<?= ($resume->isNewRecord ? 'Добавить резюме'
        : 'Обновить резюме') ?>">
    <input type="hidden" name="Resume[id]" value="<?= $resume->id ?>">
</div>

<?= $this->registerCssFile('@web/css/jquery.raty.css'); ?>
<?= $this->registerJsFile('@web/js/jquery.raty.js', ['depends' => [yii\web\JqueryAsset::className()]]); ?>