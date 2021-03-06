<?php
ini_set('display_errors', 1);
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Genre extends MY_Controller {
    
    public $user_id = null;
    public $user = null;
    public $role_id = null;

    function __construct() {
        parent::__construct();
        $this->load->config('messages');
        $this->load->model('Genre_model');
        $this->load->library('form_validation');
        $this->load->library('Session');
        $per = $this->check_per();
        if(!$per){
          redirect(base_url() . 'layout/permission_error');
        }
        $this->data['welcome'] = $this;
        $s = $this->session->all_userdata();
        $this->user_id = $s[0]->id;
        $this->user = $s[0]->username;
        $this->role_id = $s[0]->role_id;
    }
    
     protected $validation_rules = array
        (
        'add_genre' => array(
            array(
                'field' => 'genre_name',
                'label' => 'Genre name',
                'rules' => 'trim|required'
            )
        ),
        'edit_genre' => array(
            array(
                'field' => 'genre_name',
                'label' => 'Genre name',
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
            case "genre_name":
                $sort = 'genre_name';
                if ($sort_by == 'asc')
                    $this->data['show_c'] = 'desc';
                else
                    $this->data['show_c'] = 'asc';
                break;
            
            case "status":
                $sort = 'status';
                if ($sort_by == 'asc')
                    $this->data['show_s'] = 'desc';
                else
                    $this->data['show_s'] = 'asc';
                break;
            default:
                $sort = 'genre_name';
        }
        if (isset($_POST['submit']) && $_POST['submit'] == 'Search') {
            $this->session->set_userdata('search_form', $_POST);
        } elseif (isset($_POST['reset']) && $_POST['reset'] == 'Reset') {
            $this->session->unset_userdata('search_form');
        }
        $searchterm = $this->session->userdata('search_form');
        $this->load->library("pagination");
        $config = array();
        $config["base_url"] = base_url() . "genre/index";
        $config["total_rows"] = $this->Genre_model->getRecordCount($searchterm );
        $config["per_page"] = 10;
        $config["uri_segment"] = 3;
        $this->pagination->initialize($config);
        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $this->data["links"] = $this->pagination->create_links();
        $this->data['total_rows'] = $config["total_rows"];
        $this->data['category'] = $this->Genre_model->getGenre($config["per_page"], $page, $sort, $sort_by, $searchterm );
      //  $this->data['allParentCategory'] = $this->Genre_model->getparent($this->user_id);
        $this->show_view('genre', $this->data);
    }
    
    function addgenre() {
        if (isset($_GET['action'])) {
            $id = $_GET['action'];
            $cid = base64_decode($id);
        }
        if (isset($cid)) {
            if ((isset($_POST['update']) && $_POST['update'] == 'Update')) {
                unset($_POST['update']);
                $_POST['id'] = $cid;
                $this->form_validation->set_rules($this->validation_rules['edit_genre']);
                if ($this->form_validation->run()) {   
                    $this->Genre_model->saveGenre($_POST);
                    $this->session->set_flashdata('message', $this->_successmsg($this->loadPo($this->config->item('success_record_update'))));
                    redirect(base_url() . 'genre');
                } else {
                    $data['welcome'] = $this;
                    $data['genreName'] = $this->Genre_model->genreName($cid);
                    $this->show_view('editgenre', $data);
                }           
            } else {
                $data['welcome'] = $this;
                $data['genreName'] = $this->Genre_model->genreName($cid);
                $this->show_view('editgenre', $data);
            }
        } else {
            if ((isset($_POST['submit']) && $_POST['submit'] == 'Submit')) {           
                unset($_POST['submit']);
                $genre = $_POST['genre_name'];
                $genreExistsCnt = $this->Genre_model->checkIfGenreExists($genre);
                    if($genreExistsCnt > 0)
                    {
                        $msg = $this->loadPo($this->config->item('warning_record_exists'));
                        $this->session->set_flashdata('message', $this->_warningmsg($msg));
                        redirect(base_url() . 'genre');
                    } else {        
                        $this->form_validation->set_rules($this->validation_rules['add_genre']);
                        if ($this->form_validation->run()) {   
                            $this->Genre_model->saveGenre($_POST);
                            $this->session->set_flashdata('message', $this->_successmsg($this->loadPo($this->config->item('success_record_add'))));
                            redirect(base_url() . 'genre');
                        } else {
                            $data['welcome'] = $this;
                            $this->show_view('addgenre', $data);
                        }
                    }
                
            } else {
                $data['welcome'] = $this;
                $this->show_view('addgenre', $data);
            } 
        }

    }
    
    function deleteGenre(){
        $id = $_GET['id'];
        $this->Genre_model->delete_genre($id);
        $this->session->set_flashdata('message', $this->_successmsg($this->loadPo($this->config->item('success_record_delete'))));
        redirect(base_url() . 'genre');
    }

}

/* End of file welcome.php */
    /* Location: ./application/controllers/welcome.php */    