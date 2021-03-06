<?php
//header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Origin: *");
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Example
 *
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array.
 *
 * @package		CodeIgniter
 * @subpackage	Rest Server
 * @category	Controller
 * @author		Phil Sturgeon
 * @link		http://philsturgeon.co.uk/code/
*/

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH.'/libraries/REST_Controller.php';

class User extends REST_Controller
{      
   function __construct()
   {
       parent::__construct();
       $this->load->helper('url');
       $this->load->model('api/User_model');
       $this->admin_token = $this->get('token');
	   
        if($this->admin_token ==' ' || $this->admin_token==0){
            $this->admin_token = $this->post('token');
            {
            $this->response(array('code'=>0,'error' => "Must Pass Token"), 404);
            }
        }
      //$this->admin_token = '54d46a72bab49';
   }       
    
   function base64_to_jpeg($base64_string, $output_file,$extension) {
        
	$img = $base64_string;
	//$img = str_replace('data:image/png;base64,', '', $img);
	$img = str_replace(' ', '+', $img);        
        $file = uniqid() . '.'.$extension;
	$data = base64_decode($img);
        $srctmp = $output_file.'.tmp'.'.'.$extension;
	$output_file .= $file;
	$success = file_put_contents($srctmp, $data);
        
        //-- create thumbnail --//
        
        if($success){
            $this->create_thumbnail($file,$srctmp,$output_file,'100','100');
            return $file;
        }else{
            return 0;	    
        }
    }
    
    function checkdevice_get()
    {
	 $id = $this->get('uniqueID');
	 $result = $this->User_model->validate_device($id);
	if($result >0){
            $this->response(array('code'=>1, 'result' => $result), 200);
        }  else {
            $this->response(array('code'=>0,'error' => "No record found"), 200);
        }
    }
    
    function add_post()
    {
       $token = sha1($this->post('email').$this->post('password').rand());
       
       //-- check if Admin token is valid --//
	    $owner_id =  $this->User_model->checkAdminToken($this->admin_token);
	    if($owner_id <= 0){
		$this->response(array('code'=>0,'error' => "Invalid Token"), 404);
	    }
		$device_other_detail = json_decode($this->post('device_other_detail'));
        $devicedetail = json_decode($this->post('devicedetail'));
       //-----------------------------------//
       $ext = $this->post('ext');
       $my_base64_string = $this->post('pic');
        //---- Upload logo image for user --//
        if($ext !=''){
	    if($my_base64_string !=''){
		$pic = $this->base64_to_jpeg( $my_base64_string, PROFILEPIC_PATH,$ext );
		if($pic == 0){
		    $this->response(array('code'=>0,'error' => "Error uploading image."), 404);
		}
	    }
        }else{
                $incoming_tmp = @$_FILES['pic']['tmp_name'];
                $incoming_original = @$_FILES["pic"]["name"];
                if ($incoming_original != '') {                    
                    $path = PROFILEPIC_PATH;
                    $allowed = array('jpg', 'jpeg', 'png', 'gif');
                    $output = $this->uploadfile($incoming_tmp, $incoming_original, $path, $allowed);  //-- upload file --//

                    if ($output['error']) {
                        $this->response(array('code'=>0,'error' => $output['error']), 404);
                    } else {
                        $pic = base_url().$output['path'];
                    }
                } else {
                    //$_POST['file'] = $_POST['logo'];
                }
        }
        $data = array(
	    'owner_id' => $owner_id,
	    'username' => $this->post('email'), 
            'first_name' => $this->post('first_name'), 
            'last_name' => $this->post('last_name'),
            'gender' => $this->post('gender'),
            'email' => $this->post('email'), 
            'password' => $this->post('password'),
            'age' => $this->post('age'),
			'about_me' => $this->post('about_me'),
			'status' => 'inactive'           
            );
                
	    if($pic !='' && $pic != 0){
               $data['image']=base_url().PROFILEPIC_PATH.$pic;
           }
	   
        if($this->validateuser($data)){
            $data['password'] = md5($this->post('password'));
            //-- check if user already exist --//
               $user = $this->User_model->checkuser($this->post('email'));
               if($user>0){
                   $this->response(array('code'=>0,'error' => "Email is already exist."), 404);
               }
            //---------------------------------//
            $id = $this->User_model->adduser($data);
            if($id){
                
                //-- insert in user password --//
                    $datapass = array('user_id'=>$id,'u_password'=>$this->post('password'));
                    $this->User_model->addpassword($datapass);
                //----------------------------//
				//--add device detail--//
                	$devicedetail->user_id=$id;
					$customer_device_id= $this->User_model->userDeviceID($devicedetail);
				   
				//--add device detail ends--//
				//--add other device detail--//
                	$session_data['customer_id']=$id;
					$session_data['customer_device_id']=$customer_device_id;
					$session_data['status']=1;
					$session = $this->appsession($session_data);
					$device_other_detail->session_id= $session;
					$this->User_model->addotherdeviceinfo($device_other_detail);
				//--add device detail ends--//
				//-- generate activation token --//
                    $tokendata = array('user_id'=>$id,'token'=>$token,'action'=>'activation');
					//print_r($tokendata);
                    $tid=$this->User_model->activationToken($tokendata);
					//echo $tid; die;
				//---------------------------------//
					
                //-- send confirmation mail --//               
                $subject = '[MultiTV]Confirm your email address';
                $message = '<p>You recently register in our service</p>';
                $message .= '<p>Please confirm your email by clicking link below.</p>';
                $message .= '<p><a href="'.site_url('confirmation').'?t='.$token.'">Confirm your email address</a></p>';
                $message .= "<br><br> Kind Regards,<br><br>";
                $message .= "I Am Punjabi Team";
                $to = $this->post('email');
                $from = 'info@cyberlinks.in';
                $this->sendmail($subject,$message,$from,$to);
                //---------------------------//
                $this->response(array('code'=>1,'result' => "You are successfully registered. Please check you confirmation mail in your email id: ".$this->post('email')), 200);
            }
        }
        //$this->response($message, 200); // 200 being the HTTP response code
    }
    function detail_get()
    {
        //--- validate token ---//        
            //$this->validateToken();
        //------------------------//
        $id = $this->get('id');
                if(!$id)
                    {
                    
                     $this->response(array('code'=>0,'error' => "Record Not Found."), 404);
                    }
                else{
                   $userProfile = $this->User_model->userprofile($id);
                   //$socialKeywords = json_encode(unserialize($userProfile->keywords));
                    //$userProfile->keywords = $socialKeywords;
                   
                   if($userProfile){
                    $this->response(array('code'=>1,'result'=>$userProfile), 200); // 200 being the HTTP response code
                   }else{
                        $this->response(array('code'=>0,'error' => "Record Not Found."), 404);
                   }
                    
                   }

                
            
    }   
    
    function edit_post()
    {
        //--- validate token ---//        
            //$this->validateToken();
        //------------------------//
        $id = $this->get('id');
         $ext = $this->post('ext');
        //---- Upload logo image for user --//
        if($ext !=''){
           $my_base64_string = $this->post('pic');
            $pic = $this->base64_to_jpeg( $my_base64_string, PROFILEPIC_PATH,$ext );
            if($pic == 0){
                $this->response(array('code'=>0,'error' => "Error uploading image."), 404);
            }
        }else{
        //---- Upload logo image for player --//
                $incoming_tmp = $_FILES['pic']['tmp_name'];
                $incoming_original = $_FILES["pic"]["name"];

                if ($incoming_original != '') {
                    $path = PROFILEPIC_PATH;
                    $allowed = array('jpg', 'jpeg', 'png', 'gif');
                    $output = $this->uploadfile($incoming_tmp, $incoming_original, $path, $allowed);  //-- upload file --//

                    if (@$output['error']) {
                        $this->response(array('code'=>0,'error' => $output['error']), 404);
                    } else {
                        $pic = $output['path'];
                    }
                } else {
                    //$_POST['file'] = $_POST['logo'];
                }
        }
	//$keywordData = array('keywords'   => $this->post('keywords'));
	$keywordData =  $this->post('keywords');
        $data = array( 
            'first_name' => $this->post('first_name'), 
            'last_name' => $this->post('last_name'),
            'gender' => $this->post('gender'),
			'dob' => $this->post('dob'),
			'age' => $this->post('age'),
			'about_me' => $this->post('about_me'),
			'contact_no' => $this->post('contact_no'),
            'location'=>$this->post('location'),
	    'keywords' => trim($keywordData)
            );
		
            foreach($data as $k=>$val){
				if(($val=='')||($val==NULL)||($val=='0'))
				{
					unset($data[$k]);
				}
			}
           if($pic !='' && $pic != 0){
               $data['image']=base_url().PROFILEPIC_PATH.$pic;
           }
			
            $result = $this->User_model->update_user($data,$id);
         //   $result_social = $this->User_model->update_usersocial($keywordData,$id);
            if($result){
				$catdata=json_decode($this->post('category_preference'));
				$catarr=array(
					'category'=>$catdata
				);
			$output = $this->User_model->getuser($id);
			$this->User_model->updateCat($catarr,$id);
		/*if($output->image !=""){
                 //   $output->image = base_url().PROFILEPIC_PATH.$output->image;
     		}*/
		
                $this->response(array('code'=>1,'result'=>$output), 200); // 200 being the HTTP response code
            }
    }
    
    function delete_get()
    {
        //--- validate token ---//        
            $this->validateToken();
        //------------------------//
        $id = $this->get('id');
        $this->User_model->delete_user($id);
        $this->response(array('code'=>1,'success' => "User deleted successfully."), 200);
    }

    function confirm_post()
    {
        $token = $this->post('token');
        $id = $this->User_model->confirmRegistration($token);
        if($id>0){
            $this->response(array('success' => "Your email is confirm successfully."), 200);
        }  else {
            $this->response(array('error' => "Email confirmation failed. Your token is invalid/expired."), 404);
        }
    }
    
    function login_post()
    {
        $email = $this->post('email');
        $password = md5($this->post('password'));
        
        $data = array(
            'email' => $this->post('email'),
            'password' => $this->post('password'),
            );
		
         if($this->validatelogin($data)){
               $id = $this->User_model->loginuser($this->post('email'),md5($this->post('password')));
			   //echopre($id);
			   if($id>0){
                //-- api token --//
                    $this->generateApiToken($id,$this->post('email'),$this->post('password'));
                    $result = $this->User_model->getuser($id);

                if($result->image !=""){
                    $result->image = base_url().PROFILEPIC_PATH.$result->image;
				}
				$res = $this->User_model->setonlinestatus($id,'1');
				
					$this->response(array('code'=>1,'result'=>$result), 200); // 200 being the HTTP response code
               }else{
                   $this->response(array('code'=>0,'error' => "Login failed"), 404);
               }
         }
    }
    
    function logout_get(){
		//$token = $this->get('token');
		//$token = $this->get('uniqueID');
		$id = $this->get('id');
		//$result = $this->User_model->logout_social($token,$id);
		
		$result=$this->User_model->setonlinestatus($id,'0');
		
		
		
		if($result>0){
			$this->response(array('code'=>1,'result'=>'Logout successfully'), 200); // 200 being the HTTP response code
		}else{
			$this->response(array('code'=>0,'result'=>'Logout failed'), 404); // 200 being the HTTP response code
		}
	}
    
    function forgot_post()
    {
        //--- validate token ---//        
            //$this->validateToken();
        //------------------------//
        $email = $this->post('email');
        
        if($this->post('email')==''){
           $this->response(array('code'=>0,'error' => "Email cannot be blank"), 404);
        }
        //-- verify email --//
            $id = $this->User_model->checkuser($this->post('email'));
        //------------------//       
            
         if($id>0){
           // $result = $this->User_model->getforgotuser($id);
            
             $token = base64_encode($email);
              //-- send confirmation mail --//               
                $subject = '[I Am Punjabi]Forgot password';
                $message = '<p>You recently request for forgot password</p>';
                $message .= '<p>Please confirm your request by clicking link below.</p>';
                $message .= '<p><a href="'.site_url('forgot').'?t='.$token.'">Click here</a></p>';
                $message .= "<br><br> Kind Regards,<br><br>";
                $message .= "I Am Punjabi Team";
                $to = $this->post('email');
                $from = 'info@cyberlinks.in';
                $this->sendmail($subject,$message,$from,$to);
           $this->response(array('code'=>1,'result' => "You forgot password request is send. Please check your email"), 200);
        }else{
            $this->response(array('code'=>0,'error' => "Invalid email id"), 404);
        }
    }
        
    function sendpassword_post()
    {
        $token = $this->post('token');
        
        if($token == ''){
           $this->response(array('msg' => "Request can't be processed. Invalid request!"), 404);
        }
        $result = $this->User_model->getPassword(base64_decode($token));
        
           if($result){
               $password =$result->password;
               $email = $result->email;
              //-- send confirmation mail --//               
                $subject = '[I Am Punjabi]Forgot password';
                $message = '<p>Your request for password is sucessfull.</p>';
                $message .= '<p>Your password : '.$password.'</p>';               
                $message .= "<br><br> Kind Regards,<br><br>";
                $message .= "I Am Punjabi Team";
                $to = $email;
                $from = 'info@cyberlinks.in';
                $this->sendmail($subject,$message,$from,$to);
            $this->response(array('msg' => "Your password is send in your email."), 200);
        }else{
            $this->response(array('msg' => "Request can't be processed. Invalid request!"), 404);
        }
    }
    
    function reset_post()
    {
        //--- validate token ---//        
            $this->validateToken();
        //------------------------//
        $email = $this->post('email');
        $old_password = $this->post('old_password');
        $new_password = $this->post('new_password');
        $conf_password = $this->post('conf_password');
        
        if($old_password==''){
            $error = "Old password cannot be blank";             
        }
        if($new_password==''){
            $error = "New password cannot be blank";             
        }
        if($conf_password==''){
            $error = "Confirm password cannot be blank";             
        }
        if($new_password != $conf_password){
            $error = "New password and confirm password don't match";             
        }
   
        if(!empty($error)){
            $this->response(array('code'=>0,'error' => $error), 404);
        }
        
        $id = $this->User_model->validpassword($email,md5($old_password));
            if($id>0){
                //-- update password --//                
                $this->User_model->resetpassword($id,$new_password);               
                //---------------------//
                
                //-- send confirmation mail --//
                $subject = '[I Am Punjabi]Reset password';
                $message = '<p>Your password reset sucessfully.</p>';
                $message .= '<p>Please longin with your new password</p>';               
                $message .= "<br><br> Kind Regards,<br><br>";
                $message .= "I Am Punjabi Team";
                $to = $email;
                $from = 'info@cyberlinks.in';
                $this->sendmail($subject,$message,$from,$to);
                $this->response(array('code'=>1,'result' => "Your password is reset."), 200);               
            }else{
                $this->response(array('code'=>0,'error' => "Reset password failed."), 404);
            }
    }
    
    function social_post()
    {	
        $uid=0;
        $provider = $this->post('provider');
	$access_key = $this->post('access_key');
        //print_r($this->post());die;
	   //echopre($this->post('device_other_detail'));
        $device_other_detail = json_decode($this->post('device_other_detail'));
       
        $devicedetail = json_decode($this->post('devicedetail'));
        $uniqueId = $devicedetail->device_unique_id;                
	//$uniqueId = $this->post('uniqueID');
        $userdetails = json_decode($this->post('social'));  
       // print_r(get_object_vars($device_other_detail));die;
        //-- check if Admin token is valid --//
       // echopre($device_other_detail);
	$owner_id =  $this->User_model->checkAdminToken($this->admin_token);
	// $owner_id =  $this->User_model->checkAdminToken('54d46a72bab49');
           
	if($owner_id <= 0)
        {
                //$this->response(array('code'=>0,'error' => "Invalid Token"), 404);
                $response_arr = array('code'=>0,'error' => "Invalid Token");               
                $this->response($response_arr, 404);
	}
        if(!is_object($device_other_detail) && !is_object($devicedetail))
        {
            $this->response(array('code'=>0,'error' => "Incomplete Information"), 404);
        }
       //-----------------------------------//
       
       //print_r($userdetails);die;
        if(strtolower($provider)=='facebook'){
            $imageUrl = $this->social_data_image($access_key);
            $firstname = $userdetails->first_name;
            $lastname = $userdetails->last_name;
            $email = $userdetails->email;
            $gender = $userdetails->gender;
            $socialid = $userdetails->id;
			$aboutme = $userdetails->about_me;
			$age= $userdetails->age;
            $password = md5($socialid);
			$image = $imageUrl;
            $token = sha1($socialid.rand());
			$dob = @$userdetails->dob;
	    $id='';
	    //-- get user keywords --//
	    $social_keywords = $this->social_data($id,$socialid,$access_key);
        }
       
        if(strtolower($provider)=='google')
        {
	    $social_keywords='';
            $firstname = $userdetails->first_name;
            $lastname = $userdetails->last_name;
            $email = $userdetails->email;
            $gender = $userdetails->gender;
            $socialid = $userdetails->id;
			$aboutme = $userdetails->about_me;
			$age= $userdetails->age;
            $password = md5($socialid);
            $token = sha1($socialid.rand());
			$image = $userdetails->image;
			$dob = $userdetails->dob;
            //echo '<pre>'; print_r($userdetails);die;
        }
		if(strtolower($provider)=='twitter')
        {
	    $social_keywords='';
            $firstname = $userdetails->first_name;
            $lastname = $userdetails->last_name;
            $email = $userdetails->email;
            $gender = $userdetails->gender;
            $socialid = $userdetails->id;
			$aboutme = $userdetails->about_me;
			$age= $userdetails->age;
            $password = md5($socialid);
            $token = sha1($socialid.rand());
			$image = $userdetails->image;
			$dob = $userdetails->dob;
            //echo '<pre>'; print_r($userdetails);die;
        }
	if($email !='')
           { 
            $userdata = $this->User_model->loginsocial($email, $provider,$uniqueId);
	    
	    $uid = $userdata->id;
	    $deviceid = $userdata->device_unique_id;
            $customer_device_id = $userdata->customer_device_id;
	    //$deviceIdArr = unserialize($userdata->device_unique_id);
	    //print_r(unserialize($deviceIdArr));
	   }	   
	   
            if($uid>0)
           {			
		if($deviceid != $uniqueId)
                {                    
		  // $uniqueData = array('device_unique_id'=>$uniqueId,'user_id'=>$uid);
                 // insert new device information of login user    
                 $devicedetail->user_id=$uid;
		 $customer_device_id= $this->User_model->userDeviceID($devicedetail);
		}  
                /*else
                {
                   
                    $devicedata = $this->User_model->checkdevice($uniqueId);
                    
                    if(is_object($devicedata))
                    { 
                        $customer_device_id= $devicedata->id;
                        $session_use=2;
                    }
                }*/		
               $this->generateApiToken($uid,$email,$socialid);
               $result = $this->User_model->getuser($uid);	       
               //$this->response(array('code'=>1,'result'=>$result), 200);                
               $id=$uid;
               $response_arr = array('code'=>1,'result'=>$result);
               $response_code = 200;
            }
            else
             {	    
                //-----------Register user-----------------//
                $userdata = array(
                'owner_id' => $owner_id,
                'first_name' => $firstname, 
                'last_name' => $lastname,
                'gender' => $gender,
                'email' => $email, 
                'password' => $password,
                'image' => $image,
				'about_me' => $aboutme,
                'age' => $age,
				'dob' => $dob,
                //'device_unique_id' => $uniqueId,
                'device_unique_id' => serialize($deviceIdArr),
                'keywords'=>$social_keywords,
                //'token' => $token,
                'status' => 'active'
                //'created'=>date('Y-m-d h:i:s')
                    );
           
                $id = $this->User_model->adduser($userdata);
            
                if($id)
                 {		
		//--- insert device unique id ---//
		// $uniqueData = array('device_unique_id'=>$uniqueId,'user_id'=>$id);
                 $devicedetail->user_id=$id;
		 $customer_device_id= $this->User_model->userDeviceID($devicedetail);
               
                $socialdata = array('social_id' => $socialid, 
                'from' => $provider,            
                'user_id' => $id,  
                'info' => serialize($userdetails),
                'status' => 1,
                // 'keywords'=>$social_keywords,
                'created'=>date('Y-m-d h:i:s'));
                 $this->User_model->addsocial($socialdata);
                }
				
                if($id)
                {
                     //-- api token --//
                    $this->generateApiToken($id,$email,$socialid);
                    $result = $this->User_model->getuser($id);

                  // $this->response(array('code'=>1,'result'=>$result), 200);
                }
               }
        //echo $customer_device_id;die;
        /* Insert into session table */
		if($id){
			$this->User_model->setonlinestatus($id,'1');
		}
        $session_data['customer_id']=$id;
        $session_data['customer_device_id']=$customer_device_id;
        $session_data['status']=1;
        $session = $this->appsession($session_data);
        
        $device_other_detail->session_id= $session;
        
        $this->User_model->addotherdeviceinfo($device_other_detail);
         
        $result->session=$session;
        $response_arr = array('code'=>1,'result'=>$result);
        $response_code = 200;
        $this->response($response_arr, $response_code);
    }
    	
	function social_data($id,$userFacebookId,$access_key)
    {
      //  $userFacebookId = "799074976840826";
         $facebookUrl = "https://graph.facebook.com/".$userFacebookId."/likes?limit=10000&access_token=";
        // $facebookUrl = "https://graph.facebook.com/709713455756798/likes?access_token=CAACEdEose0cBAKicFwxfQWp6JGnIrRP6BCkBn8xxKgqsWtpwTEKNZCQUZCOt8vtfcTb3uEkmeZCl4Ib52RrN5vRyHPEYGYIbMwLDVZCoVaXRbBZAEUFdtmCZBgsVaiVOOMtwfZAdy1s2fWiBSe2HqkhKOn3sbg0kXXhkGNoRjTCUJgAdedJ0wgS4IoIcIVcfrGyGjtEbOUjopOc2Bl21U1e";
        // $access_key   =  $this->get('access_key');
        // $access_key   =  "CAANjUhhnAOABABSo1ZAbRqPxySiJHyxDXZAhXGcFMCkxQvkspM6ACZADFSiUcA2CzQZAvJXuINUrc6GpzU2948ynZBnjPVZCMFj2hMPXGCbKj8jmgl8ZCqhMqKfdkd3VbaV0ZAZBXL2OwrglIJ48jnIRLMavZArmgiCTIFckC6gK12S6vWdRvjR2jMPfiI4B4hZClSmv7HP48vJWFZCtzvgNQZBpCJFKUjjZAZCB5KTH2PSYyIWXzs7ZB6wnvig9";
         $facebookJsonData = $facebookUrl.$access_key;
          $curl = curl_init();
                // Set some options - we are passing in a useragent too here
                curl_setopt_array($curl, array(
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_URL => $facebookJsonData
                ));  
                // Send the request & save response to $resp
               $resp = curl_exec($curl);
              // echo $resp;
              // die();
                // Close request to clear up some resources
                curl_close($curl);
         //echo $facebookJsonData;
                //echo "<pre>";
        $facebookDataArr = json_decode($resp);
        $outputArray = array(); 
        if(is_array($facebookDataArr))
        {
        foreach($facebookDataArr as $k=>$v )
            {
                foreach($v as $key=>$val)
                    {
                        if(isset($val->name)){
                            array_push($outputArray, $val->name);   
                        }               
                    }
            }
        }    
	    $outputArray = implode(",",$outputArray);	   
            return $insertArray = $outputArray;          
    }
    function social_data_image($access_key)
    {
        //$userFacebookId = "709713455756798";
        $imageUrlFetch ='';
         $facebookUrl = "https://graph.facebook.com/me/picture?&redirect=false&width=480&height=480&access_token=";
         $facebookJsonData = $facebookUrl.$access_key;
          $curl = curl_init();
                // Set some options - we are passing in a useragent too here
                curl_setopt_array($curl, array(
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_URL => $facebookJsonData
                ));  
                // Send the request & save response to $resp
               $resp = curl_exec($curl);
              // echo $resp;
              // die();
                // Close request to clear up some resources
                curl_close($curl);
         //echo $facebookJsonData;
                //echo "<pre>";
        $facebookDataArr = json_decode($resp);
        //print_r($facebookDataArr);
        //die();
        if(is_array($facebookDataArr))
        {
        foreach($facebookDataArr as $k=>$v)
            {
            $imageUrlFetch = $v->url;
            }
        }  // echo $imageUrlFetch;
          //  die();
        //echo $facebookDataArr->url;
        ///die();
         return $imageUrl = $imageUrlFetch; 
        
            
    }    
    
    function social_old_post()
    {
        $token = sha1($this->post('firstname').$this->post('social_id').rand());
        
        //-- check if social account is already exist --//
            $uid = $this->User_model->loginuser($this->post('email'), md5($this->post('social_id')));
            
            if($uid>0){
               $result = $this->User_model->getuser($uid);
               $this->response($result, 200); 
            }else{
             
            //-----------Register user-----------------//
            $userdata = array(
            'first_name' => $this->post('firstname'), 
            'last_name' => $this->post('lastname'),
            'gender' => $this->post('gender'),
            'email' => $this->post('email'), 
            'password' => md5($this->post('social_id')),
            'token' => $token,
            'status' => 1,
            'created'=>date('Y-m-d h:i:s'));
             $id = $this->User_model->adduser($userdata);
            
             if($id){
               $socialdata = array('social_id' => $this->post('social_id'), 
               'from' => $this->post('social_site'),            
               'user_id' => $id,            
               'status' => 1,
               'created'=>date('Y-m-d h:i:s'));
                $this->User_model->addsocial($socialdata);
            }
            if($id){
                $result = $this->User_model->getuser($id);
               $this->response($result, 200); 
            }
        }
    }
    public function validateuser($data)
    {
        $error=array();
        if($data['first_name']==''){
            $error ="First name cannot be blank";             
        }
        if($data['email']==''){
            $error ="Email cannot be blank";             
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $error = "This email address is invalid.";
        }
        
        if($data['password']==''){
            $error ="Password cannot be blank";             
        }
        if(!empty($error)){
            $this->response(array('code'=>0,'error' => $error), 404);
        }
        return 1;
    }
    
    public function validatelogin($data)
    {
        $error=array();       
        if($data['email']==''){
            $error ="Email cannot be blank";             
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $error = "This email address is invalid.";
        }        
        if($data['password']==''){
            $error ="Password cannot be blank";             
        }
        if(!empty($error)){
           $this->response(array('code'=>0,'error' => $error), 404);
        }
        return 1;
    }
    
    function sendmail($subject='no reply',$body='Test',$from,$to)
    {
        $this->load->library('email');
       
        $result = $this->email
                ->from($from)
                ->reply_to($from) // Optional, an account where a human being reads.
                ->to($to)
                ->subject($subject)
                ->message($body)
                ->send();
        return $result;          
    }        
    
    function generateApiToken($id,$email,$password)
    {
        //-- generate token --//
            $token = sha1($email.$password.rand()); 
        //-- delete api token --//
            $this->User_model->delete_api($id);
        //-- add api token ---//    
            $data = array('token'=>$token,'user_id'=>$id);
            $this->User_model->add_apikey($data);
            //$this->User_model->update_user($token,$id);
        //-------------------//
    }
    
    function add_switch_user_post()
    {
        $data   =  array(
            "username" =>$this->post('username'),
            "password"=>  md5($this->post('password')),
            "adserver_user_id"=>$this->post('id'),
            'first_name'=>$this->post('contact_name'),
            'email'=>$this->post('email_address'),
			'token'=>uniqid(),
            'status'=>'active',
            'role_id'=>24
        );
        
        $id = $this->User_model->addSwitchUser($data);
        if($id>0){
            $this->response(array('success' => "successfully added."), 200);
        }  else {
            $this->response(array('error' => "error."), 404);
        }

    }    
  
  function skin_get()
  {
	$token = $this->get('token');
	$this->User_model->checkAdminToken($token); //-- check if token valid--//
	$result = $this->User_model->getskin($token);
	$result->image = base_url().$result->image;
	$result->path = base_url().$result->path;
	//echo '<pre>';print_r($result);
	/*$xml=simplexml_load_file("http://www.multitvsolution.com/multitvfinal/assets/upload/skins/one/one.xml") or die("Error: Cannot create object");
     echo '<pre>';print_r($xml);
	 $config_data->skinType = (string) $xml->skinType;
	 $config_data->skinOption = (array)$xml->skinOption;
	 $config_data->cssResource = (array)$xml->cssResource;
	// $config_data->jsResource = $xml->jsResource;	 
	 //$config_data->cssResource = $xml->cssResource;
	 
        foreach ($xml->jsResource as $listing)
        {
			$config_data->jsResource['jsLink'][] = (string) $listing->jsLink;
			
		}*/
	//echo '<pre>';print_r($config_data);
		//echo json_encode((object)$config_data);
		//$result->config_data = json_encode((array)$config_data);
		//print_r($result);
	if($result){
		$this->response(array('code'=>1,'result' => $result), 200); 
	}else{
		$this->response(array('code'=>0,'result' => 'No record found'), 404); 
	}
  }
  
  function save_skin_post()
  {
	$token = $this->get('token');
	$id = $this->User_model->checkAdminToken($token); //-- check if token valid--//
	$result = $this->User_model->saveskin($id,$token);
  }
  //save session
  function appsession($data)
  {
      $session_id = $this->User_model->addsession($data); 
      return $session_id;
  }
   
  // without Login 
  function withoutlogin_post()
    {	
    // print_r($this->post());
         $user_id = 0;
        $devicedetail = json_decode($this->post('devicedetail'));
        $device_other_detail = json_decode($this->post('device_other_detail'));
       //echo json_encode($devicedetail);
       // die;
        $deviceid = $uniqueId = $devicedetail->device_unique_id;
       
        $owner_id =  $this->User_model->checkAdminToken($this->admin_token);   //application user id
	  // $owner_id =  $this->User_model->checkAdminToken('54d46a72bab49');
        if($owner_id <= 0){
            //$this->response(array('code'=>0,'error' => "Invalid Token"), 404);
            $response_arr = array('code'=>0,'error' => "Invalid Token");
            $response_code = 404;
        }
        else
        {
	       $devicedata = $this->User_model->checkdevice($uniqueId,$user_id);
               if(is_object($devicedata))
               {
                   $customer_device_id= $devicedata->id;                  
               }
                else {
                    $devicedetail->user_id = $user_id;
                    // $uniqueData['device_unique_id'] = $uniqueId;
                    // $uniqueData['user_id'] = $user_id;                          
		    $customer_device_id= $this->User_model->userDeviceID($devicedetail);
                    
                }
            
            $session_data['customer_id']=$user_id;
            $session_data['customer_device_id']=$customer_device_id;
            $session_data['status']=1;
           // $session_data['session_use']=$session_use;
            
            /* Insert into session table */
            $session= $this->appsession($session_data); 
            
            $device_other_detail->session_id= $session;        
            //insert into other information
            $this->User_model->addotherdeviceinfo($device_other_detail);
            $result['session']=$session;
            $response_arr = array('code'=>1,'result'=>$result);
            $response_code = 200;
        }                  
               
        $this->response($response_arr, $response_code);
    }
	
	function heart_beat_post(){
		//-- check if Admin token is valid --//
	    $owner_id =  $this->User_model->checkAdminToken($this->admin_token);	  
	    if($owner_id <= 0){
		//$this->response(array('code'=>0,'error' => "Invalid Token"), 404);
            $response_arr = array('code'=>0,'error' => "Invalid Token");               
            $this->response($response_arr, 404);
	    }
       //-----------------------------------//	   
		$sess_id = $this->post('sess_id');
		$is_active = $this->post('status');
		$endtime = $this->post('end_time');		
		$this->User_model->checksession($sess_id,$is_active,$endtime);
		$this->response(array('code'=>1), 200); 
	}
    
	function socket_post(){		   
		$sess_id = $this->post('sess_id');
		$is_active = $this->post('status');
		$endtime = $this->post('end_time');		
		$this->User_model->checksession($sess_id,$is_active,$endtime);
		$this->response(array('code'=>1), 200); 
	}
	function changepassword_post(){
		$data = $this->post();
		$val = $this->User_model->changepass($data);
		echo $val;
		return $val;
	}
	function list_get(){
		$this->param =  $this->paging($this->get('p'));
		$owner_id =  $this->User_model->checkAdminToken($this->admin_token);
		$data = $this->User_model->listdata($owner_id,$this->param);
		if($data){
			$this->response(array('code'=>1,'result'=>$data), 200);
		}else{
			$this->response(array('code'=>0,'error'=>"No record found"), 200);
		}
	}
	function online_get(){
		$owner_id =  $this->User_model->checkAdminToken($this->admin_token);
		$data = $this->User_model->onlineuser($owner_id);
		if($data){
			$this->response(array('code'=>1,'result'=>$data), 200);
		}else{
			$this->response(array('code'=>0,'error'=>"No record found"), 200);
		}		
	}
	function search_post(){
		$this->param =  $this->paging($this->get('p'));
		$data = $this->post();
		$owner_id =  $this->User_model->checkAdminToken($this->admin_token);
		$data = $this->User_model->searchdata($owner_id,$this->param,$data);
		if($data){
			$this->response(array('code'=>1,'result'=>$data), 200);
		}else{
			$this->response(array('code'=>0,'error'=>"No record found"), 200);
		}		
	}
	function follow_post(){
		$data = $this->post();
		$status=$data['status'];
		unset($data['status']);
		$data = $this->User_model->follow($data,$status);
		if($data >0){
			$this->response(array('code'=>1), 200);
		}else{
			$this->response(array('code'=>0), 200);
		}
	}
	function followlist_get(){
		$data = $this->get();
		$res = $this->User_model->follow_list($data);
		if($res){
			$this->response(array('code'=>1,'result'=>$res), 200);
		}else{
			$this->response(array('code'=>0,'error'=>"No record found"), 200);
		}		
	}
    function resumesession_post()
    {	
     //rint_r($this->get());
        $user_id = 0;
        $oldsession = $this->post('session');
       
       
        $owner_id =  $this->User_model->checkAdminToken($this->admin_token);   //application user id
	  // $owner_id =  $this->User_model->checkAdminToken('54d46a72bab49');
        if($owner_id <= 0){
            //$this->response(array('code'=>0,'error' => "Invalid Token"), 404);
            $response_arr = array('code'=>0,'error' => "Invalid Token");
            $response_code = 404;
        }
        else
        {	                                
	    $sessiondetail = $this->User_model->sessioninformation($oldsession);
            $otherdetail   = $this->User_model->getotherdeviceinfo($oldsession);
            
            if(is_object($sessiondetail) && is_object($otherdetail))
            {
                $session_data['customer_id']=$sessiondetail->customer_id;
                $session_data['customer_device_id']=$sessiondetail->customer_device_id;
                $session_data['status']=1;
                // $session_data['session_use']=$session_use;            
                /* Insert into session table */
                 $session= $this->appsession($session_data); 

                 @$device_other_detail->os_version= $otherdetail->os_version;
                 $device_other_detail->app_version= $otherdetail->app_version;
                 $device_other_detail->network_type= $otherdetail->network_type;
                 $device_other_detail->network_provider= $otherdetail->network_provider;
                 $device_other_detail->session_id= $session;        
                //insert into other information
                $this->User_model->addotherdeviceinfo($device_other_detail);

                $result['session']=$session;
                $response_arr = array('code'=>1,'result'=>$result);
                $response_code = 200;
            }  else {
                $response_arr = array('code'=>0,'error' => "invalid session");
            $response_code = 404;
            }  
        }
        $this->response($response_arr, $response_code);
    }
}
