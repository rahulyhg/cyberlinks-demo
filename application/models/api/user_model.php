<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class User_model extends CI_Model {

   function __construct()
   {
       parent::__construct();
       $this->load->database();
   }
   
   public function getsplash($project)
   {
       $this->db->select('image as logo');
       $this->db->from('users');
       $this->db->where('username',$project);
       $this->db->where('owner_id',1);
       $this->db->limit(1);
       $query = $this->db->get();
      // echo $this->db->last_query();
       return $query->row();
       
   }
   
   public function adduser($data)
   {
	$this->db->set($data);
         $this->db->set('created', 'NOW()', FALSE); 
	$this->db->insert('users', $data);
	//echo '<br>'.$this->db->last_query();die;
	return $this->db->insert_id();
   }
   
   public function activationToken($data)
   {
       $this->db->set($data);
       $this->db->set('expiry','NOW()',FALSE);
       $this->db->insert('token',$data);
       return $this->db->insert_id();
   }
   
   public function addpassword($data)
   {
	$this->db->set($data);
	$this->db->insert('user_password', $data);
	//echo '<br>'.$this->db->last_query();die;
	return $this->db->insert_id();
   }
   public function addsocial($data)
   {
	$this->db->set($data);
	$this->db->insert('social_connects', $data);
	//echo '<br>'.$this->db->last_query();die;
	return $this->db->insert_id();
   }
   
   public function checkuser($email)
   {
        $this->db->select('id');
	$this->db->from('users');
        $this->db->where('email',$email);
        $query = $this->db->get();    
       // echo '<br>'.$this->db->last_query();die;
        $result = $query->row();
        if($result)
            return $result->id;
        else
            return 0;
   }
   
   public function loginuser($email,$password)
   {
        $this->db->select('a.id');
	$this->db->from('users a');
        $this->db->join('social_connects b', 'a.id = b.user_id', 'left');
        $this->db->where('a.email',$email);        
        $this->db->where('a.password',$password); 
        $this->db->where('a.status',1); 
        $query = $this->db->get();
       // echo '<br>'.$this->db->last_query();die;
        $result = $query->row();
        if($result)
            return $result->id;
        else
            return 0;
   }

    public function confirmRegistration($token)
    {
        $this->db->select('a.id');
	$this->db->from('users a');
        $this->db->join('token b','a.id = b.user_id','inner');
        $this->db->where('b.token',$token);
        $this->db->where('a.status','inactive');
        $query = $this->db->get();
        //echo '<br>'.$this->db->last_query();die;
        $result = $query->row();
        if($result){
            //-- activate user --//            
            $this->db->set('modified', 'NOW()', FALSE); 
            $this->db->where('id', $result->id);
            $this->db->update('users', array('status'=>'active'));
            //----------------------------//        
            
            //-- delete token --//
                $this->db->where('token',$token);
                $this->db->delete('token');
            //-----------------//
            
            return $result->id;
        } else
            return 0;
   }
   
    public function validateToken($token)
    {
       if($token == ''){
           $token='';
       }
        $this->db->select('id');
	$this->db->from('api_token');
        $this->db->where('token',$token);
        //$this->db->where('status',0);
        //$this->db->where('DATE_ADD(hit_time, INTERVAL 15 MINUTE) >', 'NOW()',FALSE);
        $query = $this->db->get();
        //echo '<br>'.$this->db->last_query();die;
        $result = $query->row();
        if($result)
            return $result->id;
        else
            return 0;
    }
   
   public function getuser($id)
   {
        $this->db->select('a.id,a.first_name,a.last_name,a.gender,a.email,a.created,b.token,a.image');
	$this->db->from('users a');
        $this->db->join('api_token b','a.id = b.owner_id','left');
        $this->db->where('a.id',$id);
        $query = $this->db->get();    
       // echo '<br>'.$this->db->last_query();die;
        $result = $query->row();
        if($result)
            return $result;
        else
            return 0;
   }
   public function getpassword($id)
   {
        $this->db->select('a.u_password as password,b.email');
	$this->db->from('user_password a');
        $this->db->join('users b', 'b.id = a.user_id', 'inner');
        $this->db->where('b.email',$id);
        $query = $this->db->get();    
       // echo '<br>'.$this->db->last_query();die;
        $result = $query->row();
        if($result)
            return $result;
        else
            return 0;
   }
   
    public function validpassword($email,$password)
    {
        $this->db->select('a.id');
	$this->db->from('users a');        
        $this->db->where('a.email',$email);        
        $this->db->where('a.password',$password); 
        $this->db->where('a.status',1); 
        $query = $this->db->get();
       // echo '<br>'.$this->db->last_query();die;
        $result = $query->row();
        if($result)
            return $result->id;
        else
            return 0;
    }
    
    public function resetpassword($id,$password)
    {
        $data = array('password' => md5($password));
        $this->db->where('id', $id);
        $this->db->update('users', $data); 
        
        $data = array('u_password' => $password);
        $this->db->where('user_id', $id);
        $this->db->update('user_password', $data); 
        return true;
    }
  
    public function logout($token)
    {
        $this->db->set('hit_time', 'DATE_SUB(NOW(), INTERVAL 1 DAY)', FALSE); 
        $this->db->where('token', $token);
        $this->db->update('api_token', $data); 
        return true;
    }
    
    function add_apikey($data)
    {
        $this->db->set($data);
        $this->db->set('created_time', 'NOW()', FALSE); 
        $this->db->set('hit_time', 'NOW()', FALSE); 
	$this->db->insert('api_token', $data);
	//echo '<br>'.$this->db->last_query();die;
	return $this->db->insert_id();
    }
    
  function update_user($data,$id)
  {
    $this->db->set('modified', 'NOW()', FALSE); 
    $this->db->where('id', $id);
    $this->db->update('users', $data); 
    return true;
  }
  
  function update_api($token)
  {
    $this->db->set('hit_time', 'NOW()', FALSE); 
    $this->db->where('token', $token);
    $this->db->update('api_token'); 
    
    return true;
  }
  
  function delete_api($id){
        $this->db->where('owner_id', $id);
        $this->db->delete('api_token'); 
  }
function delete_user($id){
        $this->db->set('status','inactive');
        $this->db->where('id', $id);
        $this->db->update('users');
	 return true;
  }
}
