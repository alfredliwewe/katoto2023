<?php
$db = new sqlite3("database.db");
require 'objects.php';
require 'config.php';

$system = new System($db);
?><!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?=$config['name'];?> - Login Portal</title>
	<script type="text/javascript" src="stopusers.js"></script>
	<link rel="stylesheet" type="text/css" href="w3css/w3.css">
	<link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
	<script type="text/javascript" src="vendor/jquery/jquery.min.js"></script>
	<style type="text/css">
		.pointer:hover{
			cursor: pointer;
		}
	</style>
</head>
<body>
<div class="bg-info w3-padding w3-row w3-border-bottom w3-border-black">
	<div class="w3-col m2">
		<br>
		<center>
			<img src="img/<?=$config['logo'];?>" width="60">
		</center>
	</div>
	<div class="w3-col m5">
		<center>
			<p class="w3-xxlarge"><?=$config['name'];?></p>
			<font class="w3-xlarge"><?=$config['infor'];?></font>
		</center>
	</div>
	<div class="w3-col m5">
		<br>
		<center>
			<a class="btn btn-primary" href="login.php">Login</a> <a class="btn btn-primary" href="login.php">Read User Manual</a>
		</center>
	</div>
</div>
<p>&nbsp;</p>

<?php
include("citysliders.html");
?>
<p>&nbsp;</p>
<center>
	Powered by Rodz Tecknologez
</center>
</body>
<script type="text/javascript">
	$('#login_form').on('submit', function(event) {
		event.preventDefault();
		var form_data = $('#login_form').serialize();

		$.ajax({
			url: "rest_api.php",
			method: "POST",
			data: form_data,
			success: function(response) {
				var obj = JSON.parse(response);

				if (obj.status == true) {
					window.location = obj.link;
				}
				else{
					$('#login_result').html('<div class="alert alert-danger">'+obj.message+'</div>');
				}
			}
		});
	});
</script>
</html>