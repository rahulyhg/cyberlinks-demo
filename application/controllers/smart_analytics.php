
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Smart_analytics extends MY_Controller {
    
    
	function __construct()
	{
		parent::__construct();
		//$this->load->model('/api/Video_model');
		//$this->load->model('/ads/Ads_analytics_model');
		$this->load->model('/analytics/newanalytics_model');
		$this->load->library('User_Agent');
		$this->load->helper('common');
		//  $this->load->helper('pdf_helper');
		//  $this->load->helper('csv_helper');

		$this->load->config('messages');
		$this->data['welcome'] = $this;
		$per = $this->check_per();
        if(!$per){
          redirect(base_url() . 'layout/permission_error');
        }
		//$this->load->library('User_Agent');//--regex class to get user agent --//
		//-- get browser http_user_agent info in array --//
		//   $this->result = get_browser(null, true);

		$this->result = User_Agent::getinfo();  //--regex class to get user agent --//
		//---------------------//
		$this->load->library('session');
		$s = $this->session->all_userdata();
		$this->user = $s[0]->username;
		$this->uid = $s[0]->id;
		$this->role_id = $s[0]->role_id;
	}
	
	
	function getDateIntervel($dayDifference){
		$dayDiff  = array();
		$startDate = date("Y-m-d");
		if($dayDifference === 1){
		$endDate = $startDate; 
		}else{
			$endDate =  date("Y-m-d",strtotime("-$dayDifference day", strtotime($startDate))) ;
		}
		$dayDiff['startdate'] = $endDate;
		$dayDiff['enddate'] = $startDate;
		return $dayDiff ;
	}
        
	function index(){
		$days = $this->input->post('daydiff'); 
			if($days =='')
				$days =1;
		if($days === 'Today' || $days === 0)
			 $days = 1;
		
		$dayDiff = $this->getDateIntervel($days);
		///print_r($dayDiff);
		
		$prepareData = array();
	    $sqlData = array();
		$sqlData['startdate'] = $dayDiff['startdate']." 00:00:00";
		$sqlData['enddate']=   $dayDiff['enddate']." 23:59:59";
		$sqlData['startdate']	=   "2015-05-19 00:00:00";
		$sqlData['enddate']		=   "2015-05-19 23:59:59";
	
		$TotalSession 			=	$this->newanalytics_model->getTotalSession($sqlData);
		$prepareData['totalsession'] = $TotalSession;
		$TotalUser =	$this->newanalytics_model->getTotalUser($sqlData);
		$prepareData['totaluser'] = $TotalUser;	
		$NewUser =	$this->newanalytics_model->getNewUser($sqlData);
		$prepareData['newuser'] = $NewUser;
		$Platform =	$this->newanalytics_model->getPlatform($sqlData);
		$prepareData['platform'] = $Platform;
		$Resolution =	$this->newanalytics_model->getResolution($sqlData);
		$prepareData['resolution'] = $Resolution;
		$Carrier =	$this->newanalytics_model->getCarrier($sqlData);
		$prepareData['carrier'] = $Carrier;
		$TopUser =	$this->newanalytics_model->getTopUser($sqlData);
		$prepareData['topuser'] = $TopUser;
		$CountryUser =	$this->newanalytics_model->getCountryUser($sqlData);
		$prepareData['country'] = $CountryUser;
		$TotalSessionDayWise 	= 	$this->newanalytics_model->getTotalSessionDayWise($sqlData);
		// echo "<pre>";
		//		print_r($TotalSessionDayWise);
			//	die; 
		$prepareData['graph']['totalsession'] = $TotalSessionDayWise;
		$TotalUserDaysWaise 	=	$this->newanalytics_model->getTotalUserDaysWaise($sqlData);
		$prepareData['graph']['totaluser'] = $TotalUserDaysWaise;
		$NewUserPerDay 			= 	$this->newanalytics_model->getNewUserPerDay($sqlData);
		$prepareData['graph']['totalnewuser'] = $TotalUserDaysWaise;
		$ReturningUserPerDay 			= $this->newanalytics_model->getReturningUserPerDay($sqlData);
		$prepareData['graph']['returninguser'] = $TotalUserDaysWaise;
		$data['welcome'] = $this;
		 // echo "<pre>";
		//		print_r($prepareData);
		//		die; 
	    $data['jsondata'] = json_encode($prepareData,true);
		if (!empty($_POST)) {
				ECHO $data['jsondata'] ;
				//echo $this->show_view('newdashboard',$data,false);
				exit;
		}else{
			$this->show_view('newdashboard',$data);
		}
	}
        
         
   function Users(){
	//die;
		
		$days = $this->input->post('daydiff'); 
			if($days =='')
				$days =1;
		if($days === 'Today' || $days === 0)
			 $days = 1;
		
		// $days = 1;
		$dayDiff = $this->getDateIntervel($days);
		///print_r($dayDiff);
		
		$prepareData = array();
	    $sqlData = array();
		$sqlData['startdate'] = $dayDiff['startdate']." 00:00:00";
		$sqlData['enddate']=   $dayDiff['enddate']." 23:59:59";
		$sqlData['startdate']	=   "2015-05-19 00:00:00";
	 	$sqlData['enddate']		=   "2015-05-19 23:59:59";
	
		$TotalUserDaysWaise 	=	$this->newanalytics_model->getTotalUserDaysWaise($sqlData);
		$prepareData['graph']['totaluser'] = $TotalUserDaysWaise;
		
	
		
		$NewUserPerDay 			= 	$this->newanalytics_model->getNewUserPerDay($sqlData);
		$prepareData['graph']['totalnewuser'] = $NewUserPerDay;
		
		$ReturningUserPerDay 			= $this->newanalytics_model->getReturningUserPerDay($sqlData);
		$prepareData['graph']['returninguser'] = $ReturningUserPerDay;
		
		 
		
		$gridData  =  array();
		$k=1;
		for($i = 1 ; $i<= 24 ; $i++){
		$gridData[$k]['hr']  = $prepareData['graph']['totaluser'][$i]['hr'];
		$gridData[$k]['t']  =  sprintf('%02d',($prepareData['graph']['totaluser'][$i]['hr']-1)).":00";
		$gridData[$k]['h']  = sprintf('%02d', $prepareData['graph']['totaluser'][$i]['hr']).":00";
		$gridData[$k]['totaluser']  = $prepareData['graph']['totaluser'][$i]['totaluser'];
		$gridData[$k]['totalnewuser']  = $prepareData['graph']['totalnewuser'][$i]['totaluser'];
		$gridData[$k]['returninguser']  = $prepareData['graph']['returninguser'][$i]['totaluser']; 
		$k++;
		}
		
		/* 
		 echo "<pre>";
		print_r($gridData);
		die;   */
		$data['welcome'] = $this;
		
		 $data['jsondata'] = json_encode($gridData,true);
		if (!empty($_POST)) {
				ECHO $data['jsondata'] ;
				exit;
		}else{
			$this->show_view('analytics_user',$data);
		}
		
		
		
	}
        
        
    function Loyalty(){
		$data['welcome'] = $this;
		$this->show_view('analytics_loyalty',$data);
	}
        
    function Sessions(){
        
		$days = $this->input->post('daydiff');
		if($days === 'Today')
		$days =1;
                if (!empty($_POST)) 
		{
                    $dayDiff = $this->getDateIntervel($days);

                    $prepareData = array();
                    $sqlData = array();
                    $sqlData['startdate'] = $dayDiff['startdate']." 00:00:00";
                    $sqlData['enddate']=   $dayDiff['enddate']." 23:59:59";

                    $TotalSession =	$this->newanalytics_model->getSessionDataGraph($sqlData);
                    $newSession =	$this->newanalytics_model->getNewSessionDataGraph($sqlData);                

                    $resultData['graph'] = array();
                    $resultData['grid'] = array();
                    $resultData['total'] = array();
                    
                    $total =0;
                    $new = 0;
                    $unique = 0;
                    foreach($TotalSession as $key => $value)
                    {            
                            $nkey=$value['date'];
                            if(is_array($newSession)>0 && is_array($newSession[$key]))
                            {  
                             $value['newsession']= $newSession[$key]['newsession'];
                            }
                            else
                            {
                                $value['newsession']=0;
                            }

                            $resultData['graph'][$nkey] = $value;

                            $gridArray = array($nkey,$value['totalsession'],$value['newsession'],$value['uniquesession']);
                            
                             $resultData['grid'][] = $gridArray;
                          
                             $total +=$value['totalsession'];
                             $unique +=$value['uniquesession'];
                             $new +=$value['newsession'];
                    }  
                    
                    $resultData['total']=array($total,$unique,$new);

                    echo json_encode($resultData,true);
		}
		else 
		{                
		$data['welcome'] = $this;
		$this->show_view('analytics_session',$data);
		}
	}
        
    function Frequency(){
		$data['welcome'] = $this;
		$this->show_view('analytics_frequency',$data);
	}
        
    function Countries(){
		$data['welcome'] = $this;
		$this->show_view('analytics_countries',$data);
	}
        
    function Devices(){
		$data['welcome'] = $this;
		$this->show_view('analytics_device',$data);
	}
        
    function Versions(){
		$data['welcome'] = $this;
		$this->show_view('analytics_version',$data);
	}
        
    function Carrier(){
		$data['welcome'] = $this;
		$this->show_view('analytics_carrier',$data);
	}
        
    function Platforms(){
		$data['welcome'] = $this;
		$this->show_view('analytics_platforms',$data);
	}

}
