      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          
        </section>

        <!-- Main content -->
        <section class="content" id="customCss">
          <form role="form">
          <div class="row">
            <!-- left column -->
            <div class="col-md-12">
              <!-- Form Element sizes -->
              <div class="box box-primary">
                <div class="box-header">
                    <h3 class="timeHeading pull-left">Today</h3>
          <div class="mailbox-controls pull-right">
                    <!-- Check all button -->
                    <a class="btn btn-default btn-sm reservation" onclick="dateRange()"><i class="fa fa-calendar"></i></a>
                    <div class="btn-group ">
                      <a class="btn btn-default btn-sm year-class">2015</a>
                      <a class="btn btn-default btn-sm active-header-btn today-class">Today</a>
                      <a class="btn btn-default btn-sm week-class">7 Days</a>
                      <a class="btn btn-default btn-sm month-class">30 Days</a>
                      <a class="btn btn-default btn-sm months2-class">60 Days</a>
                      <a class="btn btn-default btn-sm months3-class">90 Days</a>
                    </div><!-- /.btn-group -->
                  </div>
                </div>
                <div class="box-body" id="conditionalDiv">
<div class="dashboard" id="big-numbers-container">
					
					<div class="big-numbers six-column active">
						<div class="inner">
							<div class="logo"></div>
							<div class="number">77K</div>
							<div style="background-image:url('/images/dashboard/utrend.png');" class="trend">
								<div class="change">16.6%</div>
							</div>
							<div class="spark"><span class="usparkline">
                                                        <canvas width="120" height="30" style="width:120px;height:30px"></canvas></span></div>
							<div id="draw-total-sessions" class="select">TOTAL SESSIONS</div>
							<div class="arrow"></div>
						</div>
					</div>
					
					<div class="big-numbers six-column">
						<div class="inner">
							<div class="logo"></div>
							<div class="number">70K</div>
							<div style="background-image:url('assets/img/dashboard/dtrend.png');" class="trend">
								<div class="change">-4.4%</div>
							</div>
							<div class="spark"><span class="dsparkline"><canvas width="120" height="30" style="width:120px;height:30px"></canvas></span></div>
							<div id="draw-total-users" class="select">TOTAL USERS</div>
							<div class="arrow"></div>
						</div>
					</div>
					
					<div class="big-numbers six-column">
						<div class="inner">
							<div class="logo"></div>
							<div class="number">16.9K</div>
							<div style="background-image:url('assets/img/dashboard/utrend.png');" class="trend">
								<div class="change">27.3%</div>
							</div>
							<div class="spark"><span class="usparkline"><canvas width="120" height="30" style="width:120px;height:30px"></canvas></span></div>
							<div id="draw-new-users" class="select">NEW USERS</div>
							<div class="arrow"></div>
						</div>
					</div>
					
					<div class="big-numbers six-column">
						<div class="inner">
							<div class="logo"></div>
							<div class="number">8min</div>
							<div style="background-image:url('/images/dashboard/dtrend.png');" class="trend">
								<div class="change">-10.4%</div>
							</div>
							<div class="spark"><span class="dsparkline"><canvas width="120" height="30" style="width:120px;height:30px"></canvas></span></div>
							<div id="draw-time-spent" class="select">AVG. TIME SPENT</div>
							<div class="arrow"></div>
						</div>
					</div>
					
					<div class="big-numbers six-column">
						<div class="inner">
							<div class="logo"></div>
							<div class="number">121.7K</div>
							<div style="background-image:url('/images/dashboard/utrend.png');" class="trend">
								<div class="change">28.1%</div>
							</div>
							<div class="spark"><span class="usparkline"><canvas width="120" height="30" style="width:120px;height:30px"></canvas></span></div>
							<div id="draw-events-served" class="select">EVENTS SERVED</div>
							<div class="arrow"></div>
						</div>
					</div>
					
					<div class="big-numbers six-column">
						<div class="inner">
							<div class="logo"></div>
							<div class="number">1.7</div>
							<div style="background-image:url('/images/dashboard/utrend.png');" class="trend">
								<div class="change">31.1%</div>
							</div>
							<div class="spark"><span class="usparkline"><canvas width="120" height="30" style="width:120px;height:30px"></canvas></span></div>
							<div id="draw-avg-events-served" class="select">AVG. EVENTS SERVED</div>
							<div class="arrow"></div>
						</div>
					</div>
					
				</div>                 
                 
                 <br>
                </div><!-- /.box-body -->
              
                  <div id="line-chart" style="height: 300px;"></div>           
              
              </div><!--/.col (left) -->
            



              <div class="dashboard-summary" style="margin-bottom: 120px">
                          <div class="item">
				<div class="inner">
					<div class="title">TOP PLATFORM</div>
					
					<div class="bar">
						
							<div data-item="iOS" style="width:50%;" class="bar-inner"></div>
						
							<div data-item="Android" style="width:50%;" class="bar-inner"></div>
						
					</div>
					<div data-item="iOS" class="number">iOS</div>
					
				</div>
			</div>
                          <div class="item">
				<div class="inner">
					<div class="title">TOP PLATFORM</div>
					
					<div class="bar">
						
							<div data-item="iOS" style="width:50%;" class="bar-inner"></div>
						
							<div data-item="Android" style="width:50%;" class="bar-inner"></div>
						
					</div>
					<div data-item="iOS" class="number">iOS</div>
					
				</div>
			</div>

                          <div class="item">
				<div class="inner">
					<div class="title">TOP PLATFORM</div>
					
					<div class="bar">
						
							<div data-item="iOS" style="width:50%;" class="bar-inner"></div>
						
							<div data-item="Android" style="width:50%;" class="bar-inner"></div>
						
					</div>
					<div data-item="iOS" class="number">iOS</div>
					
				</div>
			</div>


                          <div class="item">
				<div class="inner">
					<div class="title">TOP PLATFORM</div>
					
					<div class="bar">
						
							<div data-item="iOS" style="width:50%;" class="bar-inner"></div>
						
							<div data-item="Android" style="width:50%;" class="bar-inner"></div>
						
					</div>
					<div data-item="iOS" class="number">iOS</div>
					
				</div>
			</div>                          
                          
                  </div><!-- /.box-body -->
              <div class="box box-primary">
                    <div class="row">
                      <div class="col-md-9"><div id="world-map" style="width: 104%; height: 400px"></div></div>
                      <div class="col-md-3">
                        
                        <div class="pad box-pane-right bg-green">
                          <div class="row" style="text-align: center">
                            <div class="col-md-6">UAE</div><div class="col-md-1">120</div>
                          </div>
                          <br>
                          <div class="row" style="text-align: center">
                            <div class="col-md-6">UAE</div><div class="col-md-1">120</div>
                          </div>
                          <br>
                          <div class="row" style="text-align: center">
                            <div class="col-md-6">UAE</div><div class="col-md-1">120</div>
                          </div>
                          <br>
                          <div class="row" style="text-align: center">
                            <div class="col-md-6">UAE</div><div class="col-md-1">120</div>
                          </div>
                          <br>
                          <div class="row" style="text-align: center">
                            <div class="col-md-6">UAE</div><div class="col-md-1">120</div>
                          </div>
                          <br>
                          <div class="row" style="text-align: center">
                            <div class="col-md-6">UAE</div><div class="col-md-1">120</div>
                          </div>
                          <br>
                          <div class="row" style="text-align: center">
                            <div class="col-md-6">UAE</div><div class="col-md-1">120</div>
                          </div>
                          <br>
                          <div class="row" style="text-align: center">
                            <div class="col-md-6">UAE</div><div class="col-md-1">120</div>
                          </div>
                          <br>
                          <div class="row" style="text-align: center">
                            <div class="col-md-6">UAE</div><div class="col-md-1">120</div>
                          </div>
                          <br>
                          <div class="row" style="text-align: center">
                            <div class="col-md-6">UAE</div><div class="col-md-1">120</div>
                          </div>                            
                          
                      </div>
                        
                      </div>
                    </div>
                  </div><!-- /.box-body -->
              
            </div><!--/.col (left) -->
            
          
           
            <!-- right column -->
          
            </div>   <!-- /.row -->
          </form>
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
                <!-- FLOT CHARTS -->
    <script src="<?php echo base_url() ?>assets/js/plugins/flot/jquery.flot.min.js" type="text/javascript"></script>
    <!-- FLOT RESIZE PLUGIN - allows the chart to redraw when the window is resized -->
    <script src="<?php echo base_url() ?>assets/js/plugins/flot/jquery.flot.resize.min.js" type="text/javascript"></script>
    <!-- FLOT PIE PLUGIN - also used to draw donut charts -->
    <script src="<?php echo base_url() ?>assets/js/plugins/flot/jquery.flot.pie.min.js" type="text/javascript"></script>
    <!-- FLOT CATEGORIES PLUGIN - Used to draw bar charts -->
    <script src="<?php echo base_url() ?>assets/js/plugins/flot/jquery.flot.categories.min.js" type="text/javascript"></script>
          <script src="<?php echo base_url() ?>assets/js/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url() ?>assets/js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js" type="text/javascript"></script>
      <script src="<?php echo base_url() ?>assets/js/plugins/daterangepicker/daterangepicker.js" type="text/javascript"></script>
                      <script src="http://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
                <script src="<?php echo base_url() ?>assets/js/plugins/morris/morris.min.js"></script>
      <script>
      $(document).ready(function(){
                
                var data_today = [
                  {time:'0:00',item1:2666},
                  {time:'1:00',item1:2777},
                  {time:'2:00',item1:1000},
                  {time:'3:00',item1:2000},
                  {time:'4:00',item1:2500},
                  {time:'5:00',item1:8900},
                  {time:'6:00',item1:15000},
                  {time:'7:00',item1:5600},
                  {time:'8:00',item1:5892},
                  {time:'9:00',item1:1478},
                  {time:'10:00',item1:2356},
                ];  
          var line = new Morris.Line({
          element: 'line-chart',
          resize: true,
          data: data_today, 
          xkey: 'hours',
          ykeys: ['a'],
          labels: ['Push Count'],
          //gridIntegers: true,
          parseTime: false,
          lineColors: ['#3c8dbc'],
          hideHover: 'auto'
        });
        $('#big-numbers-container .inner').click(function () {
          $('#big-numbers-container > .big-numbers').removeClass('active');
              $(this).parent().addClass('active'); 
          });
          $('.mailbox-controls .btn-default').click(function () {
            $('.btn-default').removeClass('active-header-btn');
            $(this).addClass('active-header-btn'); 
          });
        $(".reservation").daterangepicker();
        var gdpData = {
            "AF": 16.63,
            "AL": 11.58,
            "DZ": 158.97,
        };
        $('#world-map').vectorMap({
            map: 'world_mill_en',
             series: {
               regions: [{
                 values: gdpData,
                 scale: ['#C8EEFF', '#0071A4'],
                 normalizeFunction: 'polynomial'
               }]
             },
             onRegionTipShow: function(e, el, code){
               el.html(el.html()+' (GDP - '+gdpData[code]+')');
             }          
          });
 var sin = [], cos = [];
        for (var i = 0; i < 14; i += 0.5) {
          sin.push([i, Math.sin(i)]);
          cos.push([i, Math.cos(i)]);
        }
        var line_data1 = {
          data: sin,
          color: "#3c8dbc"
        };
        $.plot("#line-chart", [line_data1], {
          grid: {
            hoverable: true,
            borderColor: "#f3f3f3",
            borderWidth: 1,
            tickColor: "#f3f3f3"
          },
          series: {
            shadowSize: 0,
            lines: {
              show: true
            },
            points: {
              show: true
            }
          },
          lines: {
            fill: false,
            color: ["#3c8dbc", "#f56954"]
          },
          yaxis: {
            show: true,
          },
          xaxis: {
            show: true
          }
        });
        });
      
      function dateRange($divs) {
        $("#"+$divs+" .reservation").daterangepicker('show');
      }
function calculateTimeInterVal(timeStr,timeVal) {
    if(timeStr=='currentWeek') {
      
    }
    if (timeStr=='today') {
      
    }
    if (timeStr=='last30Days') {
      
    }
    if (timeStr=='currentYear') {
      
    }
    if (timeStr=='dateBetweenDate') {
      
    }
    if (timeStr=='last60Days') {
      
    }    
    if (timeStr=='last90Days') {
      
    }
}
      
    </script>
<style>
  h3.timeHeading
  {
    margin-top : 4px !important
    
  }
#customCss .box-header
{
  background: none repeat scroll 0 0 #3c8dbc;
  color: #fff;
  }  
#conditionalDiv {
  padding: 0px;
}
.item
{
  width: 25%;
  height: 50px
  
}
#big-numbers-container.dashboard {
    -moz-border-bottom-colors: none;
    -moz-border-left-colors: none;
    -moz-border-right-colors: none;
    -moz-border-top-colors: none;
    border-color: #b8b9b9 #c2c2c2 -moz-use-text-color;
    border-image: none;
    border-left: 1px solid #c2c2c2;
    border-right: 1px solid #c2c2c2;
    border-style: solid solid none;
    border-width: 1px 1px medium;
    padding-bottom: 8px;
}
.six-column:first-child {
    width: 15%;
}
.six-column {
    width: 17%;
}
.big-numbers {
    float: left;
    position: relative;
    cursor: pointer
}

.big-numbers:first-child .inner {
    border-left: medium none;
}
.big-numbers.active .inner {
    background-color: #fff;
}
.widget-content .big-numbers .inner {
    cursor: pointer;
    height: 190px;
}
.big-numbers .inner {
    background-color: #f9f9f9;
    border-bottom: 1px solid #a1a1a1;
    border-left: 1px solid #c9c9c9;
    height: 190px;
    text-align: center;
}

.big-numbers.active:nth-child(1) .logo {
    background-position: 5px center;
}
.big-numbers:nth-child(1) .inner .logo {
    background-position: -55px center;
}
.big-numbers .inner .logo {
    background-image: url("assets/img/dashboard_icons.png");
    height: 60px;
    margin: 0 auto;
    width: 60px;
}

.big-numbers.active .number {
    color: #333;
    text-shadow: 0 1px #fff;
}
.widget-content .big-numbers .number {
    margin-top: 1px;
}
.big-numbers .number {
    color: #666;
    font: 30px Oswald;
    text-shadow: 0 1px #fff;
}
element.style {
    background-image: url("assets/img/utrend.png");
}
.widget-content .big-numbers .trend {
    height: 9px;
    position: absolute;
    right: 7%;
    top: 11px;
    width: 12px;
}

.big-numbers.active .change {
    color: #333;
}
.widget-content .big-numbers .change {
    color: #999;
    font: 10px Ubuntu,Helvetica,sans-serif;
    position: absolute;
    right: 15px;
    text-shadow: 0 1px #fff;
    top: -1px;
    white-space: nowrap;
}

.big-numbers .spark {
    margin-top: 11px;
}
.big-numbers .spark {
    margin-top: 11px;
}

.big-numbers.active .select {
    /*background-color: #2b2b2b;
    background-image: linear-gradient(to bottom, #484848 20%, #2b2b2b 100%);*/
    background-color: #3c8dbc;
    background-image: linear-gradient(to bottom, #3c8dbc 20%, #2b2b2b 100%);
    border-bottom: 1px solid #616161;
    border-top-color: #404040;
    bottom: 0;
    box-shadow: 0 1px 0 #5e5e5e inset;
    color: #eee;
    text-shadow: 0 -1px #333;
}
.widget-content .big-numbers .select {
    border-top: 1px solid #a1a2a2;
    bottom: 1px;
    cursor: pointer;
}
.big-numbers .select {
    background-color: #d6d6d6;
    background-image: linear-gradient(to bottom, #e1e1e1 20%, #d6d6d6 100%);
    box-shadow: 0 1px 0 #f6f6f6 inset;
    color: #5b5b5b;
    font: 13px Ubuntu,Helvetica,sans-serif;
    height: 28px;
    padding-top: 7px;
    position: absolute;
    text-shadow: 0 1px #fff;
    width: 100%;
}

.big-numbers.active .arrow {
    display: block;
}
.big-numbers .arrow {
    background-image: url("assets/img/dashboard-arrow.png");
    bottom: -6px;
    display: none;
    height: 8px;
    left: 50%;
    margin-left: -9px;
    position: absolute;
    width: 18px;
    z-index: 2;
}

.dashboard-summary .item {
    float: left;
    font-family: Oswald;
}
.dashboard-summary .inner {
    background-color: #3c3c3c;
    background-image: linear-gradient(to bottom, #3c3c3c 10%, #2a2a2a 100%);
    border-radius: 5px;
    margin-left: 5px;
    padding: 10px;
}

.dashboard-summary .item .title {
    color: #fff;
    font: 12px Ubuntu,Helvetica,sans-serif;
    text-align: center;
}
.dashboard-summary .item .bar {
    background-color: transparent;
    border-radius: 5px;
    height: 6px;
    margin: 5px auto;
    overflow: auto;
    padding-bottom: 2px;
    padding-top: 2px;
    width: 80%;
}
.dashboard-summary .item .bar .bar-inner:first-child {
    border-bottom-left-radius: 5px;
    border-top-left-radius: 5px;
    box-shadow: none;
}
.dashboard-summary .item .bar .bar-inner {
    background-color: #6bb96e;
    box-shadow: 2px 0 0 0 #333 inset;
    cursor: pointer;
    float: left;
    height: 6px;
    width: 40%;
}
.dashboard-summary .item .bar .bar-inner:nth-child(2) {
    background-color: #86cbdd;
    width: 42%;
}
.dashboard-summary .item .bar .bar-inner:last-child {
    border-bottom-right-radius: 5px;
    border-top-right-radius: 5px;
}
.dashboard-summary .item .bar .bar-inner {
    background-color: #6bb96e;
    box-shadow: 2px 0 0 0 #333 inset;
    cursor: pointer;
    float: left;
    height: 6px;
    width: 40%;
}

.dashboard-summary .item .number {
    color: #6bb96e;
    font-size: 26px;
    line-height: 33px;
    overflow: hidden;
    text-align: center;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.active-header-btn,.btn-default:hover
{
  background: #2b2b2b!important;
  border: 1px solid #2b2b2b!important;
  color: white!important;
}   
 .daterangepicker .calendar th, .daterangepicker .calendar td
{
    min-width:0px
}
.table-condensed > tbody > tr > td, .table-condensed > tbody > tr > th, .table-condensed > tfoot > tr > td, .table-condensed > tfoot > tr > th, .table-condensed > thead > tr > td, .table-condensed > thead > tr > th
{
padding:3px!important;
}
.daterangepicker_start_input, .daterangepicker_end_input{
display : none!important;
}
