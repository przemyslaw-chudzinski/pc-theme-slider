<?php


class Request {

    private static $instance;

    private $query_params;

    public function __construct()
    {
    }

    static public function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getQueryParams()
    {
        if (!isset($this->query_params)){
            $queryString = $_SERVER['QUERY_STRING'];
            $parts = explode('&', $queryString);
            foreach ($parts as $part) {
                $tmp = explode('=', $part);
                $this->query_params[$tmp[0]] = trim($tmp[1]);
            }
        }
        return $this->query_params;
    }

    public function getQuerySingleParam($param, $default = false)
    {
        $query_params = $this->getQueryParams();
        if (isset($query_params[$param])) {
            return $query_params[$param];
        }
        return $default;
    }

    public function isMethod($method_name)
    {
        return $_SERVER['REQUEST_METHOD'] === $method_name;
    }

}