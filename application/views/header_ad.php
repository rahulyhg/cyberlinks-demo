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
        
        <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
        <link href="<?php echo base_url() ?>assets/css/skins/_all-skins.min.css" rel="stylesheet" type="text/css" />
        
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
        <link href="<?php echo base_url(); ?>assets/css/uploadfile.min.css" rel="stylesheet">
        
        <link href="<?php echo base_url(); ?>assets/css/bootstrap-datetimepicker.min.css">
        
        <!-- jquery-1.10.2 -->
        <script>
        var baseurl = '<?php echo base_url(); ?>' ;
        var loader = '<img class="loading" src="'+ baseurl +'assets/img/spinner.gif" />';
        var loaderCenter = '<div align="center"><img class="loading" src="'+ baseurl +'assets/img/spinner.gif" /></div>';
        </script>
        <!--script src="<?php echo base_url() ?>assets/js/jquery-1.10.2.js"></script-->
        <script src="<?php echo base_url() ?>assets/js/jQuery-2.1.3.min.js"></script>
        <script src="<?php echo base_url() ?>assets/js/bootstrap.min.js" type="text/javascript"></script>
                <script src="<?php echo base_url() ?>assets/js/bootstrap-dialog.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/bootbox.min.js" type="text/javascript"></script>
        <script src="<?php echo base_url() ?>assets/js/moment.js"></script>
        <script src="<?php echo base_url() ?>assets/js/bootstrap-datetimepicker.min.js"></script>
        <script src="<?php echo base_url() ?>assets/js/jquery.uploadfile.min.js"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jwplayer.js" ></script>
        <script type="text/javascript">jwplayer.key = "BC9ahgShNRQbE4HRU9gujKmpZItJYh5j/+ltVg==";</script>
    </head>
    <body class="skin-blue layout-top-nav">
        <div class="wrapper row-offcanvas row-offcanvas-left">
        <!-- header logo: style can be found in header.less -->
        <header class="main-header">               
        <nav class="navbar navbar-static-top">
          <div class="container">
            <div class="navbar-header">
                <a class="logo" href="<?php echo base_url(); ?>">
                    <!-- Add the class icon to your logo image or logo icon to add the margining -->
                    <img alt="User Pic" src="<?php echo $welcome->getimage() ?>" width="<?php echo $width ?>" height="<?php echo $height ?>">
                </a>
              <button data-target="#navbar-collapse" data-toggle="collapse" class="navbar-toggle collapsed" type="button">
                <i class="fa fa-bars"></i>
              </button>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div id="navbar-collapse" class="collapse navbar-collapse pull-left">
              <ul class="nav navbar-nav">
                  <?php foreach ($menu as $val){  ?>
                <?php if(!isset($val['childs'])){ ?>
                <li class="<?= $val['li-class']; ?>">
                    <a href="<?=$val['url']?>">
<!--                        <span class="sr-only">(current)</span>-->
                        <i class="fa <?=$val['class']?>"></i>
                        <span><?php echo $welcome->loadPo($val['name']); ?></span>
                    </a>
                </li>
                <?php }else { ?>
                
                <li class="dropdown">
                  <a data-toggle="dropdown" class="dropdown-toggle" href="javascript:void(0)">
                      <i class="fa <?=$val['class']?>"></i>
                      <span><?php echo $welcome->loadPo($val['name']); ?></span> <span class="caret"></span>
                  </a>
                  <ul role="menu" class="dropdown-menu">
                      <?php foreach ($val['childs'] as $child){ ?>
                        <li class="<?=$child['li-class']?>">
                           <a href="<?=$child['url']?>">
                               <i class="fa fa-angle-double-right"></i>
                                   <?php echo $welcome->loadPo($child['name']) ?>
                           </a>
                       </li>
                      <?php } ?>
                  </ul>
                  
                </li>
                  <?php } }?>
              </ul>
            </div><!-- /.navbar-collapse -->
            
            <!-- Navbar Right Menu -->
              <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <?php
                        $sess = $this->session->all_userdata();
                        if (isset($sess['lan'])) {
                            $lang = $sess['lan'];
                        } else {
                            $lang = 'eng';
                        }
                    ?>
                  
                  <!-- User Account Menu -->
                  <li class="dropdown user user-menu">
                    <!-- Menu Toggle Button -->
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#" aria-expanded="false">
                      <!-- hidden-xs hides the username on small devices so only the image appears. -->
                      <i class="glyphicon glyphicon-user"></i>
                      <span class="hidden-xs"><?php echo $s[0]->first_name; ?><i class="caret"></i></span>
                    </a>
                    <ul class="dropdown-menu">
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
              </div><!-- /.navbar-custom-menu -->
          </div><!-- /.container-fluid -->
        </nav>
      </header>