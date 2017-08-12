<?php
class Solve extends CI_Model {
    function __construct() {
        parent::__construct();
    }
    //GET 获取信息
    public function getData() {
        $id = $this->uri->segment(3);
        if (empty($id)) {
            $json = $this->initor->check('GET');
            $token = $this->initor->get_token();
            if ($token) {
                $sn = $this->initor->get_sn($token);
                if ($sn) {
                    $data = $this->db->where('sn', $sn)->get('w2_users')->first_row();
                    $da = $this->db->where('id', $data->group)->get('w2_group')->first_row(); //2个if验证token是否存在，token是否有效
                    if ($da->level >= 5) { //验证是否拥有权限
                        $config['base_url'] = base_url() . 'index.php/project/is_manger/';
                        $array = array(
                            'group' => $da->group,
                            'level <' => $da->level
                        );
                        $config['total_rows'] = $this->db->where($array)->get('w2_group')->num_rows();
                        $config['per_page'] = '10';
                        $this->pagination->initialize($config);
                        $data = $this->db->get('w2_group', $config['per_page'], $this->uri->segment(3, 0))->result_array(); //$this->deal->get_show($this->uri->segment(3,0),$config['per_page']);
                        $this->load->view('......', $data);
                        $arr = array(
                            'status' => 0,
                            'info' => '查看成员列表成功'
                        );
                        return $arr;
                    } else {
                        $arr = array(
                            'status' => - 1,
                            'info' => '您的权限不足'
                        );
                        return $arr;
                    }
                } else {
                    $arr = array(
                        'status' => - 1,
                        'info' => '无效，请重新登录'
                    );
                    return $arr;
                }
            } else {
                $arr = array(
                    'status' => - 1,
                    'info' => '请先登录'
                );
                return $arr;
            }
        } else {
            $json = $this->initor->check('GET');
            $token = $this->initor->get_token();
            if ($token) {
                $sn = $this->initor->get_sn($token);
                if ($sn) { //2个if验证token是否存在，token是否有效
                    $data = $this->db->where('sn', $sn)->get('w2_users')->first_row();
                    $da = $this->db->where('id', $data->group)->get('w2_group')->first_row(); //查看者的权限
                    $da1 = $this->db->where('id', $id)->get('w2_group')->first_row(); //被查看者的权限
                    if (($da->level >= 5) && ($da->level >= $da1->level)) { //验证是否拥有权限
                        $data1 = $this->db->where('name', $da1->name)->select('nickname,name,sn,email,qq,wechat,grade,pic,direct,group')->get('w2_users')->result_array();
                        $arr = array(
                            'status' => 0,
                            'info' => '获取成功',
                            'data' => $data1[0]
                        );
                        return $arr;
                    } else {
                        $arr = array(
                            'status' => - 1,
                            'info' => '您的权限不足,无法查看'
                        );
                        return $arr;
                    }
                } else {
                    $arr = array(
                        'status' => - 1,
                        'info' => '无效，请重新登录'
                    );
                    return $arr;
                }
            } else {
                $arr = array(
                    'status' => - 1,
                    'info' => '请先登录'
                );
                return $arr;
            }
        }
    }
    public function postData() {
    }
    //PUT 修改信息
    public function putData() {
        $json = $this->initor->check('PUT');
        $id = $this->uri->segment(3);
        $token = $this->initor->get_token();
        if ($token) {
            $sn = $this->initor->get_sn($token);
            if ($sn) { //2个if验证token是否存在，token是否有效
                $data = $this->db->where('sn', $sn)->get('w2_users')->first_row();
                $da = $this->db->where('id', $data->group)->get('w2_group')->first_row(); //操作者的权限group表
                $da1 = $this->db->where('id', $id)->get('w2_group')->first_row(); //被操作者的权限group表
                if ($da->group == $da1->group) {
                    if (($da->level >= 5) && ($da->level > $da1->level)) { //验证是否拥有权限
                        if ($json->position == 'tourists') {
                            $ar = array(
                                'position' => 'tourists',
                                'level' => 0
                            );
                            $this->db->where('id', $id);
                            $sql = $this->db->update('w2_group', $ar);
                            $arr = array(
                                'status' => 0,
                                'info' => '权限游客修改成功'
                            );
                            return $arr;
                        } else if ($json->position == 'informal') {
                            $ar = array(
                                'position' => 'informal',
                                'level' => 1
                            );
                            $this->db->where('id', $id);
                            $sql = $this->db->update('w2_group', $ar);
                            $arr = array(
                                'status' => 0,
                                'info' => '权限考核成员修改成功'
                            );
                            return $arr;
                        } else if ($json->position == 'formal') {
                            $ar = array(
                                'position' => 'formal',
                                'level' => 3
                            );
                            $this->db->where('id', $id);
                            $sql = $this->db->update('w2_group', $ar);
                            $arr = array(
                                'status' => 0,
                                'info' => '权限正式成员修改成功'
                            );
                            return $arr;
                        } else if ($json->position == 'header') {
                            $ar = array(
                                'position' => 'header',
                                'level' => 5
                            );
                            $this->db->where('id', $id);
                            $sql = $this->db->update('w2_group', $ar);
                            $arr = array(
                                'status' => 0,
                                'info' => '权限小组长修改成功'
                            );
                            return $arr;
                        } else if ($json->position == 'leader') {
                            $ar = array(
                                'position' => 'leader',
                                'level' => 7
                            );
                            $this->db->where('id', $id);
                            $sql = $this->db->update('w2_group', $ar);
                            $arr = array(
                                'status' => 0,
                                'info' => '权限负责人修改成功'
                            );
                            return $arr;
                        }
                    } else {
                        $arr = array(
                            'status' => - 1,
                            'info' => '您的权限不足'
                        );
                        return $arr;
                    }
                } else {
                    $arr = array(
                        'status' => - 1,
                        'info' => '无法修改其他用户组成员的权限'
                    );
                    return $arr;
                }
            } else {
                $arr = array(
                    'status' => - 1,
                    'info' => '无效，请重新登录'
                );
                return $arr;
            }
        } else {
            $arr = array(
                'status' => - 1,
                'info' => '请先登录'
            );
            return $arr;
        }
    }
    public function deleteData() {
    }
}
?>
