<div class="site-index">
    <section class="team">
        <div class="container">
            <div class="row">
                <div class="col-md-10 col-md-offset-1">
                    <div class="col-lg-12">
                        <h6 class="description">СПИСОК РЕЗЮМЕ</h6>
                        <div class="row pt-md">
                            <?php foreach ($list as $resume) { ?>
                                <?= $this->render("list_item", ["resume" => $resume]) ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
