<?php

class Webtv extends MY_Controller {

    public $uid = null;

    function __construct() {
        parent::__construct();
        $this->load->config('messages');
        $this->load->model('webtv_model');
        $this->load->helper('pdf_helper');
        $this->load->helper('csv_helper');
        $per = $this->check_per();
        if(!$per){
          redirect(base_url() . 'layout/permission_error');
        }
        $s = $this->session->all_userdata();
        $this->uid = $s[0]->id;
    }

    function index() {
        if (isset($_POST['submit']) && $_POST['submit'] == 'Search') {
            $this->session->set_userdata('search_form', $_POST);
        } else if (isset($_POST['reset']) && $_POST['reset'] == 'Reset') {
            $this->session->unset_userdata('search_form');
        }
        $searchterm = $this->session->userdata('search_form');
        $data['welcome'] = $this;
        $this->load->library("pagination");
        $config["base_url"] = base_url() . "webtv/index/";
        $config["total_rows"] = $this->webtv_model->countAll_channels($this->uid, $searchterm);
        $config["per_page"] = 10;
        $config["uri_segment"] = 3;
        $this->pagination->initialize($config);
        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $data['res'] = $this->webtv_model->fetchchannels($this->uid, $config["per_page"], $page, $searchterm);
        $data["links"] = $this->pagination->create_links();
        $data['category'] = $this->webtv_model->get_category($this->uid);
        $this->show_view('channels', $data);
    }

    function Epg() {
        $chanel_id = $this->uri->segment(3);
        $this->db->select('name');
        $this->db->where('id', $chanel_id);
        $result = $this->db->get('channels')->result();
        //print_r($result); die;
        $channel_name = $result[0]->name;
        $file = $_FILES['csv']['tmp_name'];
        //print_r($_FILES); die;
        $fp = fopen($file, 'r') or die(json_encode(array('result' => 'error')));
        $num = 0;
        while ($csv_line = fgetcsv($fp, 1024)) {
            //print_r($csv_line);
            for ($i = 0, $j = count($csv_line[$i]); $i < $j; $i++) {
                if ($num == 0) {
                    if (!isset($csv_line[1]) || !isset($csv_line[5])) {
                        echo "invalid csv";
                        die;
                    }
                    $title = $csv_line;
                } else {
                    $this->db->delete('livechannel_epg', ['channel_id' => $chanel_id]);
                    $temp = array(
                        'channel_id' => $chanel_id,
                        'channel_name' => $channel_name,
                        'date' => ($csv_line[5]=='')? date('Y-m-d') : date('Y-m-d', strtotime($csv_line[4])),
                        'show_title' => $csv_line[5],
                        'show_time' => $csv_line[1], //($csv_line[1] == '') ? '' : date("H:i", strtotime($csv_line[1])),
                        'show_duration' => $csv_line[3],
                        'show_thumb' => "",
                        'show_language' => "",
                        'show_description' => "",
                        //'show_type' => $csv_line[3],
                        'media_type' => $csv_line[6],
                        'valid' => ($csv_line[1] == '' || $csv_line[5] == '') ? 'invalid' : 'valid'
                    );
                    $array[] = $temp;
                    //$this->db->insert('livechannel_epg', $temp);
                    //echo $this->db->last_query();
                }
                $num++;
            }
            //print_r($temp);
        }
        fclose($fp);
        
        $view_data['result'] = $array;
        $this->load->view('epg_view', $view_data);
    }

    function playlist() {
        $channel_id = $this->uri->segment(3);
        if (isset($_POST['submit']) && $_POST['submit'] == 'Search') {
            $this->session->set_userdata('search_form', $_POST);
        } else if (isset($_POST['reset']) && $_POST['reset'] == 'Reset') {
            $this->session->unset_userdata('search_form');
        }
        $searchterm = $this->session->userdata('search_form');
        $data['welcome'] = $this;
        $this->load->library("pagination");
        $config["base_url"] = base_url() . "webtv/playlist/";
        $config["total_rows"] = $this->webtv_model->countAll($this->uid, $searchterm, $channel_id);
        $config["per_page"] = 100000;
        $config["uri_segment"] = 4;
        $this->pagination->initialize($config);
        $page = 0;
        $data['res'] = $this->webtv_model->fetchplaylists($this->uid, $config["per_page"], $page, $searchterm, $channel_id);
        $data["links"] = $this->pagination->create_links();
        $this->show_view('webtv', $data);
    }

    function resetplaylist() {
        $id = $this->uri->segment('3');
        switch ($id) {
            case 'all' :
                $query = sprintf("update playlists set status = '0', publish = '0', url = '' where channel_id in (select id from channels where type = 'Linear' and uid = %d)", $this->uid);
                $dataset = $this->db->query($query);
                //echo $this->db->last_query(); die;
                redirect(base_url() . 'webtv');
                break;
            default :
                $this->db->where('channel_id', $id);
                $this->db->set('publish', '0');
                $this->db->set('status', '0');
                $this->db->set('url', '');
                $this->db->update('playlists');
                redirect(base_url() . 'webtv/playlist/' . $id);
                break;
        }
        exit;
    }

    function publishplaylist() {
        $id = $this->uri->segment('3');
        switch ($id) {
            case 'all' :
                $query = sprintf("update playlists set publish = '1' where channel_id in (select id from channels where type = 'Linear' and uid = %d)", $this->uid);
                $dataset = $this->db->query($query);
                //echo $this->db->last_query(); die;
                redirect(base_url() . 'webtv');
                break;
            default :
                $this->db->where('channel_id', $id);
                $this->db->set('publish', '1');
                $this->db->update('playlists');
                redirect(base_url() . 'webtv/playlist/' . $id);
                break;
        }
        exit;
    }

    function unpublishplaylist() {
        $id = $this->uri->segment('3');
        switch ($id) {
            case 'all' :
                $query = sprintf("update playlists set publish = '0' where channel_id in (select id from channels where type = 'Linear' and uid = %d)", $this->uid);
                $dataset = $this->db->query($query);
                //echo $this->db->last_query(); die;
                redirect(base_url() . 'webtv');
                break;
            default :
                $this->db->where('channel_id', $id);
                $this->db->set('publish', '0');
                $this->db->update('playlists');
                redirect(base_url() . 'webtv/playlist/' . $id);
                break;
        }
        exit;
    }

    function publish() {
        $id = $this->uri->segment('3');
        $publish = ($this->uri->segment('4') == 0) ? '1' : '0';
        $this->db->where('id', $id);
        $this->db->set('publish', $publish);
        $this->db->update('playlists');
        echo $publish;
    }

    function add() {
        $data['welcome'] = $this;
        if (isset($_POST['submit']) && $_POST['submit'] = 'Submit') {
            $post['name'] = $_POST['name'];
            $date = explode('-', $_POST['start_date']);
            $post['start_date'] = str_replace('/', '-', $date['0']);
            $post['end_date'] = str_replace('/', '-', $date['1']);
            $post['uid'] = $this->uid;
            $post['channel_id'] = $this->uri->segment(3);
            $post['description'] = $_POST['description'];
            $this->webtv_model->insert($post);
            $this->session->set_flashdata('message', $this->_successmsg($this->loadPo($this->config->item('success_record_add'))));
            redirect($_POST['url']);
        }
        $this->show_view('add_play', $data);
    }

    function add_channels() {
        $data['welcome'] = $this;
        $data['catogory'] = $this->webtv_model->get_category($this->uid);
        //print_r($data['catogory']);
        if (isset($_POST['submit']) && $_POST['submit'] = 'Submit') {
            $post['name'] = $_POST['name'];
            $post['number'] = $_POST['number'];
            $post['type'] = $_POST['type'];
            $post['uid'] = $this->uid;
            $post['category_id'] = $_POST['category_id'];
            $post['status'] = $_POST['status'];
            $post['keywords'] = $_POST['tags'];
            $this->webtv_model->insert_channels($post);
            $this->session->set_flashdata('message', $this->_successmsg($this->loadPo($this->config->item('success_record_add'))));
            redirect(base_url() . 'webtv');
        }
        $this->show_view('add_channels', $data);
    }

    function ch_number() {
        $this->db->select('number');
        $this->db->where('category_id', $_GET['cat_id']);
        $numbers = $this->db->get('channels')->result();
        $notValid = array();
        foreach ($numbers as $key => $val) {
            $notValid[] = $val->number;
        }
        $this->db->select('range_from, range_to');
        $this->db->where('id', $_GET['cat_id']);
        $result = $this->db->get('channel_categories')->result();
        $temp = array();
        for ($i = $result[0]->range_from; $i <= $result[0]->range_to; $i++) {
            if (!in_array($i, $notValid)) {
                $temp[]['range'] = $i;
            }
        }
        echo json_encode($temp);
    }

    function delete() {
        $id = $_GET['id'];
        //echo $id; die;
        $this->webtv_model->delete($id);
        $this->session->set_flashdata('message', $this->_successmsg($this->loadPo($this->config->item('success_record_delete'))));
        redirect($_GET['curl']);
    }

    function delete_channels() {
        $id = $_GET['id'];
        // echo $id; die;
        $this->webtv_model->delete_channels($id);
        $this->session->set_flashdata('message', $this->_successmsg($this->loadPo($this->config->item('success_record_delete'))));
        redirect(base_url() . 'webtv');
    }

    function edit() {
        $data['welcome'] = $this;
        $id = base64_decode($_GET['action']);
        if (isset($_POST['submit']) && $_POST['submit'] = 'Submit') {
            $post['id'] = $id;
            $post['name'] = $_POST['name'];
            $date = explode('-', $_POST['start_date']);
            $post['start_date'] = str_replace('/', '-', $date['0']);
            $post['end_date'] = str_replace('/', '-', $date['1']);
            $post['uid'] = $this->uid;
            $post['description'] = $_POST['description'];
            $post['status'] = $_POST['status'];
            $this->webtv_model->insert($post);
            $this->session->set_flashdata('message', $this->_successmsg($this->loadPo($this->config->item('success_record_update'))));
            redirect(base_url() . 'webtv/playlist/' . $this->uri->segment(3));
        }
        $data['value'] = $this->webtv_model->fetchEventbyId($id);
        $this->show_view('add_play', $data);
    }

    function edit_channels() {
        $data['welcome'] = $this;
        $data['catogory'] = $this->webtv_model->get_category($this->uid);
        $id = base64_decode($_GET['action']);
        if (isset($_POST['submit']) && $_POST['submit'] = 'Submit') {
            $post['id'] = $id;
            $post['name'] = $_POST['name'];
            $post['number'] = $_POST['number'];
            $post['category_id'] = $_POST['category_id'];
            $post['uid'] = $this->uid;
            $post['status'] = $_POST['status'];
            $post['keywords'] = $_POST['tags'];
            $this->webtv_model->insert_channels($post);
            $this->session->set_flashdata('message', $this->_successmsg($this->loadPo($this->config->item('success_record_update'))));
            redirect(base_url() . 'webtv');
        }
        $data['value'] = $this->webtv_model->fetchChannelsbyId($id);
        $this->show_view('add_channels', $data);
    }

    function video_detail() {
        $data['welcome'] = $this;
        $id = $this->uri->segment(3);
        $data['package'] = $this->webtv_model->getPack($id);
        $data['playlist_id'] = $id;
        $data['result'] = $this->webtv_model->get_video($id);
        $this->show_view('playlist_video', $data);
    }

    function changeStatus() {
        $data['id'] = $this->uri->segment(3);
        $data['status'] = $this->uri->segment(4);
        $data['p_id'] = $this->uri->segment(5);
        $this->webtv_model->changeStatus($data);
        redirect($_GET['url']);
    }

    function videoEpg() {
        $data['welcome'] = $this;
        $id = $this->uri->segment(3);
        $list = $this->webtv_model->getemgVideo($id);
        $ids = array('0' => 0);
        foreach ($list as $val) {
            $ids[] = $val->id;
        }
        $data['playlist_id'] = $id;
        $data['package'] = $this->webtv_model->getPack($id);
        $tmp = $this->webtv_model->get_videoid($id, $ids);
        foreach ($tmp as $key => $val) {
            if ($val->type == 'youtube') {
                $data['result']['youtube'][] = $val;
            } else {
                $data['result']['vod'][] = $val;
            }
        }
        $this->show_view('epg', $data);
    }

    function addVideo() {
        $chid = $this->uri->segment(4);
        $this->db->select('type');
        $this->db->where('id', $chid);
        $result = $this->db->get('channels')->result();
        $type = $result[0]->type;
        //echo $type;
        $searchterm = '';
        if (isset($_POST['reset'])) {
            $this->session->unset_userdata('search_form');
        }
        $sort = $this->uri->segment(3);
        $sort_by = $this->uri->segment(4);
        $data['welcome'] = $this;
        if (isset($_POST['submit']) && $_POST['submit'] == 'Search') {
            $this->session->set_userdata('search_form', $_POST);
        } else if (isset($_POST['reset']) && $_POST['reset'] == 'Reset') {
            $this->session->unset_userdata('search_form');
        }
        $pid = $this->uri->segment(3);
        $chid = $this->uri->segment(4);
        $list = $this->webtv_model->get_video($pid);
        $ids = array('0' => 0);
        foreach ($list as $val) {
            $ids[] = $val->id;
        }
        $searchterm = $this->session->userdata('search_form');
        $this->load->library("pagination");
        $config = array();
        $config["base_url"] = base_url() . "webtv/addVideo/" . $pid . '/' . $chid;
        $config["total_rows"] = $this->webtv_model->get_videocount($this->uid, $ids, $searchterm, $type);
        //echo $config["total_rows"];

        $config["per_page"] = 10;
        $config["uri_segment"] = 5;
        $this->pagination->initialize($config);
        $page = ($this->uri->segment(5)) ? $this->uri->segment(5) : 0;
        $data['result'] = $this->webtv_model->get_allvideo($ids, $this->uid, PER_PAGE, $page, $searchterm, $type);
        $data["links"] = $this->pagination->create_links();
        $data['category'] = $this->webtv_model->get_Vidcategory($this->uid);

        //print_r($data['category']);

        $data['total_rows'] = $config["total_rows"];
        $this->show_view('video_playlist', $data);
    }

    function VideoPack() {
        $pid = $this->uri->segment(3);
        $cid = $this->uri->segment(4);
        $this->webtv_model->add_video($pid, $cid);
        echo json_encode(array('success' => TRUE, 'message' => "Article added"));
    }

    function unlink() {
        $id = $this->uri->segment(3);
        $this->webtv_model->delete_vid($id);
        echo json_encode(array('success' => TRUE, 'message' => "Article deleted"));
    }

    function renderevent() {
        $data = array();
        $query = sprintf('select * from playlist_epg pe
                         left join (select * from playlist_video pv where pv.playlist_id = %d ) as pv on pv.content_id = pe.content_id
                         where pe.playlist_id = %d and pe.user_id = %d and pe.start_date between "%s" and "%s" order by pe.id ', $_GET['playlist_id'], $_GET['playlist_id'], $this->uid, date('Y-m-d 00:00:00', $_GET['start']), date('Y-m-d 23:59:59', $_GET['end']));
        $dataset = $this->db->query($query)->result();
        foreach ($dataset as $key => $val) {
            $data[] = array('id' => $val->content_id,
                'title' => $val->playlist_id,
                'title' => strlen($val->title) > 15 ? substr($val->title, 0, 12) . '...' : $val->title,
                'start' => date('Y-m-d H:i:s', strtotime($val->start_date)),
                'end' => date('Y-m-d H:i:s', strtotime($val->end_date)),
                'backgroundColor' => $val->color,
                'borderColor' => '',
                'allDay' => false,
                'axisFormat' => 'HH:mm');
        }
        echo json_encode($data);
        exit;
    }

    function saveevent() {
        $response = array();
        if (isset($_POST['id'])) {
            $data = array();
            $data['content_id'] = $_POST['id'];
            $data['playlist_id'] = $_POST['playlist_id'];
            $data['title'] = $_POST['title'];
            $data['user_id'] = $this->uid;
            $data['start_date'] = isset($_POST['start_date']) && $_POST['start_date'] != '' ? date('Y-m-d H:i:s', strtotime($_POST['start_date'])) : date('Y-m-d H:i:s');
            $data['end_date'] = isset($_POST['end_date']) && $_POST['end_date'] != '' ? date('Y-m-d H:i:s', strtotime($_POST['end_date'])) : date('Y-m-d H:i:s', strtotime($data['start_date']) + 60 * 60);
            $tmp = isset($_POST['action']) ? $_POST['action'] : 'save';
            switch ($tmp) {
                case 'delete' :
                    $query = sprintf('delete from playlist_epg where content_id = %d and playlist_id = %d and user_id = %d', $data['content_id'], $data['playlist_id'], $this->uid);
                    $dataset = $this->db->query($query)->result();
                    $response['success'] = 'Data deleted';
                    break;
                default :
                    $query = sprintf('select * from playlist_epg pe where pe.content_id = %d and pe.playlist_id = %d and user_id = %d', $data['content_id'], $data['playlist_id'], $this->uid);
                    $dataset = $this->db->query($query)->result();
                    if (count($dataset) > 0) {
                        $this->db->where('id', $dataset[0]->id);
                        $this->db->where('playlist_id', $dataset[0]->playlist_id);
                        $this->db->update('playlist_epg', $data);
                    } else {
                        $data['created'] = date('Y-m-d h:i:s');
                        $this->db->insert('playlist_epg', $data);
                    }
                    $response['success'] = 'Data saved';
                    break;
            }
        } else {
            $response['error'] = 'Invalid Data';
        }
        echo json_encode($response);
        exit;
    }

    function updateindex() {
        if (isset($_POST['playlist_id']) && $_POST['playlist_id'] != '') {
            foreach ($_POST as $key => $val) {
                if ($key == 'playlist_is')
                    continue;
                $data = array();
                $data['index'] = $val;
                $this->db->where('pv.playlist_id', $_POST['playlist_id']);
                $this->db->where('pv.content_id', $key);
                $this->db->update('playlist_video pv', $data);
            }
        }
        exit;
    }

    function export() {
        $type = $this->uri->segment(3);
        $cid = $this->uri->segment(4);
        $this->data['result'] = $this->webtv_model->epgList($cid, $this->uid);
        switch ($type) {
            case 'pdf':
                $content = $this->load->view('templates/epg_pdf', $this->data, true);
                create_pdf($content, 'EPG Report');
                break;
            case 'csv':
                $heading = array('Sl.No.', 'Playlist Id', 'Title', 'Content Id', 'Start Time', 'End Time', 'Status');
                //$content =  $this->load->view('templates/pdf_content',$this->data,true);				
                $dataRpt = array();
                $num = 0;
                $i = 0;
                foreach ($this->data['result'] as $p) {
                    $i++;
                    $dataRpt[$num]['title'] = $i;
                    $dataRpt[$num]['content_provider'] = $p->playlist_id;
                    $dataRpt[$num]['platform'] = $p->title;
                    $dataRpt[$num]['browser'] = $p->content_id;
                    $dataRpt[$num]['location'] = $this->dateFormat($p->start_date);
                    $dataRpt[$num]['date'] = $this->dateFormat($p->end_date);
                    $dataRpt[$num]['hits'] = $p->status;
                    $num++;
                }
                query_to_csv($dataRpt, $heading, '');
                break;
            case 'xml':
                echo 'xml';
                break;
        }
    }

    function eventCopy() {
        if ($_POST) {
            //print_r($_POST);
            $data = $this->webtv_model->eventcopy($_POST);
            //echo $data; die;
            $this->session->set_flashdata('message', $this->_successmsg($this->loadPo($this->config->item('success_record_update'))));
            echo $data;
            exit;
        } else {
            $this->load->view('copyEvent');
        }
    }

}
