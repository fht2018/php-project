<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Initor extends CI_Model {
    public function __construct() {
        parent::__construct();
        // 如果某一model, library, helper 需广泛使用, 可在此处直接 加载
        $this->load->database();
        $this->requestMethod = $_SERVER['REQUEST_METHOD']; // 获取请求协议 GET POST PUT DELETE 等
        
    }
    // 统一使用 JSON 格式响应 data 返回的数据, httpStatusCode 请阅览更多 http状态码 资料
    public function responseJson($data, $httpStatusCode = 200) {
        // 此函数功能可继续完善
        if ($httpStatusCode != 200) {
            header($this->getHttpStatus($httpStatusCode));
        }
        if (is_array($data) || is_object($data)) {
            echo json_encode($data);
        } else {
            echo $data;
        }
        exit(); // 注意, 输出数据后将exit
        
    }
    // 接受json数据
    public function receiveJson() {
        $string = file_get_contents("php://input");
        if (!empty($string)) {
            $this->json = json_decode($string);
            if (json_last_error() != JSON_ERROR_NONE) {
                $this->responseJson("Bad Request. json_error_code:" . json_last_error() , 400); //非法JSON数据
                
            }
        }
    }
    private function getHttpStatus($httpStatusCode) {
        // 此处应添加 $code 错误处理
        $httpStatus = [ 
            100 => "HTTP/1.1 100 Continue", 
            101 => "HTTP/1.1 101 Switching Protocols", 
            200 => "HTTP/1.1 200 OK", 
            201 => "HTTP/1.1 201 Created", 
            202 => "HTTP/1.1 202 Accepted", 
            203 => "HTTP/1.1 203 Non-Authoritative Information", 
            204 => "HTTP/1.1 204 No Content", 
            205 => "HTTP/1.1 205 Reset Content", 
            206 => "HTTP/1.1 206 Partial Content", 
            300 => "HTTP/1.1 300 Multiple Choices", 
            301 => "HTTP/1.1 301 Moved Permanently", 
            302 => "HTTP/1.1 302 Found", 
            303 => "HTTP/1.1 303 See Other", 
            304 => "HTTP/1.1 304 Not Modified", 
            305 => "HTTP/1.1 305 Use Proxy", 
            307 => "HTTP/1.1 307 Temporary Redirect", 
            400 => "HTTP/1.1 400 Bad Request", 
            401 => "HTTP/1.1 401 Unauthorized", 
            402 => "HTTP/1.1 402 Payment Required", 
            403 => "HTTP/1.1 403 Forbidden", 
            404 => "HTTP/1.1 404 Not Found", 
            405 => "HTTP/1.1 405 Method Not Allowed", 
            406 => "HTTP/1.1 406 Not Acceptable", 
            407 => "HTTP/1.1 407 Proxy Authentication Required", 
            408 => "HTTP/1.1 408 Request Time-out", 
            409 => "HTTP/1.1 409 Conflict", 
            410 => "HTTP/1.1 410 Gone", 
            411 => "HTTP/1.1 411 Length Required", 
            412 => "HTTP/1.1 412 Precondition Failed", 
            413 => "HTTP/1.1 413 Request Entity Too Large", 
            414 => "HTTP/1.1 414 Request-URI Too Large", 
            415 => "HTTP/1.1 415 Unsupported Media Type", 
            416 => "HTTP/1.1 416 Requested range not satisfiable", 
            417 => "HTTP/1.1 417 Expectation Failed", 
            500 => "HTTP/1.1 500 Internal Server Error", 
            501 => "HTTP/1.1 501 Not Implemented", 
            502 => "HTTP/1.1 502 Bad Gateway", 
            503 => "HTTP/1.1 503 Service Unavailable", 
            504 => "HTTP/1.1 504 Gateway Time-out"  
        ];
        return $httpStatus[$httpStatusCode];
    }

    public function check($meth) {
        if ($this->requestMethod == $meth) {
            if ($meth == 'POST') {
                $this->receiveJson(); // 接受客户端 json
                return $this->json;
            } else if ($meth == 'GET') {
                return true;
            } else if ($meth == 'PUT') {
                $this->receiveJson(); // 接受客户端 json
                return $this->json;
            } else if ($meth == 'DELETE') {
                return true;
            } else {
                $arr = array(
                    "status" => - 1,
                    "info" => "方式错误"
                );
                return $arr;
            }
        } else {
            $arr = array(
                "status" => - 1,
                "info" => "方式错误"
            );
            return $arr;
        }
    }
    public function get_sn($token) {
        $data = $this->db->where('token', $token)->get('w2_token')->first_row();
        $timenow = time();
        $da = $this->db->where('token_id', $data->id)->get('w2_users')->first_row();
        $secret_key = '837553577faeee0a8c00af4a46ff8fc6';
        $nowtoken = md5($da->sn . $da->password . $secret_key . $data->time_out);
        //token过期或者失效返回0，正常返回sn
        if ($nowtoken == $token) {
            if ($timenow < $data->time_out) {
                return $da->sn;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }
    public function get_token() {
        if (isset($_SERVER['HTTP_X_USER_TOKEN'])) {
            return $_SERVER['HTTP_X_USER_TOKEN'];
        } else {
            return 0;
        }
    }
    public function setToken($sn, $pass) {
        $time = strtotime("+3 days");
        $secret_key = '837553577faeee0a8c00af4a46ff8fc6';
        $token = md5($sn . $pass . $secret_key . $time);
        $arr = array(
            "token" => $token,
            "time_out" => $time
        );
        $this->db->insert('w2_token', $arr); //把token存w2_token里
        $da = $this->db->where('token', $token)->get('w2_token')->first_row();
        $ar = array(
            "token_id" => $da->id
        );
        $this->db->where('sn', $sn)->update('w2_users', $ar); //把token的id存在w2_users里
        return $token;
    }
}