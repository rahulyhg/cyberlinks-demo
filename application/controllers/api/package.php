<?php defined('BASEPATH') OR exit('No direct script access allowed');
//ini_set('display_errors',1);
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

class Package extends REST_Controller
{
   function __construct()
   {
       parent::__construct();	
      $this->load->model('api/package_model');
       $this->load->model('api/subscription_model');
      $this->load->model('api/Video_model');
      $this->load->helper('url');  
        
       //-- validate token --//
       $token = $this->get('token');
     // $action = $this->get('action');
      $this->owner_id = $this->validateToken($token);
      
       //--paging limit --//
          $this->param =  $this->paging($this->get('p'));
   }
   
   function list_get()
   {
        $get = $this->get();    
        $package = $this->package_model->getpackagelist($get);
        
        $result['package'] = $this->package_array($package);
       
       // echo '<pre>';print_r($result);
        if($result)
        {         
            $this->response(array('code'=>1,'result'=>$result), 200); // 200 being the HTTP response code
        }else{
            $this->response(array('code'=>0,'error' => 'No record found'), 404);
        }
   }
}

?>