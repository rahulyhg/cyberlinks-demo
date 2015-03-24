<!DOCTYPE html>
<?php
$s = $this->session->all_userdata();
    $width = '180';
    $height = "45";
?>

<?php 
$menu = array();
switch ($s[0]->username){
    case('superadmin') :
        $menu = array(
                    array('name'=>'Dashboard','url'=>base_url().'layout/dashboard',  'class'=>'fa-dashboard', 'li-class'=>($this->uri->segment(1) == 'layout') ? 'active' : ''),
                    array('name'=>'User', 'url'=>'#', 'class'=>'fa-users', 'li-class'=>(($this->uri->segment(1) == 'user') || ($this->uri->segment(1) == 'role')) ? 'treeview active' : 'treeview', 'type'=>'parent','childs'=>array(
                        array('name'=>'Users', 'li-class'=>($this->uri->segment(1) === 'user') ? 'active' : '', 'url'=>base_url().'user', 'type'=>'child'),
                        //array('name'=>'User Role', 'li-class'=>($this->uri->segment(1) === 'role') ? 'active' : '', 'url'=>base_url().'role', 'type'=>'child'),
                )));
        break;
    
    default :
        $menu = array(
                    
                    array('name'=>'Analytics','url'=>base_url().'ads_analytics/report', 'class'=>'fa-dashboard','li-class'=>($this->uri->segment(1) == 'ads_analytics') ? 'active' : ''),
                    array('name'=>'Ads', 'url'=>base_url().'ads/index', 'class'=>'fa-video-camera',
                        'li-class'=>(
                            ($this->uri->segment(1) == 'ads' || $this->uri->segment(1) == 'category') ||
                            ($this->uri->segment(2) == 'videoUploadSrc') || 
                            ($this->uri->segment(2) == 'bulkupload') || 
                            ($this->uri->segment(2) == 'video_status') ||                         
                            ($this->uri->segment(1) . "/" . $this->uri->segment(2) == 'ads/index') ) ? 'treeview active' : '',
                        'type'=>'parent','childs'=>array(
                        array('name'=>'Category', 'li-class'=> ($this->uri->segment(1) === 'category') ? 'active' : '', 'url'=>base_url().'category', 'type'=>'child'),
                        array('name'=>'Ad List', 'li-class'=> (($this->uri->segment(1) == 'ads') && ($this->uri->segment(2) == 'index')) ? 'active' : '', 'url'=>base_url().'ads/index', 'type'=>'child'),
                        array('name'=>'Ad Upload', 'li-class'=>($this->uri->segment(2) == 'videoUploadSrc' || $this->uri->segment(2) == 'upload_other') ? 'active' : '', 'url'=>base_url().'ads/videoUploadSrc/Upload', 'type'=>'child'),
                        array('name'=>'Ad Bulk upload', 'li-class'=>($this->uri->segment(2) == 'bulkupload' || $this->uri->segment(2) == 'ftp') ? 'active' : '' ,'url'=>base_url().'ads/bulkupload/csv', 'type'=>'child'),
                        array('name'=>'Ad Status', 'li-class'=>($this->uri->segment(2) == 'video_status') ? 'active' : '', 'url'=>base_url().'ads/video_status', 'type'=>'child'),
                        )),                   
                    array('name'=>'Campaign Manager','url'=>base_url().'layout/adserver_login', 'class'=>'fa-film', 'li-class'=>($this->uri->segment(1) == '') ? 'active' : ''),                   
            );
        break;        
}
  //echo "<pre>";
   //print_r($menu); die;

?>

<html>
    <head>
        <meta charset="UTF-8">
        <title>MultiTV</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <!-- bootstrap 3.0.2 -->
        <link href="<?php echo base_url() ?>assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <!-- font Awesome -->
        <link href="<?php echo base_url() ?>assets/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Ionicons -->
        <link href="<?php echo base_url() ?>assets/css/ionicons.min.css" rel="stylesheet" type="text/css" />
        <!-- Morris chart -->
        <link href="<?php echo base_url() ?>assets/css/AdminLTE.css" rel="stylesheet" type="text/css" />
        <!-- fullCalendar -->
        <link href="<?php echo base_url() ?>assets/css/fullcalendar/fullcalendar.css" rel="stylesheet" type="text/css" />
        <!-- bootstrap3-wysihtml5.min  -->
        <link href="<?php echo base_url() ?>assets/css/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css" rel="stylesheet" type="text/css" />
        <!-- daterangepicker  -->
        <link href="<?php echo base_url() ?>assets/css/daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css" /> 
        <!-- jcrop -->
        <link href="<?php echo base_url() ?>assets/css/jcrop/jquery.Jcrop.css" rel="stylesheet" type="text/css" />
        <!-- jquery.tagit.css -->
        <link href="<?php echo base_url() ?>assets/css/jquery.tagit.css" rel="stylesheet" type="text/css">
        <!-- tagit.ui-zendesk.css-->
        <link href="<?php echo base_url() ?>assets/css/tagit.ui-zendesk.css" rel="stylesheet" type="text/css">
        <link href="<?php echo base_url(); ?>assets/css/timepicker/bootstrap-timepicker.min.css" rel="stylesheet"/>
        <link href="<?php echo base_url(); ?>assets/css/ion.rangeSlider.css" rel="stylesheet"/>
        <link href="<?php echo base_url(); ?>assets/css/ion.rangeSlider.skinFlat.css" rel="stylesheet"/>
        <!-- jquery-1.10.2 -->
        <script>
        var baseurl = '<?php echo base_url(); ?>' ;
        var loader = '<img class="loading" src="'+ baseurl +'assets/img/spinner.gif" />';
        var loaderCenter = '<div align="center"><img class="loading" src="'+ baseurl +'assets/img/spinner.gif" /></div>';
        </script>
        <script src="<?php echo base_url() ?>assets/js/jquery-1.10.2.js"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jwplayer.js" ></script>
        <script type="text/javascript">jwplayer.key = "BC9ahgShNRQbE4HRU9gujKmpZItJYh5j/+ltVg==";</script>
    </head>
    <body class="skin-blue">
        <!-- header logo: style can be found in header.less -->
        <header class="header">
            <a href="<?php echo base_url(); ?>" class="logo">
                <!-- Add the class icon to your logo image or logo icon to add the margining -->
                <img alt="User Pic" src="<?php echo $welcome->getimage() ?>" width="<?php echo $width ?>" height="<?php echo $height ?>">
            </a>
            <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-static-top" role="navigation">
                <!-- Sidebar toggle button-->
                <div style="float:left;"><section class="sidebar">
                    <ul class="sidebar-menu">
            <?php foreach ($menu as $val){?>
            <li class="<?= $val['li-class']; ?>">
                <a href="<?=$val['url']?>">
                    <i class="fa <?=$val['class']?>"></i>
                    <span><?php echo $welcome->loadPo($val['name']); ?></span>
                    <?php if(isset($val['childs'])){ ?><i class="fa fa-angle-left pull-right"></i> <?php } ?>
                </a>
                <?php if(isset($val['childs'])){ ?>
                <ul class="treeview-menu">
                   <?php foreach ($val['childs'] as $child){ ?>
                       <li class="<?=$child['li-class']?>">
                           <a href="<?=$child['url']?>">
                               <i class="fa fa-angle-double-right"></i>
                                   <?php echo $welcome->loadPo($child['name']) ?></a>
                       </li>
                    
                    <?php } ?> 
                        </ul>
                        <?php } ?>
            </li> <?php } ?>
        </ul>
        </section></div>
                <div class="navbar-right">
                
                    <ul class="nav navbar-nav">
                        <!-- Messages: style can be found in dropdown.less-->
                        <?php
                        $sess = $this->session->all_userdata();
                        if (isset($sess['lan'])) {
                            $lang = $sess['lan'];
                        } else {
                            $lang = 'eng';
                        }
                        ?>
                        <!-- User Account: style can be found in dropdown.less -->
                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="glyphicon glyphicon-user"></i>
                                <span><?php echo $s[0]->first_name; ?><i class="caret"></i></span>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- User image -->
                  <?php /*  <li class="user-header bg-light-blue">
                                    <div><img alt="User Pic" src="<?php echo $welcome->getimage() ?>" width="<?php echo $width ?>" height="<?php echo $height ?>"></div>
                                    <p>
                                        <?php echo $s[0]->email; ?>
                                        <small>Member since.  <?php echo $s[0]->created; ?></small>
                                        <small>Role.  <?php echo $s[0]->role; ?></small>
                                    </p>
                                </li> 
                   */ ?>
                                <!-- Menu Footer-->
                                <li class="user-footer">
                                    <div class="pull-left">
                                        <a href="<?php echo base_url() ?>layout/profile" class="btn btn-default btn-flat">Profile</a>
                                    </div>
                                    <div class="pull-right">
                                        <a href="<?php echo base_url() ?>layout/logout" class="btn btn-default btn-flat">Sign out</a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                     
                </div>
            </nav>
        </header>