<div class="wrap">
    <h2>
        <?= $this->plugin_name; ?>
        <a href="<?= $this->getAdminPageUrl() ?>" class="page-title-action">Wszystkie slajdy</a>
        <a href="<?= $this->getAdminPageUrl(['view' => 'form']) ?>" class="page-title-action">Dodaj nowy slajd</a>
    </h2>
    <hr>

    <?php if($this->hasFlashMsg()): ?>
    <div class="<?= $this->getFlashMsgStatus(); ?>">
        <p><?= $this->getFlashMsg(); ?></p>
    </div>
    <?php endif; ?>

    <div id="pc-theme-slider-ajax-msg" style="display: none;">
        <p class="pc-theme-slider-ajax-msg-content"></p>
    </div>

    <div>
        <?php require $view;?>
    </div>

    <br class="clear">

</div>