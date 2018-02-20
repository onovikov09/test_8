<nav class="navbar navbar-inverse">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="collapsed navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-9" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a href="<?= \yii\helpers\Url::to(["/"]) ?>" class="navbar-brand"><?= Yii::$app->id ?></a>
        </div>
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-9">
            <ul class="nav navbar-nav">
                <li class="<?= $this->context->checkActive("site/index") ?>"><a href="<?= \yii\helpers\Url::to(["/"]) ?>">Список резюме</a></li>
                <?php if (Yii::$app->user->isGuest) { ?>
                    <li><a href="#" class="js_signin_open">Войти</a></li>
                    <li><a href="#" class="js_signup_open">Зарегистрироваться</a></li>
                <?php } else { ?>
                    <li class="<?= $this->context->checkActive("profile/edit") ?>">
                        <a href="<?= \yii\helpers\Url::to(["profile/edit"]) ?>">
                            Профиль (<?= Yii::$app->user->getIdentity()->getFull_name() ?>)
                        </a>
                    </li>
                    <li class="<?= $this->context->checkActive("resume/create") ?>"><a href="<?= \yii\helpers\Url::to(["resume/create"]) ?>">Добавить новое резюме</a></li>
                    <li><a href="<?= \yii\helpers\Url::to(["site/logout"]) ?>">Выйти</a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
</nav>