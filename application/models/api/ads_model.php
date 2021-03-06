<?php

class Ads_model extends CI_Model{
    
    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    function getAds()
    {
        $this->db->select('a.id, a.ad_title as title,f.relative_path as url');
        $this->db->from('ads a');
        $this->db->join('files f','a.file_id=f.id');
        $this->db->where('a.status',1);
        $query = $this->db->get();
        return $query->result();
    }
    
    function getAdsFlavour()
    {     
       $this->db->select('a.id,c.name,c.minetype as content_type,c.relative_path as video_file_name');
       $this->db->from('ads a');   
       $this->db->join('files c','a.file_id=c.id','inner');      
       $this->db->where('a.transcode_status','pending');      
       $query = $this->db->get(); 
        //echo '<br>'.$this->db->last_query();die;
        return $query->result();   
    }
    
    function getTranscodedAds($id)
    {
        $this->db->select('a.ad_title,a.duration,fv.ads_id,fv.type,fv.path');
        $this->db->from('ads a');
        $this->db->join('ads_flavored_video fv','fv.ads_id=a.id');
       // $this->db->where('f.status','completed');
        $this->db->where('a.id',$id);
        $this->db->where('a.vast_file_id',0);
        $query = $this->db->get();        
        return $query->result();
    }
    
    public function update_ads_flavors($id,$data){
        $this->db->where('id', $id);
        $this->db->update('ads', $data);
        return true;
   }
   
    public function save_flavored_ads($data)
    {
       $this->db->set($data);
       $this->db->set('created','NOW()',FALSE);
       $this->db->insert('ads_flavored_video',$data);
       return $this->db->insert_id();
    }
    
    function update_ads($data,$id)
   {
    $this->db->where('id', $id);
    $this->db->update('ads', $data); 
    return true;
   }
   
   function save_file($data)
   {
	$this->db->set($data);
	$this->db->set('created','NOW()',false);
	$this->db->insert('files', $data); 
	//echo '<br>'.$this->db->last_query();die;
	return $this->db->insert_id(); 
   }
   
   function getReviveAds($type='all',$id='')
    {
       if($type=='manager' && $id!=''){
            $this->db->select('id');
            $this->db->from('users');
            $this->db->where('adserver_user_id',$id);
            $query = $this->db->get();
            $adserver_user_id = $query->row()->id;
            
            $this->db->select('a.id as ads_id, a.ad_title as title,af.path');
            $this->db->from('ads a');
            $this->db->join('ads_flavored_video af','a.id=af.ads_id');
            $this->db->where('a.uid',$adserver_user_id);
            $this->db->where('af.type','desktop');
            $query = $this->db->get();
            return $query->result();
           
       }else if($type=='all' && $id!=''){
           
            $this->db->select('a.id as ads_id, a.ad_title as title,af.path');
            $this->db->from('ads a');
            $this->db->join('ads_flavored_video af','a.id=af.ads_id');
            $this->db->where('af.type','desktop');
            $query = $this->db->get();
            return $query->result();
       }
    }
    
    function getCuePoints($id,$type,$flag=0){
      if($flag){
	 $this->db->select('count(id) as tot');
	 $this->db->limit(1);
      }else{
	 $this->db->select('cue_points');
      }
      
      if($type=='live'){
	 $this->db->where('type',$type);
      }
      $this->db->from('content_cuepoints');
      $this->db->where('content_id',$id);
      $this->db->order_by('cue_points');
      $query = $this->db->get();
      //echo '<br>'.$this->db->last_query();
      if($flag){
      $result = $query->row();
	 return $result->tot;
      }else{
	 $result = $query->result_array();
	 //-- convert array into element --//       
        foreach ($result AS $key => $value) {
            $cuepoints[] = $value['cue_points'];
        }
	return $cuepoints;
        //-----------------------------------//
      }
    }
    
    function getUserKeywords($id)
    {
      $this->db->select('c.keywords,c.gender,c.dob');
      //$this->db->from('user_content_keywords k');
      $this->db->from('customers c');
      //$this->db->join('customers c','c.id=k.user_id','left');
      $this->db->where('id',$id);
      $this->db->limit(1);
      $query = $this->db->get();
     // echo $this->db->last_query();echo '<br>';
     return $query->row_array();
      /*if($result){
	 return unserialize($result->keywords);
      }else{
	 return 0;
      }*/
    }
    
    function getChannels()
    {
        $this->db->select('id as channel_id,name,type');
        $this->db->from('channels');
        $query = $this->db->get();
        return $query->result();
    }
}
    ?>