<?php

if(!function_exists('pc_theme_slider_get_image')) {
    function pc_theme_slider_get_image($image_url, array $attrs = [])
    {
        ?>
        <img src="<?= $image_url; ?>"
             <?php if(count($attrs) > 0): ?>
             <?php foreach ($attrs as $attr => $value): ?>
                 <?= $attr; ?>="<?= $value; ?>"
             <?php endforeach; ?>
             <?php endif; ?>
        >
        <?php
    }
}

if (!function_exists('pc_theme_slider_get_slides')) {
    function pc_theme_slider_get_slides()
    {
        $model = new PC_Theme_Slider_Model();
        $slides = $model->getSlides();
        return $slides;
    }
}