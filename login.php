<!DOCTYPE html>
<html>
<head>
	<title>Login Portal</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script type="text/javascript" src="stopusers.js"></script>
	<link rel="stylesheet" type="text/css" href="w3css/w3.css">
	<link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="fontawesome/css/all.min.css">
	<script type="text/javascript" src="vendor/jquery/jquery.min.js"></script>
	<!--====== Default CSS ======-->
	<link rel="stylesheet" href="assets/css/default.css">

	<!--====== Style CSS ======-->
	<link rel="stylesheet" href="assets/css/style.css">
	<style type="text/css">
		.form-control:focus{
			border: 2px solid #17a2b8 !important;
		}
		.pointer{
			cursor: pointer;
		}
	</style>
</head>
<body>
	<img src="img/teachers.jpg" width="100%">
	<img src="img/5.JPG" class="w3-hide-medium w3-hide-large" width="100%">
	<img src="img/4.JPG" class="w3-hide-medium w3-hide-large" width="100%">
<div class="w3-modal" style="display: block;">
	<div class="w3-modal-content w3-card-16 w3-round-large" style="width: 350px;">
		<div class="w3-padding-large bg-info rounded-top w3-text-white pointer w3-hover-text-red" onclick="window.location = 'index.php'">
			Login portal
		</div>
		<div class="w3-padding-large">
			<form id="login_form" autocomplete="off">
				<div id="login_result"></div>
				<div class="sign_form_wrapper">
	                <div class="single_form">
	                    <input type="text" name="phone" class="form-control" placeholder="Enter Username..."  spellcheck="false">
	                    <i class="far fa-user"></i>
	                </div>
	            </div>
	            <div class="sign_form_wrapper">
	                <div class="single_form">
	                    <input type="password" name="password" class="form-control" placeholder="And password..">
	                    <i class="fa fa-key"></i>
	                </div>
	            </div>
	            <br>
				<button class="btn btn-info btn-block w3-padding-large">Login</button>
			</form>
		</div>
	</div>
</div>
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
				try{
					var obj = JSON.parse(response);

					if (obj.status == true) {
						window.location = obj.link;
					}
					else{
						$('#login_result').html('<div class="alert alert-danger">'+obj.message+'</div>');
					}
				}
				catch(E){
					alert(E.toString()+"\n"+response);
				}
			}
		});
	});
</script>
</html>