<form action="<?= $this->getAdminPageUrl(); ?>" method="get">

    <input type="hidden" name="page" value="<?= static::$plugin_id; ?>">
    <input type="hidden" name="current_page" value="<?= $pagination->getCurrentPage(); ?>">

    Sortuj według:
    <select name="order_by">
        <?php foreach ($this->model->getOrderByOpts() as $key => $order_opt): ?>
            <option <?= $order_opt === $pagination->getOrderBy() ? 'selected' : ''; ?> value="<?= $order_opt; ?>"><?= $key; ?></option>
        <?php endforeach; ?>
    </select>

    <select name="sort">
        <?php if($pagination->getSort() === 'asc'): ?>
            <option selected value="asc">Rosnąco</option>
            <option value="desc">Malejąco</option>
        <?php else: ?>
            <option value="asc">Rosnąco</option>
            <option selected value="desc">Malejąco</option>
        <?php endif; ?>
    </select>

    <input type="submit" value="Sortuj" class="button-secondary">
</form>