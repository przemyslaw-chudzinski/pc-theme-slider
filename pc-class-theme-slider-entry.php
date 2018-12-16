<?php

require_once 'pc-class-theme-slider-model.php';

class PC_Theme_Slider_Entry {

    private $id = FALSE;

    private $slide_title = NULL;

    private $slide_subheader = NULL;

    private $slide_description = NULL;

    private $slide_publish = TRUE;

    private $slide_order = 0;

    private $slide_link_url = NULL;

    private $slide_link_label = 'Więcej';

    private $slide_image = NULL;

    private $errors = [];

    private $model;

    private $exists = FALSE;

    public function __construct($id = false)
    {
        $this->model = new PC_Theme_Slider_Model();
        $this->id = $id;
        $this->load();
    }

    private function load()
    {
        if (isset($this->id)) {
            $row = $this->model->fetchRow($this->id);
            if (isset($row)) {
                $this->setFields($row);
                $this->exists = TRUE;
            }
        }
    }

    public function exists()
    {
        return $this->exists;
    }

    public function getField($field)
    {
        return isset($this->{$field}) ? $this->{$field} : NULL;
    }

    public function setFields(array $fields)
    {
        foreach ($fields as $key => $value) {
            $this->{$key} = $value;
        }
    }

    private function setError($field, $error)
    {
        $this->errors[$field] = $error;
    }

    public function getError($field)
    {
        return isset($this->errors[$field]) ? $this->errors[$field] : NULL;
    }

    public function hasError($field)
    {
        return isset($this->errors[$field]);
    }

    public function hasErrors()
    {
        return count($this->errors) > 0;
    }

    public function validate()
    {
        if (empty($this->slide_title)) {
            $this->setError('slide_title', 'To pole nie może być puste');
        } elseif(strlen($this->slide_title) > 100) {
            $this->hasError('slide_title', 'To pole może mieć maksymalnie 100 znaków');
        }

        if (strlen($this->slide_subheader) > 100) {
            $this->hasError('slide_subheader', 'To pole może mieć maksymalnie 100 znaków');
        }

        if (strlen($this->slide_description) > 255) {
            $this->hasError('slide_description', 'To pole może mieć maksymalnie 255 znaków');
        }

        if (strlen($this->slide_link_url) > 255) {
            $this->hasError('slide_link_url', 'To pole może mieć maksymalnie 255 znaków');
        }

        if (strlen($this->slide_link_label) > 50) {
            $this->hasError('slide_link_label', 'To pole może mieć maksymalnie 50 znaków');
        }

        return !$this->hasErrors();
    }

    public function hasId()
    {
        return isset($this->id) && $this->id;
    }

}