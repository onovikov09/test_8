<?php $user = Yii::$app->user->getIdentity() ?>

<h1><?= yii\helpers\Html::encode($this->context->title) ?></h1>

<div class="row profile">
    <div class="col-xs-12 col-lg-6">
        <div class="lk_photo-drop change-form change-avatar" id="lk_photo-drop-image">
            <input type="text" name="User[avatar]" class="hidden" value="<?= $user->avatar ?>" />
            <div class="lk_photo-drop-preview">
                <div class="lk_photo-drop-image change-form-avatar">
                    <img data-dz-thumbnail src="<?= $user->avatar_or_stub ?> "/>
                </div>
            </div>
            <p class="change-form-help avatar">Для загрузки аватара нажмите на картику
                <br>Максимальный размер загружаемой картинки 500Кб</p>
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
                <input type="radio" name="User[gender]" autocomplete="off" value="men"> Мужчина
            </label>
            <label class="btn btn-primary <?= $user->isWomen() ? "active" : '' ?> radio_items">
                <input type="radio" name="User[gender]" autocomplete="off" value="women"> Женщина
            </label>
        </div>
        <div class="wrap_field">
            <textarea type="text" name="User[biography]" placeholder="Краткая биография"><?= $user->biography ?></textarea>
        </div>
        <p>Смена пароля</p>
        <div class="wrap_field">
            <input type="password" name="User[password]" placeholder="Новый пароль">
        </div>
        <div class="wrap_field">
            <input type="password" name="User[password_confirm]" placeholder="Новый пароль еще раз">
        </div>

        <input type="submit" class="btn btn-primary js_profile_edit" value="Обновить профиль">
    </div>
    <div class="col-xs-12 col-lg-6">
        <h2>Ваши опубликованные резюме:</h2>
        <ul class="list-group">
            <?php if(empty($user->resume)) { ?>
                <li class="list-group-item"><span class="link_resume">У вас пока нет опубликованных резюме</span> <a href="<?= \yii\helpers\Url::to(["resume/create"]) ?>" class="btn btn-success delete_resume">Добавить</a></li>
            <?php } ?>
            <?php foreach($user->resume as $item) { ?>
                <li class="list-group-item">
                    <a href="<?= \yii\helpers\Url::to(["resume/delete","id"=>$item->id]) ?>" class="btn btn-danger delete_resume">Удалить</a>
                    <a href="<?= \yii\helpers\Url::to(["resume/edit","id"=>$item->id]) ?>" class="btn btn-success delete_resume">Редактировать</a>
                    <a href="<?= \yii\helpers\Url::to(["resume/view","id"=>$item->id]) ?>" class="link_resume"><?= $item->title ?></a>
                </li>
            <?php } ?>
        </ul>
    </div>
</div>

<?= $this->registerJsFile('@web/js/dropzone.min.js', ['depends' => [yii\web\JqueryAsset::className()]]); ?>