<?php

class Analytics_model extends CI_Model{
    
    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    public function save($post,$where=null)
    {
        $this->db->set($post);
        if(@$post['pause'] || @$post['complete']){
            $this->db->set('modified','NOW()',false);
            $this->db->update('analytics',$post,$where);
            return $this->db->affected_rows();
        }else{
            $this->db->set('created','NOW()',false);
            $this->db->insert('analytics');
            return $this->db->insert_id();
        }
    }
    
    public function getCountry()
    {
        $this->db->select('code,name')    ;
        $this->db->from('countries');
        $this->db->order_by('name asc');
        $query = $this->db->get();
        return $query->result();
    }
    
    public function getReport($param=array(),$sort,$sort_by)
    {
        
        //--- search val --//
            if(@$param['search']){
                if($param['search']['platform'] !=''){
                    $this->db->like('a.platform',$param['search']['platform']);
                }
                
                if($param['search']['browser'] != ''){
                    $this->db->like('a.browser',$param['search']['browser']);                   
                }
                     
                if($param['search']['country'] != ''){
                    $this->db->like('a.country',$param['search']['country']);                   
                }
                
                if($param['search']['startdate'] != '' && $param['search']['enddate']==''){
                    $this->db->where('DATE_FORMAT(a.created,"%Y-%m-%d") >',date('Y-m-d',strtotime($param['search']['startdate'])));
                }
                
                if($param['search']['startdate'] != '' && $param['search']['enddate']!=''){
                    $startdate = date('Y-m-d',strtotime($param['search']['startdate']));
                    $enddate = date('Y-m-d',strtotime($param['search']['enddate']));
                    
                    $this->db->where("DATE_FORMAT(a.created,'%Y-%m-%d') BETWEEN '$startdate' AND '$enddate'");
                }
            }
            
        $group='';
        switch($param['type']){

        case 'content':
            $select = 'c.title,a.platform,a.browser,a.created,a.country,a.city,a.content_id,concat(u.first_name," ",u.last_name) as content_provider,count(content_id) as total_hits,sum(watched_time) as total_watched_time,           
            SUM(IF( a.complete =1, 1, 0 )) AS complete, 
            SUM(IF( a.complete =0 && a.pause =1, 1, 0 )) AS partial, 
            SUM(IF( a.replay =1, 1, 0) ) AS replay ';
           // $group = 'a.content_id';
           // $group = 'a.id';
           
           if($param['top'] == 1){  //-- top video --//
                $this->db->group_by('a.content_id');
                $this->db->order_by('count(content_id) desc');
           }else{
                $this->db->group_by('a.id');
           }
           
          //  $join = "users u";
           // $cond = "a.content_provider=u.id";
            $this->db->join('users u',"a.content_provider=u.id");
            //-- user contents --//
            if($param['id']>0){
                $this->db->select('CONCAT(cu.first_name," " ,cu.last_name) AS customer_name',false);
                $this->db->where('a.user_id',$param['id']);
                $this->db->join('customers cu','cu.id=a.user_id');
            }
            //--- search val --//
            if(@$param['search']){
                if($param['search']['title'] !=''){
                    $this->db->like('c.title',$param['search']['title']);
                }
                
                if($param['search']['contentprovider'] != ''){
                    $this->db->where('u.id',$param['search']['contentprovider']);
                   // $join = "users u";
                   // $cond = "a.content_provider=u.id";
                }             
            }
            if($sort){
            $this->db->order_by($sort,$sort_by);
            }
            break;
        case 'useragent':
            $select = 'a.platform, a.browser, count( a.id ) as total_hits , sum( a.watched_time ) as total_watched_time';
            //$group = 'a.platform, a.browser';            
             
            if($param['top'] == 1){  //-- top video --//               
                $this->db->order_by('count(a.id) desc');
            }
            $this->db->group_by('a.platform, a.browser');
            
            if($sort){
            $this->db->order_by($sort,$sort_by);
            }
            
            break;
        case 'summary':
            $select = "count(distinct a.user_id) unique_hits,count(a.id) as total_hits,
            SUM(IF( a.complete =0 && a.pause =1, 1, 0 )) AS total_partial,
            SUM(IF(a.complete=1,1,0)) as total_complete,
            SUM(IF(a.replay=1,1,0)) as total_replay, sum( a.watched_time ) as total_watched_time";
            
             //-- user contents --//
            if($param['id']>0){
                $this->db->where('a.user_id',$param['id']);
            }
            //--- search val --//
            if(@$param['search']){
                if($param['search']['title'] !=''){
                    $this->db->like('c.title',$param['search']['title']);
                }
                
                if($param['search']['name'] != ''){
                    $this->db->like('cu.first_name',$param['search']['name']);
                    $join = "customers cu";
                    $cond = "a.user_id=cu.id";
                }
                
                if($param['search']['contentprovider'] != ''){
                    $this->db->where('u.id',$param['search']['contentprovider']);
                    $join = "users u";
                    $cond = "a.content_provider=u.id";
                }
                               
            }
            
            break;
        case 'map':
             $select = 'a.country,a.country_code as code,count( a.id ) as total_hits , sum( a.watched_time ) as total_watched_time';
            //$group = 'a.country_code';
            $this->db->group_by('a.country_code');
            break;
        case 'country':
            $select = 'a.country_code as code,a.country,count( a.id ) as total_hits , sum( a.watched_time ) as total_watched_time';
           // $group = 'a.country_code';
           
           if($param['top'] == 1){  //-- top video --//                
                $this->db->order_by('count(a.id) desc');
            }
            if($param['code'] !=''){
                $this->db->where('a.country_code',$param['code']);
            }
           $this->db->group_by('a.country_code');
           
            break;
         case 'region':            
            $select = 'a.country_code as code,a.country, a.state,a.city,a.postal_code,count( a.id ) as total_hits , sum( a.watched_time ) as total_watched_time';
           // $group = 'a.country_code';
           $this->db->where('country_code',$param['code']);           
           $this->db->group_by('a.state');           
            break;
        case 'content_provider':
            $select = 'concat(u.first_name," ",u.last_name) as name,count( a.id ) as total_hits , sum( a.watched_time ) as total_watched_time';
           // $group = 'a.content_provider';
           $this->db->group_by('a.content_provider');
           $this->db->join('users u','a.content_provider=u.id');
            //$join = "users u";
            //$cond = "a.content_provider=u.id";          
            break;
        case 'user':
            $select = 'cu.id,concat(cu.first_name," ",cu.last_name) as name,count( a.id ) as total_hits , sum( a.watched_time ) as total_watched_time';
            //$group = 'u.id';
            $this->db->group_by('cu.id');
            $this->db->join('customers cu','a.user_id=cu.id');
            //$join = "customers u";
            //$cond = "a.user_id=u.id";
            
            //--- search val --//
            if(@$param['search']){
                if($param['search']['name'] !=''){
                    $this->db->like('u.first_name',$param['search']['name']);
                }                                
            }
            if($sort){
            $this->db->order_by($sort,$sort_by);
            }
            break;
        
        case 'usercontent':
            $select = 'c.title,a.platform,a.browser,a.created,a.country,a.city,a.content_id,concat(u.first_name," ",u.last_name) as content_provider,count(content_id) as total_hits,sum(watched_time) as total_watched_time,           
            SUM(IF( a.complete =1, 1, 0 )) AS complete, 
            SUM(IF( a.complete =0 && a.pause =1, 1, 0 )) AS partial, 
            SUM(IF( a.replay =1, 1, 0) ) AS replay ';
           // $group = 'a.content_id';
            $this->db->group_by('a.id');
            $this->db->join('users u','a.content_provider=u.id');
            
           // $join = "users u";
          // $cond = "a.content_provider=u.id";
            
            //-- user contents --//
            if($param['id']>0){
                $this->db->where('a.user_id',$param['id']);
            }
            //--- search val --//
            if(@$param['search']){
                if($param['search']['title'] !=''){
                    $this->db->like('c.title',$param['search']['title']);
                }
                
                if($param['search']['contentprovider'] != ''){
                    $this->db->where('u.id',$param['search']['contentprovider']);
                  //  $join = "users u";
                 //   $cond = "a.content_provider=u.id";
                }
            }
            if($sort){
            $this->db->order_by($sort,$sort_by);
            }
            break;
        }
        
        if($param['l'] > 0){
            $this->db->limit($param['l']);            
        }
        if($join !=''){
            
            $this->db->join($join,$cond );
        }
        if($type !='content_provider'){
            // $this->db->where('a.content_provider',$this->uid);
        }
        
        $this->db->select($select,false);
        $this->db->from('analytics a');
        $this->db->join('contents c','a.content_id=c.id');
       
        //$this->db->group_by($group);
        $query = $this->db->get();
    //echo '<br>'.$this->db->last_query();
        return $query->result();
        
    }
    
    function getContentProvider()
    {
        $this->db->select('id,concat(first_name," ",last_name) as name',false);
        $this->db->from('users');
        $this->db->where('status','active');
        $this->db->where('role_id',1);
        $query = $this->db->get();
        return $query->result();
    }
    
    function getDailyReport($daterange)
    {
        $fieldsArr = array();
        for($i=0;$i < count($daterange);$i++){                                    
           $between = sprintf("a.created between '%s' and '%s'",date('Y-m-d 00:00:00',strtotime($daterange[$i])),date('Y-m-d 23:59:59',strtotime($daterange[$i])));            
          $alias = date("M d",strtotime($daterange[$i]));
           $fieldsArr[] = "SUM(IF($between,1,0)) as '$alias'";
        }
       
        $fields = implode(',',$fieldsArr);
        $this->db->select("$fields", false);
        $this->db->from('analytics a');
        $query =  $this->db->get();
       // echo $this->db->last_query();
        $result = $query->result();
        $rst['color'] = $this->randColor(1);
        foreach($result[0] as $key=>$val){
            $rst['data'][] = array('y'=>$key,'value'=>$val);   
       }
       //print_r($rst);
       return $rst;
    }
    
    function randColor( $numColors ) {
        $chars = "ABCDEF0123456789";   
        $size = strlen( $chars );
        $str = array();
        for( $i = 0; $i < $numColors; $i++ ) {
            $tmp = '#';
            for( $j = 0; $j < 6; $j++ ) {
                $tmp .= $chars[ rand( 0, $size - 1 ) ];
            }
            $str[$i] = $tmp;
        }
        return $str;
    }
}

?>