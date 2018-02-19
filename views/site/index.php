<div class="site-index">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <section class="team">
        <div class="container">
            <div class="row">
                <div class="col-md-10 col-md-offset-1">
                    <div class="col-lg-12">
                        <h6 class="description">РЕЗЮМЕ</h6>
                        <div class="row pt-md">
                            <?php foreach ($list as $resume) { ?>
                                <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12 profile">
                                    <div class="img-box">
                                        <img src="<?= $resume->user->avatar ?>" class="img-responsive">
                                        <ul class="text-center">
                                            <a href="#">Перейти в профиль</a>
                                        </ul>
                                    </div>
                                    <h1><?= $resume->user->full_name ?></h1>
                                    <h2><?= $resume->title ?></h2>
                                    <!--<button data-toggle="collapse" data-target="#text_id<?/*= $resume->id */?>">Подробнее</button>
                                    <div id="text_id<?/*= $resume->id */?>" class="collapse"><?/*= $resume->description */?></div>-->
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
