<link href="<?=base_url()?>assets/css/morris/morris.css" rel="stylesheet" type="text/css" />
<aside class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> 
            <?php echo $welcome->loadPo('Dashboard'); ?>
            <small><?php echo $welcome->loadPo('Control panel'); ?></small>
        </h1>
        <ol class="breadcrumb">
            <i class="fa fa-dashboard"></i>
            <li class="active"><?php echo $welcome->loadPo('Dashboard') ?></li>
        </ol>
    </section>
	<div>
            <div id="msg_div">
                <?php echo $this->session->flashdata('message');?>
            </div>	
	</div>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-lg-2 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-green">
                    <div class="inner">
                        <h3>
                            <?php echo $videos[0]->total_video > 0 ? $videos[0]->total_video : 0  ?>
                        </h3>
                        <p>
                           <?php echo $welcome->loadPo('Total Videos'); ?> 
                        </p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-video-camera"></i>
                    </div>
                    <a href="<?php echo base_url() ?>video" class="small-box-footer">
                        <?php echo $welcome->loadPo('All Video List'); ?> <i class="fa fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div><!-- ./col -->
            <div class="col-lg-2 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-light-blue">
                    <div class="inner">
                        <h3>
                            <?php echo $videos[0]->youtube_video > 0 ? $videos[0]->youtube_video : 0 ?>
                        </h3>
                        <p>
                           <?php echo $welcome->loadPo('Youtube Videos'); ?> 
                        </p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-youtube-play"></i>
                    </div>
                    <a href="<?php echo base_url() ?>video?filter=youtube" class="small-box-footer">
                        <?php echo $welcome->loadPo('Video List'); ?> <i class="fa fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-2 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-fuchsia">
                    <div class="inner">
                        <h3>
                            <?php echo $videos[0]->total_jobs > 0 ? $videos[0]->total_jobs : 0 ?>
                        </h3>
                        <p>
                           <?php echo $welcome->loadPo('Transcoding Jobs'); ?> 
                        </p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-forward"></i>
                    </div>
                    <a href="<?php echo base_url() ?>video/video_status" class="small-box-footer">
                        <?php echo $welcome->loadPo('All Jobs'); ?> <i class="fa fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-2 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-red">
                    <div class="inner">
                        <h3>
                           <?php echo $videos[0]->completed_jobs > 0 ? $videos[0]->completed_jobs : 0 ?>
                        </h3>
                        <p>
                            <?php echo $welcome->loadPo('Completed Jobs'); ?>
                        </p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-thumbs-up"></i>
                    </div>
                    <a href="<?php echo base_url() ?>video/video_status?filter=completed" class="small-box-footer">
                        <?php echo $welcome->loadPo('All Jobs'); ?> <i class="fa fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div><!-- ./col -->
            <div class="col-lg-2 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h3>
                            <?php echo $videos[0]->inprocess_jobs > 0 ? $videos[0]->inprocess_jobs : 0 ?>
                        </h3>
                        <p>
                            <?php echo $welcome->loadPo('In-process Jobs'); ?>
                        </p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-clock-o"></i>
                    </div>
                    <a href="<?php echo base_url() ?>video/video_status?filter=inprocess" class="small-box-footer">
                        <?php echo $welcome->loadPo('All Jobs'); ?> <i class="fa fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div><!-- ./col -->
            <div class="col-lg-2 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-purple">
                    <div class="inner">
                        <h3>
                            <?php echo $videos[0]->pending_jobs > 0 ? $videos[0]->pending_jobs : 0 ?>
                        </h3>
                        <p>
                            <?php echo $welcome->loadPo('Pending Jobs'); ?>
                        </p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-thumbs-down"></i>
                    </div>
                    <a href="<?php echo base_url() ?>video/video_status?filter=pending" class="small-box-footer">
                        <?php echo $welcome->loadPo('All Jobs'); ?> <i class="fa fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div><!-- ./col -->
        </div><!-- /.row -->

        <div class="row">
	    <section class="col-lg-6"> 
		<!-- Box (with bar chart) -->
		<div class="box box-danger">
		    <div class="box-header">
			<!-- tools box -->
			<div class="pull-right box-tools">
			    <button class="btn btn-danger btn-sm" data-widget='collapse' data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
			</div><!-- /. tools -->
			<h3 class="box-title">Category Videos</h3>
		    </div><!-- /.box-header -->
		    <div class="box-body no-padding">
			<div class="row">
			    <div class="col-sm-10">
				<div class="chart" id="category-chart-div" style="height: 250px;"></div>
			    </div>
			</div><!-- /.row - inside box -->
		    </div><!-- /.box-body -->
		</div><!-- /.box -->
	    </section><!-- /.Left col -->
	    
	    <section class="col-lg-6"> 
		<!-- Box (with bar chart) -->
		<div class="box box-danger">
		    <div class="box-header">
			<!-- tools box -->
			<div class="pull-right box-tools">
			    <button class="btn btn-danger btn-sm" data-widget='collapse' data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
			</div><!-- /. tools -->
			<h3 class="box-title">User Videos</h3>
		    </div><!-- /.box-header -->
		    <div class="box-body no-padding">
			<div class="row">
			    <div class="col-sm-10">
				<div class="chart" id="uservideo-chart-div" style="height: 250px;"></div>
			    </div>
			</div><!-- /.row - inside box -->
		    </div><!-- /.box-body -->
		</div><!-- /.box -->
	    </section><!-- /.Left col -->
        </div><!-- /.row (main row) -->
	
	<div class="row">
	    <section class="col-lg-12"> 
		<!-- Box (with bar chart) -->
		<div class="box box-danger">
		    <div class="box-header">
			<!-- tools box -->
			<!--div class="pull-right box-tools"-->
                        <div style="padding: 5px 10px 5px 5px; float: right;">
			    <button class="btn btn-danger btn-sm" data-widget='collapse' data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
			</div><!-- /. tools -->
			<!--div class="pull-right box-tools"-->
                        <div style="padding: 5px 10px 5px 5px; float: right;">
			    <select class="form-control onchange" id="year">
				<?php
				    foreach($years as $key=>$val){
					if($key == date('Y'))
					echo sprintf('<option value="%s" selected="selected">%s</option>',$val,$val);
					else
					echo sprintf('<option value="%s">%s</option>',$val,$val);
				    }
				?>
			    </select>
			</div>
			<!--div class="pull-right box-tools"-->
                        <div style="padding: 5px 10px 5px 5px; float: right;">
			    <select class="form-control onchange" id="month">
				<?php
				    foreach($months as $key=>$val){
					if($key == date('m'))
					echo sprintf('<option value="%s" selected="selected">%s</option>',$key,$val);
					else
					echo sprintf('<option value="%s">%s</option>',$key,$val);
				    }
				?>
			    </select>
			</div>
			
		    <h3 class="box-title">Daily Videos</h3>
		    </div><!-- /.box-header -->
		    <div class="box-body no-padding">
			<div class="row">
			    <div class="col-sm-12">
				<div class="chart" id="dailyvideo-chart-div" style="height: 250px;"></div>
			    </div>
			</div><!-- /.row - inside box -->
		    </div><!-- /.box-body -->
		</div><!-- /.box -->
	    </section><!-- /.Left col -->
	</div>
	
	<script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
        <script src="<?=base_url()?>assets/js/plugins/morris/morris.min.js" type="text/javascript"></script>
	<script>
	    $(function(){
		$('#category-chart-div').html(loaderCenter);
		$.ajax({
		    type: "GET",
		    url: '<?=base_url()?>layout/dashboardchart/category_video',
		    dataType: "html",
		    success: function(response) {
			var data = $.parseJSON(response);
			$('#category-chart-div').html('');
			var donut = new Morris.Donut({
			    element: 'category-chart-div',
			    resize: true,
			    colors: data.color,
			    data: data.data,
			    hideHover: 'auto'
			});
		    }
		});
		
		$('#uservideo-chart-div').html(loaderCenter);
		$.ajax({
		    type: "GET",
		    url: '<?=base_url()?>layout/dashboardchart/users_video',
		    dataType: "html",
		    success: function(response) {
			var data = $.parseJSON(response);
			$('#uservideo-chart-div').html('');
			var donut = new Morris.Donut({
			    element: 'uservideo-chart-div',
			    resize: true,
			    colors: data.color,
			    data: data.data,
			    hideHover: 'auto'
			});
		    }
		});
		
		$('.onchange').on('change',function(){
		    dailyGraph();
		});
		
		function dailyGraph(){
		    $('#dailyvideo-chart-div').html(loaderCenter);
		    $.ajax({
			type: "GET",
			url: '<?=base_url()?>layout/dashboardchart/dailyvideo?month=' + $('#month').val() + '&year=' + $('#year').val(),
			dataType: "html",
			success: function(response) {
			    var data = $.parseJSON(response);
			    $('#dailyvideo-chart-div').html('');
			    var line = new Morris.Line({
				element: 'dailyvideo-chart-div',
				resize: true,
				data:  data.data,
				xkey: 'y',
				ykeys: ['value'],
				labels: ['Videos'],
				xLabelAngle : 35,
				parseTime : false,
				lineColors: data.color,
				hideHover: 'auto'
			    });
			}
		    });
		}
		dailyGraph();
	    });
	</script>
    </section><!-- /.content -->
</aside><!-- /.right-side -->