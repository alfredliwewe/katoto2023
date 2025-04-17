<?php

session_start();

$db = new sqlite3("../database.db");


if (!isset($_SESSION['student_id'])) {
	header("Location: ../logout.php");
}
else{
	$user_id = $_SESSION['student_id'];

	$sql = $db->query("SELECT * FROM student WHERE id = '$user_id'");
	$data = $sql->fetchArray();
	if ($data) {
		$username = $data['fullname'];
		$user_phone = $data['regnumber'];
	}
	else{
		header("Loction: ../logout.php");
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Students Portal</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="../w3css/w3.css">
	<link rel="stylesheet" type="text/css" href="../vendor/bootstrap/css/bootstrap.min.css">
	<script type="text/javascript" src="../vendor/jquery/jquery.min.js"></script>
	<link rel="stylesheet" type="text/css" href="../fontawesome/css/all.min.css">
	<style type="text/css">
		.pointer:hover{
			cursor: pointer;
		}
		.form-control:focus{
			border: 2px solid #17a2b8 !important;
		}
		.tp, .tpp{
			padding-bottom: 10px !important;
			padding-top: 10px !important;
			background: inherit !important;
			color: white;
		}
		.activeBtn{
			border: none;
			border-left: 2px #17a2b8 !important;
			color: #17a2b8;
			background: #e6e6ff !important;
			font-weight: bold !important;
		}
		.w3-padding{
			border: none;
		}
		.w3-block{
			width: 100% !important;
			text-align: left;
			align-items: left;
		}
	</style>
</head>
<body>
	<div class="w3-padding clearfix w3-hide-medium w3-hide-large">
		<button class="btn btn-sm btn-outline-info" onclick="$('#menu-drop').show();"><i class="fa fa-bars"></i></button>
		<button class="btn btn-sm btn-outline-danger float-right"><?=$username;?></button>
	</div>
	<div class="w3-row">
		<div class="w3-col m2 w3-hide-small" style="height:calc(100%);">
			<div class="bg-info w3-row w3-border-bottom w3-border-black" style="height:calc(100%);" onclick="e=>e.stopPropagation();" id="leftNav">
				<div class="w3-padding">
					<div class="pb-2" id="heading"><b>School Report System</b></div>
					<div class="py-3">
						<a href="#" class="w3-text-white"><?=$username;?></a>
					</div>
				</div>
				<div class="" id="topLinks">
					<button class="bn pointer w3-padding tp w3-block activeBtn" data="home">Home</button>
					<button class="bn pointer w3-padding tp w3-block" data="register">Register</button>
					<button class="bn pointer w3-padding tp w3-block" data="results">Results</button> 
					<button class="w3-hover-text-red pointer pointer w3-right w3-padding tp w3-block" onclick="$('#logout_modal').show();">Logout</button>
				</div>
			</div>
		</div>
		<div class="w3-col m10" id="rightNav">
			<p>&nbsp;</p>
			<div class="w3-row">
				<div class="w3-col m1 w3-hide-small">&nbsp;</div>
				<div class="w3-col m10 w3-border">
					<div class="w3-row w3-padding-jumbo tt" id="home">
						<div class="w3-col s6 pointer w3-hover-text-blue" onclick="home(2)">
							<center>
								<i class="fa fa-user-graduate fa-4x"></i>
								<br><br>
								<h3>Results</h3>
							</center>
						</div>
						<div class="w3-col s6 pointer w3-hover-text-blue" onclick="home(3)">
							<center>
								<i class="fa fa-outdent fa-4x"></i>
								<br><br>
								<h3>Register</h3>
							</center>
						</div>
					</div>
					<div class="w3-row w3-padding-jumbo tt" id="register" style="display: none;">
						<h5>View registered subjects</h5>
						<div class="w3-border-bottom w3-border-blue">
							<button class="w3-btn w3-small btn-info bh" data="register_subjects">Register Subjects</button><button class="w3-btn w3-small w3-light-grey bh" data="view_registered_subjects">View registration</button>
						</div><br>
				        <div class="hu" id="register_subjects">
				        	<h5>Student subject registration</h5>
				        	<div class="w3-row">
				        		<div class="w3-col m4 w3-padding w3-border-right">
						        	<form id="setup_registration">
						        		<p><select name="acayear" class="form-control" required>
						        			<option value="">--Choose academic year</option>
						        			<?php
						        			$read = $db->query("SELECT * FROM year");
						        			while ($row = $read->fetchArray()) {
						        				echo "<option value=\"{$row['id']}\">{$row['name']}</option>";
						        			}
						        			?>
						        		</select></p>
						        		<p><select name="form" class="form-control" required>
						        			<option value="">--Choose form</option>
						        			<option>1</option>
						        			<option>2</option>
						        			<option>3</option>
						        			<option>4</option>
						        		</select></p>
						        		<p><select name="group" class="form-control" required>
						        			<option value="">--Choose group</option>
						        			<option>DAY</option>
						        			<option>B</option>
						        			<option>C</option>
						        			<option>D</option>
						        			<option>E</option>
						        			<option>F</option>
						        			<option>OPEN</option>
						        			<option>EVENING</option>
						        			<option>G</option>
						        			
						        		</select></p>
						        		<p><select name="term" class="form-control" required>
						        			<option value="">--Choose term</option>
						        			<option>1</option>
						        			<option>2</option>
						        			<option>3</option>
						        		</select></p>
						        		<input type="hidden" name="start_registration" value="true">
						        		<center>
						        			<button class="btn btn-sm btn-info" id="do_me">Next</button> &nbsp;<button type="reset" class="btn btn-sm btn-default">Cancel</button>
						        		</center>
						        	</form>
						        </div>
						        <div class="w3-col m8 w3-padding" id="reg_container">
						        	<br><br><br>
						        	<center>
						        		<i class="fa fa-search-location fa-4x"></i>
						        	</center>
						        </div>
						    </div>
				        </div>
				        <div class="hu" id="view_registered_subjects" style="display: none;">
				        	<h5>View registered subjects</h5>
				        	<div class="w3-row">
				        		<div class="w3-col m4 w3-padding w3-border-right">
						        	<form id="view_registration">
						        		<p><select name="acayear" class="form-control" required>
						        			<option value="">--Choose academic year</option>
						        			<?php
						        			$read = $db->query("SELECT * FROM year");
						        			while ($row = $read->fetchArray()) {
						        				echo "<option value=\"{$row['id']}\">{$row['name']}</option>";
						        			}
						        			?>
						        		</select></p>
						        		<p><select name="form" class="form-control" required>
						        			<option value="">--Choose form</option>
						        			<option>1</option>
						        			<option>2</option>
						        			<option>3</option>
						        			<option>4</option>
						        		</select></p>
						        		<p><select name="group" class="form-control" required>
						        			<option value="">--Choose group</option>
						        			<option>DAY</option>
						        			<option>B</option>
						        			<option>C</option>
						        			<option>D</option>
						        			<option>E</option>
						        			<option>F</option>
						        			<option>OPEN</option>
						        			<option>EVENING</option>
						        			<option>G</option>
						        			
						        		</select></p>
						        		<p><select name="term" class="form-control" required>
						        			<option value="">--Choose term</option>
						        			<option>1</option>
						        			<option>2</option>
						        			<option>3</option>
						        		</select></p>
						        		<input type="hidden" name="view_registration" value="true">
						        		<center>
						        			<button class="btn btn-sm btn-info" id="do_me">View</button>
						        		</center>
						        	</form>
						        </div>
						        <div class="w3-col m8 w3-padding" id="reg_container_view">
						        	<br><br><br>
						        	<center>
						        		<i class="fa fa-search-location fa-4x"></i>
						        	</center>
						        </div>
						        <div class="w3-col m8 w3-padding" id="details_view" style="display: none;"></div>
						    </div>
				        </div>
					</div>
					<div class="w3-row w3-padding-jumbo tt" id="results" style="display: none;">
						<h5>View academic results</h5>
						<div class="w3-row">
				        		<div class="w3-col m4 w3-padding w3-border-right">
						        	<form id="start_upload">
						        		<p><select name="acayear" class="form-control gg" required>
						        			<option value="">--Choose academic year</option>
						        			<?php
						        			$read = $db->query("SELECT * FROM year");
						        			while ($row = $read->fetchArray()) {
						        				echo "<option value=\"{$row['id']}\">{$row['name']}</option>";
						        			}
						        			?>
						        		</select></p>
						        		<p><select name="form" class="form-control gg " required>
						        			<option value="">--Choose form</option>
						        			<option value="1">Form 1</option>
						        			<option value="2">Form 2</option>
						        			<option value="3">Form 3</option>
						        			<option value="4">Form 4</option>
						        		</select></p>
						        		<p><select name="group" class="form-control" required>
						        			<option value="">--Choose group</option>
						        			<option>DAY</option>
						        			<option>B</option>
						        			<option>C</option>
						        			<option>D</option>
						        			<option>E</option>
						        			<option>F</option>
						        			<option>OPEN</option>
						        			<option>EVENING</option>
						        			<option>G</option>
						        		</select></p>
						        		<p><select name="term" class="form-control gg" required>
						        			<option value="">--Choose term</option>
						        			<option value="1">Term 1</option>
						        			<option value="2">Term 2</option>
						        			<option value="3">Term 3</option>
						        		</select></p>
						        		<p>
						        			<select class="form-control" name="mode" id="resultsMode">
						        				<option value="0">End of term</option>
						        			</select>
						        		</p>
						        		<input type="hidden" name="start_upload_service" value="true">
						        		<center>
						        			<button class="btn btn-sm btn-info" id="do_me2">View</button>
						        		</center>
						        	</form>
						        </div>
						        <div class="w3-col m8 w3-padding" id="upload_container">
						        	<br><br><br>
						        	<center>
						        		<i class="fa fa-search-location fa-4x"></i>
						        	</center>
						        </div>
						        <div class="w3-col m8 w3-padding" id="upload_view" style="display: none;"></div>
						    </div>
				        </div>
					</div>
				</div>
			</div>

			<!--THE LOGOUT MODAL -->
			<div class="w3-modal" id="logout_modal">
			    <div class="w3-modal-content w3-card-16 w3-round-large" style="width: 300px;">
			        <div class="w3-padding-large bg-info w3-text-white rounded-top">
			            Confirm logout <i class="fa fa-times w3-right w3-hover-text-red pointer" onclick="$('#logout_modal').slideUp();"></i>
			        </div>
			        <div class="w3-padding">
			            Are you sure you want to logout??
			            <p>&nbsp;</p>
			            <div class="clearfix">
			                <button class="btn btn-sm btn-danger" onclick="window.location = '../logout.php' ">Yes</button>
			                <button class="btn btn-sm float-right" onclick="$('#logout_modal').slideUp();">No</button>
			            </div>
			        </div>
			    </div>
			</div>
			<!--LOGOUT MODAL ENDS HERE -->

			<!--THE PROFILE OPTIONS MODAL -->
			<div class="w3-modal" id="show_profile_options">
			    <div class="w3-modal-content w3-card-16 w3-round-large" style="width: 450px;">
			        <div class="w3-padding-large bg-info w3-text-white rounded-top">
			            Control Panel - <?php echo $_SESSION['fullname']; ?> <i class="fa fa-times w3-right w3-hover-text-red pointer" onclick="$('#show_profile_options').slideUp();"></i>
			        </div>
			        <div class="w3-border-bottom w3-border-blue">
			        	<button>General Info</button>
			        </div>
			        <div class="w3-padding">
			            Are you sure you want to logout??
			            <p>&nbsp;</p>
			            <div class="clearfix">
			                <button class="btn btn-sm btn-danger" onclick="window.location = '../logout.php' ">Yes</button>
			                <button class="btn btn-sm float-right" onclick="$('#logout_modal').slideUp();">No</button>
			            </div>
			        </div>
			    </div>
			</div>


			<div class="w3-modal" id="edit_profile_modal">
				<div class="w3-modal-content w3-card-16 w3-round-large" style="width: 300px;">
					<div class="w3-padding-large bg-info rounded-top w3-text-white">
						Edit Profile <i class="fa fa-times w3-right w3-hover-text-red pointer" onclick="$('#edit_profile_modal').fadeOut();"></i>
					</div>
					<div class="w3-padding-large">
						<form id="edit_profile_form">
							<div id="edit_profile_result"></div>
							<p>Reg number<input type="text" name="phone_edit" value="<?="$user_phone";?>" class="form-control" placeholder="Enter new phone number..." style="border-radius: 0;border-top: none;border-left: none;border-right: none;" required></p>
							<p>Username<input type="text" name="fullname_edit" class="form-control"  value="<?="$username";?>" placeholder="Enter new name.." style="border-radius: 0;border-top: none;border-left: none;border-right: none;" required></p>

							<p>Enter old password<input type="password" name="old_password" class="form-control"  value="<?="$username";?>" placeholder="Enter old password.." style="border-radius: 0;border-top: none;border-left: none;border-right: none;" required></p>
							<p>Enter new password<input type="password" name="new_password" class="form-control"  value="<?="$username";?>" placeholder="Enter new password.." style="border-radius: 0;border-top: none;border-left: none;border-right: none;" required></p>
							<input type="hidden" name="edit_profile" value="true">
							<button class="btn btn-info btn-block" type="submit">Update profile</button>
							<input type="reset" name="" id="edit_profile_reset" style="display: none;">
						</form>
					</div>
				</div>
			</div>
		</div>

<div class="w3-col m2" style="height:calc(100%);background: rgba(0, 0, 0, .30);position: fixed;width: 100%;top: 0;left: 0;display: none;" id="menu-drop" onclick="$(this).hide();">
	<div class="bg-info w3-row w3-border-bottom w3-border-black w3-animate-left" style="height:calc(100%);width: 80%;" onclick="e=>e.stopPropagation();">
		<div class="w3-padding">
			<div class="pb-2" id="heading"><b>School Report System</b></div>
			<div class="py-3">
				<a href="#" class="w3-text-white"><?=$username;?></a>
			</div>
		</div>
		<div class="" id="topLinks">
			<button class="bn pointer w3-padding tp w3-block activeBtn" data="home">Home</button>
			<button class="bn pointer w3-padding tp w3-block" data="register">Register</button>
			<button class="bn pointer w3-padding tp w3-block" data="results">Results</button> 
			<button class="w3-hover-text-red pointer pointer w3-right w3-padding tp w3-block" onclick="$('#logout_modal').show();">Logout</button>
		</div>
	</div>
</div>
</body>
<link rel="stylesheet" type="text/css" href="../media/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="../media/css/dataTables.jqueryui.css">
<link rel="stylesheet" type="text/css" href="../examples/resources/syntax/shCore.css">
<link rel="stylesheet" type="text/css" href="../examples/resources/demo.css">
<script type="text/javascript" language="javascript" src="../media/js/jquery.js"></script>
<script type="text/javascript" language="javascript" src="../media/js/jquery.dataTables.js"></script>
<script type="text/javascript" language="javascript" src="../media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="../examples/resources/syntax/shCore.js"></script>
<script type="text/javascript" language="javascript" src="../examples/resources/demo.js"></script>
<script type="text/javascript" language="javascript" class="init">
	function _(id) {
		return document.getElementById(id);
	}
	
	$(document).ready(function() {
		$('#example').DataTable();

		["leftNav", "rightNav"].map((r=>{
			$('#'+r).height(innerHeight);
		}))
	});

	$(document).ready(function(event) {
		if(screen.width < 500){
			//its mobile remove the padding
			//alert("yeah");
			$('#heading').addClass('w3-padding-large');
			$('#topLinks').addClass('w3-padding-top');
			$('.tt').removeClass('w3-padding-jumbo').addClass('w3-padding');
		}
	});

	$('.bn').on('click',function(event) {
		var targ = $(this).attr('data');
		$('.bn').removeClass('w3-text-brown').removeClass("activeBtn");
		$(this).addClass('w3-text-brown').addClass("activeBtn");
		$('.tt').hide();
		$('#'+targ).fadeIn();
	});

	$('.bh').on('click',function(event) {
		var targ = $(this).attr('data');
		$('.bh').removeClass('bg-info').addClass('w3-light-grey');
		$(this).addClass('bg-info');
		$('.hu').hide();
		$('#'+targ).fadeIn();
	});

	$('#edit_profile_form').on('submit', function(event){
		event.preventDefault();
		var form_data = $(this).serialize();

		$.ajax({
			url: "rest_api.php",
			method: "POST",
			data: form_data,
			success: function(response) {
				Toast(response);
			}
		})
	});

	function Toast(text_input) {
		document.getElementById('toast_modal_content').innerHTML = text_input;

		$('#toast_modal').show();

		var close = function() {
			$('#toast_modal').fadeOut('slow');
		}

		setTimeout(close, 3000);
	}

	function delete_registered_subject(id, name) {
		$.ajax({
			url: "rest_api.php",
			method: "POST",
			data: {delete_registered_subject:"true", id:id},
			success: function(response) {
				$('#hu'+id).remove();
				Toast("The subject <b>"+name+"</b> was removed from the list of selected subjects! However the numbering while change after reload");
			}
		});
	}

	function show_profile_options() {
		$('#show_profile_options').show();
	}

	function view_students_registered(){
		$('#reg_container_view_grand').load("rest_api.php?load_all_registered=true");
		$('#grand_register').show()
	}

	$('#add_student_form').on('submit', function(event) {

		event.preventDefault();
		var form_data = $('#add_student_form').serialize();
		
		
		$.ajax({
			url: "rest_api.php",
			method: "POST",
			data: form_data,
			success: function(response) {
				var obj = JSON.parse(response);

				if (obj.status == true) {
					$('#add_student_result').html('<div class="alert alert-success">'+obj.message+'</div>');
					$('#add_student_reset').click();
					$('#all_students').load("rest_api.php?reload_all_students=true");
				}
				else{
					$('#add_student_result').html('<div class="alert alert-danger">'+obj.message+'</div>');
				}
			}
		});
	});

	function home(num) {
		switch(num){
			case 3:
				$('[data="register"]').click();
				break;

			case 2:
				$('[data="results"]').click();
				break;
		}
	}

	$('#setup_registration').on('submit', function(event) {
		event.preventDefault();
		var form_data = $('#setup_registration').serialize();
		$.ajax({
			url: "rest_api.php",
			method: "POST",
			data: form_data,
			success: function(response) {
				$('#reg_container').html(response);
			}
		});
	});

	$('#start_upload').on('submit', function(event) {
		event.preventDefault();
		var form_data = $('#start_upload').serialize();
		$.ajax({
			url: "rest_api.php",
			method: "POST",
			data: form_data,
			success: function(response) {
				$('#upload_container').html(response);

				$('#upload_view').hide();
				$('#upload_container').fadeIn();
			}
		});
	});

	$('.gg').on('change', function(event) {
		var form = _('start_upload');
		$.ajax({
			url: "rest_api.php",
			method: "GET",
			data: {acayear:form.acayear.value, form:form.form.value, term:form.term.value},
			success: function(response) {
				//Toast(response)
				$('#resultsMode').html(response);
			}
		})
		
	})

	function edit_grades(id) {
		$('#edit_results_content').load("rest_api.php?edit_grades=true&studentId="+id);
		$('#edit_grades_modal').show();
	}

	$('#view_registration').on('submit', function(event) {
		event.preventDefault();
		var form_data = $('#view_registration').serialize();
		$.ajax({
			url: "rest_api.php",
			method: "POST",
			data: form_data,
			success: function(response) {
				$('#reg_container_view').html(response);

				$('#details_view').hide();
				$('#reg_container_view').fadeIn();
			}
		});
	});

	function search_student_name(input, div) {
		var text = $('#'+input).val();
		$.ajax({
			url: "rest_api.php",
			method: "POST",
			data: {search_text:text, searching:"true"},
			success: function(response) {
				$('#'+div).html(response);
			}
		});
	}

	function put_on_desk(id){
		$.ajax({
			url: "rest_api.php",
			method: "POST",
			data: {studentId:id, put_on_desk:"true"},
			success: function(response) {
				$('#reg_container').html(response);
			}
		});
	}

	function see_selected_subjects(event) {
		event.preventDefault();

		var form_data = $('#myForm').serialize();
		$.ajax({
			url: "rest_api.php",
			method: "POST",
			data: form_data,
			success: function(response) {
				$('#reg_container').html(response);
			}
		});
	}

	function view_subjects(id) {
		$.ajax({
			url: "rest_api.php",
			method: "POST",
			data: {studentId:id, view_subjects_task:"true"},
			success: function(response) {
				$('#details_view').html(response);
				$('#reg_container_view').hide();
				$('#details_view').fadeIn();
			}
		});
	}

	function upload_results(id) {
		$.ajax({
			url: "rest_api.php",
			method: "POST",
			data: {studentId:id, upload_results_task:"true"},
			success: function(response) {
				$('#upload_view').html(response);
				$('#upload_container').hide();
				$('#upload_view').fadeIn();
			}
		});
	}

	function save_scores(event) {
		event.preventDefault();

		var form_data = $('#adding_score').serialize();
		$.ajax({
			url: "rest_api.php",
			method: "POST",
			data: form_data,
			success: function(response) {
				$('#upload_view').html(response);
				$('#upload_view').fadeIn();
			}
		});
	}

	function edit_scores(event) {
		event.preventDefault();

		var form_data = $('#editing_score').serialize();
		$.ajax({
			url: "rest_api.php",
			method: "POST",
			data: form_data,
			success: function(response) {
				$('#edit_grades_modal').hide();
				$('#upload_view').html(response);
				$('#upload_view').fadeIn();
			}
		});
	}
</script>
</html>