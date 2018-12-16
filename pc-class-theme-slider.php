<?php

require_once 'pc-class-theme-slider-model.php';
require_once 'Request.php';
require_once 'pc-class-theme-slider-entry.php';

class PC_Theme_Slider {

    private static $plugin_id = 'pc-theme-slider';

    private $plugin_version = '1.0.0';

    private $model;

    private $plugin_name = 'PC THEME SLIDER';

    private $tmpl_dir = 'views/';

    private $request;

    private $action_token = 'to-jest-jakis-token';

    private $pagination_limit = 10;

    public function __construct()
    {
        $this->model = new PC_Theme_Slider_Model();
        $this->request = Request::getInstance();
        $this->registerActions();
    }

    public function onActivate()
    {
        $ver_opt = static::$plugin_id.'-version';
        $installed_version = get_option($ver_opt);
        $this->install($ver_opt);
        if (!$installed_version) {
            $this->install($ver_opt);
        } else {
            $this->compareVersion($installed_version);
        }
    }

    private function install($ver_opt)
    {
        $this->model->createPluginTable();
        update_option($ver_opt, $this->plugin_version);
    }

    private function compareVersion($installed_version)
    {
        switch (version_compare($installed_version, $this->plugin_version)) {
            case 0:
                // zainstalowana wrsja jest identyczna
                break;

            case 1:
                // zainstalowana wersja jest nowsza niż obecna
                break;

            case -1:
                // zainstalowana wersja jest starsza niz obecna
                break;
        }
    }

    public function createAdminMenu()
    {
        $page_title = $this->plugin_name;
        $menu_title = 'Theme slider';
        $capability = 'manage_options';
        $menu_slug = static::$plugin_id;
        $function = [$this, 'renderAdminPage'];
        $icon_url = null;
        $position = null;
        add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position);
    }

    public function renderAdminPage()
    {
        $view = $this->request->getQuerySingleParam('view', 'index');
        $slide_id = (int) $this->request->getQuerySingleParam('slide_id');

        if ($slide_id > 0) {
            $entry = new PC_Theme_Slider_Entry($slide_id);
            if (!$entry->exists()) {
                $this->setFlashMsg('Brak takiego wpisu w bazie danych', 'error');
                $this->redirect($this->getAdminPageUrl());
            }
        } else {
            $entry = new PC_Theme_Slider_Entry();
        }

        $this->requestActions($entry);

        switch ($view) {

            case 'index':
                $this->render('index', ['pagination' => $this->getPagination()]);
                break;
            case 'form':
                $this->render('form', ['entry' => $entry]);
                break;
            default:
                $this->render('404');
                break;
        }
    }

    private function registerActions()
    {
        add_action('admin_menu', [$this, 'createAdminMenu']);
        add_action('admin_enqueue_scripts', [$this, 'registerScriptsAndStyles']);

        /* AJAX */
        add_action('wp_ajax_change_slide_status', [$this, 'changeSlideStatus']);
        add_action('wp_ajax_change_slides_order', [$this, 'changeSlidesOrder']);
    }

    public function render($view = '', array $args = [])
    {
        extract($args);
        $view = plugin_dir_path(__FILE__).$this->tmpl_dir.$view.'.php';
        require plugin_dir_path(__FILE__).$this->tmpl_dir.'layout.php';
    }

    public function registerScriptsAndStyles()
    {
        $style_handle = static::$plugin_id.'-styles';
        wp_register_style($style_handle, plugins_url('/assets/css/style.css', __FILE__));
        if(get_current_screen()->id === 'toplevel_page_'.static::$plugin_id) {
            wp_enqueue_style($style_handle);
            $script_handler = static::$plugin_id.'-scripts';
            wp_register_script($script_handler, plugins_url('/assets/js/scripts.js', __FILE__), ['jquery','jquery-ui-sortable'], false, true);
            wp_enqueue_script('jquery');
            wp_enqueue_script('jquery-ui-sortable');
            wp_enqueue_media();
            wp_enqueue_script($script_handler);
        }
    }

    private function getAdminPageUrl(array $params = [])
    {
        $admin_url = admin_url().'admin.php?page='.static::$plugin_id;
        $admin_url = add_query_arg($params, $admin_url);
        return $admin_url;
    }

    private function requestActions(PC_Theme_Slider_Entry $entry)
    {
        $action = $this->request->getQuerySingleParam('action');

        if ($this->request->isMethod('POST') && isset($_POST['entry'])) {

            $entry->setFields($_POST['entry']);

            if(!check_admin_referer($this->action_token)) {
                $this->setFlashMsg('Błędny token formularza', 'error');
                return;
            }

            if (!$entry->validate()) {
                $this->setFlashMsg('Popraw błędy formularza', 'error');
                return;
            }

            $entry_id = false;

            switch ($action) {
                case 'save':
                    $entry_id = $this->model->saveSlide($entry);
                    break;
                case 'update':
                    $entry_id = $this->model->updateSlide($entry);
                    break;
            }

            if ($entry_id !== false) {

                if ($entry->hasId()) {
                    $this->setFlashMsg('Wpis został zaktualizowany');
                } else {
                    $this->setFlashMsg('Wpis został dodany poprawnie');
                }

                $this->redirect($this->getAdminPageUrl(['view' => 'form', 'slide_id' => $entry_id]));

            } else {
                $this->setFlashMsg('Wystąpiły błędy z zapisem do bazy danych', 'error');
            }

        }

        $this->removeSlide($entry, $action);

        $this->massiveActions($action);
    }


    private function massiveActions($action)
    {
        if ($this->request->isMethod('POST') && $action === 'massive') {
            if (check_admin_referer($this->getActionToken().'massive')) {

                $massive_action = isset($_POST['massive_action']) ? $_POST['massive_action'] : false;
                $massive_check = isset($_POST['massive_check']) ? $_POST['massive_check'] : [];

                if (count($massive_check) < 1) {
                    $this->setFlashMsg('Brak slajdów do zmiany', 'error');
                } else {
                    switch ($massive_action){
                        case 'delete':
                            $this->removeMassiveSlides($massive_check);
                            break;
                        case 'enabled':
                            $this->changeMassiveSlideStatus($massive_check, 1);
                            break;
                        case 'disabled':
                            $this->changeMassiveSlideStatus($massive_check, 0);
                            break;
                    }
                    $this->redirect($this->getAdminPageUrl());
                }
            }
        }
    }

    private function removeSlide(PC_Theme_Slider_Entry $entry, $action)
    {
        $slide_id = (int)$this->request->getQuerySingleParam('slide_id');
        if ($this->request->isMethod('GET') && $slide_id > 0 && $action === 'delete') {
            $token_name = $this->action_token.$entry->getField('id');
            $wpnonce = $this->request->getQuerySingleParam('_wpnonce', false);
            if (wp_verify_nonce($wpnonce, $token_name)) {
                if ($this->model->removeSlide($slide_id) !== false) {
                    $this->setFlashMsg('Slajd został usunięty poprawnie');
                } else {
                    $this->setFlashMsg('Slajd nie został usunięty', 'error');
                }
            } else {
                $this->setFlashMsg('Niepoprawny token', 'error');
            }
            $this->redirect($this->getAdminPageUrl());
        }
    }

    private function changeMassiveSlideStatus($massive_check, $status)
    {
        $status = (int) $status;
        if ($this->model->changeMassiveSlideStatus($massive_check, $status) !== false) {
            $this->setFlashMsg('Status został zmieniony');
        } else {
            $this->setFlashMsg('Wystąpił błąd podczas zmiany statusu', 'error');
        }
    }

    private function removeMassiveSlides($massive_check)
    {
        if ($this->model->removeMassiveSlides($massive_check) !== false) {
            $this->setFlashMsg('Zaznaczone slajdy zostały usunięte poprawnie');
        } else {
            $this->setFlashMsg('Wystąpił błąd podczas usuwania slajdów', 'error');
        }
    }

    private function setFlashMsg($message, $status = 'updated')
    {
        $_SESSION[__CLASS__]['message'] = $message;
        $_SESSION[__CLASS__]['status'] = $status;
    }

    public function getFlashMsg()
    {
        if(isset($_SESSION[__CLASS__]['message'])) {
            $msg = $_SESSION[__CLASS__]['message'];
            unset($_SESSION[__CLASS__]['message']);
            return $msg;
        }
        return false;
    }

    public function getFlashMsgStatus()
    {
        if(isset($_SESSION[__CLASS__]['status'])) {
            return $_SESSION[__CLASS__]['status'];
        }
        return false;
    }

    public function hasFlashMsg()
    {
        return isset($_SESSION[__CLASS__]['message']);
    }

    public function getActionToken()
    {
        return $this->action_token;
    }

    private function redirect($location)
    {
        wp_safe_redirect($location);
        exit;
    }

    private function getPagination()
    {
        $current_page = (int) $this->request->getQuerySingleParam('current_page', 1);
        $sort = $this->request->getQuerySingleParam('sort', 'asc');
        $order_by = $this->request->getQuerySingleParam('order_by', 'id');

        $pagination = $this->model->getPagination($current_page, $this->pagination_limit, $order_by, $sort);

        return $pagination;
    }

    public function changeSlideStatus()
    {
        if (!$this->request->isMethod('POST')) {
            $message['error'] = true;
            $message['message'] = 'Wrong request method';
            echo json_encode($message);
            wp_die();
        }
        $response = $this->model->changeStatus((int)$_POST['id']);
        if ($response['id'] !== false) {
            $message['error'] = false;
            $message['message'] = 'Status został zmeniony proprawnie';
            $message['prev_state'] = $response['prev_state'];
        }  else {
            $message['error'] = true;
            $message['message'] = 'Wystąpił nieoczekiwany błąd serwera';
        }
        echo json_encode($message);
        wp_die();
    }

    public function changeSlidesOrder()
    {
        if (!$this->request->isMethod('POST')) {
            $message['error'] = true;
            $message['message'] = 'Wrong request method';
            echo json_encode($message);
            wp_die();
        }

        $order = $_POST['order'];
        $response = $this->model->setSlidesOrder($order);

        if ($response !== false) {
            $message['error'] = false;
            $message['message'] = 'Kolejność slajdów została zmieniona poprawnie';
        } else {
            $message['error'] = true;
            $message['message'] = 'Wystąpił błąd podczas sortowania danych';
        }

        echo json_encode($message);
        wp_die();
    }

}

