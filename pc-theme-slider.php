<?php
session_start();
/**
 * Plugin Name: PC THEME SLIDER
 * Description: Plugin zarządzający karuzelą zdjęć.
 * Author: Przemysław Chudziński
 * Author URI: przemyslawchudzinski.pl
 * Version: 1.0.0
 */

require_once 'pc-class-theme-slider.php';

$pc_theme_slider = new PC_Theme_Slider();

register_activation_hook(__FILE__, [$pc_theme_slider, 'onActivate']);

require_once 'functions.php';