<?php
session_start();
$db = new sqlite3("../database.db");
require '../objects.php';
require '../config.php';


$system = new System($db);
if (isset($_SESSION['user_id'])) {
	$user_id = $_SESSION['user_id'];

	$sql = $db->query("SELECT * FROM staff WHERE id = '$user_id' AND role = 'head' ");
	//$sql = $db->query("SELECT * FROM student WHERE id = '$user_id' AND village = 'village' ");
	$data = $sql->fetchArray();
	if ($data) {
		$username = $data['fullname'];
		$user_phone = $data['phone'];
		//$village = $data['village'];
	}
	else{
		header("Location: ../logout.php");
	}
}
else{
	header("Location: ../logout.php");
}

$years = [];
$r = $db->query("SELECT * FROM year");
while ($row = $r->fetchArray()) {
	$years[$row['id']] = $row['name'];
}

$settings = [];
$r = $db->query("SELECT * FROM systemctl");
while ($row = $r->fetchArray()) {
	$settings[$row['name']] = $row['value'];
}
if (!isset($settings['next_term'])) {
	$settings['next_term'] = "";
}
?>
<!DOCTYPE html>
<html>
<head>
	<title><?=$config['name'];?> - Head Master Portal</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script type="text/javascript" src="js/stopusers.js"></script>
	<!--====== Default CSS ======-->
	<link rel="stylesheet" href="../assets/css/default.css">
	<link rel="icon" href="../img/<?=$config['logo'];?>">

	<!--====== Style CSS ======-->
	<link rel="stylesheet" href="../assets/css/style.css">
	<link rel="stylesheet" type="text/css" href="../w3css/w3.css">
	<link rel="stylesheet" type="text/css" href="../vendor/bootstrap/css/bootstrap.min.css">
	<script type="text/javascript" src="../vendor/jquery/jquery.min.js"></script>
	<link rel="stylesheet" type="text/css" href="../fontawesome/css/all.min.css">
	<link rel="stylesheet" type="text/css" href="../resources/tailwind.css">
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
		.drawer{
			position: fixed;
			top: 0;
			height: calc(100%);
			width: 100%;
			left: 0;
			z-index: 30;
			background: rgba(0, 0, 0, .10);
		}
		.drawer-container{
			position: absolute;
			width: 350px;
			right: 0px;
			background: white;
			height: calc(100%);
			-webkit-animation:animateright 0.4s;animation:animateright 0.4s;
			box-shadow:0 2px 4px 0 rgba(0,0,0,0.16),0 2px 10px 0 rgba(0,0,0,0.12) !important
		}
		.block{
			display: block;
		}
		.bold{
			font-weight: bold;
		}
	</style>
</head>
<body style="padding: 0;">
	<div class="w3-row">
		<div class="w3-col m2 bg-info" id="leftNav" style="height: 500px;overflow-y: auto;">
			<br>
			<center>
				<img src="../img/<?=$config['logo'];?>" width="60">
			</center>
			<div class="w3-padding-left">
				<br>
				<button class="w3-padding tp w3-block activeBtn" data="home"><i class="fa fa-home"></i> Home</button> 
				<button class="w3-padding tp w3-block" data="register"><i class="fa fa-users"></i>Students</button> 
				<button class="w3-padding tp w3-block" data="staff"><i class="fa fa-user-friends"></i> Staff</button> 
				<button class="w3-padding tp w3-block" data="results"><i class="fa fa-graduation-cap"></i> Results</button>
				<button class="w3-padding tp w3-block" data="settings"><i class="fa fa-cogs"></i> Settings</button>
				<button class="w3-padding tpp w3-block text-danger" onclick="logout();"><i class="fa fa-sign-out-alt"></i> Logout</button>
			</div>
		</div>
		<div class="w3-col m10" id="rightNav" style="overflow-y: auto;">

			<!--The home page tab -->
			<div class="w3-row w3-padding-jumbo tt" id="home">
				<div style="padding-top: 40px;padding-bottom: 20px;">
					<center>
						<img src="../img/<?=$config['logo'];?>" width="60"><br><br>
						<font class="w3-xlarge"><?=$config['name'];?></font>
					</center>
				</div>
				<div class="w3-col m4 pointer w3-hover-text-blue w3-padding" onclick="home_tab(1);">
					<div class="w3-light-grey w3-hover-light-blue pointer w3-round-large w3-padding-large">
						<center>
							<i class="fa fa-users fa-4x"></i>
							<br><br>
							<h3>Staff</h3>
						</center>
					</div>
				</div>
				<div class="w3-col m4 pointer w3-hover-text-blue w3-padding" onclick="home_tab(2);">
					<div class="w3-light-grey w3-hover-light-blue pointer w3-round-large w3-padding-large">
						<center>
							<i class="fa fa-users-cog fa-4x"></i>
							<br><br>
							<h3>Students</h3>
						</center>
					</div>
				</div>
				<div class="w3-col m4 pointer w3-hover-text-blue w3-padding" onclick="home_tab(3);">
					<div class="w3-light-grey w3-hover-light-blue pointer w3-round-large w3-padding-large">
						<center>
							<i class="fa fa-outdent fa-4x"></i>
							<br><br>
							<h3>Results</h3>
						</center>
					</div>
				</div>
				<div class="w3-col m4 pointer w3-hover-text-blue w3-padding" onclick="home_tab(4);">
					<div class="w3-light-grey w3-hover-light-blue pointer w3-round-large w3-padding-large">
						<center>
							<i class="fa fa-cog fa-4x"></i>
							<br><br>
							<h3>Settings</h3>
						</center>
					</div>
				</div>
			</div>


			<!--The settings tab pane -->
			<div class="w3-row w3-padding-jumbo tt" id="settings" style="display: none;">
				<h5>Adjust things in the system</h5>
				<div class="w3-border-bottom w3-border-blue">
					<button class="w3-btn w3-small btn-info ch" data="reg_upload">Registration &amp; Exam Uploading</button><button class="w3-btn w3-small w3-light-grey ch" data="attributes">Attributes</button><button class="w3-btn w3-small w3-light-grey ch" data="extraExams">Extra Exams</button><button class="w3-btn w3-small w3-light-grey ch" data="uploadStamp">Upload stamp</button><button class="w3-btn w3-small w3-light-grey" onclick="$('#edit_profile_modal').show();">Edit Profile</button><button class="w3-btn w3-small w3-light-grey" onclick="window.location = 'download_database.php' " style="display:none"><i class="fa fa-arrow-down"></i> Download database</button>
				</div><br>
				<div id="reg_upload" class="cd">
					<div class="w3-padding w3-border-bottom">
						<br>
						Registration status:
						<label class="switch w3-right">On
							<input type="checkbox" id="tab1" onchange="see('tab1');" <?php
							$sql = $db->query("SELECT * FROM systemctl WHERE name = 'registration'");
							$data = $sql->fetchArray();
							if ($data['value'] == "true") {
								echo "checked";
							}?>>
							<span class="slider round"></span>
						</label>
					</div>

					<div class="w3-padding w3-border-bottom">
						<br>
						Uploading exam:
						<label class="switch w3-right">On
							<input type="checkbox" id="tab2" onchange="see('tab2');" <?php
							$sql = $db->query("SELECT * FROM systemctl WHERE name = 'exam_uploading'");
							$data = $sql->fetchArray();
							if ($data['value'] == "true") {
								echo "checked";
							}?>>
							<span class="slider round"></span>
						</label>
					</div>
					<div class="w3-padding w3-border-bottom clearfix">
						<br>
						Delete zero (0) grades <button class="btn btn-info btn-sm float-right" onclick="deleteZero();">Delete</button>
					</div>
				</div>
				<div id="uploadStamp" class="cd" style="display: none;">
					<div class="w3-row">
						<div class="w3-col m4 w3-padding">
							<h5>Upload a stamp</h5>
							<form id="uploadStampForm" method="POST" enctype="multipart/form-data" action="rest_api.php">
								<p><select name="acayear" class="form-control" required>
				        			<option value="">--Choose academic year</option>
				        			<?php
				        			$read = $db->query("SELECT * FROM year");
				        			while ($row = $read->fetchArray()) {
				        				echo "<option value=\"{$row['id']}\">{$row['name']}</option>";
				        			}
				        			?>
				        		</select></p>
				        		<p><select name="term" class="form-control" required>
				        			<option value="">--Choose term</option>
				        			<option>1</option>
				        			<option>2</option>
				        			<option>3</option>
				        		</select></p>
				        		<p>
				        			<input type="file" name="stampImage" accept="image/*" class="form-control">
				        		</p>
				        		<p>
				        			<input type="submit" class="btn btn-info" name="saveStamp" value="Save stamp">
				        		</p>
							</form>
						</div>
						<div class="w3-col m8">
							<h4>Available stamps</h4>
							<table class="w3-table-all">
								<thead>
									<th>#</th>
									<th>Academic Year</th>
									<th>Term</th>
									<th>File</th>
									<th>Action</th>
								</thead>
								<tbody>
									<?php
									$sql = $db->query("SELECT *, stamps.id AS stamp_id FROM stamps JOIN year ON stamps.year = year.id");
									$i = 1;
									while ($row = $sql->fetchArray()) {
										echo "<tr id='stamp_tr_{$row['stamp_id']}'><td>$i</td><td>{$row['name']}</td><td>{$row['term']}</td><td>{$row['file']}</td><td><a class='text-danger pointer deleteStamp' data='{$row['stamp_id']}'>Delete</a></td></tr>";
										$i += 1;
									}
									?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div id="extraExams" class="cd" style="display: none;">
					<div class="w3-row">
						<div class="w3-col m8 w3-padding-small">
							<h5>Available exams</h5>
							<table id="availableExams" class="table-striped">
								<thead>
									<th>#</th><th>Name</th><th>Year</th><th>Term</th><th>Form</th><th>Action</th>
								</thead>
								<tbody id="allExtraExams">
									<?php
									$r = $db->query("SELECT * FROM exams");
									$i = 1;
									while ($row = $r->fetchArray()) {
										?>
										<tr>
											<td><?=$i;?></td>
											<td><?=$row['name'];?></td>
											<td><?=$years[$row['year']];?></td>
											<td><?=$row['term'];?></td>
											<td><?=$row['form'];?></td>
											<td>
												<button class='btn btn-outline-danger btn-sm deleteExam' data='<?=$row['id'];?>'>Delete</button>
												<button class='btn btn-outline-warning btn-sm editExam' data='<?=$row['id'];?>'>Edit</button>
											</td>
										</tr>
										<?php
										$i += 1;
									}
									?>
								</tbody>
							</table>
						</div>
						<div class="w3-col m4 w3-padding">
							<h5>Create new</h5>
							<form id="newExtraExam">
								<p>
									Exam name
									<input type="text" name="name" class="form-control" required>
								</p>
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
				        		<p><select name="term" class="form-control" required>
				        			<option value="">--Choose term</option>
				        			<option value="1">Term 1</option>
				        			<option value="2">Term 2</option>
				        			<option value="3">Term 3</option>
				        		</select></p>
				        		<br>
				        		<p>
				        			<input type="reset" name="reset" style="display: none;">
				        			<button class="btn btn-info">Create Exam</button>
				        		</p>
							</form>
						</div>
					</div>
				</div>
				<div id="attributes" class="cd" style="display: none;">
					<div class="w3-row">
						<div class="w3-col m3 w3-border-right" style="height: 300px;overflow-y: hidden;">
							<button class="w3-btn btn-block btn-info ui" data="academic_year">Academic Years</button>
							<button class="w3-btn btn-block w3-light-grey ui" data="subjects_container">Subjects</button>
						</div>
						<div class="w3-col m9">
							<div class="kl w3-row" id="academic_year">
								<div class="w3-col s12 w3-padding-small">
									<p><button class="btn btn-info btn-sm" onclick="$('#add_year_modal').show();"><i class="fa fa-plus"></i> Create New</button><br></p>
									<table class="w3-table w3-table-all">
										<th>#</th><th>Academic Name</th><th>Fees</th><th>Uniform</th><th>Action</th>
										<tbody id="all_years">
											<?php
											$sql = $db->query("SELECT * FROM year");
											$i = 1;
											while ($row = $sql->fetchArray()) {
												echo "<tr><td>$i</td><td>{$row['name']}</td><td>{$row['fees']}</td><td>{$row['uniform']}</td><td><a class='btn btn-info btn-sm' onclick=\"edit_year('{$row['id']}')\"><i class='fa fa-pen-alt'></i> Edit</a></td></tr>";
												$i += 1;
											}
											?>
										</tbody>
									</table>
									<p>&nbsp;</p>
									
								</div>
								
							</div>
							<div class="kl w3-row" id="subjects_container" style="display: none;">
								<div class="w3-half w3-padding-small">
									<table class="w3-table w3-table-all">
										<th>#</th><th>Subject Name</th><th>Action</th>
										<tbody id="all_subjects">
											<?php
											$sql = $db->query("SELECT * FROM subject");
											$i = 1;
											while ($row = $sql->fetchArray()) {
												echo "<tr><td>$i</td><td>{$row['name']}</td><td><a class='btn btn-info btn-sm' onclick=\"edit_subject('{$row['id']}', '{$row['name']}')\"><i class='fa fa-pen-alt'></i> Edit</a></td></tr>";
												$i += 1;
											}
											?>
										</tbody>
									</table>
								</div>
								<div class="w3-half w3-padding-small sign_form_wrapper">
									<form id="add_subject">
										<div id="subject_result"></div>
										<div class="single_form">
						                    <input type="text" name="new_subject" class="form-control" placeholder="Subject name..">
						                    <i class="fa fa-i-cursor"></i>
						                </div><br>
										<input type="hidden" name="create_subject" value="true">
										<center>
											<button class="btn btn-sm btn-info w3-padding-large">Create subject</button>
										</center>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>


			<!--Register tab pane -->
			<div class="w3-row w3-padding-jumbo tt" id="register" style="display: none;">
				<h5>View registered students</h5>
				<div class="w3-border-bottom w3-border-blue">
					<button class="w3-btn w3-small btn-info">All</button><button class="w3-btn w3-small w3-light-grey" onclick="$('#add_student_modal').show();">Add Student</button><button class="w3-btn w3-small w3-light-grey" onclick="$('#importFile').click();">Import Students</button><button class="w3-btn w3-small w3-red" onclick="deleteSelectedStudents();">Delete selected</button><input type="file" name="importFile" id="importFile" style="display: none;" onchange="uploadStudentsFile(this.files[0]);" accept=".csv">
				</div><br>
				<table id="students_table" class="display" style="width:100%">
					<thead>
						<tr>
							<th><input type="checkbox" onchange="selectStudents(this)"></th>
							<th>#</th>
							<th>Reg Number</th>
							<th>Fullname</th>
							<th>Status</th>
							<th>Action</th>
							<th>Village</th>
							<th>Church</th>
							<th>Gurdian</th>
							<th>Phone</th>
						</tr>
					</thead>
					<tbody id="all_students">
						<?php
						$read = $db->query("SELECT * FROM student");
						$i = 1;
						while ($row = $read->fetchArray()) {
							$row_id = $row['id'];
							?>
							<tr>
								<td>
									<input type="checkbox" class="studentSelect" data="<?=$row['id'];?>">
								</td>
								<td><?=$i;?></td>
								<td><?=$row['regnumber'];?></td>
								<td>
									<a href="#" class="edit_student" data="<?=$row['id'];?>"><?=$row['fullname'];?></a>
								</td>
								<td id="student_status<?=$row_id;?>"><?=$row['status'];?></td>
								<td id="row<?=$row_id;?>">
									<?php
									if ($row['status'] == "deactivated") {
										echo "<button class='btn btn-sm btn-info' onclick=\"activateStudent('$row_id')\">Activate</button>";
									}
									else{
										echo "<button class='btn btn-sm btn-danger' onclick=\"deactivateStudent('$row_id')\">Deactivate</button>";
									}
									?>
								</td>
								<td><?=$row['village'];?></td>
								<td><?=$row['church'];?></td>
								<td><?=$row['guardian'];?></td>
								<td><?=$row['lamwa'];?></td>
							</tr>
							<?php
							$i += 1;
						}
						?>
	        		</tbody>
	        	</table>
			</div>


			<!--Staff tab Pane -->
			<div class="w3-row w3-padding-jumbo tt" id="staff" style="display: none;">
				<h5>Manage staff</h5>
				<div class="w3-border-bottom w3-border-blue">
					<button class="w3-btn w3-small btn-info">All</button><button class="w3-btn w3-small w3-light-grey" onclick="$('#add_teacher_modal').show();">Add Teacher</button>
				</div><br>
				<table id="example6" class="display" style="width:60%">
					<thead>
						<tr>
							<th>#</th><th>Phone Number</th><th>Fullname</th><th>Action</th>
						</tr>
					</thead>
					<tbody id="all_teachers">
						<?php
						$read = $db->query("SELECT * FROM staff WHERE role != 'head'");
						$i = 1;
						while ($row = $read->fetchArray()) {
							echo "<tr><td>$i</td><td>{$row['phone']}</td><td>{$row['fullname']}</td><td><a class='btn btn-info btn-sm' onclick=\"edit_teacher('{$row['id']}')\"><i class='fa fa-pen-alt'></i> Edit</a> <a class='btn btn-danger btn-sm' onclick=\"delete_teacher('{$row['id']}', '{$row['fullname']}')\"><i class='fa fa-trash'></i> Delete</a></td></tr>";
							$i += 1;
						}
						?>
	        		</tbody>
	        	</table>
			</div>


			<!--Results tab pane -->
			<div class="w3-row w3-padding-jumbo tt" id="results" style="display: none;">
				<h5>View academic results</h5>
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
				        			<option>OPEN</option>
				        			<option>EVENING</option>
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
	<?php
	require 'modals.php';
	?>
	<div id="reusable"></div>
</body>
<link rel="stylesheet" type="text/css" href="../toastify/src/toastify.css">
<script type="text/javascript" src="../toastify/src/toastify.js"></script>
<link rel="stylesheet" type="text/css" href="../media/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="../media/css/dataTables.jqueryui.css">
<link rel="stylesheet" type="text/css" href="../examples/resources/syntax/shCore.css">
<link rel="stylesheet" type="text/css" href="../examples/resources/demo.css">
<script src="../dist/sweetalert2.min.js"></script>
<link rel="stylesheet" href="../dist/sweetalert2.min.css">
<script type="text/javascript" language="javascript" src="../media/js/jquery.js"></script>
<script type="text/javascript" language="javascript" src="../media/js/jquery.dataTables.js"></script>
<script type="text/javascript" language="javascript" src="../media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="../examples/resources/syntax/shCore.js"></script>
<script type="text/javascript" language="javascript" src="../examples/resources/demo.js"></script>
<script type="text/javascript" language="javascript" class="init">
	$(document).ready(function() {
		var students_table = $('#students_table').DataTable();
		$('#example6').DataTable();
		$('#availableExams').DataTable();
		//setting the height of the left Nav
		_('leftNav').style.height = window.innerHeight+"px";
		_('rightNav').style.height = window.innerHeight+"px";
		console.log(students_table);
	});

	function Toast(text) {
		Toastify({
			text: text,
			gravity: "top",
			position: 'center',
		}).showToast();
	}

	function _(id) {
		return document.getElementById(id);
	}

	$(document).on('click', '.tp', function(event) {
		$('.tp').removeClass('activeBtn');
		$(this).addClass('activeBtn');

		var id = $(this).attr('data');
		//Toast(id);
		$('.tt').hide();
		$('#'+id).show();
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

	function edit_year(id) {
		$('#edit_year_content').load("rest_api.php?year_id_edit="+id);
		$('#edit_year_modal').show();
	}

	function deleteSelectedStudents() {
		var all = document.getElementsByClassName('studentSelect');
		var selected = [];
		for(var dent of all){
			if (dent.checked == true) {
				selected.push($(dent).attr('data'));
			}
		}

		if (selected.length > 0) {
			Swal.fire({
	            title: 'Delete Students',
	            text: 'Are you sure you want to delete '+selected.length+" student(s)?",
	            icon: 'question',
	            showCancelButton: true,
	            confirmButtonText: 'Yes',
	            cancelButtonText: 'No'
	        }).then((result) => {
	            if (result.value) {
	            	$.ajax({
	            		url: "rest_api.php",
	            		data: {deleteStudents: selected.toString()},
	            		method:"POST",
	            		success: function(response) {
	            			Toast(response);
	            		}
	            	})
	            }
	        });
		}
		else{
			Toast("Please tick some students");
		}
	}

	function delete_the_teacher() {
		var id = $('#del_teacher_id').val();

		$.ajax({
			url: "rest_api.php",
			method: "POST",
			data: {delete_teacher_id:id},
			success: function(response){
				$('#confirm_delete').hide();
				Toast("Teacher deleted successfully");
				$('#all_teachers').load("rest_api.php?reload_all_teachers=true");
			}
		})
	}

	function edit_this_year(event) {
		event.preventDefault();

		var form_data = $('#edit_academic_form').serialize();
		$.ajax({
			url: "rest_api.php",
			method: "POST",
			data: form_data,
			success: function(response) {
				Toast(response);
				$('#all_years').load("rest_api.php?reload_years=true");
			}
		})
	}

	function confirmStudentsUpload() {
		$('#reusable').html('');
		$.ajax({
			url: "rest_api.php",
			method: "POST",
			data: {confirmStudentsUpload:"true"},
			success: function(response) {
				Toast(response);
			}
		})
		
	}

	$(document).on('click', '.edit_student', function(event){
		$('#reusable').load("rest_api.php?open_student_editor="+$(this).attr('data'), function(){
			let form = document.getElementById("edit_student_form");
			form.addEventListener('submit', function(event){
				event.preventDefault();
				$.post("rest_api.php", $(form).serialize(), response=>{
					Toast(response);
					$('#reusable').html('');
					var students_table = $('#students_table').DataTable();
					students_table.destroy();
					$('#all_students').load("rest_api.php?reload_students", function(){
						var students_table = $('#students_table').DataTable();
						//students_table.draw();
					})
				})
			})
		});
	})

	$(document).on('click', '.editExam', function(event){
		$('#reusable').load("rest_api.php?open_exam_editor="+$(this).attr('data'), function(){
			let form = document.getElementById("edit_year_form");
			form.addEventListener('submit', function(event){
				event.preventDefault();

				$.post("rest_api.php", $(form).serialize(), response=>{
					Toast(response);
					$('#reusable').html('');
					var students_table = $('#availableExams').DataTable();
					students_table.destroy();
					$('#allExtraExams').load("rest_api.php?reloadExtraExams", function(){
						var students_table = $('#availableExams').DataTable();
						//students_table.draw();
					})
				})
			})
		});
	})

	$('.bn').on('click',function(event) {
		var targ = $(this).attr('data');
		$('.bn').removeClass('w3-text-brown');
		$(this).addClass('w3-text-brown');
		$('.tt').hide();
		$('#'+targ).fadeIn();
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

	function logout() {
		Swal.fire({
            title: 'Logout',
            text: 'Are you sure you want to logout',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No'
        }).then((result) => {
            if (result.value) {
            	window.location = '../logout.php?logout';
            }
        });
	}

	function edit_subject(id, name) {
		$('#edit_subject_id').val(id);
		$('#edit_subject_name').val(name);
		$('#edit_subject_modal').show();
		$('#edit_subject_name').focus();
	}

	$(document).on('click', '.deleteStamp', function(event) {
		var id = $(this).attr('data');

		$.ajax({
			url:"rest_api.php",
			method:"POST",
			data: {deleteStamp:id},
			success: function(response) {
				$('#stamp_tr_'+id).remove();
			}
		})
	})

	function selectStudents(element) {
		if (element.checked == true) {
			var all = document.getElementsByClassName('studentSelect');
			for(var dent of all){
				dent.checked = "checked";
			}
		}
		else{
			var all = document.getElementsByClassName('studentSelect');
			for(var dent of all){
				dent.checked = "";
			}
		}
	}

	function fileExtension(filename) {
        var chars = filename.split(".");

        chars.reverse();

        return chars[0].toLowerCase();
    }

	function uploadStudentsFile(file) {
		try{
			var filename = file.name;
			var ext = fileExtension(filename);
			if (ext == "csv") {
				//proceed to upload
				var formdata = new FormData();
	            formdata.append('students_file', file);
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

	$('.ch').on('click',function(event) {
		var targ = $(this).attr('data');
		$('.ch').removeClass('btn-info').addClass('w3-light-grey');
		$(this).removeClass('w3-light-grey').addClass('btn-info');
		$('.cd').hide();
		$('#'+targ).fadeIn();
	});

	$('#edit_subject_form').on('submit', function(event){
		event.preventDefault();
		var form_data = $(this).serialize();

		$.ajax({
			url: "rest_api.php",
			method: "POST",
			data: form_data,
			success: function(response) {
				Toast(response);
				$('#all_subjects').load("rest_api.php?reload_subjects=true");
			}
		})
	});

	$('#edit_profile_form').on('submit', function(event){
		event.preventDefault();
		var form_data = $(this).serialize();

		$.ajax({
			url: "rest_api.php",
			method: "POST",
			data: form_data,
			success: function(response) {
				$('#edit_profile_modal').hide();
				Toast(response);
			}
		})
	});

	$('#newExtraExam').on('submit', function(event){
		event.preventDefault();
		var form_data = $(this).serialize();
		var formElement = this;

		$.ajax({
			url: "rest_api.php",
			method: "POST",
			data: form_data,
			success: function(response) {
				$(formElement.reset).click();
				Toast(response);
				$('#allExtraExams').load("rest_api.php?reloadExtraExams");
			}
		})
	});

	function activateStudent(id) {
		$('#hid').load("rest_api.php?activateStudent="+id);
		$('#row'+id).html("<button class='btn btn-sm btn-danger' onclick=\"deactivateStudent('"+id+"')\">Deactivate</button>");
		$('#student_status'+id).html("Active");
	}

	function deactivateStudent(id) {
		$('#hid').load("rest_api.php?deactivateStudent="+id);
		$('#row'+id).html("<button class='btn btn-sm btn-info' onclick=\"activateStudent('"+id+"')\">Activate</button>");
		$('#student_status'+id).html("Deactivated");
		Toast("You have locked the student");
	}

	$('#add_subject').on('submit', function(event){
		event.preventDefault();
		var form_data = $(this).serialize();

		$.ajax({
			url: "rest_api.php",
			method: "POST",
			data: form_data,
			success: function(response) {
				//Toast(response);
				try{
					var obj = JSON.parse(response);
					if (obj.status == true) {
						$('#add_subject_result').html('<div class="alert alert-success">'+obj.message+'</div>');
					}
					else{
						$('#add_subject_result').html('<div class="alert alert-danger">'+obj.message+'</div>');
					}
				}
				catch(E){
					alert(E.toString()+"\n"+response);
				}
			}
		})
	})

	$('#add_academic').on('submit', function(event){
		event.preventDefault();
		var form_data = $(this).serialize();

		$.ajax({
			url: "rest_api.php",
			method: "POST",
			data: form_data,
			success: function(response) {
				try{
					//Toast(response);
					var obj = JSON.parse(response);
					if (obj.status == true) {
						$('#aca_result').html('<div class="alert alert-success">'+obj.message+'</div>');
						$('#all_years').load("rest_api.php?reload_years=true");
					}
					else{
						$('#aca_result').html('<div class="alert alert-danger">'+obj.message+'</div>');
					}
				}
				catch(E){
					alert(E.toString()+"\n"+response);
				}
			}
		})
	});

	$('#edit_next_term_form').on('submit', function(event){
		event.preventDefault();
		var form_data = $(this).serialize();

		$.ajax({
			url: "rest_api.php",
			method: "POST",
			data: form_data,
			success: function(response) {
				Toast(response);
				$('#next_term_modal').hide();
			}
		})
	});

	$('#add_subject').on('submit', function(event){
		event.preventDefault();
		var form_data = $(this).serialize();

		$.ajax({
			url: "rest_api.php",
			method: "POST",
			data: form_data,
			success: function(response) {
				//Toast(response);
				var obj = JSON.parse(response);
				if (obj.status == true) {
					$('#subject_result').html('<div class="alert alert-success">'+obj.message+'</div>');
					$('#all_subjects').load("rest_api.php?reload_subjects=true");
				}
				else{
					$('#subject_result').html('<div class="alert alert-danger">'+obj.message+'</div>');
				}
			}
		})
	});

	$('.ui').on('click',function(event) {
		var targ = $(this).attr('data');
		$('.ui').removeClass('btn-info').addClass('w3-light-grey');
		$(this).removeClass('w3-light-grey').addClass('btn-info');
		$('.kl').hide();
		$('#'+targ).fadeIn();
	});

	function deleteZero() {
		$.ajax({
			url: "rest_api.php",
			method: "POST",
			data: {deleteZero:"true"},
			success: function(response) {
				Toast(response);
			}
		})
	}

	function delete_teacher(id, name) {
		$('#delete_teacher_name').html(name);
		$('#del_teacher_id').val(id);

		$('#confirm_delete').show();
	}

	function edit_teacher(id) {
		//Toast("teacher id is "+id)
		$.ajax({
			url: "rest_api.php",
			method: "POST",
			data: {show_teacher:id},
			success: function(response) {
				//Toast(response);

				var obj = JSON.parse(response);
				$('[name="phone_number_edit"]').val(obj.phone);
				$('[name="fullname_edit"]').val(obj.name);
				$('#edit_teacher').val(id);

				$('#edit_teacher_modal').show();
			}
		})
	}

	$('#add_student_form').on('submit', function(event) {

		event.preventDefault();
		var form_data = $('#add_student_form').serialize();
		
		
		$.ajax({
			url: "rest_api.php",
			method: "POST",
			data: form_data,
			success: function(response) {
				try{
					var obj = JSON.parse(response);

					if (obj.status == true) {
						$('#add_student_result').html('<div class="alert alert-success">'+obj.message+'</div>');
						$('#add_student_reset').click();
						$('#all_students').load("rest_api.php?reload_all_students=true");
					}
					else{
						$('#add_student_result').html('<div class="alert alert-danger">'+obj.message+'</div>');
					}
				}catch(E){
					alert(E.toString()+"\n"+response);
				}
			}
		})
		
	});

	function see(tab) {
		var val = document.getElementById(tab).checked;
		$.ajax({
			url: "rest_api.php",
			method: "POST",
			data: {switch_control:"yes", tab:tab, val:val},
			success: function(response) {
				$('#toast_content').html(response);
				$('#toast_notification').show();
				var close = function() {
					$('#toast_notification').fadeOut();
				}

				setTimeout(close, 4000);
			}
		})
	}

	
	$('#add_teacher_form').on('submit', function(event) {

		event.preventDefault();
		var form_data = $('#add_teacher_form').serialize();
		
		
		$.ajax({
			url: "rest_api.php",
			method: "POST",
			data: form_data,
			success: function(response) {
				try{
					var obj = JSON.parse(response);

					if (obj.status == true) {
						$('#add_teacher_result').html('<div class="alert alert-success">'+obj.message+'</div>');
						$('#add_teacher_reset').click();
						$('#all_teachers').load("rest_api.php?reload_all_teachers=true");
					}
					else{
						$('#add_teacher_result').html('<div class="alert alert-danger">'+obj.message+'</div>');
					}
				}
				catch(E){
					alert(E.toString()+"\n"+response);
				}
			}
		})
		
	});

	$('#edit_teacher_form').on('submit', function(event) {

		event.preventDefault();
		var form_data = $('#edit_teacher_form').serialize();
		
		
		$.ajax({
			url: "rest_api.php",
			method: "POST",
			data: form_data,
			success: function(response) {
				var obj = JSON.parse(response);

				if (obj.status == true) {
					$('#edit_teacher_result').html('<div class="alert alert-success">'+obj.message+'</div>');
					//$('#edit_teacher_reset').click();
					$('#all_teachers').load("rest_api.php?reload_all_teachers=true");
				}
				else{
					$('#edit_teacher_result').html('<div class="alert alert-danger">'+obj.message+'</div>');
				}
			}
		})
		
	});

	$('#start_upload').on('submit', function(event) {
		event.preventDefault();
		var form_data = $('#start_upload').serialize();
		Toast("Loading results, please wait..");
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

	function home_tab(num) {
		switch(num){
			case 1:
				$('[data="staff"]').click();
				break;

			case 2:
				$('[data="register"]').click();
				break;

			case 3:
				$('[data="results"]').click();
				break;

			case 4:
				$('[data="settings"]').click();
				break;
		}
	}

	$(document).on('click', '.drawer', function(event) {
		$(this).remove();
	})

	$(document).on('click', '.drawer-container', function(event) {
		event.stopPropagation();
	})
</script>
</html>