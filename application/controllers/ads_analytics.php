<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
error_reporting(1);
class Ads_analytics extends MY_Controller {

	function __construct()
	{
            parent::__construct();
            $this->load->model('/api/Video_model');
	    $this->load->model('/ads/Ads_analytics_model');
	    $this->load->library('User_Agent');
	    $this->load->helper('common');
	    $this->load->helper('pdf_helper');
	    $this->load->helper('csv_helper');
	    $this->load->config('messages');
		$per = $this->check_per();
        if(!$per){
          redirect(base_url() . 'layout/permission_error');
        }
	    $this->data['welcome'] = $this;
	    
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
	
	function form()
	{		
		$this->load->view('analytics-form');
	}
	
	function index()
	{
		// print_r($this->result);die;
		//-- get geocoding google api --//
		$this->data['lat'] = $lat = $_GET['lat'];
		$this->data['long'] = $lng = $_GET['lng'];
		$url = "http://maps.googleapis.com/maps/api/geocode/json?latlng=".$lat.",".$lng."&sensor=true";
		$data = @file_get_contents($url);
		$result = json_decode($data,true);
		//echo '<pre>';print_r($result);die;
		$this->data['geodata'] = $result['results'][0]['address_components'];
		//------------------------------
		$this->load->helper('url');
                $id = $_GET['id'];
                $device = $_GET['device'];
                $this->data['result'] = $this->Video_model->video_play($id,$device);
                $this->data['user_id'] = $_GET['user_id'];		      
                $this->load->view('ads_analytics',$this->data);
	}
        
        function addview()
        {
            $id = $_GET['id'];
            $this->Video_model->updateView($id);   
            echo 1;
        }
	
	function play()
	{
		$post = $_POST;                
		$post['browser'] = $this->result['browser'];
		$post['browser_version'] = $this->result['version'];
                $post['platform'] = $this->result['platform'];
               echo $this->Ads_analytics_model->save($post);
	}
        
        function pause()
        {
            $post = $_POST;           
            $where = array('id'=>$post['id']);
           echo $this->Ads_analytics_model->save($post,$where);
        }
        
        function complete()
        {
            $post = $_POST;           
            $where = array('id'=>$post['id']);
           echo $this->Ads_analytics_model->save($post,$where);
        }
	
	function replay()
	{
		$post = $_POST;
		$post['browser'] = $this->result['browser'];
		$post['browser_version'] = $this->result['version'];
                $post['platform'] = $this->result['platform'];
		//$where = array('id'=>$post['id']);
		echo $this->Ads_analytics_model->save($post);
	}
	
	function report()
	{
            $daterange = isset($_POST['searchby']) ? $_POST['searchby'] : 'default';
                switch($daterange){
				case 'today': 
                                    $date_from = date('Y-m-d');
                                    $date_to = date('Y-m-d');
				break;
				case 'date':
                                    $date_from = str_replace('/', '-', $_POST['datepickerstart']);
                                    $date_from =  date('Y-m-d', strtotime($date_from));

                                    $date_to = str_replace('/', '-', $_POST['datepickerend']);
                                    $date_to =  date('Y-m-d', strtotime($date_to));
				break;
                                case 'default':
                                    $date_from = date('Y-m-d');
                                    $date_to = date('Y-m-d');
				break;
		}
                //echo $date_from;
		$limit=5;
		$summary = $this->Ads_analytics_model->getReport(array('type'=>'summary','date_from'=>$date_from,'date_to'=>$date_to));
		//echo '<pre>';print_r($summary);die;
		$this->data['summary'] = $summary[0];
		/* $url = "http://localhost:8085/solr/collection1/select?q=content_provider:".$this->uid."&wt=json&indent=true";
			$result = file_get_contents($url);
			$summary = json_decode($result);
		*/	
			//if($summary) {
			//  $this->data['summary'] = $summary->response->docs[0];            
		//}
	    
		$this->data['content'] = $this->Ads_analytics_model->getReport(array('type'=>'content','l'=>$limit,'date_from'=>$date_from,'date_to'=>$date_to));
		$this->data['useragent'] = $this->Ads_analytics_model->getReport(array('type'=>'useragent','l'=>$limit,'date_from'=>$date_from,'date_to'=>$date_to));
                //echo "<pre>"; print_r($this->data['useragent']); die;
		$this->data['location'] = $this->Ads_analytics_model->getReport(array('type'=>'location','l'=>$limit,'date_from'=>$date_from,'date_to'=>$date_to));
		$this->data['map'] = $this->Ads_analytics_model->getReport(array('type'=>'map','l'=>$limit,'date_from'=>$date_from,'date_to'=>$date_to));
		$this->data['country'] = $this->Ads_analytics_model->getReport(array('type'=>'country','l'=>$limit,'date_from'=>$date_from,'date_to'=>$date_to));
                //echo "<pre>";                print_r($this->data['country']);
                $this->data['city'] = $this->Ads_analytics_model->getReport(array('type'=>'city','l'=>$limit,'date_from'=>$date_from,'date_to'=>$date_to));
		$this->data['content_provider'] = $this->Ads_analytics_model->getReport(array('type'=>'content_provider','l'=>$limit,'date_from'=>$date_from,'date_to'=>$date_to));
		$this->data['customer'] = $this->Ads_analytics_model->getReport(array('type'=>'user','l'=>$limit,'date_from'=>$date_from,'date_to'=>$date_to));
		$this->data['topcontent'] = $this->Ads_analytics_model->getReport(array('type'=>'content','l'=>$limit,'top'=>1,'search'=>$search,'date_from'=>$date_from,'date_to'=>$date_to));
		$this->data['stitchingReport'] = $this->Ads_analytics_model->getstitchingReport($limit,array('date_from'=>$date_from,'date_to'=>$date_to));
		
                $this->data['os'] = $this->Ads_analytics_model->getReport(array('type'=>'useragent','l'=>$limit,'mode'=>'os','date_from'=>$date_from,'date_to'=>$date_to));
                //echo '<pre>'; //               print_r($this->data['os']); 
                
                foreach ($this->data['os'] as $val){
                    
                    $os[] = $val;
                    
                }
                //print_r($os); die;
                $this->data['browser'] = $this->Ads_analytics_model->getReport(array('type'=>'useragent','l'=>$limit,'mode'=>'browser','date_from'=>$date_from,'date_to'=>$date_to));
                
		$this->show_view('ads/ads_report',$this->data);		
	}
	
	function ajax()
	{
		$id = $_GET['user_id'];
		$result = $this->Ads_analytics_model->getReport(array('id'=>$id,'type'=>'content'));
		echo json_encode($result);
	}
	
	function search_post($post)
	{
		if (isset($post['search']) && $post['search'] == 'Search') {			
			$this->session->set_userdata('search_form', $post);
		} else if (isset($post['reset']) && $post['reset'] == 'Reset') {
			$this->session->unset_userdata('search_form');
		} 
		return $this->session->userdata('search_form');	
	}
	
	function sort_input($sort_i,$sort_by)
	{
		switch ($sort_i) {
		    case "v":
			$sort = 'c.ad_title';
			if ($sort_by == 'asc')
			    $this->data['show_c'] = 'desc';
			else
			    $this->data['show_c'] = 'asc';

			break;
		    case "p":
			$sort = 'u.first_name';
			if ($sort_by == 'asc')
			    $this->data['show_p'] = 'desc';
			else
			    $this->data['show_p'] = 'asc';

			break;
		    case "h":
			$sort = 'count(a.content_id)';
			if ($sort_by == 'asc')
			    $this->data['show_h'] = 'desc';
			else
			    $this->data['show_h'] = 'asc';

			break;
			case "t":
			$sort = 'sum(a.watched_time)';
			if ($sort_by == 'asc')
			    $this->data['show_t'] = 'desc';
			else
			    $this->data['show_t'] = 'asc';
			    			    
			break;
		 case "brw":
			$sort = 'a.browser';
			if ($sort_by == 'asc')
			    $this->data['show_brw'] = 'desc';
			else
			    $this->data['show_brw'] = 'asc';

			break;
		 case "os":
			$sort = 'a.platform';
			if ($sort_by == 'asc')
			    $this->data['show_os'] = 'desc';
			else
			    $this->data['show_os'] = 'asc';

			break;
		case "loc":
			$sort = 'a.country';
			if ($sort_by == 'asc')
			    $this->data['show_loc'] = 'desc';
			else
			    $this->data['show_loc'] = 'asc';

			break;
		case "dt":
			$sort = 'a.created';
			if ($sort_by == 'asc')
			    $this->data['show_dt'] = 'desc';
			else
			    $this->data['show_dt'] = 'asc';

			break;
		    default:
			$sort_by = 'desc';
			$sort = 'a.id';
		}
		return $sort;
	}
	
	function content()
	{
		//$this->session->unset_userdata('search_form');
		$search = $this->search_post($_POST);
		
		$sort_i = $this->uri->segment(3); 
		$sort_by = $this->uri->segment(4);
		if($sort_i !=''){
			$this->data['sort_by'] =  $sort_by;
			$this->data['sort_i'] =  $sort_i;
		}else{
			$this->data['sort_by'] =  'asc';
			$this->data['sort_i'] = 'i';
		}
		//-- sorting input --//
		 $sort = $this->sort_input($sort_i,$sort_by);
		//-----//
		
		//-- get country list --//
		$this->data['country'] = $this->Ads_analytics_model->getCountry();
		
		//--------------------//
		//-- summary report --//
		$summary = $this->Ads_analytics_model->getReport(array('type'=>'summary','search'=>$search),$sort,$sort_by);
		$this->data['summary'] = $summary[0];
		//--- search form content provider --//
		$this->data['content_provider'] = $this->Ads_analytics_model->getContentProvider();
		//-----------------------//
		
		
		
                $this->load->library("pagination");
                $config = array();
                $config["base_url"] = base_url() . "ads_analytics/content/$sort_i/$sort_by/?";
                $config["total_rows"] = $this->Ads_analytics_model->getReportCounts(array('type'=>'content','search'=>$search),$sort,$sort_by);
                $config["per_page"] = 10;
                $config["uri_segment"] = 3;
                $config["page_query_string"] = true;
                $this->pagination->initialize($config);
                //$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
                $page = ($_GET['per_page']) ? $_GET['per_page'] : 0;
                $this->data['content'] = $this->Ads_analytics_model->getReport(array('type'=>'content','search'=>$search),$sort,$sort_by,PER_PAGE,$page);
                $this->data["links"] = $this->pagination->create_links();
                $this->data['total_rows'] = $config["total_rows"];
        
		$this->show_view('ads/ads_content_report',$this->data);
	}
        
        function allStitchingReports(){
            $this->load->library("pagination");
            $config = array();
            $config["base_url"] = base_url() . "ads_analytics/allStitchingReports/?";
            $config["total_rows"] = $this->Ads_analytics_model->getStichingReportCounts();
            $config["per_page"] = 10;
            $config["uri_segment"] = 3;
            $config["page_query_string"] = true;
            $this->pagination->initialize($config);
            //$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
            $page = ($_GET['per_page']) ? $_GET['per_page'] : 0;
            $this->data['content'] = $this->Ads_analytics_model->getAllStichingReports(PER_PAGE,$page);
            $this->data["links"] = $this->pagination->create_links();
            $this->data['total_rows'] = $config["total_rows"];
            $this->show_view('ads/stitching_reports',$this->data);
        }
	
	function user()
	{
		//$this->session->unset_userdata('search_form');
		$search = $this->search_post($_POST);
		
		$sort_i = $this->uri->segment(3); 
		$sort_by = $this->uri->segment(4);
		
		if($sort_i !=''){
			$this->data['sort_by'] =  $sort_by;
			$this->data['sort_i'] =  $sort_i;
		}else{
			$this->data['sort_by'] =  'asc';
			$this->data['sort_i'] = 'i';
		}
		
		//-- sorting input --//
		$sort = $this->sort_input($sort_i,$sort_by);
		//-----//
		
		//-- summary report --//
		$summary = $this->Ads_analytics_model->getReport(array('type'=>'summary','search'=>$search),$sort,$sort_by);
		$this->data['summary'] = $summary[0];		
		//echo '<pre>';print_r($summary);die;
		
                $this->load->library("pagination");
                $config = array();
                $config["base_url"] = base_url() . "ads_analytics/user/$sort_i/$sort_by/?";
                $config["total_rows"] = $this->Ads_analytics_model->getReportCounts(array('type'=>'user','search'=>$search),$sort,$sort_by);
                $config["per_page"] = 10;
                $config["uri_segment"] = 3;
                $config["page_query_string"] = true;
                $this->pagination->initialize($config);
                //$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
                $page = ($_GET['per_page']) ? $_GET['per_page'] : 0;
                $this->data['user'] = $this->Ads_analytics_model->getReport(array('type'=>'user','search'=>$search),$sort,$sort_by,PER_PAGE,$page);
                $this->data["links"] = $this->pagination->create_links();
                $this->data['total_rows'] = $config["total_rows"];
                
		$this->show_view('ads/ads_user_report',$this->data);
	}

		
	function usercontent()
	{
		//$this->session->unset_userdata('search_form');
		$search = $this->search_post($_POST);
		if($_REQUEST['id']){
			$this->data['userid'] = $id = $_REQUEST['id'];
		}elseif($this->uri->segment(6) !='')
		{
			$this->data['userid'] = $id = $this->uri->segment(6);
		}
		
		$sort_i = $this->uri->segment(3); 
		$sort_by = $this->uri->segment(4);
		
		if($sort_i !=''){
			$this->data['sort_by'] =  $sort_by;
			$this->data['sort_i'] =  $sort_i;
		}else{
			$this->data['sort_by'] =  'asc';
			$this->data['sort_i'] = 'i';
		}
		//-- sorting input --//
		$sort = $this->sort_input($sort_i,$sort_by);
		//-----//
	
		//-- get country list --//
		$this->data['country'] = $this->Ads_analytics_model->getCountry();
		
		//-- summary report --//
		$summary = $this->Ads_analytics_model->getReport(array('id'=>$id,'type'=>'summary','search'=>$search),$sort,$sort_by);
		$this->data['summary'] = $summary[0];
		//--- search form content provider --//
		$this->data['content_provider'] = $this->Ads_analytics_model->getContentProvider();
		//-----------------------//
		
		//$this->data['content'] = $this->Ads_analytics_model->getReport(array('id'=>$id,'type'=>'content','search'=>$search),$sort,$sort_by);
		
                $this->load->library("pagination");
                $config = array();
                $config["base_url"] = base_url() . "ads_analytics/usercontent/$sort_i/$sort_by/?";
                $config["total_rows"] = $this->Ads_analytics_model->getReportCounts(array('id'=>$id,'type'=>'content','search'=>$search),$sort,$sort_by);
                $config["per_page"] = 10;
                $config["uri_segment"] = 3;
                $config["page_query_string"] = true;
                $this->pagination->initialize($config);
                //$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
                $page = ($_GET['per_page']) ? $_GET['per_page'] : 0;
                $this->data['content'] = $this->Ads_analytics_model->getReport(array('id'=>$id,'type'=>'content','search'=>$search),$sort,$sort_by,PER_PAGE,$page);
                $this->data["links"] = $this->pagination->create_links();
                $this->data['total_rows'] = $config["total_rows"];
                
		$this->show_view('ads/ads_user_content_report',$this->data);
	}
		
	function device()
	{
		//$this->session->unset_userdata('search_form');
		$search = $this->search_post($_POST);
		
		$sort_i = $this->uri->segment(3); 
		$sort_by = $this->uri->segment(4);
		
		if($sort_i !=''){
			$this->data['sort_by'] =  $sort_by;
			$this->data['sort_i'] =  $sort_i;
		}else{
			$this->data['sort_by'] =  'asc';
			$this->data['sort_i'] = 'i';
		}
		
		//-- sorting input --//
		$sort = $this->sort_input($sort_i,$sort_by);
		//-----//
		
		//-- summary report --//
		$summary = $this->Ads_analytics_model->getReport(array('type'=>'summary','search'=>$search),$sort,$sort_by);
		$this->data['summary'] = $summary[0];		
		//echo '<pre>';print_r($summary);die;		
		//$this->data['useragent'] = $this->Ads_analytics_model->getReport(array('type'=>'useragent','search'=>$search),$sort,$sort_by);
		
                $this->load->library("pagination");
                $config = array();
                $config["base_url"] = base_url() . "ads_analytics/device/$sort_i/$sort_by/?";
                $config["total_rows"] = $this->Ads_analytics_model->getReportCounts(array('type'=>'useragent','search'=>$search),$sort,$sort_by);
                $config["per_page"] = 10;echo 'here';
                $config["uri_segment"] = 3;
                $config["page_query_string"] = true;
                $this->pagination->initialize($config);
                //$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
                $page = ($_GET['per_page']) ? $_GET['per_page'] : 0;
                $this->data['useragent'] = $this->Ads_analytics_model->getReport(array('type'=>'useragent','search'=>$search),$sort,$sort_by,PER_PAGE,$page);
                $this->data["links"] = $this->pagination->create_links();
                $this->data['total_rows'] = $config["total_rows"];
                
		$this->show_view('ads/ads_device_report',$this->data);
	}
	
	function export()
	{
		$search = $this->session->userdata('search_form');
		//echo $this->uri->segment(4);die;
		//-- sorting input --//
		
		$sort_i = $this->uri->segment(5); 
		$sort_by = $this->uri->segment(6);
		
		$type = $this->uri->segment(8);
		
		if($this->uri->segment(9)=='country'){
			$country = $this->uri->segment(10);
		}else{
			$id = $this->uri->segment(10);
		}
		$sort = $this->sort_input($sort_i,$sort_by);
		//-----//
		//-- result --//		
			$this->data['result'] = $this->Ads_analytics_model->getReport(array('code'=>$country,'id'=>$id,'type'=>$type,'search'=>$search,'export'=>1),$sort,$sort_by);
		//echo '<pre>';print_r($this->data['result']);die;
		
		if($type == 'content'){
			if($this->uri->segment(4)=='pdf'){
				
				 $content =  $this->load->view('templates/ads_pdf_content',$this->data,true);
				//-- create pdf --//
				create_pdf($content, 'Campaign Base Report');
			}elseif($this->uri->segment(4)=='csv'){
				$heading = array('Campaigns','Advertiser','Platform','Browser','Location','Date','Total Impressions');
				//$content =  $this->load->view('templates/pdf_content',$this->data,true);				
				
				$dataRpt = array();
				$num=0;
				foreach($this->data['result'] as $p) {
				    $dataRpt[$num]['title']       = $p->ad_title;
				    $dataRpt[$num]['content_provider']  = $p->content_provider;
				    $dataRpt[$num]['platform']  = $p->platform;
				    $dataRpt[$num]['browser']  = $p->browser;
				    $dataRpt[$num]['location']  = $p->country;
				    $dataRpt[$num]['date']  = $p->created;
				    $dataRpt[$num]['hits']        = $p->total_hits;
				    //$dataRpt[$num]['watched time'] = time_from_seconds($p->total_watched_time);                 
				    $num++;
			       }
				query_to_csv($dataRpt,$heading);
				//echo query_to_csv($content);
				//exit;
			}
		}
		
		if($type == 'usercontent'){
			if($this->uri->segment(4)=='pdf'){
				 $content =  $this->load->view('templates/ads_pdf_usercontent',$this->data,true);
				//-- create pdf --//
				create_pdf($content, 'User campaign Report');
				
			}elseif($this->uri->segment(4)=='csv'){
				$heading = array('Campaigns','Advertiser','Platform','Browser','Location','Date','Total Impressions');
				//$content =  $this->load->view('templates/pdf_content',$this->data,true);				
				//print_r($this->data['result']);die;
				$dataRpt = array();
				$num=0;
				foreach($this->data['result'] as $p) {
				    $dataRpt[$num]['title']       = $p->ad_title;
				    $dataRpt[$num]['content_provider']  = $p->content_provider;
				    $dataRpt[$num]['platform']  = $p->platform;
				    $dataRpt[$num]['browser']  = $p->browser;
				    $dataRpt[$num]['location']  = $p->country;
				    $dataRpt[$num]['date']  = $p->created;
				    $dataRpt[$num]['hits']        = $p->total_hits;				    
				   // $dataRpt[$num]['watched time'] = time_from_seconds($p->total_watched_time);                 
				    $num++;
			       }
				query_to_csv($dataRpt,$heading);
				//echo query_to_csv($content);
				//exit;
			}
		}
		
		if($type == 'user'){
			if($this->uri->segment(4)=='pdf'){
				$user =  $this->load->view('templates/ads_pdf_user',$this->data,true);				
				//-- create pdf --//
				create_pdf($user,'User Based Report');
			}elseif($this->uri->segment(4)=='csv'){
				$heading = array('Name','Total Impressions');
				//$content =  $this->load->view('templates/pdf_content',$this->data,true);
				$dataRpt = array();
				$num=0;
				foreach($this->data['result'] as $p) {
				    $dataRpt[$num]['name']          = $p->name;
				    $dataRpt[$num]['hits']        = $p->total_hits;
				    //$dataRpt[$num]['watched time'] = time_from_seconds($p->total_watched_time);                 
				    $num++;
			       }
				query_to_csv($dataRpt,$heading);
				//echo query_to_csv($content);
				//exit;
			}
		}
		
		if($type == 'useragent'){
			if($this->uri->segment(4)=='pdf'){
				 $content =  $this->load->view('templates/ads_pdf_device',$this->data,true);
				//-- create pdf --//
				create_pdf($content, 'Device Base Report');
			}elseif($this->uri->segment(4)=='csv'){
				$heading = array('Platform','Browser','Total Impressions');
				//$content =  $this->load->view('templates/pdf_content',$this->data,true);				
				
				$dataRpt = array();
				$num=0;
				foreach($this->data['result'] as $p) {
				    $dataRpt[$num]['platform']       = $p->platform;
				    $dataRpt[$num]['browser']  = $p->browser;
				    $dataRpt[$num]['hits']        = $p->total_hits;
				  //  $dataRpt[$num]['watched time'] = time_from_seconds($p->total_watched_time);                 
				    $num++;
			       }
				query_to_csv($dataRpt,$heading);
				//echo query_to_csv($content);
				//exit;
			}
		}
		
		if($type == 'region'){			
			if($this->uri->segment(4)=='pdf'){				
				$geomap =  $this->load->view('templates/ads_pdf_geomap',$this->data,true);				
				//-- create pdf --//
				create_pdf($geomap,'Region Based Report');
			}elseif($this->uri->segment(4)=='csv'){ 
				$heading = array('Country','Region','Total Impressions');
				//$content =  $this->load->view('templates/pdf_content',$this->data,true);
				$dataRpt = array();
				$num=0;
				foreach($this->data['result'] as $p) {
				    $dataRpt[$num]['country']          = $p->country;
				    $dataRpt[$num]['state']          = $p->state;
				    $dataRpt[$num]['hits']        = $p->total_hits;
				  //  $dataRpt[$num]['watched time'] = time_from_seconds($p->total_watched_time);                 
				    $num++;
			       }
				query_to_csv($dataRpt,$heading);
				//echo query_to_csv($content);
				//exit;
			}
		}
		
		if($type == 'country'){
			$this->data['c']=1;
			if($this->uri->segment(4)=='pdf'){				
				$geomap =  $this->load->view('templates/ads_pdf_geomap',$this->data,true);				
				//-- create pdf --//
				create_pdf($geomap,'Country Based Report');
			}elseif($this->uri->segment(4)=='csv'){ 
				$heading = array('Country','Total Impressions');
				//$content =  $this->load->view('templates/pdf_content',$this->data,true);
				$dataRpt = array();
				$num=0;
				foreach($this->data['result'] as $p) {
				    $dataRpt[$num]['country']          = $p->country;				   
				    $dataRpt[$num]['hits']        = $p->total_hits;
				  //  $dataRpt[$num]['watched time'] = time_from_seconds($p->total_watched_time);                 
				    $num++;
			       }
				query_to_csv($dataRpt,$heading);
				//echo query_to_csv($content);
				//exit;
			}
		}
	}
        
        function export_stitching(){
            $this->data['result'] = $this->Ads_analytics_model->getAllStichingReports(0,0,true);
            //echo '<pre>'; print_r($this->data['result']); exit;
            if($this->uri->segment(3)=='pdf'){
                        $content =  $this->load->view('templates/stitching_pdf_content',$this->data,true);
                       //-- create pdf --//
                       create_pdf($content, 'In Stream Stitching Report');
            }elseif($this->uri->segment(3)=='csv'){
                    $heading = array('Creative','Duration','UserCount','DateTime');
                    //$content =  $this->load->view('templates/pdf_content',$this->data,true);				

                    $dataRpt = array();
                    $num=0;
                    foreach($this->data['result'] as $p) {
                        //$dataRpt[$num]['id']       = $p->id;
                        $dataRpt[$num]['Commercial']  = isset($p->ad_title) ? $p->ad_title : $p->Commercial;
                        $dataRpt[$num]['Duration']  = $p->Duration;
                        $dataRpt[$num]['UserCount']  = $p->UserCount;
                        $dataRpt[$num]['StartTime']  = $p->StartTime;
                        $num++;
                   }
                    query_to_csv($dataRpt,$heading);
                    //echo query_to_csv($content);
                    //exit;
            }
        }
	
	function top()
	{
		$limit=5;
		$search = $this->search_post($_POST);		
		
		$summary = $this->Ads_analytics_model->getReport(array('type'=>'summary','search'=>$search));
		$this->data['summary'] = $summary[0];
		
		$this->data['topcontent'] = $this->Ads_analytics_model->getReport(array('type'=>'content','l'=>$limit,'top'=>1,'search'=>$search));
		$this->data['topuseragent'] = $this->Ads_analytics_model->getReport(array('type'=>'useragent','l'=>$limit,'top'=>1,'search'=>$search));
		$this->data['topcountry'] = $this->Ads_analytics_model->getReport(array('type'=>'country','l'=>$limit,'top'=>1,'search'=>$search));
		//echopre($this->data);
		$this->show_view('ads/ads_top_report',$this->data);
	}
	
	function dailyreport()
	{
		$search = $this->search_post($_POST);
		$startdate = $_GET['startdate'];
		$enddate = $_GET['enddate'];
		//-- generate daily graph data --//
		if($startdate=='' && $enddate == ''){
			$startdate = date('d-m-Y', strtotime(date('Y-m-01')));
			$enddate = date('Y-m-d');			
		}
		if($startdate == ''){
			$startdate = date('d-m-Y', strtotime(date('Y-m-01')));
		}
		if($enddate == ''){
			$enddate = date('Y-m-d');
		}
		
		$dateRange = createDateRangeArray($startdate,$enddate);//-- helper to get date range
		$dailyreport = $this->Ads_analytics_model->getDailyReport($dateRange);
		
		echo json_encode($dailyreport);die;
		
		//-----------------------------//		
	}
	
	function geographic()
	{
		if(@$_GET['country'] =='' && @$_GET['c'] ==''){
			$this->data['country_code'] = 'IN';
		}else{
			$this->data['country_code'] = $_GET['country'];
		}
		
		if(@$_GET['c'] == 1){
			$type = 'country';
		}else{
			$type = 'region';
		}
		
		$search = $this->search_post($_POST);
				
		$sort_i = $this->uri->segment(3); 
		$sort_by = $this->uri->segment(4);
		
		if($sort_i !=''){
			$this->data['sort_by'] =  $sort_by;
			$this->data['sort_i'] =  $sort_i;
		}else{
			$this->data['sort_by'] =  'asc';
			$this->data['sort_i'] = 'i';
		}
		//-- sorting input --//
		$sort = $this->sort_input($sort_i,$sort_by);

		$this->data['country'] = $this->Ads_analytics_model->getReport(array('type'=>'country'));
		$this->data['country_name'] = $this->Ads_analytics_model->getReport(array('type'=>'country','code'=>$this->data['country_code']));
		$this->data['geomap'] = $this->Ads_analytics_model->getReport(array('type'=>$type,'code'=>$this->data['country_code'],'search'=>$search),$sort,$sort_by);			
		if(@$_GET['c'] == 1){
			$this->show_view('ads/ads_geomap_country',$this->data);
		}else{
			$this->show_view('ads/ads_geomap_region',$this->data);
		}
	}
        
        /*
         * Function to add user Interest tags
         */
        function user_content_tags(){
           
           $content_ids = $this->Ads_analytics_model->getContentKeywords($_POST['content_id']);
           if($content_ids!=null){
               $this->Ads_analytics_model->saveUserContentKeywords($_POST['user_id'],$content_ids);
           }
           
        }
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
