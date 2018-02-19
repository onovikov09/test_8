<div class="modal fade modal_signin_wrap" id="login-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-container">
            <h1>Авторизация</h1><br>
            <div class="wrap_field">
                <input type="text" name="User[email]" placeholder="Email">
            </div>
            <div class="wrap_field">
                <input type="password" name="User[password]" placeholder="Пароль">
            </div>

            <a href="<?= \yii\helpers\Url::to(array_merge(\Yii::$app->request->get(), ['site/auth', 'authclient' => 'vkontakte'])) ?>">
                <button type="button" class="btn btn-primary signin_vk"><i class="fa fa-vk"></i> Войти через ВК</button>
            </a>

            <input type="submit" name="login" class="js_signin_submit btn btn-primary" value="Войти">

            <div class="login-help">
                <a href="#" class="js_signup_open">Зарегистрироваться</a>
            </div>
        </div>
    </div>
</div>