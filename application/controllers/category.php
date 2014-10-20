<?php
ini_set('display_errors', 1);
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Category extends MY_Controller {

    public $user = null;
    public $role_id = null;
    public $uid = null;

    function __construct() {
        parent::__construct();
        $this->load->config('messages');
        $this->load->model('Category_model');
        $this->load->library('form_validation');
        $this->load->library('Session');
        $this->data['welcome'] = $this;
        $s = $this->session->all_userdata();
        $this->user = $s[0]->username;
        $this->role_id = $s[0]->role_id;
        $this->uid = $s[0]->id;
    }

    protected $validation_rules = array
        (
        'add_category' => array(
            array(
                'field' => 'category',
                'label' => 'Category name',
                'rules' => 'trim|required'
            ),
            array(
                'field' => 'description',
                'label' => 'Description',
                'rules' => 'trim|required'
            )
        ),
        'update_Category' => array(
            array(
                'field' => 'category',
                'label' => 'Category name',
                'rules' => 'trim|required'
            ),
            array(
                'field' => 'description',
                'label' => 'Description',
                'rules' => 'trim|required'
            )
        )
    );

    function index() {
        $searchterm='';
        if($this->uri->segment(2) ==''){                
            $this->session->unset_userdata('search_form');
        }
        $sort = $this->uri->segment(3);
        $sort_by = $this->uri->segment(4);
        switch ($sort) {
            case "category":
                $sort = 'a.category';
                if ($sort_by == 'asc')
                    $this->data['show_c'] = 'desc';
                else
                    $this->data['show_c'] = 'asc';
                break;
            case "parent":
                $sort = 'parent';
                if ($sort_by == 'asc')
                    $this->data['show_p'] = 'desc';
                else
                    $this->data['show_p'] = 'asc';
                break;
            case "status":
                $sort = 'a.status';
                if ($sort_by == 'asc')
                    $this->data['show_s'] = 'desc';
                else
                    $this->data['show_s'] = 'asc';
                break;
            default:
                $sort = 'a.category';
        }
        if (isset($_POST['submit']) && $_POST['submit'] == 'Search') {
            $this->session->set_userdata('search_form', $_POST);
        } elseif (isset($_POST['reset']) && $_POST['reset'] == 'Reset') {
            $this->session->unset_userdata('search_form');
        }
        $searchterm = $this->session->userdata('search_form');
        $this->load->library("pagination");
        $config = array();
        $config["base_url"] = base_url() . "category/index";
        $config["total_rows"] = $this->Category_model->getRecordCount($this->uid,$searchterm );
        $config["per_page"] = 10;
        $config["uri_segment"] = 3;
        $this->pagination->initialize($config);
        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $this->data["links"] = $this->pagination->create_links();
        $this->data['total_rows'] = $config["total_rows"];
        $this->data['category'] = $this->Category_model->getCategory($this->uid, $config["per_page"], $page, $sort, $sort_by, $searchterm);
        $this->data['allParentCategory'] = $this->Category_model->getAllCategory();
        $this->show_view('category', $this->data);
    }

    /* 	Add and Edit Category	 */

    function addCategory() {
        $per = $this->checkpermission($this->role_id, 'add');
        if ($per) {
            if (isset($_GET['action'])) {
                $id = $_GET['action'];
                $cid = base64_decode($id);
            }
            if (isset($cid)) {
                if (isset($_POST['submit']) && $_POST['submit'] == "Update") {
                    $this->form_validation->set_rules($this->validation_rules['update_Category']);
                    if ($this->form_validation->run()) {
                        $_POST['id'] = $cid;
                        $_POST['status'] = $this->input->post('status') == 'on' ? 1 : 0;
                        $this->Category_model->_saveCategory($_POST);
                        $msg = $this->loadPo($this->config->item('success_record_update'));
                        $this->log($this->user, $msg);
                        $this->session->set_flashdata('message', $this->_successmsg($msg));
                        redirect('category');
                    } else {
                        $this->data['allParentCategory'] = $this->Category_model->getAllCategory();
                        $this->data['edit'] = $this->Category_model->getAllParentCategory($cid);
                        $this->show_view('edit_category', $this->data);
                    }
                } else {
                    $this->data['allParentCategory'] = $this->Category_model->getAllCategory();
                    $this->data['edit'] = $this->Category_model->getAllParentCategory($cid);
                    $this->show_view('edit_category', $this->data);
                }
            } else {
                if (isset($_POST['submit']) && $_POST['submit'] == 'Submit') {
                    $_POST['u_id'] = $this->uid;
                    $this->form_validation->set_rules($this->validation_rules['add_category']);
                    if ($this->form_validation->run()) {
                        $result = $this->Category_model->checkCategory($_POST['category'],$this->uid);
                        if ($result == 0) {
                            $this->Category_model->_saveCategory($_POST);
                            $msg = $this->loadPo($this->config->item('success_record_add'));
                            $this->log($this->user, $msg);
                            $this->session->set_flashdata('message', $this->_successmsg($msg));
                            redirect('category');
                        } else {
                            $msg = $this->loadPo($this->config->item('warning_record_exists'));
                            $this->session->set_flashdata('message', $this->_warningmsg($msg));
                            redirect('category/');
                        }
                    } else {
                        $this->data['allParentCategory'] = $this->Category_model->getAllCategory();
                        $this->show_view('add_category', $this->data);
                    }
                } else {
                    $this->data['allParentCategory'] = $this->Category_model->getAllCategory();
                    $this->show_view('add_category', $this->data);
                }
            }
        } else {
            $this->session->set_flashdata('message', $this->_errormsg($this->loadPo($this->config->item('error_permission'))));
            redirect(base_url() . 'category');
        }
    }

    /* 	Delete Category */

    function deleteCategory() {
        $per = $this->checkpermission($this->role_id, 'delete');
        //echo $this->role_id;
        if ($per) {
            $id = $_GET['id'];
            $video = $this->Category_model->getCategoryVideo($id);
            if ($video == '0') {
                $child = $this->Category_model->getCategoryChild($id);
                if($child == '0'){
                    $this->Category_model->delete_category($id);
                    $this->session->set_flashdata('message', $this->_successmsg($this->loadPo($this->config->item('success_record_delete'))));
                    redirect(base_url() . 'category');
                }else{
                    $this->session->set_flashdata('message', $this->_warningmsg($this->loadPo($this->config->item('warning_category_record'))));
                    redirect(base_url() . 'category');
                }
            } else {
                $this->session->set_flashdata('message', $this->_warningmsg($this->loadPo($this->config->item('warning_category_record'))));
                redirect(base_url() . 'category');
            }
        } else {
            $this->session->set_flashdata('message', $this->_errormsg($this->loadPo($this->config->item('error_permission'))));
            redirect(base_url() . 'category');
        }
    }

}

/* End of file welcome.php */
    /* Location: ./application/controllers/welcome.php */    