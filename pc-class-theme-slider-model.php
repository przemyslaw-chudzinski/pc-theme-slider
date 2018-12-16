<?php

require_once 'pc-class-theme-slider-pagination.php';

class PC_Theme_Slider_Model {

    private $wpdb;

    private $order_by_opts = [
        'ID' => 'id',
        'Tytuł' => 'slide_title',
        'Status' => 'slide_publish'
    ];

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
    }


    public function createPluginTable()
    {
        $plugin_table_name = $this->getPluginTableName();

        $sql = '
            CREATE TABLE IF NOT EXISTS '.$plugin_table_name.' (
            id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            slide_title VARCHAR(100) NOT NULL,
            slide_image VARCHAR(255) NULL,
            slide_subheader VARCHAR(100) NULL,
            slide_description VARCHAR(255) NULL,
            slide_publish INT DEFAULT 1,
            slide_order INT,
            slide_link_url VARCHAR(255) NULL,
            slide_link_label VARCHAR(50) NULL
        ) DEFAULT CHARSET=utf8';

        require_once ABSPATH.'wp-admin/includes/upgrade.php';

        dbDelta($sql);
    }

    public function getPluginTableName()
    {
        return $this->wpdb->prefix.'pc_theme_slider_slides';
    }

    private function getAllData(PC_Theme_Slider_Entry $entry)
    {
        return [
            'slide_title'       => $entry->getField('slide_title'),
            'slide_subheader'   => $entry->getField('slide_subheader'),
            'slide_description' => $entry->getField('slide_description'),
            'slide_publish'     => $entry->getField('slide_publish'),
            'slide_order'       => $entry->getField('slide_order'),
            'slide_link_url'    => $entry->getField('slide_link_url'),
            'slide_link_label'  => $entry->getField('slide_link_label'),
            'slide_image'       => $entry->getField('slide_image')
        ];
    }

    private function getFormat()
    {
        return ['%s', '%s', '%s', '%d', '%d', '%s', '%s', '%s'];
    }

    public function saveSlide(PC_Theme_Slider_Entry $entry)
    {

        $data = $this->getAllData($entry);

        $maps = $this->getFormat();

        $plugin_table_name = $this->getPluginTableName();

        $result = $this->wpdb->insert($plugin_table_name, $data, $maps);

        return $result ? $this->wpdb->insert_id : false;
    }

    public function updateSlide(PC_Theme_Slider_Entry $entry)
    {
        $plugin_table_name = $this->getPluginTableName();
        if ($entry->hasId()) {
            $data = $this->getAllData($entry);
            $maps = $this->getFormat();
            $result = $this->wpdb->update($plugin_table_name, $data, ['id' => $entry->getField('id')], $maps, '%d');
            if ($result) {
                return $entry->getField('id');
            }
            return false;
        }
        return false;
    }

    public function fetchRow($id)
    {
        $plugin_table_name = $this->getPluginTableName();
        $sql = "SELECT * FROM {$plugin_table_name} WHERE id = %d";
        $prep = $this->wpdb->prepare($sql, $id);
        return $this->wpdb->get_row($prep, 'ARRAY_A');
    }

    public function getPagination($current_page, $limit = 10, $order_by = 'id', $sort = 'asc')
    {
        $current_page = (int) $current_page;
        $current_page < 1 ? $current_page = 1 : $current_page;
        $limit = (int) $limit;
        $order_by = !in_array($order_by, $this->order_by_opts) ? 'id' : $order_by;
        $sort = in_array($sort, ['asc', 'desc']) ? $sort : 'asc';
        $offset = ($current_page - 1)*$limit;

        $count_sql = 'SELECT COUNT(*) FROM '.$this->getPluginTableName();
        $total_count = $this->wpdb->get_var($count_sql);

        $last_page = ceil($total_count/$limit);


//        $sql = 'SELECT * FROM '.$this->getPluginTableName().' ORDER BY '.$order_by.' '.$sort.' LIMIT '.$offset.' ,'.$limit;
        $sql = "SELECT * FROM {$this->getPluginTableName()} ORDER BY slide_order asc LIMIT {$offset}, {$limit}";
        $slides_list = $this->wpdb->get_results($sql);

        $pagination = new PC_Theme_Slider_Pagination($slides_list, $order_by, $sort, $limit, $total_count, $current_page, $last_page);

        return $pagination;
    }

    public function getOrderByOpts()
    {
        return $this->order_by_opts;
    }

    public function changeStatus($id)
    {
        $current_status_sql = 'SELECT slide_publish FROM '.$this->getPluginTableName().' WHERE id='.$id;
        $current_status = (int)$this->wpdb->get_var($current_status_sql);
        if ($current_status === 1) {
            $new_status = 0;
        } else {
            $new_status = 1;
        }
        $sql = 'UPDATE '.$this->getPluginTableName().' SET slide_publish='.$new_status.' WHERE id='.$id;
        $slide_id = $this->wpdb->query($sql);
        return [
            'id' => $slide_id,
            'prev_state' => $current_status
        ];
    }

    public function removeSlide($slide_id)
    {
        $id = (int)$slide_id;
        $sql = 'DELETE FROM '.$this->getPluginTableName().' WHERE id=%d';
        $prep = $this->wpdb->prepare($sql, $id);
        $result = $this->wpdb->query($prep);
        return $result ? $id : false;
    }

    public function removeMassiveSlides(array $ids)
    {
        $ids = array_map('intval', $ids);

        $table_name = $this->getPluginTableName();

        $ids_str = implode(',', $ids);
        $sql = "DELETE FROM {$table_name} WHERE id IN ({$ids_str})";
        return $this->wpdb->query($sql);
    }

    public function changeMassiveSlideStatus(array $ids, $status = 0)
    {
        $ids = array_map('intval', $ids);
        $status = (int) $status;

        $table_name = $this->getPluginTableName();

        $ids_str = implode(',', $ids);
        $sql = "UPDATE {$table_name} SET slide_publish='.$status.' WHERE id IN ({$ids_str})";
        return $this->wpdb->query($sql);
    }

    public function getSlides()
    {
        $table_name = $this->getPluginTableName();
        $sql = "SELECT * FROM {$table_name} WHERE slide_publish=1 ORDER BY slide_order";
        return $this->wpdb->get_results($sql);
    }

    public function setSlidesOrder(array $order)
    {
        $errors = [];
        if (count($order) > 0) {
            foreach ($order as $order => $id) {
                $sql = "UPDATE {$this->getPluginTableName()} SET slide_order={$order} WHERE id={$id}";
                if ($this->wpdb->query($sql) === false) {
                    $errors[] = 'error';
                }
            }
        } else {
            return false;
        }

        return count($errors) === 0;
    }
}