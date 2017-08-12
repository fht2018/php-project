<?php
  class Work extends  CI_Model{
   function __construct(){
   	parent::__construct();
   }
public  function getData()
{
    $json=$this->initor->check('GET');
    $token = $this->initor->get_token();
    if($token){
                  $sn = $this->initor->get_sn($token);
                  if($sn){
                            $data=$this->db->where('sn',$sn)->get('w2_users')->first_row();
                            $da1=$this->db->where('id',$data->group)->get('w2_group')->first_row();
                            if($data->direct!=NULL){
                              $da2=$this->db->where('id',$data->direct)->get('w2_direct')->first_row();
                              $name = $da2->name;}else{$name=NULL;}
                            $da = array("nickname" => $data->nickname,
                              "name" => $data->name,
                              "sn" => $data->sn,
                              "email" => $data->email,
                              "qq" => $data->qq,
                              "wechat" => $data->wechat,
                              "grade" => $data->grade,
                              "pic" => $data->pic,
                              "direct" => $name,
                              "group" => $da1->group,
                              "position" => $da1->position);
                            $arr = array('status' => 0, 'info' => '获取成功','data'=> $da);
                            echo json_encode($arr);
                            exit();         
                        }else{
                            $arr = array('status' => -1, 'info' => '无效，请重新登录');
                            echo json_encode($arr);
                            exit();
                        }
              }else{
                     $arr = array('status' => -1, 'info' => '请先登录');
                     echo json_encode($arr);exit();    
              }
} 
public  function postData()
{
    $id=$this->uri->segment(3);
    if($id==1){
                  $json = $this->initor->check('POST');
                  $sn = $json->sn;
                  $jwch_pass=hash("sha512",$json->jwch_password);
                  $email = $json->email;
                  $pass=hash("sha512",$json->password);
                  if(empty($sn)){
                                    $re = array("status" => -1, "info" => "学号不能为空");
                                    echo json_encode($re);
                                    exit();
                                  }
                  if(empty($json->jwch_password)){
                                    $re = array("status" => -1,"info" => "教务处密码不能为空");
                                    echo json_encode($re);
                                    exit();
                                  }
                  if(empty($json->password)){
                                    $re = array("status" => -1,"info" => "密码不能为空");
                                    echo json_encode($re);
                                    exit();
                                  }
                  if(empty($email)){
                                    $re = array("status" => -1,"info" => "邮箱不能为空");
                                    echo json_encode($re);
                                    exit();
                                  }
                  $preg1 = "/^\d{8}$/";
                  $preg2 = "/^(\w)+(\.\w+)*@(\w)+((\.\w+)+)$/";
                  preg_match($preg1,$sn,$match1);
                  preg_match($preg2,$email,$match2);
                  if($match1[0] != $sn){
                  $re = array("status" => -1, "info" => "请输入有效学号");
                  echo json_encode($re);
                  exit();}
                  if($match2[0] != $email){
                  $re = array("status" => -1,"info" => "请输入有效邮箱");
                  echo json_encode($re);exit();}
                  $query = $this->db->where('sn',$sn)->get('w2_users');
                  if(empty($query->num_rows())){
    
                  $json->jwch_password=$jwch_pass;
                  $json->password=$pass;
    
                  $this->users->check_sn($json);
                }else{
                  $re = array("status" => -1,"info" => "该学号已被注册");}
                  echo json_encode($re);
                  exit();}
    if($id==2){
                  $json = $this->initor->check('POST');
                  $content = $json->content;
                  $pass=hash("sha512",$json->password);
 
                  if(empty($content)){
                  $re = array("status" => -1,"info" => "学号或邮箱不能为空");
                  echo json_encode($re);
                  exit();}
                  if(empty($json->password)){
                  $re = array("status" => -1,"info" => "密码不能为空");
                  echo json_encode($re);
                  exit();}
                  $preg1 = "/^\d{8}$/";
                  $preg2 = "/^(\w)+(\.\w+)*@(\w)+((\.\w+)+)$/";
                  preg_match($preg1,$content,$match1);
                  preg_match($preg2,$content,$match2);
                  if($match1[0]==$content){
                  $query = $this->db->where('sn',$content)->get('w2_users')->first_row();
                  if(empty($query)){
                  $re = array("status" => -1,"info" => "该账号尚未注册");
                  echo json_encode($re);
                  exit();}else if($query->password == $pass){            
                  $token=$this->initor->setToken($content,$pass);
                  $re = array("status" => 0,"info" => "登录成功","token" => $token);
                  echo json_encode($re);
                  exit();}else{
                  $re = array("status" => -1,"info" => "学号或密码错误");
                  echo json_encode($re);
                  exit();}
                }else if($match2[0]==$content){
                  $query = $this->db->where('email',$content)->get('w2_users')->first_row();
                  $sn=$query->sn;
                  if(empty($query)){
                  $re = array("status" => -1,"info" => "该账号尚未注册");
                  echo json_encode($re);
                  exit();}else if($query->password==$pass){
                  $token=$this->initor->setToken($sn,$pass);
                  $re = array("status" => 0,"info" => "登录成功","token" => $token);
                  echo json_encode($re);
                  exit();}else{
                  $re = array("status" => -1,"info" => "账号或密码不正确");
                  echo json_encode($re);exit();}
                 }else{
                  $re = array("status" => -1,"info" => "邮箱或学号格式错误");
                  echo json_encode($re);
                  exit();}
    }

}
public  function putData()
{
    $id=$this->uri->segment(3);
    if($id==1){
                   $json = $this->initor->check('PUT');
                   $token = $this->initor->get_token();
                   if($token){
                                  $sn = $this->initor->get_sn($token);
                                  if($sn){
                                             $data=$this->db->where('sn',$sn)->get('w2_users')->first_row();
                                             if($json->direct){
                                             $da = $this->db->where('name',$json->direct)->get('w2_direct')->first_row();
                                             $json->direct = $da->id;}else{$json->direct = $data->direct;}

                                             if($json->group){
                                             $query = $this->db->where('id',$data->group)->update('w2_group', array("group"=>$json->group));
                                             $json->group = $data->group;}else{$json->group = $data->group;}
                                             if($json->name){
                                             $query = $this->db->where('id',$data->group)->update('w2_group', array("name"=>$json->name));}
                                          
                                             if($json->nickname){
                                             $query = $this->db->where('id',$data->group)->update('w2_group', array("nickname"=>$json->nickname));}
                                                                                                                                     
                                             $sql = $this->db->where('sn',$sn)->update('w2_users', $json);
                                             if($sql){
                                                          $arr = array("status" => 0, 'info' => '修改成功');
                                                          echo json_encode($arr); exit();     
                                                      }else{
                                                          $arr = array("status" => -1, 'info' => '修改失败');
                                                           echo json_encode($arr); exit();     
                                          }
                                    }else{
                                             $arr = array('status' => -1, 'info' => '无效，请重新登录');
                                             echo json_encode($arr);exit();
                                    }
                      }else{
                               $arr = array('status' => -1, 'info' => '请先登录');
                               echo json_encode($arr);exit();
                              }
                }
     if($id==2){
                   $json = $this->initor->check('PUT');
                   $token = $this->initor->get_token();
                   if($token){
                                 $password = hash("sha512",$json->password);
                                 $newpassword = hash("sha512",$json->newpassword);
                                 $renewpassword = hash("sha512",$json->renewpassword);

                                 $sn = $this->initor->get_sn($token);
                                 if($sn){
                                            $this->db->where('sn',$sn);
                                            $data=$this->db->select('password')->get('w2_users')->result_array();
                                            if($newpassword==$renewpassword){
                                                    if($password==$data[0]['password']){
                                                            $da=array("password"=>$newpassword,'token_id'=>NULL);
                                                            $this->db->delete('w2_token', array('token' => $token));
                                                            $this->db->where('sn',$sn)->update('w2_users', $da);

                                                            $arr = array('status' => 0, 'info' => '修改密码成功,请重新登录');
                                                            echo json_encode($arr);exit();
                                                }else{
                                                          $arr = array('status' => -1, 'info' => '旧密码错误');
                                                          echo json_encode($arr);exit();
                                                }
                                         }else{
                                               $arr = array('status' => -1, 'info' => '2次密码不一样');
                                               echo json_encode($arr);exit();
                                        }
                                 }else{
                                         $arr = array('status' => -1, 'info' => '无效，请重新登录');
                                         echo json_encode($arr);exit();
                                }      
                        }else{
                                 $arr = array('status' => -1, 'info' => '请先登录');
                                 echo json_encode($arr);exit();
                       }
                }
     if($id==3){
                  $json = $this->initor->check('PUT');
                  $jwch_pass=hash("sha512",$json->jwch_password);
                  if(empty($json->sn)){
                                    $re = array("status" => -1, "info" => "学号不能为空");
                                    echo json_encode($re);
                                    exit();
                                  }
                  if(empty($json->jwch_password)){
                                    $re = array("status" => -1,"info" => "教务处密码不能为空");
                                    echo json_encode($re);
                                    exit();
                                  }
                   $token = $this->initor->get_token();
                   if($token){
                                 $sn = $this->initor->get_sn($token);
                                 if($sn){
                                      $query = $this->db->where('sn',$json->sn)->get('w2_users');
                                      if(empty($query->num_rows())){
                                          $json->jwch_password=$jwch_pass;
                                          $this->users->change_sn($json,$sn);
                                    }else{
                                          $re = array("status" => -1,"info" => "该用户名已被注册");
                                          echo json_encode($re);
                                          exit();}
                                 }else{
                                         $arr = array('status' => -1, 'info' => '无效，请重新登录');
                                         echo json_encode($arr);exit();
                                }                       
                              }else{
                                 $arr = array('status' => -1, 'info' => '请先登录');
                                 echo json_encode($arr);exit();
                       }
                }

}
public  function deleteData()
{
    $json = $this->initor->check('DELETE');
    $token = $this->initor->get_token();
    if($token){
          $sn = $this->initor->get_sn($token);
          if($sn){
          $this->db->delete('w2_token', array('token' => $token));
          $da=array('token_id'=>NULL);
          $this->db->where('sn',$sn)->update('w2_users', $da);

          $arr = array('status' => 0, 'info' => '退出成功');
          echo json_encode($arr);exit();
          
          }else{
            $arr = array('status' => -1, 'info' => '无效，请重新登录');
            echo json_encode($arr);exit();
        }
  }else{
    $arr = array('status' => -1, 'info' => '请先登录');
        echo json_encode($arr);exit();
  }
}
}
?>