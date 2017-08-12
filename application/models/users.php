<?php
class users extends CI_Model {
    function __construct() {
        parent::__construct();
    }
    //身份验证
    public function check_sn($json) {
        $data = $this->db->where('sn', $json->sn)->get('all_students')->first_row();
        if (empty($data)) {
            $arr = array(
                "status" => - 1,
                'info' => '学号错误'
            );
            return $arr;
        } else {
            if ($json->jwch_password != $data->jwch_password) {
                $arr = array(
                    'status' => - 1,
                    'info' => '学号与密码不符'
                );
                return $arr;
            } else {
                $json->name = $data->name;
                $json->pic = $data->pic;
                $json->grade = $data->grade;
                $ar = array(
                    "name" => $json->name,
                    "nickname" => $json->nickname,
                    "position" => "tourists",
                    "level" => 0
                );
                $this->db->insert('w2_group', $ar);
                $da = $this->db->where('name', $json->name)->get('w2_group')->first_row();
                $json->group = $da->id;
                $this->db->insert('w2_users', $json);
                $arr = array(
                    'status' => 0,
                    'info' => '注册成功'
                );
                return $arr;
            }
        }
    }
    public function change_sn($json, $sn) {
        $data = $this->db->where('sn', $json->sn)->get('all_students')->first_row();
        $sql = $this->db->where('sn', $sn)->get('w2_users')->first_row();
        $query = $this->db->where('id', $sql->group)->get('w2_group')->first_row();
        if (empty($data)) {
            $arr = array(
                "status" => - 1,
                'info' => '学号错误'
            );
            return $arr;
        } else {
            if ($json->jwch_password != $data->jwch_password) {
                $arr = array(
                    'status' => - 1,
                    'info' => '学号与密码不符'
                );
                return $arr;
            } else {
                $json->name = $data->name;
                $json->pic = $data->pic;
                $json->grade = $data->grade;
                $ar = array(
                    "name" => $json->name
                );
                $this->db->where('id', $sql->group)->update('w2_group', $ar);
                $this->db->where('group', $sql->group)->update('w2_users', $json);
                $arr = array(
                    'status' => 0,
                    'info' => '修改学号成功'
                );
                return $arr;
            }
        }
    }
}
?>
