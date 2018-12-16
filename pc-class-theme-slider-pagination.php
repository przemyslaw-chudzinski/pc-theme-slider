<?php


class PC_Theme_Slider_Pagination {

    private $items;

    private $order_by;

    private $sort;

    private $limit;

    private $total_count;

    private $current_page;

    private $last_page;


    public function __construct($items, $order_by, $sort, $limit, $total_count, $current_page, $last_page)
    {
        $this->items = $items;
        $this->order_by = $order_by;
        $this->sort = $sort;
        $this->limit = $limit;
        $this->total_count = $total_count;
        $this->current_page = $current_page;
        $this->last_page = $last_page;
    }

    public function getItems()
    {
        return $this->items;
    }

    public function getOrderBy()
    {
        return $this->order_by;
    }

    public function getSort()
    {
        return $this->sort;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function getTotalCount()
    {
        return $this->total_count;
    }

    public function getCurrentPage()
    {
        return $this->current_page;
    }

    public function getLastPage()
    {
        return $this->last_page;
    }

    public function hasItems()
    {
        return !empty($this->items);
    }
}