<?php
//ini_set('display_errors', 1);
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Apilist extends MY_Controller {

    public $user = null;
    public $role_id = null;
    public $uid = null;
    public $usertoken = null;

    function __construct() {
        parent::__construct();
        $this->load->config('messages');
        $this->load->library('form_validation');
        $this->load->model('Category_model');
        $this->load->library('Session');
        $per = $this->check_per();
        if(!$per){
          redirect(base_url() . 'layout/permission_error');
        }
        $this->data['welcome'] = $this;
        $s = $this->session->all_userdata();
        $this->user = $s[0]->username;
        $this->role_id = $s[0]->role_id;
        $this->uid = $s[0]->id;
        $this->usertoken = $this->Category_model->checkToken($this->uid); 
    }


    function index() {
        $this->data['welcome'] = $this;
        $tab = base64_decode(@$_GET['action']);
        if(isset($_GET['id'])){
            $catId = base64_decode(@$_GET['id']); 
        } else {
            $catId = '1';
        }
        $this->data['tab'] = $tab;
        switch ($tab) {
            case "users":
                $this->data['response'] = '';
                break;
            case "category":
                $url = base_url().'api/video/category/token/'.$this->usertoken;
                $this->data['url'] = $url;
                $this->data['response'] = $this->get_urlcontent($url);
                break;
            case "videoslist":
                $url = base_url().'api/video/all/token/'.$this->usertoken;
                $this->data['url'] = $url;
                $this->data['response'] = $this->get_urlcontent($url);
                break;
            case "categoryvideos":
                $url = base_url().'api/video/list/device/3g/p/1/token/'.$this->usertoken;
                $urlfull = $url.'/id/'.$catId;
                $this->data['url'] = $urlfull;
                $this->data['catIdN'] = $catId; 
                $this->data['categoryData'] = $this->Category_model->getAllCatList($this->uid);
                $this->data['response'] = $this->get_urlcontent($urlfull);
                break;
            case "videodetails":
                $url = base_url().'api/video/detail/id/1/device/3g/token/'.$this->usertoken;
                $this->data['url'] = $url;
                $this->data['response'] = $this->get_urlcontent($url);
                break;
            case "mostpopular":
                $url = base_url().'api/video/popular/l/1/device/3g/token/'.$this->usertoken;
                $urlfull = $url.'/id/'.$catId;
                $this->data['url'] = $urlfull;
                $this->data['catIdN'] = $catId; 
                $this->data['categoryData'] = $this->Category_model->getAllCatList($this->uid);
                $this->data['response'] = $this->get_urlcontent($urlfull);
                break;
            case "relatedvideos":
                $url = base_url().'api/video/related/type/2/id/1/device/3g/token/'.$this->usertoken;
                $urlfull = $url.'/category_id/'.$catId;
                $this->data['url'] = $urlfull;
                $this->data['catIdN'] = $catId; 
                $this->data['categoryData'] = $this->Category_model->getAllCatList($this->uid);
                $this->data['response'] = $this->get_urlcontent($urlfull);
                break;
            case "recentvideos":
                $url = base_url().'api/video/latest/l/1/device/3g/token/'.$this->usertoken;
                $urlfull = $url.'/id/'.$catId;
                $this->data['url'] = $urlfull;
                $this->data['catIdN'] = $catId; 
                $this->data['categoryData'] = $this->Category_model->getAllCatList($this->uid);
                $this->data['response'] = $this->get_urlcontent($urlfull);
                break;
            case "featuredvideos":
                $url = base_url().'api/video/featured/token/'.$this->usertoken;
                $this->data['url'] = $url;
                $this->data['response'] = $this->get_urlcontent($url);
                break;
            default:
                $url = base_url().'api/video/category/token/'.$this->usertoken;
                $this->data['url'] = $url;
                $this->data['response'] = $this->get_urlcontent($url);
        }
        $this->show_view('apilist', $this->data);
    }
    


}



/* End of file welcome.php */
    /* Location: ./application/controllers/welcome.php */    