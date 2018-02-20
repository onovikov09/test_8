<?php $signupSocialData = json_encode(Yii::$app->session->get('social', []), JSON_UNESCAPED_UNICODE); ?>
<?php $this->registerJs("var signupSocialData = $signupSocialData", \yii\web\View::POS_HEAD); ?>

<div class="modal fade modal_signup_wrap" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-container">
            <h1>Регистрация</h1><br>
            <div class="wrap_field">
                <input type="text" name="User[first_name]" placeholder="Имя">
            </div>
            <div class="wrap_field">
                <input type="text" name="User[last_name]" placeholder="Фамилия">
            </div>
            <div class="wrap_field">
                <input type="text" name="User[email]" placeholder="Email">
            </div>
            <div class="wrap_field">
                <input type="password" name="User[password]" placeholder="Пароль">
            </div>
            <div class="wrap_field">
                <input type="password" name="User[password_confirm]" placeholder="Пароль еще раз">
            </div>

            <input type="hidden" name="User[social_id]">

            <div class="btn-group wrap_field" data-toggle="buttons">
                <label class="btn btn-primary">
                    <input type="radio" name="User[gender]" autocomplete="off" value="men"> Мужчина
                </label>
                <label class="btn btn-primary">
                    <input type="radio" name="User[gender]" autocomplete="off" value="women"> Женщина
                </label>
            </div>

            <input type="submit" class="js_signup_submit btn btn-primary" value="Зарегистрироваться">

            <div class="login-help">
                <a href="#" class="js_signin_open">Войти</a>
            </div>
        </div>
    </div>
</div>