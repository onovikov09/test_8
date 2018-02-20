<div class="col-lg-3 col-md-3 col-sm-4 col-xs-12 profile">
    <a href="<?= \yii\helpers\Url::to(["resume/view", "id"=>$resume->id ]) ?>" class="img-box">
        <div class="avatar_user" style="background-image: url(<?= $resume->user->avatar_or_stub ?>)"></div>
        <div class="text-center"><span><?= $resume->description ?></span></div>
    </a>
    <h1><?= $resume->user->full_name ?></h1>
    <h2><?= $resume->title ?></h2>
</div>