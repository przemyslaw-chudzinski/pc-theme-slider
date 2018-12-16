<?php
$action_params = ['view' => 'form', 'action' => 'save'];
if ($entry->hasId()){
    $action_params['slide_id'] = (int)$entry->getField('id');
    $action_params['action'] = 'update';
}
?>
<form action="<?= $this->getAdminPageUrl($action_params) ?>" method="post">

    <table class="form-table" id="pc-theme-slider-form-table">
        <?php wp_nonce_field($this->getActionToken()); ?>
        <input type="hidden" name="entry[slide_order]" value="<?= $entry->getField('slide_order'); ?>">
        <input type="hidden" id="pc-theme-slider-slide-input" name="entry[slide_image]" value="<?= $entry->getField('slide_image'); ?>">
        <tbody>
        <tr class="form-field">
            <th class="row"><label for="">Slajd</label></th>
            <td>
                <a href="#" id="pc-theme-slider-open-media-btn" style="display: <?= $entry->getField('slide_image') === NULL ? 'inline-block' : 'none'; ?>;;">Wybierz obrazek</a>
                <a href="#" id="pc-theme-slider-clear-media-btn" style="display: <?= $entry->getField('slide_image') !== NULL ? 'inline-block' : 'none'; ?>;">Usuń aktualny obrazek</a>
                <div id="pc-theme-slider-slide-preview-container">
                    <?= pc_theme_slider_get_image($entry->getField('slide_image'), ['width' => 400]); ?>
                </div>
            </td>
        </tr>
        <tr class="form-field">
            <th class="row"><label for="">Tytuł</label></th>
            <td>
                <input type="text" class="regular-text" name="entry[slide_title]" value="<?= $entry->getField('slide_title'); ?>" autocomplete="off">
                <?php if($entry->hasError('slide_title')): ?>
                    <p class="description error"><?= $entry->getError('slide_title'); ?></p>
                <?php endif; ?>
            </td>
        </tr>
        <tr class="form-field">
            <th class="row"><label for="">Podtytuł</label></th>
            <td>
                <input type="text" class="regular-text" name="entry[slide_subheader]" value="<?= $entry->getField('slide_subheader'); ?>" autocomplete="off">
                <?php if($entry->hasError('slide_subheader')): ?>
                    <p class="description error"><?= $entry->getError('slide_subheader'); ?></p>
                <?php endif; ?>
            </td>
        </tr>
        <tr class="form-field">
            <th class="row"><label for="">Opis</label></th>
            <td>
                <textarea rows="5" class="regular-text" name="entry[slide_description]"><?= $entry->getField('slide_description'); ?></textarea>
                <?php if($entry->hasError('slide_description')): ?>
                    <p class="description error"><?= $entry->getError('slide_description'); ?></p>
                <?php endif; ?>
            </td>
        </tr>
        <tr class="form-field">
            <th class="row"><label for="">Link</label></th>
            <td>
                <input type="text" class="regular-text" name="entry[slide_link_url]" value="<?= $entry->getField('slide_link_url'); ?>" autocomplete="off">
                <?php if($entry->hasError('slide_link_url')): ?>
                    <p class="description error"><?= $entry->getError('slide_link_url'); ?></p>
                <?php endif; ?>
            </td>
        </tr>
        <tr class="form-field">
            <th class="row"><label for="">Etykieta przycisku</label></th>
            <td>
                <input type="text" class="regular-text" name="entry[slide_link_label]" value="<?= $entry->getField('slide_link_label'); ?>" autocomplete="off">
                <?php if($entry->hasError('slide_link_label')): ?>
                    <p class="description error"><?= $entry->getError('slide_link_label'); ?></p>
                <?php endif; ?>
            </td>
        </tr>
        <tr class="form-field">
            <th class="row"><label for="">Wybierz status</label></th>
            <td>
                <select class="regular-text" name="entry[slide_publish]">
                    <option value="1" <?= (bool)(int)$entry->getField('slide_publish') !== false ? 'selected' : null; ?>>Aktywny</option>
                    <option value="0" <?= (bool)(int)$entry->getField('slide_publish') === false ? 'selected' : null; ?>>Nieaktywny</option>
                </select>
            </td>
        </tr>
        </tbody>
    </table>
    <p class="submit">
        <input type="submit" class="button-primary" value="Zapisz">
    </p>
</form>