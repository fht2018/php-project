<?php
  class Deal extends  CI_Model{
   function __construct(){
   	parent::__construct();
   }
//GET 获取信息
public  function getData()
{  
    $json = $this->initor->check('GET'); 
    $id=$this->uri->segment(3);
    $token = $this->initor->get_token();
    if($token)
    {
         $sn = $this->initor->get_sn($token);
         if($sn)
         {
             $data=$this->db->where('sn',$sn)->get('w2_users')->first_row();
             $da=$this->db->where('id',$data->group)->get('w2_group')->first_row();//2个if验证token是否存在，token是否有效
             if(!$id)
             {              
                 if($da->level>=3)
                 {//验证是否拥有权限
                     $config['base_url'] = base_url().'index.php/project/';
                     $config['total_rows'] = $this->db->get('w2_project')->num_rows();
                     $config['per_page'] = '10';
                     $this->pagination->initialize($config);
                     $data=$this->db->get('w2_project',$this->uri->segment(3,0), $config['per_page'])->result_array();
                 //  $this->load->view('......',$data);
                     $arr = array('status' => 0, 'info' => '查看列表成功');
                     return $arr;
                   }else{
                           $arr = array('status' => -1, 'info' => '您的权限不足');
                           return $arr;
                    }
              }else{ 
              	       if($da->group=="technology")
              	       {
                           if($da->level>=3)
                           {//验证是否拥有权限
                               $this->db->where('id',$id);
                               $data=$this->db->select('name,uploader,grade,description,detail,site_address,git_address,partner,contract,service,start_time,end_time')->get('w2_project')->result_array();
                               $arr = array('status' => 0, 'info' => '获取成功','data'=> $data[0]);
                               return $arr;
                            }else{
                                     $arr = array('status' => -1, 'info' => '您的权限不足');
                                     return $arr;
                            }
                        }else{
                                 $arr = array('status' => -1, 'info' => '只有技术组才能浏览项目');
                                 return $arr;
                        }
                    }
         }else{
                  $arr = array('status' => -1, 'info' => '无效，请重新登录');
                  return $arr;
         }
    }else{
             $arr = array('status' => -1, 'info' => '请先登录');
             return $arr;
    }
}

public  function postData()
{
    $json = $this->initor->check('POST');
    $token = $this->initor->get_token();
    if($token){      
                  $sn = $this->initor->get_sn($token);
                  if($sn){
                             $data=$this->db->where('sn',$sn)->get('w2_users')->first_row();
                             $da=$this->db->where('id',$data->group)->get('w2_group')->first_row();//2个if验证token是否存在，token是否有效
                             if($da->group=="technology"){
                                  if($da->level>=3){//验证是否拥有权限
                                       if(empty($json->view_level)){$json->view_level=3;}
                                       if(empty($json->down_level)){$json->down_level=5;}
                                       $json->uploader = $data->nickname;

                                       $this->db->insert('w2_project',$json);

                                       $arr = array('status' => 0, 'info' => '发表成功');
                                       return $arr;
                                      }else{
                                      $arr = array('status' => -1, 'info' => '您的权限不足');
                                      return $arr;
                                      }

                             }else{
                                      $arr = array('status' => -1, 'info' => '只有技术组才能上传项目');
                                      return $arr;
                             }
                  }else{
                           $arr = array('status' => -1, 'info' => '无效，请重新登录');
                           return $arr;
                  }
         }else{
                  $arr = array('status' => -1, 'info' => '请先登录');
                  return $arr;
         }
}
//PUT /class/ID：更新某个指定班的信息（全部信息）
public  function putData()
{
    $json = $this->initor->check('PUT');
    $id=$this->uri->segment(3);//设置url时带上id
    $token = $this->initor->get_token();
    if($token){
                  $sn = $this->initor->get_sn($token);
                  if($sn){
                             $pro=$this->db->where('id',$id)->get('w2_project')->first_row();
                             $data=$this->db->where('sn',$sn)->get('w2_users')->first_row();
                             $da=$this->db->where('id',$data->group)->get('w2_group')->first_row();//2个if验证token是否存在，token是否有效
                             if($da->group=="technology"){
                             if($da->level>=3){
                             if($da->nickname==$pro->uploader){//验证是否拥有权限
                                        $this->db->where('id',$id)->update('w2_project',$json);

                                        $arr = array('status' => 0, 'info' => '修改成功');
                                        return $arr;
                                     }else{
                                              $arr = array('status' => -1, 'info' => '只有发布者才能修改项目');
                                              return $arr;
                                     }
                          }else{
                                   $arr = array('status' => -1, 'info' => '您的权限不足');
                                   return $arr;
                          }
                  }else{
                           $arr = array('status' => -1, 'info' => '只有技术组才能修改项目');
                           return $arr;
                  }
         }else{
                  $arr = array('status' => -1, 'info' => '无效，请重新登录');
                  return $arr;
         }
    }else{
             $arr = array('status' => -1, 'info' => '请先登录');
             return $arr;
    }
}
public  function deleteData()
{
 	$json = $this->initor->check('DELETE');
    $id=$this->uri->segment(3);//设置url时带上id
    $token = $this->initor->get_token();
    if($token){
                  $sn = $this->initor->get_sn($token);
                  if($sn){
                             $pro=$this->db->where('id',$id)->get('w2_project')->first_row();
                             $data=$this->db->where('sn',$sn)->get('w2_users')->first_row();
                             $da=$this->db->where('id',$data->group)->get('w2_group')->first_row();//2个if验证token是否存在，token是否有效
                             if($da->group=="technology"){
                                  if($da->level>=3){
                                        if($da->nickname==$pro->uploader){//验证是否拥有权限
                                               $this->db->where('id',$id)->delete('w2_project');
                                               $arr = array('status' => 0, 'info' => '删除成功');
                                               return $arr;
                                        }else{
                                                 $arr = array('status' => -1, 'info' => '只有发布者才能删除项目');
                                                 return $arr;
                                        }
                        }else{
                                 $arr = array('status' => -1, 'info' => '您的权限不足');
                                 return $arr;
                        }
              }else{
                       $arr = array('status' => -1, 'info' => '只有技术组才能删除项目');
                       return $arr;
              }
         }else{
                  $arr = array('status' => -1, 'info' => '无效，请重新登录');
                  return $arr;
         }
    }else{
             $arr = array('status' => -1, 'info' => '请先登录');
             return $arr;
    }
}
//json格式
public  function encodeJson($responseData)
{
    echo json_encode($responseData);
    exit();
}
//xml格式
public  function encodeXml($responseData)
{
	$xml = new SimpleXMLElement('<?xml version="1.0"?><rest></rest>');
    foreach ($responseData as $key => $value) 
    {
        if (is_array($value)) 
        {
            foreach ($value as $k => $v) 
            {
                $xml->addChild($k, $v);
            }
        }else{
                 $xml->addChild($key, $value);
        }
     }
        return $xml->asXML();
}
//html格式
public  function encodeHtml($responseData)
{
    $html = "<table border='1'>";
    foreach ($responseData as $key => $value) 
    {
        $html .= "<tr>";
        if (is_array($value))
        {
            foreach ($value as $k => $v) 
            {
                $html .= "<td>" . $k . "</td><td>" . $v . "</td>";
            }
        }else{
                 $html .= "<td>" . $key . "</td><td>" . $value . "</td>";
        }
        $html .= "</tr>";
    }
    $html .= "</table>";
    return $html;
}
 







}
?>