<link href="<?= base_url() ?>assets/css/morris/morris.css" rel="stylesheet" type="text/css" />
<aside class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> 
            <?php echo $welcome->loadPo('Top Video Viewed'); ?>
            <small><?php echo $welcome->loadPo('Control panel'); ?></small>
        </h1>
        <ol class="breadcrumb">

            <li><a href="<?php echo base_url(); ?>analytics/report"><i class="fa fa-dashboard"></i><?php echo $welcome->loadPo('Analytics') ?></a></li>
            <li class="active"><?php echo $welcome->loadPo('Top video viewed') ?></li>
        </ol>
    </section>
    <div>
        <div id="msg_div">
            <?php echo $this->session->flashdata('message'); ?>
        </div>	
    </div>
    <?php $search = $this->session->userdata('search_form'); ?>
    <!-- Main content -->
    <section class="content">		
        <form  method="post" action="" id="searchIndexForm" name="searchIndexForm" accept-charset="utf-8">
            <div class="box box-primary collapsed-box">
                <div class="box-header">
                    <!-- tools box -->
                    <div class="pull-right box-tools">
                        <button class="btn btn-danger btn-sm" data-widget='collapse' data-toggle="tooltip" title="Collapse"><i class="fa fa-plus"></i></button>
                    </div><!-- /. tools -->
                    <h3 class="box-title">Search</h3>
                </div>

                    <div class="row">
                        <div class="box-body" style="display:none;">
                            <!-- form start -->                
                            <div class="form-group col-lg-4">
                                <div class="input text">
                                    <label for="url"><?php echo $welcome->loadPo('Start Date') ?></label>
                                    <input type="text" class="form-control datepicker"  id="startdate" name="startdate" placeholder="<?php echo $welcome->loadPo('Start Date') ?>" value="<?php echo (isset($search['startdate'])) ? $search['startdate'] : ''; ?>" >											
                                </div>
                            </div>
                            <div class="form-group col-lg-4">
                                <div class="input text">
                                    <label for="url"><?php echo $welcome->loadPo('End Date') ?></label>
                                    <input type="text" class="form-control datepicker"  id="enddate" name="enddate" placeholder="<?php echo $welcome->loadPo('End Date') ?>" value="<?php echo (isset($search['enddate'])) ? $search['enddate'] : ''; ?>">
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="box-footer" style="display:none;">
                        <div class="input text">		   
                            <button type="submit" id="submit" name="search" value="Search"class="btn btn-primary"><?php echo $welcome->loadPo('Search') ?></button>
                            <button type="submit" name="reset" value="Reset"class="btn btn-primary"><?php echo $welcome->loadPo('Reset') ?></button>
                            <span id="error_msg" style="color: red"></span>
                        </div>
                    </div>


            </div>
        </form>    
        <script>
            $("#submit").click(function (data) {
                var startdate = $.trim($("#startdate").val());
                var enddate = $.trim($("#enddate").val());
                if (
                        startdate === ""
                        && enddate === ""
                        ) {
                    $("#error_msg").html('One on the above is required');
                    return false;
                }
            });
        </script>

        <div class="row">
            <section class="col-lg-12"> 
                <!-- Box (with bar chart) -->
                <div class="box box-danger">
                    <div class="box-header">
                        <!-- tools box -->
                        <div class="pull-right box-tools">
                            <button class="btn btn-danger btn-sm" data-widget='collapse' data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                        </div><!-- /. tools -->						
                        <h3 class="box-title">Daily Videos Hits</h3>
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

        <div class="row">
            <section class="col-lg-6"> 
                <!-- Bar chart -->
                <div class="box box-primary">
                    <div class="box-header">

                        <h3 class="box-title">Performance overview</h3>
                    </div>
                    <div class="box-body">
                        <h4> Video Viewed : <strong><?php echo $summary->total_hits; ?> </strong></h4>
                        <h4> Total Time Watched : <strong><?php echo time_from_seconds($summary->total_watched_time); ?> </strong> </h4>
                    </div><!-- /.box-body-->
                </div><!-- /.box -->
                <div class="box box-primary">
                    <div class="box-header">

                        <h3 class="box-title">Top video viewed by country</h3>
                    </div>
                    <div class="box-body">
                        <table id="example2" class="table table-bordered table-striped"> 
                            <thead>
                                <tr>
                                    <th width='5%'>Sl.</th>
                                    <th>Title</th>
                                    <th>Hits</th>
                                    <th>Watched time</th>
                                </tr>				    
                            </thead>
                            <?php
                            $i = 0;
                            foreach ($topcountry as $row) {
                                $i++;
                                ?>
                                <tr>
                                    <td><?php echo $i; ?></td>
                                    <td  width="30%"><?php echo $row->country; ?></td>				
                                    <td width="10%"><?php echo $row->total_hits; ?></td>
                                    <td width="20%"><?php echo time_from_seconds($row->total_watched_time); ?></td>				
                                </tr>
<?php } ?>
                            </tbody></table><!-- /.table -->
                    </div><!-- /.box-body-->
                </div><!-- /.box -->
                <div class="box box-primary">
                    <div class="box-header">

                        <h3 class="box-title">Top video viewed by Device</h3>
                    </div>
                    <div class="box-body">
                        <!-- .table - Uses sparkline charts-->
                        <table id="example2" class="table table-bordered table-striped"> 
                            <thead>
                                <tr>
                                    <th width='5%'>Sl.</th>
                                    <th>Title</th>
                                    <th>Hits</th>
                                    <th>Watched time</th>
                                </tr>				    
                            </thead>
                            <?php
                            $i = 0;
                            foreach ($topuseragent as $row) {
                                $i++;
                                ?>
                                <tr>
                                    <td><?php echo $i; ?></td>
                                    <td  width="30%"><?php echo $row->platform; ?> : <?php echo $row->browser; ?></td>				
                                    <td width="10%"><?php echo $row->total_hits; ?></td>
                                    <td width="20%"><?php echo time_from_seconds($row->total_watched_time); ?></td>				
                                </tr>
<?php } ?>
                            </tbody></table><!-- /.table -->
                    </div><!-- /.box-body-->
                </div><!-- /.box -->	
            </section>
            <section class="col-lg-6"> 
                <!-- Bar chart -->
                <div class="box box-primary">
                    <div class="box-header">

                        <h3 class="box-title">Top video viewed</h3>
                    </div>
                    <div class="box-body">                                    
                        <table id="example2" class="table table-bordered table-striped">                                    
                            <thead>
                                <tr>
                                    <th>Sl.</th>
                                    <th>Title</th>
                                    <th>Hits</th>
                                    <th>Watched time</th>
                                </tr>				    
                            </thead>
                            <tbody>
<?php
$i = 0;
foreach ($topcontent as $value) {
    $i++;
    ?>
                                    <tr id="<?php echo $value->id ?>">
                                        <td><?php echo $i; ?></td>  
                                        <td  width="70%"><?php echo $value->title; ?></td>                                               
                                        <td><?php echo $value->total_hits; ?></td>
                                        <td><?php echo time_from_seconds($value->total_watched_time); ?></td>                                                                                        
                                    </tr>
<?php } ?>
                            </tbody>
                        </table>
                    </div><!-- /.box-body-->
                </div><!-- /.box -->			    			      
            </section>
        </div>

    </section>
</aside>
<script src="<?= base_url() ?>assets/js/raphael-min.js"></script>
<script src="<?= base_url() ?>assets/js/plugins/morris/morris.min.js" type="text/javascript"></script>

<script>

            $(function () {
                $(".datepicker").datepicker({
                    dateFormat: 'dd-mm-yy',
                    numberOfMonths: 1,
                });


                function dailyGraph() {
                    var startdate = "<?php echo $search['startdate']; ?>";
                    var enddate = "<?php echo $search['enddate']; ?>";

                    //$('#dailyvideo-chart-div').html(loaderCenter);
                    $.ajax({
                        type: "GET",
                        url: '<?= base_url() ?>analytics/dailyreport?startdate=' + startdate + '&enddate=' + enddate,
                        dataType: "html",
                        success: function (response) {
                            var data = $.parseJSON(response);
                            console.log(data);
                            Morris.Line({
                                element: 'dailyvideo-chart-div',
                                data: data,
                                xkey: 'y',
                                ykeys: ['linear', 'live', 'vod'],
                                labels: ['linear', 'live', 'vod'],
                                xLabelAngle : 35,
				parseTime : false
                            });
                        }
                    });
                }
                dailyGraph();
            });

</script>    