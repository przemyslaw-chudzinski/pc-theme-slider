<?php

$current_page  = (int)$pagination->getCurrentPage();
$last_page = (int)$pagination->getLastPage();

$first_disabled = '';
$last_disabled = '';

$url_params = [
    'order_by' => $pagination->getOrderBy(),
    'sort' => $pagination->getSort()
];

$url_params['current_page'] = 1;
$first_page_url = $this->getAdminPageUrl($url_params);

$url_params['current_page'] = $current_page - 1;
$prev_page_url = $this->getAdminPageUrl($url_params);

$url_params['current_page'] = $last_page;
$last_page_url = $this->getAdminPageUrl($url_params);

$url_params['current_page'] = $current_page + 1;
$next_page_url = $this->getAdminPageUrl($url_params);

if ($current_page === 1) {
    $first_page_url = '#';
    $prev_page_url = '#';
    $first_disabled = 'disabled';
}

if ($current_page === $last_page) {
    $last_page_url = '#';
    $next_page_url = '#';
    $last_disabled = 'disabled';
}

?>
<div class="tablenav-pages">
    <span class="displaying-num"><?= $pagination->getTotalCount(); ?> slajdy</span>

    <span class="pagination-links">
            <a href="<?= $first_page_url; ?>" title="Idź do pierwszej strony <?= $first_disabled; ?>" class="first-page"><<</a>
            <a href="<?= $prev_page_url; ?>" title="Idź do poprzedniej strony <?= $first_disabled; ?>" class="prev-page"><</a>

            <span class="paging-input"><?= $current_page; ?> z <span class="total-pages"><?= $last_page; ?></span></span>

            <a href="<?= $next_page_url; ?>" title="Idź do następnej strony" class="next-page <?= $last_disabled; ?>">></a>
            <a href="<?= $last_page_url; ?>" title="Idź do ostatniej strony" class="lat-page <?= $last_disabled; ?>">>></a>
        </span>
</div>