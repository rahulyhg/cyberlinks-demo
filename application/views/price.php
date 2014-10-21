<?php if(count($result) !='0') { ?>
<form action="<?php echo base_url()?>package/price" method="POST" id="registerId">
    <table class="table table-bordered table-hover dataTable">
<tr>
    <td><input type="radio" name="package_type" class="package_type" value="free" <?php if($type['0']->type == 'free'){ echo "checked";} ?>/>&nbsp;Free</td>
    <td><input type="radio" name="package_type" class="package_type" value="paid" <?php if($type['0']->type == 'paid'){ echo "checked";} ?>/>&nbsp;Paid</td>
</tr>
</table>
<div class="video">
    <table class="table table-bordered table-hover dataTable">
        <tr>
            <td>Duration Name</td>
            <td>Days</td>
            <td>Price</td>
        </tr>
<?php foreach($result as $value){?>
    <tr>
        <td><?php echo $value->name; ?></td>
        <td><?php echo $value->days; ?></td>
        <td><input type="number" name="prive[<?php echo $value->id?>]" value="<?php if(isset($value->price)){ echo $value->price; } ?>"></td>
    </tr>
<?php } ?>
    </table>
</div>
<input type="hidden" name="content_id" value="<?php echo $this->uri->segment(3)?>">
<input type="hidden" name="content_type" value="<?php echo $_GET['type']?>">
<input class="btn btn-success" type="submit" name="submit" value="submit">
</form>
<?php }else{
    echo "No Duration Found";
} ?>
<script src="<?php echo base_url(); ?>assets/js/jquery-1.10.2.js"></script>
<script>
    $(function(){
        if('<?php echo $type['0']->type; ?>'=="free"){
                $(".video").hide();
        }
        $('.package_type').on('change',function(){
            if($(this).attr("value")=="free"){
                $(".video").hide();
            }
            if($(this).attr("value")=="paid"){
                $(".video").show();
            }
        });
    });
</script>