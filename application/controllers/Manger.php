<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Manger extends CI_Controller {

  function __construct(){
	parent::__construct();
	$this->load->helper('url');
	$this->load->database();
  $this->load->model("initor");
  $this->load->model("solve");
  $this->load->model("deal");	
}
public function name()
{
    $data = $this->getRequest();

    $this->sendResponse($data);
}
//根据请求方式调用对应方法
public  function getRequest()
{
    $method_type = array('get', 'post', 'put', 'patch', 'delete');//允许的请求方式              
    $method = strtolower($_SERVER['REQUEST_METHOD']);//请求方式
    if (in_array($method, $method_type)) 
    {//调用请求方式对应的方法
        $data_name = $method . 'Data';
        return  $this->solve->$data_name();
    }
    return false;
}
//返回结果
public function sendResponse($data)
{//获取数据        
    if ($data) 
      {
          $code = 200;
          $message = 'OK';
      }else{
               $code = 404;
               $data = array('error' => 'Not Found');
               $message = 'Not Found';
      }
      $HTTP_VERSION = "HTTP/1.1";
      //输出结果
      header($HTTP_VERSION . " " . $code . " " . $message);
      $content_type = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : $_SERVER['HTTP_ACCEPT'];
      if (strpos($content_type, 'application/json') !== false) 
      {
          header("Content-Type: application/json");
          echo $this->deal->encodeJson($data);
      }else if(strpos($content_type, 'application/xml') !== false) {
          header("Content-Type: application/xml");
          echo $this->deal->encodeXml($data);
      } else {
          header("Content-Type: text/html");
          echo $this->deal->encodeHtml($data);
      }
  }
}
