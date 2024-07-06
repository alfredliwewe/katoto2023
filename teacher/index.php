<?php
session_start();
$db = new sqlite3("../database.db");
require '../objects.php';
require '../config.php';

$system = new System($db);
if (isset($_SESSION['user_id'])) {
	$user_id = $_SESSION['user_id'];

	$sql = $db->query("SELECT * FROM staff WHERE id = '$user_id' AND role = 'teacher' ");
	$data = $sql->fetchArray();
	if ($data) {
		$username = $data['fullname'];
		$user_phone = $data['phone'];
	}
	else{
		header("Loction: ../logout.php");
	}
}
else{
	header("Loction: ../logout.php");
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Teachers Portal</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script type="text/javascript" src="js/stopusers.js"></script>
	<!--====== Default CSS ======-->
	<link rel="stylesheet" href="../assets/css/default.css">

	<!--====== Style CSS ======-->
	<link rel="stylesheet" href="../assets/css/style.css">
	<link rel="stylesheet" type="text/css" href="../w3css/w3.css">
	<link rel="stylesheet" type="text/css" href="../vendor/bootstrap/css/bootstrap.min.css">
	<script type="text/javascript" src="../vendor/jquery/jquery.min.js"></script>
	<link rel="stylesheet" type="text/css" href="../fontawesome/css/all.min.css">
	<script type="text/javascript" src="../vendor/bootstrap/js/bootstrap.bundle.js"></script>
	<script type="text/javascript" src="../vendor/bootstrap/js/bootstrap.min.js"></script>

	<link rel="stylesheet" type="text/css" href="../resources/rodz.css">
	<script type="text/javascript" src="../resources/rodz.js"></script>
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
	<div class="w3-row">
		<div class="w3-col m2 bg-info w3-text-white" id="leftNav" style="overflow-y: auto;">
			<br>
			<center>
				<img src="../img/<?=$config['logo'];?>" width="60">
			</center>
			<div class="w3-padding-left">
				<br>
				<button class="w3-padding bn pointer tp w3-block activeBtn" data="home"><i class="fa fa-home"></i> Home</button>
				<button class="w3-padding bn pointer tp w3-block" data="register"><i class="fa fa-user-check"></i> Register</button>
				<button class="w3-padding bn pointer tp w3-block" data="results"><i class="fa fa-cloud-upload-alt"></i> Upload Results</button>
				<button class="w3-padding w3-hover-text-red pointer pointer w3-right tp w3-block" onclick="$('#logout_modal').show();"><i class="fa fa-sign-out-alt"></i> Logout</button>
			</div>
		</div>
		<div class="w3-col m10" id="rightNav" style="overflow-y: auto;">
<div class="w3-row">
	<div class="w3-col m12">
		<div class="w3-row w3-padding-jumbo tt" id="home">
			<div>
				<center><img src="../img/<?=$config['logo'];?>" width="60"> <br><br><font class="w3-xxlarge"> <?=$config['name'];?></font></center>
			</div>
			<h1>&nbsp;</h1>
			<div class="w3-col m4 pointer w3-hover-text-blue" onclick="home(1)">
				<center>
					<i class="fa fa-users fa-4x"></i>
					<br><br>
					<h3>Students</h3>
				</center>
			</div>
			<div class="w3-col m4 pointer w3-hover-text-blue" onclick="home(2)">
				<center>
					<i class="fa fa-user-graduate fa-4x"></i>
					<br><br>
					<h3>Upload Results</h3>
				</center>
			</div>
			<div class="w3-col m4 pointer w3-hover-text-blue" onclick="$('#edit_profile_modal').show();">
				<center>
					<i class="fa fa-user-edit fa-4x"></i>
					<br><br>
					<h3>Edit Profile</h3>
				</center>
			</div>
		</div>
		<!--View registered students starts here-->
		<div class="w3-row w3-padding-jumbo tt" id="register" style="display: none;">
			<h5>View registered students</h5>
			<div class="w3-border-bottom w3-border-blue">
				<button class="w3-btn w3-small btn-info bh" data="all">All</button><button class="w3-btn w3-small w3-light-grey bh" data="register_subjects">Register Subjects</button><button class="w3-btn w3-small w3-light-grey bh" data="view_registered_subjects">View Registration</button><button class="w3-btn w3-small w3-light-grey bh" data="marking_template">Download marking template</button>  <button class="w3-btn w3-small w3-light-grey bh" data="view_registered_subjects">View particulars</button>
			</div><br>
			<div class="hu" id="all">
				<table id="example" class="display" style="width:60%">
					<thead>
						<tr>
							<th>#</th><th>Reg Number</th><th>Fullname</th><th>Church</th><th>Village</th><th>Guardian</th>
						</tr>
					</thead>
					<tbody id="all_students">
						<?php
						$read = $db->query("SELECT * FROM student");
						$i = 1;
						while ($row = $read->fetchArray()) {
							echo "<tr><td>$i</td><td>{$row['regnumber']}</td><td>{$row['fullname']}</td><td>{$row['church']}</td><td>{$row['village']}</td><td>{$row['guardian']}</td></tr>";
							$i += 1;
						}
						?>
	        		</tbody>
	        	</table>
	        </div>
	        <div class="hu" id="register_subjects" style="display: none;">
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
			        		<p><select name="term" class="form-control" required>
			        			<option value="">--Choose term</option>
			        			<option value="1">Term 1</option>
			        			<option value="2">Term 2</option>
			        			<option value="3">Term 3</option>
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
			        		<i class="fa fa-search-location fa-4x w3-opacity"></i>
			        	</center>
			        </div>
			    </div>
	        </div>
	        <div class="hu" id="marking_template" style="display: none;">
	        	<h5>Download marking template for specific course</h5>
	        	<div class="w3-row">
	        		<div class="w3-col m4 w3-padding w3-border-right">
			        	<form id="setup_template">
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
			        		<p><select name="term" class="form-control" required>
			        			<option value="">--Choose term</option>
			        			<option value="1">Term 1</option>
			        			<option value="2">Term 2</option>
			        			<option value="3">Term 3</option>
			        		</select></p>
			        		<p><select name="subject" class="form-control" required>
			        			<option value="">--Choose subject</option>
			        			<?php
			        			$read = $db->query("SELECT * FROM subject");
			        			while ($row = $read->fetchArray()) {
			        				echo "<option value=\"{$row['id']}\">{$row['name']}</option>";
			        			}
			        			?>
			        		</select></p>
			        		<input type="hidden" name="start_template" value="true">
			        		<center>
			        			<button class="btn btn-sm btn-info" id="do_me">Start</button> &nbsp;<button type="reset" class="btn btn-sm btn-default">Cancel</button>
			        		</center>
			        	</form>
			        </div>
			        <div class="w3-col m8 w3-padding" id="template_container">
			        	<br><br><br>
			        	<center>
			        		<i class="fa fa-search-location fa-4x w3-opacity"></i>
			        	</center>
			        </div>
			    </div>
	        </div>
	        
	         
	        <div class="hu" id="view_registered_subjects" style="display: none;">
	        	<h5>View registered students</h5>
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
			        		<p><select name="term" class="form-control" required>
			        			<option value="">--Choose term</option>
			        			<option value="1">Term 1</option>
			        			<option value="2">Term 2</option>
			        			<option value="3">Term 3</option>
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
			        		<i class="fa fa-search-location fa-4x w3-opacity"></i>
			        	</center>
			        </div>
			        <div class="w3-col m8 w3-padding" id="details_view" style="display: none;"></div>
			    </div>
	        </div>


	        
		</div>
		<!--View registered students ends here-->

		<div class="w3-row w3-padding-jumbo tt" id="results" style="display: none;">
			<h5>Upload academic results</h5>
			<div class="w3-row">
	        		<div class="w3-col m4 w3-padding w3-border-right" id="upload_view1">
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
			        		<p><select name="form" class="form-control gg" required>
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
			        		<i class="fa fa-search-location fa-4x w3-opacity"></i>
			        	</center>
			        </div>
			        <div class="w3-col m8 w3-padding" id="upload_view" style="display: none;"></div>
			    </div>
	        </div>
		</div>
	</div>
</div><p>&nbsp;</p><p>&nbsp;</p>
<!--view registered students Ends here-->




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


<!--THE EDIT GRADE MODAL MODAL -->
<div class="w3-modal" id="edit_grades_modal">
    <div class="w3-modal-content w3-card-16 w3-round-large" style="width: 700px;">
        <div class="w3-padding-large bg-info w3-text-white rounded-top">
            Edit grades <i class="fa fa-times w3-right w3-hover-text-red pointer" onclick="$('#edit_grades_modal').slideUp();"></i>
        </div>
        <div class="w3-padding" id="edit_results_content">
            
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
				<p>Username<input type="text" name="phone_edit" value="<?="$user_phone";?>" class="form-control" placeholder="Enter new phone number..." style="border-radius: 0;border-top: none;border-left: none;border-right: none;" required></p>
				<p>Full name<input type="text" name="fullname_edit" class="form-control"  value="<?="$username";?>" placeholder="Enter new name.." style="border-radius: 0;border-top: none;border-left: none;border-right: none;" required></p>

				<p>Enter old password<input type="password" name="old_password" class="form-control"  value="<?="$username";?>" placeholder="Enter old password.." style="border-radius: 0;border-top: none;border-left: none;border-right: none;" required></p>
				<p>Enter new password<input type="password" name="new_password" class="form-control"  value="<?="$username";?>" placeholder="Enter new password.." style="border-radius: 0;border-top: none;border-left: none;border-right: none;" required></p>
				<input type="hidden" name="edit_profile" value="true">
				<button class="btn btn-info btn-block" type="submit">Update profile</button>
				<input type="reset" name="" id="edit_profile_reset" style="display: none;">
			</form>
		</div>
	</div>
</div>


<!--THE EDIT GRADE MODAL MODAL -->
<div class="w3-modal" id="grand_register" style="display: none;padding-top: 30px;">
    <div class="w3-modal-content w3-card-16 w3-round-large" style="width: 90%;min-height: 300px;max-height: 500px;overflow-y: auto;">
        <div class="w3-padding-large bg-info w3-text-white rounded-top">
            View registered students <i class="fa fa-times w3-right w3-hover-text-red pointer" onclick="$('#grand_register').slideUp();"></i>
        </div>
        <div class="w3-padding" id="reg_container_view_grand">
            
        </div>
    </div>
</div>

<!--Toast modal -->
<div class="w3-modal" id="toast_modal" style="padding-top: 30px;">
	<div class="w3-modal-content w3-padding-large rounded w3-text-white shadow" style="width: 300px;background: rgba(0,0,0,.56);" id="toast_modal_content">
		Hello World To Our Toast
	</div>
</div>
<div id="reusable"></div></div>
</body>
<link rel="stylesheet" type="text/css" href="../toastify/src/toastify.css">
<script type="text/javascript" src="../toastify/src/toastify.js"></script>
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
	$(document).ready(function() {
		$('#example').DataTable();

		//setting the height of the left Nav
		_('leftNav').style.height = window.innerHeight+"px";
		_('rightNav').style.height = window.innerHeight+"px";
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
		$('.bn').removeClass('activeBtn');
		$(this).addClass('activeBtn');
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

	function fileExtension(filename) {
        var chars = filename.split(".");

        chars.reverse();

        return chars[0].toLowerCase();
    }

    function _(id) {
		return document.getElementById(id);
	}

	var activeExtraTr = null;

	$(document).on('click', '.uploadExtra', function(event) {
		var id = $(this).attr('data');
		activeExtraTr = this.parentElement;
		$('#reusable').load("rest_api.php?uploadStudentExtraExams="+id);
	})

	$(document).on('submit', '#uploadExtraResults', function(event) {
		event.preventDefault();
		var formdata = $(this).serialize();

		$.ajax({
			url: "rest_api.php",
			method: "POST",
			data: formdata,
			success: function(response) {
				$('#reusable').html('');
				Toast(response);
				activeExtraTr.getElementsByTagName('button')[0].innerHTML = "Edit"
				activeExtraTr.innerHTML = "<i class='fa fa-check text-success'></i> done ";
				//$('#reg_tr_'+id).remove();
			}
		})
	})

	function uploadCSV(file) {
		var subject = $('#chooseSubject').val();
		if (subject != "") {
			try{
				var filename = file.name;
				var ext = fileExtension(filename);
				if (ext == "csv") {
					//proceed to upload
					var formdata = new FormData();
		            formdata.append('grades_file', file);
		            formdata.append('subject', subject);
		            var ajax = new XMLHttpRequest();

		            var completeHandler = function(event) {
		            	var response = event.target.responseText;
		            	//alert(response);
		            	$('#reusable').html(response);
		            }
		            //adding event listeners
		            //ajax.upload.addEventListener("progress", progressHandler, false);
		            ajax.addEventListener("load", completeHandler, false);
		            //ajax.addEventListener("error", errorHandler, false);
		            //ajax.addEventListener("abort", abortHandler, false);
		            ajax.open("POST", "rest_api.php");
		            ajax.send(formdata);

				}
				else{
					Toast("Unsupported file type");
				}
			}
			catch(E){
				alert(E.toString());
			}
		}
		else{
			Toast("Please choose a subject");
		}
	}

	function uploadCSVExtra(file) {
		var subject = $('#extraSubject').val();
		Toast("Upload results. Wait a moment...")
		if (subject != "") {
			try{
				var filename = file.name;
				var ext = fileExtension(filename);
				if (ext == "csv") {
					//proceed to upload
					var formdata = new FormData();
		            formdata.append('grades_file_extra', file);
		            formdata.append('subject', subject);
		            var ajax = new XMLHttpRequest();

		            var completeHandler = function(event) {
		            	var response = event.target.responseText;
		            	//alert(response);
		            	$('#reusable').html(response);
		            }
		            //adding event listeners
		            //ajax.upload.addEventListener("progress", progressHandler, false);
		            ajax.addEventListener("load", completeHandler, false);
		            //ajax.addEventListener("error", errorHandler, false);
		            //ajax.addEventListener("abort", abortHandler, false);
		            ajax.open("POST", "rest_api.php");
		            ajax.send(formdata);

				}
				else{
					Toast("Unsupported file type");
				}
			}
			catch(E){
				alert(E.toString());
			}
		}
		else{
			Toast("Please choose a subject");
		}
	}

	$(document).on('click', '.deleteReg', function(event) {
		var id = $(this).attr('data');
		
		$.ajax({
			url: "rest_api.php",
			method: "POST",
			data: {deleteReg:id},
			success: function(response) {
				$('#reg_tr_'+id).remove();
			}
		})
	})


	function confirmGradeUpload() {
		Toast("Please wait...");
		$.ajax({
			url: "rest_api.php",
			method: "POST",
			data: {confirmGradeUpload:"true"},
			success: function(response) {
				//alert(response)
				$('#reusable').html('');
				Toast("Successfully uploaded grades");
			}
		})
	}

	function confirmGradeUploadExtra() {
		Toast("Please wait...");
		$.ajax({
			url: "rest_api.php",
			method: "POST",
			data: {confirmGradeUploadExtra:"true"},
			success: function(response) {
				$('#reusable').html('');
				Toast("Successfully uploaded grades");
			}
		})
	}

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

	function home(num) {
		switch(num){
			case 1:
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

	$('#setup_template').on('submit', function(event) {
		event.preventDefault();
		var form_data = $('#setup_template').serialize();
		$.ajax({
			url: "rest_api.php",
			method: "POST",
			data: form_data,
			success: function(response) {
				$('#template_container').html(response);
			}
		});
	});

	function Toast(text) {
		Toastify({
			text: text,
			gravity: "top",
			position: 'center',
		}).showToast();
	}

	function filterMockName(text, event) {
		var tbody = document.getElementById('mockStudents');
		var trs = tbody.getElementsByTagName('tr');
		text = text.toLowerCase();
		for(var tr of trs){
			var innerText = $(tr).text().toLowerCase();
			if (innerText.indexOf(text) == -1) {
				$(tr).hide();
			}
			else{
				$(tr).show();
			}
		}
	}

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

	$('#start_upload').on('submit', function(event) {
		event.preventDefault();
		var form_data = $('#start_upload').serialize();
		Toast("Loading please wait...");
		$.ajax({
			url: "rest_api.php",
			method: "POST",
			data: form_data,
			success: function(response) {
				$('#upload_container').html(response).removeClass('m8').addClass('m12');

				$('#upload_view1').hide();
				$('#upload_container').fadeIn();
			}
		});
	});

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

	function searchRegisteredStudents(text){
		var all_rows = document.getElementsByClassName('yu');
		text = text.toLowerCase();
		if (text == '') {
			$('.yu').show();
		}
		else{
			for(var i = 0; i < all_rows.length; i++){
				var innerText = $(all_rows[i]).text().toLowerCase();

				if (innerText.indexOf(text) != -1) {
					$(all_rows[i]).show();
				}
				else{
					$(all_rows[i]).hide();
				}
			}
		}
	}

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

	function findTheStudents(text){
		if (text == '') {
			$('.gj').show();
		}
		else{
			//do the search filter
			text = text.toLowerCase();

			var all_trs = document.getElementsByClassName('gj');

			for(var i = 0; i < all_trs.length; i++){
				var innerText = $(all_trs[i]).text().toLowerCase();

				if (innerText.indexOf(text) != -1) {
					$(all_trs[i]).show();
				}
				else{
					$(all_trs[i]).hide();
				}
			}
		}
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
				var modal = new Modal();
				modal.setTitle("Upload results");

				var div = new Div();
				div.addClasses(['w3-padding-large']);
				div.css('height', '600px').css('overflow-y', 'auto');
				modal.addView(div);

				modal.show();
				$(div.view).html(response);
				
				var form = document.getElementById('adding_score');

				form.addEventListener('submit', function(event) {
					event.preventDefault();

					var form_data = $('#adding_score').serialize();
					$.ajax({
						url: "rest_api.php",
						method: "POST",
						data: form_data,
						success: function(response) {
							$('#upload_view').html(response);
							$('#upload_view').fadeIn();
							modal.cancel();
						}
					});
				})
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

	function usePrevious() {
		$.post("rest_api.php", {showPreviousTermReg:"true"}, function(response1, status) {
			
			var modal = new Modal();
			modal.setTitle("Previous term registration");
			modal.setWidth(1100);
			modal.show();

			var div = new Div();
			div.addClasses(['w3-padding-large']);
			div.css('height', '600px').css('overflow-y', 'auto');
			modal.addView(div);

			$(div.view).html(response1);
			
			var btn = document.getElementById('confirmRegistration');

			btn.addEventListener('click', function(event) {
				modal.close();
				Toast("Please wait");

				$.post("rest_api.php", {confirmPreviousTermReg:"true"}, function(response, status) {
					Toast(response);
				})
			})
		})
	}
</script>
</html>