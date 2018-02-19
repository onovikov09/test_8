<nav class="navbar navbar-inverse">
    <div class="container">
        <div class="col-md-10 col-md-offset-1 text-center">
            <?php if (Yii::$app->user->isGuest) { ?>
                <div class="navbar-header">
                    <button type="button" class="btn btn-default navbar-btn js_signin_open">Войти</button>
                    <button type="button" class="btn btn-default navbar-btn js_signup_open">Зарегистрироваться</button>
                </div>
            <?php } else { ?>
                <div class="navbar-header">
                    <a href="<?= \yii\helpers\Url::to(["profile/edit"]) ?>" class="header_full_name">
                        <?= Yii::$app->user->getIdentity()->getFull_name() ?>
                    </a>
                    <a href="<?= \yii\helpers\Url::to(["resume/create"]) ?>" class="btn btn-default navbar-btn">Новое резюме</a>
                    <a href="<?= \yii\helpers\Url::to(["site/logout"]) ?>" class="btn btn-default navbar-btn">Выйти</a>
                </div>
            <?php } ?>
        </div>
    </div>
</nav>