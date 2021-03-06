<!-- Right side column. Contains the navbar and content of the page -->
<aside class="content-wrapper">                
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Electronic Program Guide
            <small>Control panel</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url() ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?php echo base_url() . "webtv/playlist/" . $this->uri->segment(4) ?>"><i class="fa fa-dashboard"></i> Playlist</a></li>
            <li class="active">EPG</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-3 eventlist">
                <div class="box box-primary">
                    <div class="box-header">
                        <h4 class="box-title">Vod Video list</h4>
                    </div>
                    <div class="box-body">
                        <div id='external-events'>
                            <?php if(isset($result['vod'])) foreach ($result['vod'] as $key => $value) { $value->duration = $value->duration > 0 ? $value->duration : 300; ?>
                                <div class='external-event' duration="<?= $value->duration ?>" style="background-color: <?= $value->color ?>" id="<?= $value->id ?>"><?= $value->title ?>[<?= $welcome->time_from_seconds($value->duration) ?>]</div><br>
                            <?php } ?>
                            <p class="loader"></p>
                        </div>
                    </div><!-- /.box-body -->
                </div><!-- /. box -->
                <div class="box box-primary">
                    <div class="box-header">
                        <h4 class="box-title">Youtube Video list</h4>
                    </div>
                    <div class="box-body">
                        <div id='external-events'>
                            <?php if(isset($result['youtube'])) foreach ($result['youtube'] as $key => $value) { //echo '<pre>';print_r($value);echo '</pre>'; ?>
                                <div class='external-event' duration="<?= $value->duration ?>" style="background-color: <?= $value->color ?>" id="<?= $value->id ?>"><?= $value->title ?>[<?= $welcome->time_from_seconds($value->duration) ?>]</div><br>
                            <?php } ?>
                            <p class="loader"></p>
                            <!-- <input type='checkbox' id='drop-remove' /> <label for='drop-remove'>remove after drop</label> -->
                        </div>
                    </div><!-- /.box-body -->
                </div><!-- /. box -->
                <a class="btn btn-warning" href="<?php echo base_url() . "webtv/playlist/" . $this->uri->segment(4) ?>">Back</a>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-download"></i> Dropdown
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        <li><a target="_blank" href="<?= base_url() . 'webtv/export/pdf/' . $this->uri->segment(3) ?>"> PDF</a></li>
                        <li><a target="_blank" href="<?= base_url() . 'webtv/export/csv/' . $this->uri->segment(3) ?>"> CSV</a></li>
                        <li><a target="_blank" href="<?= base_url() . 'webtv/export/xml/' . $this->uri->segment(3) ?>"> XML</a></li>
                    </ul>
                </div>
                <a class="btn btn-warning zoom" href="javascript:void(0)">Zoom</a>
            </div><!-- /.col -->
            <div class="col-md-9 fullview">
                <div class="box box-primary">                                
                    <div class="box-body no-padding">
                        <!-- THE CALENDAR -->
                        <div id="calendar"></div>
                    </div><!-- /.box-body -->
                </div><!-- /. box -->
            </div><!-- /.col -->
        </div><!-- /.row -->  
    </section><!-- /.content -->
</aside><!-- /.right-side -->
</div><!-- ./wrapper -->
<script src="<?= base_url() ?>assets/js/plugins/fullcalendar/fullcalendar.min.js" type="text/javascript"></script>
<!-- Page specific script -->
<script type="text/javascript">
    $(function () {
        /* initialize the external events
         -----------------------------------------------------------------*/

        var playlist_id = <?= $this->uri->segment(3); ?>;
        $('#drop-remove').prop('checked', true);

        $('.box-body').each(function (index) {
            if ($(this).height() > 200) {
                //$(this).attr('style','height: 300px; overflow-x: auto;');
            }
        });
        
        $('.zoom').on('click',function(){
            $('.sidebar-toggle').trigger('click');
        });
        
        function ini_events(ele) {
            ele.each(function () {

                // create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
                // it doesn't need to have a start or end
                var eventObject = {
                    title: $.trim($(this).text()) // use the element's text as the event title
                };

                // store the Event Object in the DOM element so we can get to it later
                $(this).data('eventObject', eventObject);

                // make the event draggable using jQuery UI
                $(this).draggable({
                    zIndex: 1070,
                    revert: true, // will cause the event to go back to its
                    revertDuration: 50  //  original position after the drag
                });

            });
        }
        ini_events($('#external-events div.external-event'));

        /* initialize the calendar
         -----------------------------------------------------------------*/
        //Date for the calendar events (dummy data)
        var date = new Date();
        var d = date.getDate(), m = date.getMonth(), y = date.getFullYear();
        var cal = $('#calendar').fullCalendar({
            header: {
                left: 'next today',
                center: 'title',
                right: 'month,agendaDay'
            },
            buttonText: {//This is to add icons to the visible buttons
                prev: "<span class='fa fa-caret-left'></span>",
                next: "<span class='fa fa-caret-right'></span>",
                today: 'today',
                month: 'month',
                week: 'week',
                day: 'day'
            },
            defaultView: 'agendaDay',
            events: "<?= base_url() ?>webtv/renderevent?playlist_id=" + playlist_id + '&',
            minTime: 0,
            maxTime: 12,
            slotMinutes: 15,
            editable: true,
            droppable: true, // this allows things to be dropped onto the calendar !!!
            drop: function (date, allDay, ui) {
                
                var view = $('#calendar').fullCalendar('getView');
                switch (view.name) {
                    case 'month' :
                        //alert('month view');
                        break;
                    default :
                        // retrieve the dropped element's stored Event Object
                        var originalEventObject = $(this).data('eventObject');
        
                        // we need to copy it, so that multiple events don't have a reference to the same object
                        var copiedEventObject = $.extend({}, originalEventObject);
        
                        var duration = $(this).attr("duration");
        
                        // assign it the date that was reported
                        copiedEventObject.id = $(this).attr("id");
                        copiedEventObject.start = date;
                        copiedEventObject.end = new Date(date.getTime() + (duration * 1000));
                        copiedEventObject.allDay = allDay;
                        copiedEventObject.backgroundColor = $(this).css("background-color");
                        copiedEventObject.borderColor = $(this).css("border-color");
        
                        if(isOverlapping(copiedEventObject)){
                            $('#calendar').fullCalendar('renderEvent', copiedEventObject, true);
                            __saveEvent(copiedEventObject);
                            $(this).remove();
                        }
                        break;
                }
            },
            eventDrop: function (event, dayDelta, minuteDelta, allDay, revertFunc) {
                var view = $('#calendar').fullCalendar('getView');
                switch (view.name) {
                    case 'month' :
                        break;
                    default :
                        if(isOverlapping(event)){
                            event.action = 'update';
                            __saveEvent(event);    
                        }else{
                            revertFunc();
                        }
                        break;
                }
            },
            eventResize: function (event, dayDelta, minuteDelta, revertFunc) {
                if(isOverlapping(event)){
                    event.action = 'update';
                    __saveEvent(event);    
                }else{
                    revertFunc();
                }
            },
            eventClick: function (event, jsEvent, view) {
                var r = confirm("Delete " + event.title);
                if (r === true) {
                    event.action = 'delete';
                    __saveEvent(event);
                }
            },
            slotEventOverlap: false,
            forceEventDuration : true
            //eventDurationEditable: false
        });

        
        function isOverlapping(event) {
            var validDrop = true;
            var ev = $("#calendar").fullCalendar("clientEvents");
            for(var e in ev)
            {   
                if(ev[e].id!=event.id)
                {
                    if(ev[e].start>=event.start && ev[e].end<=event.end)
                        validDrop=false;
                    if(ev[e].start<=event.start && ev[e].end>=event.end) 
                        validDrop=false;
                    if(ev[e].start<event.start && ev[e].end<event.end && ev[e].end>event.start)
                        validDrop=false;
                    if(ev[e].end>event.end && ev[e].start>event.start && ev[e].start<event.end)
                        validDrop=false;
                }
            }
            return validDrop;
        }
        
        custom_buttons = '<span class="fc-button fc-button-today fc-state-default fc-corner-left fc-corner-right fc-state-disabled" unselectable="on" style="-moz-user-select: none;">today</span>'
        $('.fc-button-today').after(custom_buttons);
        
        custom_buttons = '<span class="fc-button fc-button-eventcopy fc-state-default fc-corner-left" unselectable="on" style="-moz-user-select: none;">Event Copy</span>'
        $('.fc-button-agendaDay').after(custom_buttons);

        $('.fc-button-eventcopy').on('click', function () {
            var moment = $('#calendar').fullCalendar('getDate');
            var d = new Date(moment);
            var url = '<?= base_url() . 'webtv/eventCopy?url='.  current_full_url().'&playlist_id=' . $this->uri->segment(3).'&date='?>' + d.getUTCFullYear() + '-' + (d.getUTCMonth() + 1) + '-' + d.getUTCDate(); 
            bootbox.dialog({message: 'wait...', title: "Event Copy"});
            $.ajax({
                type: "GET",
                url: url,
                dataType: "html",
                success: function (response) {
                    $('.modal-dialog .modal-content .modal-body .bootbox-body').html(response);
                }
            });
        });
        
        function __saveEvent(event) {
            var subUrl = "<?= base_url() ?>webtv/saveevent";
            $('.loader').html('loading.....');
            $.ajax({
                url: subUrl,
                type: "POST",
                data: {id: event.id, playlist_id: <?= $playlist_id ?>, title: event.title, start_date: event.start, end_date: event.end, action: event.action},
                success: function (data, textStatus) {
                    if (event.action == 'delete') {
                        location.reload();
                    } else {
                        $('.loader').html('');
                    }
                },
                error: function (data, textStatus) {
                    alert('An error has occured retrieving data!');
                }
            });
        }
    });
</script>