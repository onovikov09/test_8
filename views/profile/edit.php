<?php $user = Yii::$app->user->getIdentity() ?>

<a href="<?= \yii\helpers\Url::to(["site/index"]) ?>">на главную</a>

<h1><?= yii\helpers\Html::encode($this->context->title) ?></h1>

<div class="profile">
    <div class="lk_photo-drop change-form change-avatar" id="lk_photo-drop-image">
        <input type="text" name="User[avatar]" class="hidden" value="<?= $user->avatar ?>" />
        <div class="lk_photo-drop-preview">
            <div class="lk_photo-drop-image change-form-avatar">
                <img data-dz-thumbnail src="<?= $user->avatar_or_stub ?> "/>
            </div>
        </div>
        <p class="change-form-help avatar"><?= Yii::t('app', 'MINIMAL_SIZE _AVATAR') ?></p>
    </div>
    <div class="wrap_field">
        <input type="text" name="User[email]" placeholder="Email" value="<?= $user->email ?>" disabled>
    </div>
    <div class="wrap_field">
        <input type="text" name="User[first_name]" placeholder="Имя" value="<?= $user->first_name ?>">
    </div>
    <div class="wrap_field">
        <input type="text" name="User[last_name]" placeholder="Фамилия" value="<?= $user->last_name ?>">
    </div>
    <div class="btn-group wrap_field" data-toggle="buttons">
        <label class="btn btn-primary <?= $user->isMen() ? "active" : '' ?> radio_items">
            <input type="radio" name="User[gender]" autocomplete="off" value="men" checked="<?= $user->isMen() ?>"> Мужчина
        </label>
        <label class="btn btn-primary <?= $user->isWomen() ? "active" : '' ?> radio_items">
            <input type="radio" name="User[gender]" autocomplete="off" value="women" checked="<?= $user->isWomen() ?>"> Женщина
        </label>
    </div>
    <div class="wrap_field">
        <?php
            echo \yii\widgets\MaskedInput::widget([
                'name' => "User[birth]",
                'clientOptions' => ['alias' => 'dd/mm/yyyy'],
                'value' => $user->birthday_formatted
            ]);
        ?>
    </div>
    <div class="wrap_field">
        <textarea type="text" name="User[biography]" placeholder="Краткая биография"><?= $user->biography ?></textarea>
    </div>
    <div class="wrap_field">
        <input type="password" name="User[password]" placeholder="Новый пароль">
    </div>
    <div class="wrap_field">
        <input type="password" name="User[password_confirm]" placeholder="Новый пароль еще раз">
    </div>

    <input type="submit" class="btn btn-primary js_profile_edit" value="Обновить профиль">
</div>

<?= $this->registerJsFile('@web/js/dropzone.min.js', ['depends' => [yii\web\JqueryAsset::className()]]); ?>