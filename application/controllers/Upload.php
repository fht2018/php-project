<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Upload extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->database();
        $this->load->model("initor");
        $this->load->model("uploads");
        $this->load->model("deal");
        $this->load->helper(array('form', 'url'));
    }
    public function name() {
        $data = $this->getRequest();
    }
    //根据请求方式调用对应方法
    public function getRequest() {
        $method_type = array( //允许的请求方式
            'get',
            'post',
            'put',
            'patch',
            'delete'
        );
        $method = strtolower($_SERVER['REQUEST_METHOD']); //请求方式
        if (in_array($method, $method_type)) { //调用请求方式对应的方法
            $data_name = $method . 'Data';
            return $this->uploads->$data_name();
        }
        return false;
    }
}