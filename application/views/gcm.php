<html>
    <head>
        <title>Google Cloud Messaging (GCM) in PHP</title>
		<style>
			div#formdiv, p#status{
			text-align: center;
			background-color: #FFFFCC;
			border: 2px solid #FFCC00;
			padding: 10px;
			}
			textarea{
			border: 2px solid #FFCC00;
			margin-bottom: 10px;			
			text-align: center;
			padding: 10px;
			font-size: 25px;
			font-weight: bold;
			}
			input{
			background-color: #FFCC00;
			border: 5px solid #fff;
			padding: 10px;
			cursor: pointer;
			color: #fff;
			font-weight: bold;
			}
			 
		</style>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		<script>
		$(function(){
			$("textarea").val("");
		});
		function checkTextAreaLen(){
			var msgLength = $.trim($("textarea").val()).length;
			if(msgLength == 0){
				alert("Please enter message before hitting submit button");
				return false;
			}else{
				return true;
			}
		}
		</script>
    </head>
	<body>
		<div id="formdiv">
		<h1>Push notification</h1>	
		<form method="post" action="<?php echo base_url();?>push_notification/index/?push=true" onsubmit="return checkTextAreaLen()">					                                                      
				<textarea rows="5" name="message" cols="45" placeholder="Message to send via GCM"></textarea> <br/>
				<br>
				Type: <input class='push' type='checkbox' name='type[]' id='typeios' value='apns'> IOS <input class='push' type='checkbox' name='type[]' id='typegcm' value='gcm'> Android<br>				
                <input type="submit"  value="Send Push Notification through GCM" />
		</form>
		</div>
		<p id="status">
		<?php echo $pushStatus; ?>
		</p>		
    </body>
</html>