<form method="post" action="<?= $this->getAdminPageUrl(['view' => 'index', 'action' => 'massive']); ?>" onsubmit="return confirm('Czy na pewno chcesz zastosować zmiany?');">

    <?= wp_nonce_field($this->getActionToken().'massive'); ?>

    Masowe działania:
    <select name="massive_action">
        <option value="0">Masowe działania</option>
        <option value="delete">Usuń</option>
        <option value="enabled">Aktywny</option>
        <option value="disabled">Nieaktywny</option>
    </select>

    <input type="submit" class="button-secondary" value="Wykonaj">
<table class="wp-list-table widefat pc-theme-slider-table" id="pc-theme-slider-table">
    <thead>
    <tr>
        <th class="manage-column check-column">
            <input type="checkbox" style="margin: 0">
        </th>
        <th>
            <a href="">ID</a>
        </th>
        <th scope="col" class="manage-column">
            <a href="#">Miniaturka</a>
        </th>
        <th scope="col" class="manage-column">
            <a href="">Tytuł</a>
        </th>
        <th scope="col" class="manage-column">
            <a href="">Podtytuł</a>
        </th>
        <th scope="col" class="manage-column">
            <a href="">Opis</a>
        </th>
        <th scope="col" class="manage-column">
            <a href="">Status</a>
        </th>
    </tr>
    </thead>
    <tbody id="pc-theme-slider-sortable-wrapper">
    <?php if($pagination->hasItems()): ?>

        <?php foreach ($pagination->getItems() as $item): ?>
            <tr class="pc-theme-slider-sortable-item" id="<?= $item->id; ?>">
                <td class="check-column">
                    <input type="checkbox" value="<?= $item->id; ?>" name="massive_check[]">
                </td>
                <td><?= $item->id; ?></td>
                <td>
                    <?= pc_theme_slider_get_image($item->slide_image, ['width' => 50]); ?>
                </td>
                <td>
                    <strong><?= $item->slide_title; ?></strong>
                    <div class="row-actions">
                        <span class="edit"><a href="<?= $this->getAdminPageUrl(['view' => 'form', 'slide_id' => $item->id]); ?>" class="edit">Edytuj</a></span>|
                        <span class="trash">
                            <?php
                                $token_name = $this->getActionToken().$item->id;
                                $remove_url = $this->getAdminPageUrl(['action' => 'delete', 'slide_id' => $item->id]);
                            ?>
                            <a href="<?= wp_nonce_url($remove_url, $token_name); ?>" class="delete" onclick="return confirm('Czy na pewno chcesz usunąć ten element?');">Usuń</a>
                        </span>
                    </div>
                </td>
                <td>
                    <?= $item->slide_subheader; ?>
                </td>
                <td>
                    <?= $item->slide_description; ?>
                </td>
                <td>
                    <?php if(!(bool)$item->slide_publish): ?>
                        <a href="#" class="button-secondary pc-theme-slider-change-status-btn" data-id="<?= $item->id; ?>">Nieaktywny</a>
                    <?php else: ?>
                        <a href="#" class="button-primary pc-theme-slider-change-status-btn" data-id="<?= $item->id; ?>">Aktywny</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>

    <?php else: ?>
        <tr>
            <td colspan="7">Brak slajdów do wyświetlenia</td>
        </tr>
    <?php endif; ?>
        <tr class="pc-theme-slider-table-loader" style="display: none">
            <td colspan="7">
                <p>Loading...</p>
            </td>
        </tr>
    </tbody>
</table>
</form>
<?php require_once 'components/pagination.php' ?>
