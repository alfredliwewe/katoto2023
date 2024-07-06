<?php
session_start();

$db = new sqlite3("../database.db");
require 'functions.php';

if (isset($_POST['add_student'], $_POST['reg_number'], $_POST['fullname'])) {
	$reg_number = $db->escapeString($_POST['reg_number']);
	$fullname = $db->escapeString($_POST['fullname']);

	$password = md5("1234");

	//check reg number first
	$sql = $db->query("SELECT * FROM student WHERE regnumber = '$reg_number'");
	$data = $sql->fetchArray();
	if ($data) {
		
		$res = $db->query("INSERT INTO `student`(`id`, `regnumber`, `fullname`, `password`) VALUES (NULL, '$reg_number', '$fullname', '$password')");

		if ($res) {
			echo json_encode(['status' => true, 'message' => 'Successfully registered '.$fullname.' as a new student']);


		}
		else{
			echo json_encode(['status' => false, 'message' => $db->error]);
		}
	}
	else{
		echo json_encode(['status' => false, 'message' => '<b>'.$reg_number.'</b> is already registered']);
	}
}
elseif (isset($_POST['edit_profile'], $_POST['phone_edit'], $_SESSION['user_id'])) {
	# get all data...

	$user_id = $_SESSION['user_id'];

	$phone = $db->escapeString($_POST['phone_edit']);
	$fullname = $db->escapeString($_POST['fullname_edit']);
	$old_password = $db->escapeString($_POST['old_password']);
	$new_password = $db->escapeString($_POST['new_password']);

	//check the old password
	$old_hash = md5($old_password);
	$sql_check = $db->query("SELECT * FROM staff WHERE id = '$user_id' AND password = '$old_hash' ");
	$df = $sql_check->fetchArray();
	if ($data) {
		$pass_hash = md5($new_password);

		$upd = $db->query("UPDATE staff SET phone = '$phone', fullname = '$fullname', password = '$pass_hash' WHERE id = '$user_id' ");

		if ($upd) {
			echo "Successfully edited your account! Please reload the pages to see changes";
		}
		else{
			echo "Failed to update your profile because of ".$db->error;
		}
	}
	else{
		echo "Your old password is incorrect";
	}
}
elseif (isset($_GET['reload_all_students'])) {
	$read = $db->query("SELECT * FROM student");
	$i = 1;
	while ($row = $read->fetchArray()) {
		echo "<tr><td>$i</td><td>{$row['regnumber']}</td><td>{$row['fullname']}</td></tr>";
		$i += 1;
	}
}
elseif (isset($_GET['acayear'], $_GET['form'], $_GET['term'])) {
	$year = (int)trim($_GET['acayear']);
	$form = (int)trim($_GET['form']);
	$term = (int)trim($_GET['term']);
	echo "$year,$form,$term";
	echo "<option value='0'>End of term</option>";
	$read = $db->query("SELECT * FROM exams WHERE year = '$year' AND form = '$form' AND term = '$term' ");
	while ($row = $read->fetchArray()) {
		echo "<option value='{$row['id']}'>{$row['name']}</option>";
	}
}
elseif (isset($_POST['start_registration'], $_POST['term'], $_POST['form'], $_POST['group'], $_POST['acayear'])) {
	$sql1 = $db->query("SELECT * FROM systemctl WHERE name = 'registration'");
	$data = $sql1->fetchArray();
	if ($data['value'] == "true") {
		$_SESSION['term'] = $term = $db->escapeString($_POST['term']);
		$_SESSION['form'] = $form = $db->escapeString($_POST['form']);
		$_SESSION['group'] = $group = $db->escapeString($_POST['group']);
		$_SESSION['acayear'] = $acayear = $db->escapeString($_POST['acayear']);
		?>
		<div class="alert alert-info">
			You are now trying to register subjects to students for academic year <b><?php echo acayear($acayear);?></b>, form <b><?="$form $group";?></b>, term <b><?="$term";?></b>
		</div>
		<?php 
		if ($term != "1") {
			?>
			<div class="alert alert-secondary">
				Would you like to use previous term registration<br>
				<button class="btn btn-info" onclick="usePrevious();">Yes</button>
			</div>
			<?php
		}
		?>
		<input type="text" name="student_name" id="student_name" class="form-control" placeholder="Type student name..." onkeyup="search_student_name('student_name', 'stud_result');"><br>
		<div id="stud_result"></div>
		<?php
	}
	else{
		?>
		<div class="alert alert-danger">
			<center>
				<i class="fa fa-lock fa-4x"></i><br>
				<p>Registration is closed! Please contact the headteacher or system administrator</p>
			</center>
		</div>
		<?php
	}
}
elseif (isset($_POST['confirmPreviousTermReg'])) {
	$term = $_SESSION['term'];
	$form = $_SESSION['form'];
	$group = $_SESSION['group'];
	$acayear = $_SESSION['acayear'];

	$prev_term = $term - 1;

	//do the registration

	$read = $db->query("SELECT * FROM registered WHERE term = '$prev_term' AND form = '$form' AND `group` = '$group' AND year = '$acayear' ");

	//delete the previous records
	$db->query("DELETE FROM registered WHERE term = '$term' AND form = '$form' AND `group` = '$group' AND year = '$acayear' ");
	while ($row = $read->fetchArray()) {
		# insert reg record...

		$ins = $db->query("INSERT INTO `registered`(`id`, `term`, `form`, `subject`, `student`, `year`, `group`) VALUES (NULL, '$term', '$form', '{$row['subject']}', '{$row['student']}', '$acayear', '$group')");
	}

	echo "Successfully copied registration from previous term";
}
elseif (isset($_POST['showPreviousTermReg'])) {
	$term = $_SESSION['term'];
	$form = $_SESSION['form'];
	$group = $_SESSION['group'];
	$acayear = $_SESSION['acayear'];

	$term -= 1;

	$sql = $db->query("SELECT DISTINCT student FROM registered WHERE form = '$form' AND term = '$term' AND year = '$acayear' AND `group` = '$group' ");
	$c = 0;
	while ($r = $sql->fetchArray()) {
		$c += 1;
	}
	$sql = $db->query("SELECT DISTINCT student FROM registered WHERE form = '$form' AND term = '$term' AND year = '$acayear' AND `group` = '$group' ");
	if ($c > 0) {
		?>

		<div class="alert alert-info">Viewing registered students for academic year <b><?php echo acayear($acayear);?></b>, form <b><?="$form $group";?></b>, term <b><?="$term";?></b></div>
		<div class="w3-responsive">
			<div class="w3-padding">
				<button class="w3-margin btn btn-info btn-sm" id="confirmRegistration">Confirm Registration</button>
			</div>
			<table class="w3-table w3-table-all">
				<th>#</th><th>Student Name</th>
				<?php
				$sql = $db->query("SELECT * FROM subject");
				$subjects_array = [];
				while ($row = $sql->fetchArray()) {
					echo "<th>".substr($row['name'], 0,5)."</td>";
					array_push($subjects_array, $row['id']);
				}
				?>
				<tbody>
					<?php
					$i = 1;
					$sql = $db->query("SELECT DISTINCT student FROM registered WHERE form = '$form' AND term = '$term' AND year = '$acayear' AND `group` = '$group'");
					while ($row = $sql->fetchArray()) {
						$studentId = $row['student'];
						$user_sql = $db->query("SELECT * FROM student WHERE id = '$studentId' ");
						$user_data = $user_sql->fetchArray();
						$student_name = $user_data['fullname'];

						$count_sql = $db->query("SELECT * FROM registered WHERE student = '$studentId' AND form = '$form' AND term = '$term' AND year = '$acayear' AND `group` = '$group'");
						$reg_subjects = [];
						while ($yd = $count_sql->fetchArray()) {
							array_push($reg_subjects, $yd['subject']);
						}
						echo "<tr><td>$i</td><td>$student_name</td>";
						for($h = 0; $h < count($subjects_array); $h++){
							if (in_array($subjects_array[$h], $reg_subjects)) {
								echo "<td>".subject($subjects_array[$h])."</td>";
							}
							else{
								echo "<td> - </td>";
							}
						}
						echo "</tr>";
						$i += 1;
					}
					?>
				</tbody>
			</table>
			<br>
		</div>
		<?php
		
	}
	else{
		?>
		<div class="alert alert-danger">
			There is no student for academic year <b><?php echo acayear($acayear);?></b>, form <b><?="$form $group";?></b>, term <b><?="$term";?></b>
		</div>
		<?php
	}
}
elseif (isset($_GET['load_all_registered'])) {
	$term = $_SESSION['term'];
	$form = $_SESSION['form'];
	$group = $_SESSION['group'];
	$acayear = $_SESSION['acayear'];

	$sql = $db->query("SELECT DISTINCT student FROM registered WHERE form = '$form' AND term = '$term' AND year = '$acayear' AND `group` = '$group' ");
	$c = 0;
	while ($r = $sql->fetchArray()) {
		$c += 1;
	}
	$sql = $db->query("SELECT DISTINCT student FROM registered WHERE form = '$form' AND term = '$term' AND year = '$acayear' AND `group` = '$group' ");
	if ($c > 0) {
		?>

		<div class="alert alert-info">Viewing registered students for academic year <b><?php echo acayear($acayear);?></b>, form <b><?="$form $group";?></b>, term <b><?="$term";?></b></div>
		<div class="w3-responsive">
			<div class="w3-padding">
				<a href="download.pdf.php" class="w3-margin btn btn-info btn-sm"><i class="fa fa-arrow-down"></i> Download pdf</a>
			</div>
			<table class="w3-table w3-table-all">
				<th>#</th><th>Student Name</th>
				<?php
				$sql = $db->query("SELECT * FROM subject");
				$subjects_array = [];
				while ($row = $sql->fetchArray()) {
					echo "<th>".substr($row['name'], 0,5)."</td>";
					array_push($subjects_array, $row['id']);
				}
				?>
				<tbody>
					<?php
					$i = 1;
					$sql = $db->query("SELECT DISTINCT student FROM registered WHERE form = '$form' AND term = '$term' AND year = '$acayear' AND `group` = '$group'");
					while ($row = $sql->fetchArray()) {
						$studentId = $row['student'];
						$user_sql = $db->query("SELECT * FROM student WHERE id = '$studentId' ");
						$user_data = $user_sql->fetchArray();
						$student_name = $user_data['fullname'];

						$count_sql = $db->query("SELECT * FROM registered WHERE student = '$studentId' AND form = '$form' AND term = '$term' AND year = '$acayear' AND `group` = '$group'");
						$reg_subjects = [];
						while ($yd = $count_sql->fetchArray()) {
							array_push($reg_subjects, $yd['subject']);
						}
						echo "<tr><td>$i</td><td>$student_name</td>";
						for($h = 0; $h < count($subjects_array); $h++){
							if (in_array($subjects_array[$h], $reg_subjects)) {
								echo "<td>".subject($subjects_array[$h])."</td>";
							}
							else{
								echo "<td> - </td>";
							}
						}
						echo "</tr>";
						$i += 1;
					}
					?>
				</tbody>
			</table>
			<br>
		</div>
		<?php
		
	}
	else{
		?>
		<div class="alert alert-danger">
			There is no student for academic year <b><?php echo acayear($acayear);?></b>, form <b><?="$form $group";?></b>, term <b><?="$term";?></b>
		</div>
		<?php
	}
}
elseif (isset($_POST['view_registration'], $_POST['term'], $_POST['form'], $_POST['group'], $_POST['acayear'])) {
	$_SESSION['term'] = $term = $db->escapeString($_POST['term']);
	$_SESSION['form'] = $form = $db->escapeString($_POST['form']);
	$_SESSION['group'] = $group = $db->escapeString($_POST['group']);
	$_SESSION['acayear'] = $acayear = $db->escapeString($_POST['acayear']);

	$sql = $db->query("SELECT DISTINCT student FROM registered WHERE form = '$form' AND term = '$term' AND year = '$acayear' AND `group` = '$group'");
	$all_ini = 0;

	while ($r = $sql->fetchArray()) {
		$all_ini += 1;
	}
	$sql = $db->query("SELECT DISTINCT student FROM registered WHERE form = '$form' AND term = '$term' AND year = '$acayear' AND `group` = '$group'");
	if ($all_ini > 0) {
		?>
		<div class="alert alert-info">Viewing registered students for academic year <b><?php echo acayear($acayear);?></b>, form <b><?="$form $group";?></b>, term <b><?="$term";?></b></div>
		<div class="w3-row w3-padding-small">
			<div class="w3-half">
				<a class="btn btn-info btn-sm" onclick="view_students_registered();"><i class="fa fa-eye"></i> View</a> <a class="btn btn-info btn-sm" href="download.pdf.php"><i class="fa fa-arrow-down"></i> Download</a>
			</div>
			<div class="w3-half">
				<div class="sign_form_wrapper" style="margin-top: 0;">
	                <div class="single_form" style="margin-top: 0;">
	                    <input type="text" name="searchRegisteredStudents" id="searchRegisteredStudents" onkeyup="searchRegisteredStudents(this.value)" placeholder="Search names" class="form-control">
	                    <i class="fa fa-search"></i>
	                </div>
	            </div>
				
			</div>
		</div>
		<table class="w3-table w3-table-all" border="1">
			<th>#</th><th>Student Name</th><th>Reg number</th><th>Total</th><th>Action</th>
			<?php
			$i = 1;
			while ($row = $sql->fetchArray()) {
				$studentId = $row['student'];
				$user_sql = $db->query("SELECT * FROM student WHERE id = '$studentId' ");
				$user_data = $user_sql->fetchArray();
				$student_name = $user_data['fullname'];
				$regnumber = $user_data['regnumber'];

				$count_sql = $db->query("SELECT COUNT(student) AS countAll FROM registered WHERE student = '$studentId' AND form = '$form' AND term = '$term' AND year = '$acayear' AND `group` = '$group'");
				$df = $count_sql->fetchArray();
				$total_count = $df['countAll'];
				echo "<tr class='yu'><td>$i</td><td>$student_name</td><td>$regnumber</td><td>$total_count</td><td><button class='btn btn-danger btn-sm' onclick=\"view_subjects('$studentId');\"><i class='fa fa-pen'></i> Edit</button> <button class='btn btn-sm btn-info' onclick=\"view_subjects('$studentId');\" style='display:none'> View</button></td></tr>";
				$i += 1;
			}
			?>
		</table>
		<br>
		<?php
		
	}
	else{
		?>
		<div class="alert alert-danger">
			There is no student for academic year <b><?php echo acayear($acayear);?></b>, form <b><?="$form $group";?></b>, term <b><?="$term";?></b>
		</div>
		<?php
	}
}
elseif (isset($_POST['start_template'], $_POST['term'], $_POST['form'], $_POST['group'], $_POST['acayear'])) {
	$_SESSION['term'] = $term = $db->escapeString($_POST['term']);
	$_SESSION['form'] = $form = $db->escapeString($_POST['form']);
	$_SESSION['group'] = $group = $db->escapeString($_POST['group']);
	$_SESSION['acayear'] = $acayear = $db->escapeString($_POST['acayear']);
	$subject = $_SESSION['subject'] = $db->escapeString($_POST['subject']);

	$subjectObj = new subject($db, $subject);

	$sql = $db->query("SELECT DISTINCT student FROM registered WHERE form = '$form' AND term = '$term' AND year = '$acayear' AND `group` = '$group' AND subject = '$subject' ");
	$them = [];
	while ($row = $sql->fetchArray()) {
		array_push($them, $row['student']);
	}

	if (count($them) > 0) {
		echo "<div class='w3-margin alert alert-success'>Download marking template for <b>". acayear($acayear)."</b>, form <b>$form $group</b>, term <b>$term</b> Subject: ".$subjectObj->getName()."</div>";
		?>
		<p>
			<a href="rest_api.php?downloadMarkingTemplate" class="btn btn-sm btn-info">Download</a>
		</p>
		<table class="w3-table-all">
			<thead>
				<th>#</th>
				<th>Reg</th>
				<th>Name</th>
				<th>CA</th>
				<th>ET</th>
			</thead>
			<tbody>
				<?php
				$i = 1;
				foreach ($them as $student_id) {
					$student = new student($db, $student_id);
					echo "<tr><td>$i</td><td>".$student->getReg()."</td><td>".$student->getName()."</td><td></td><td></td></tr>";
					$i++;
				}
				?>
			</tbody>
		</table>
		<br>
		<?php
	}
	else{
		echo "<div class='w3-margin alert alert-danger'>No student is registered for <b>". acayear($acayear)."</b>, form <b>$form $group</b>, term <b>$term</b> Subject: ".$subjectObj->getName()."</div>";
	}
}
elseif (isset($_GET['downloadMarkingTemplate'])) {
	$subject = $_SESSION['subject'];
	$term = $_SESSION['term'];
	$form = $_SESSION['form'];
	$group = $_SESSION['group'];
	$acayear = $_SESSION['acayear'];

	$subjectObj = new subject($db, $subject);
	$string = "Reg number,Student Name,Village,Church,Guardian,Lamwa";

	$sql = $db->query("SELECT DISTINCT student FROM registered WHERE form = '$form' AND term = '$term' AND year = '$acayear' AND `group` = '$group' AND subject = '$subject' ");
	$them = [];
	while ($row = $sql->fetchArray()) {
		$student = new student($db, $row['student']);
		$string .= "\n".trim(trim($student->reg)).",".trim(trim(str_replace("\n", "", $student->getName()))).",".trim(trim(str_replace("\n", "", $student->getVillage()))).",".trim(trim(str_replace("\n", "", $student->getChurch()))).",".trim(trim(str_replace("\n", "", $student->getGuardian()))).",".trim(trim(str_replace("\n", "", $student->getLamwa()))).",,";
	}

	$file = "templates/".$subjectObj->getName()."-form $form-term $term.csv";

	if (file_put_contents($file, $string)) {
	    header('Content-Description: File Transfer');
	    header('Content-Type: application/octet-stream');
	    header('Content-Disposition: attachment; filename="'.basename($file).'"');
	    header('Expires: 0');
	    header('Cache-Control: must-revalidate');
	    header('Pragma: public');
	    header('Content-Length: ' . filesize($file));
	    readfile($file);
	    exit;
	}
	else{
		echo "Failed";
	}
}
elseif (isset($_POST['start_upload_service'], $_POST['term'], $_POST['form'], $_POST['group'], $_POST['acayear'])) {
	$sql1 = $db->query("SELECT * FROM systemctl WHERE name = 'exam_uploading'");
	$data = $sql1->fetchArray();

	$_SESSION['term'] = $term = $db->escapeString($_POST['term']);
	$_SESSION['form'] = $form = $db->escapeString($_POST['form']);
	$_SESSION['group'] = $group = $db->escapeString($_POST['group']);
	$year = $_SESSION['acayear'] = $acayear = $db->escapeString($_POST['acayear']);


	if ($data['value'] == "true") {
		

		if ($_POST['mode'] == "0") {
			# end of term...
		
		$sql = $db->query("SELECT DISTINCT student FROM registered WHERE form = '$form' AND term = '$term' AND year = '$acayear' AND `group` = '$group'");
			$dt = $sql->fetchArray();
			if ($dt) {
				$sql = $db->query("SELECT DISTINCT student FROM registered WHERE form = '$form' AND term = '$term' AND year = '$acayear' AND `group` = '$group'");
				?>
				<div class="alert alert-info">Upload results for academic year <b><?php echo acayear($acayear);?></b>, form <b><?="$form $group";?></b>, term <b><?="$term";?></b><br><br><button class="btn btn-info btn-sm" onclick="$('#upload_container').removeClass('m12').addClass('m8');$('#upload_view1').show();"><i class="fa fa-arrow-left"></i> Choose another class</button></div>
				<div class="w3-row">
					<div class="w3-half">
						<div class="" id="hideUpload" style="display: none;">
							<p>Please choose subject
								<select name="chooseSubject" id="chooseSubject" onchange="$('#file1').click();">
									<option value="">--Choose--</option>
									<?php
									$r = $db->query("SELECT * FROM subject");
									while ($row = $r->fetchArray()) {
										echo "<option value='{$row['id']}'>{$row['name']}</option>";
									}
									?>
								</select>
							</p>
						</div>

						<input type="file" name="file1" id="file1" onchange="uploadCSV(this.files[0]);" style="display: none;" accept=".csv">
						<button class="btn btn-info" onclick="$('#hideUpload').slideToggle();">Upload excel</button>
					</div>
					<div class="w3-half w3-padding-small">
						<input type="text" name="" class="form-control" placeholder="Search student" onkeyup="findTheStudents(this.value);">
					</div>
				</div>
				<table class="w3-table w3-table-all" border="1">
					<th>#</th><th>Student Name</th><th>Reg number</th><th>Subjects</th><th>Action</th>
					<tbody id="allStudentsResultNames">
						<?php
						$i = 1;
						while ($row = $sql->fetchArray()) {
							$studentId = $row['student'];
							$user_sql = $db->query("SELECT * FROM student WHERE id = '$studentId' ");
							$user_data = $user_sql->fetchArray();
							$student_name = $user_data['fullname'];
							$regnumber = $user_data['regnumber'];

							$count_sql = $db->query("SELECT COUNT(student) AS countAll FROM registered WHERE student = '$studentId' AND form = '$form' AND term = '$term' AND year = '$acayear' AND `group` = '$group'");
							$d = $count_sql->fetchArray();
							$total_count = $d['countAll'];
							$count_sql = $db->query("SELECT * FROM registered WHERE student = '$studentId' AND form = '$form' AND term = '$term' AND year = '$acayear' AND `group` = '$group'");
							echo "<tr class='gj'><td>$i</td><td>$student_name</td><td>$regnumber</td><td>$total_count</td><td>".button($studentId, $count_sql, $total_count)."</td></tr>";
							$i += 1;
						}
						?>
					</tbody>
				</table>
				<?php
				
			}
			else{
				?>
				<div class="alert alert-danger">
					There is no student for academic year <b><?php echo acayear($acayear);?></b>, form <b><?="$form $group";?></b>, term <b><?="$term";?></b><br><br><button class="btn btn-info btn-sm" onclick="$('#upload_container').removeClass('m12').addClass('m8');$('#upload_view1').show();"><i class="fa fa-arrow-left"></i> Choose another class</button>
				</div>
				<?php
			}
		}
		else{
			$mode = $_SESSION['mode'] = (int)trim($_POST['mode']);
			$exam_data = $db->query("SELECT * FROM exams WHERE id = '$mode' ")->fetchArray();
			echo "<font class='w3-xlarge'>Exam name: ".$exam_data['name']."</font>";
			?>
			<div class="alert alert-info">
				Upload for academic year <b><?php echo acayear($acayear);?></b>, form <b><?="$form $group";?></b>, term <b><?="$term";?></b><br><br><button class="btn btn-info btn-sm" onclick="$('#upload_container').removeClass('m12').addClass('m8');$('#upload_view1').show();"><i class="fa fa-arrow-left"></i> Choose another class</button>
			</div>
			<div id="excelContainer" style="display: none;">
				<p>Please choose subject <select onchange="$('#extraCSV').click();" id="extraSubject">
					<option value="">--Choose subject--</option>
					<?php
					$h = $db->query("SELECT * FROM subject");
					while ($row = $h->fetchArray()) {
						echo "<option value='{$row['id']}'>{$row['name']}</option>";
					}
					?>
				</select></p><input type="file" name="extraCSV" id="extraCSV" style="display: none;" accept=".csv" onchange="uploadCSVExtra(this.files[0])">
			</div>
			<div id="templateContainer" style="display: none;">
				<p>Please choose subject <select onchange="window.location = 'rest_api.php?downloadExtraTemplate='+this.value;">
					<option value="">--Choose subject--</option>
					<?php
					$h = $db->query("SELECT * FROM subject");
					while ($row = $h->fetchArray()) {
						echo "<option value='{$row['id']}'>{$row['name']}</option>";
					}
					?>
				</select></p>
			</div>
			<div class="w3-padding">
				<button class="btn btn-sm btn-info" onclick="$('#templateContainer').hide();$('#excelContainer').toggle();">Upload excel</button> <button class="btn btn-sm btn-info" onclick="$('#excelContainer').hide();$('#templateContainer').toggle();">Get template</button> <input type="text" name="searchFilter" id="searchFilter" class="form-control" style="display: inline;width: 300px;" onkeyup="filterMockName(this.value, event);" placeholder="Filter students..">
			</div>
			<div class="table-responsive">
				<table class="table table-stripped">
					<thead>
						<th>#</th><th>Reg</th><th>Name</th><th>Subjects</th><th>Action</th>
					</thead>
					<tbody id="mockStudents">
						<?php
						$r = $db->query("SELECT DISTINCT student, student.regnumber, student.fullname FROM registered INNER JOIN student ON registered.student = student.id WHERE form = '$form' AND term = '$term' AND year = '$acayear'");
						$i = 1;
						//echo $db->lastErrorMsg();
						while ($row = $r->fetchArray()) {
							$student_id = $row['student'];
							$reg = $db->query("SELECT COUNT(id) AS count FROM registered WHERE student = '$student_id' AND form = '$form' AND term = '$term' AND year = '$year' ")->fetchArray()['count'];
							$uploaded = $db->query("SELECT COUNT(id) AS count FROM extra_scores WHERE student = '$student_id' AND form = '$form' AND term = '$term' AND year = '$year' ")->fetchArray()['count'];
							if ($uploaded == "0") {
								$rem = "";
							}
							elseif ($reg > $uploaded) {
								$rem = ($reg - $uploaded)." rem";
							}
							else{
								$rem = "<i class='fa fa-check text-success'></i> done ";
							}

							echo "<tr><td>$i</td><td>{$row['regnumber']}</td><td>{$row['fullname']}</td><td>{$row['regnumber']}</td><td>$rem <button class='btn btn-sm btn-info uploadExtra' data='{$row['student']}' id='extra{$row['student']}'>Upload</button></td></tr>";
							$i += 1;
						}
						?>
					</tbody>
				</table>
			</div>
			<?php
		}
	}
	else{
		?>
		<div class="alert alert-danger">
			<center>
				<i class="fa fa-lock fa-4x"></i><br>
				<p>Exam uploading is closed! Please contact the headteacher or system administrator</p>
			</center>
		</div>
		<?php
	}
}
elseif (isset($_GET['downloadExtraTemplate'])) {
	$subject = (int)trim($_GET['downloadExtraTemplate']);
	$term = $_SESSION['term'];
	$form = $_SESSION['form'];
	$group = $_SESSION['group'];
	$year = $_SESSION['acayear'];
	$subject_data = $db->query("SELECT * FROM subject WHERE id = '$subject' ")->fetchArray();

	$read = $db->query("SELECT DISTINCT student, student.regnumber, student.fullname FROM registered INNER JOIN student ON registered.student = student.id WHERE registered.subject = '$subject' AND form = '$form' AND term = '$term' AND year = '$year' ");
	$text = "Reg,Name,Score";
	while ($row = $read->fetchArray()) {
		$text .= "\n".trim(trim(trim($row['regnumber']))).",".trim(trim(trim($row['fullname']))).",";
	}

	$filename = "uploads/".trim(trim($subject_data['name']))." form $form term $term.csv";
	file_put_contents($filename, $text);
	header("location: ".$filename);
}
elseif (isset($_GET['uploadStudentExtraExams'])) {
	$student = $_SESSION['student'] = (int)trim($_GET['uploadStudentExtraExams']);
	$term = $_SESSION['term'];
	$form = $_SESSION['form'];
	$group = $_SESSION['group'];
	$year = $_SESSION['acayear'];
	$student_data = $db->query("SELECT * FROM student WHERE id = '$student' ")->fetchArray();

	$read = $db->query("SELECT * FROM registered INNER JOIN subject ON registered.subject = subject.id WHERE form = '$form' AND term = '$term' AND year = '$year' AND student = '$student'");
	$available_scores = [];
	$del = $db->query("SELECT * FROM extra_scores WHERE student = '$student' AND form = '$form' AND term = '$term' ");
	while ($df = $del->fetchArray()) {
		$available_scores[$df['subject']] = $df['score'];
	}
	?>
	<div class="w3-modal" style="display: block;padding-top: 10px;">
		<div class="w3-modal-content shadow w3-round-large" style="width: 450px;">
			<div class="w3-padding-large bg-info w3-text-white w3-large rounded-top">
				Upload results for <?=$student_data['fullname'];?> <i class="fa fa-times w3-right text-danger pointer" onclick="$('#reusable').html('');"></i>
			</div>
			<div class="w3-padding-large rounded-bottom" style="height: 500px;overflow-y: auto;">
				<form id="uploadExtraResults">
					<input type="hidden" name="saveExtraResults" value="true">
					<?php
					//echo $db->lastErrorMsg();
					//echo count($available_scores);
					while ($row = $read->fetchArray()) {
						if (isset($available_scores[$row['subject']])) {
							$score = $available_scores[$row['subject']];
						}
						else{
							$score = "";
						}
						echo "<p>{$row['name']}<input type='number' class='form-control' name='{$row['subject']}' min='0' max='100' value='$score' required></p>";
					}
					?>
					<p>
						<button class="btn btn-sm btn-info">Save Results</button>
					</p>
				</form>
			</div>
		</div>
	</div>
	<?php
}
elseif (isset($_POST['saveExtraResults'])) {
	$term = $_SESSION['term'];
	$form = $_SESSION['form'];
	$group = $_SESSION['group'];
	$year = $_SESSION['acayear'];
	$student = $_SESSION['student'];
	$exam = $_SESSION['mode'];

	foreach ($_POST as $key => $value) {
		if (is_numeric($key)) {
			$del = $db->query("DELETE FROM extra_scores WHERE student = '$student' AND form = '$form' AND term = '$term' AND subject = '$key' ");
			$ins = $db->query("INSERT INTO extra_scores (id, student, subject, term, form, score, exam, year) VALUES (NULL, '$student', '$key', '$term', '$form', '$value', '$exam', '$year')");
		}
	}

	echo "Successfully added results";
}
elseif (isset($_FILES['grades_file'], $_POST['subject'], $_SESSION['term'], $_SESSION['form'], $_SESSION['group'], $_SESSION['acayear'])) {
	$filename = $_SESSION['filename'] = $_FILES['grades_file']['name'];
	$subject = $_SESSION['subject'] = $_POST['subject'];
	$term = $_SESSION['term'];
	$form = $_SESSION['form'];
	$group = $_SESSION['group'];
	$year = $_SESSION['acayear'];

	if (move_uploaded_file($_FILES['grades_file']['tmp_name'], "uploads/".$filename)) {
		//get all name and put them in the array
		$stu = $db->query("SELECT * FROM student");
		$student_names = [];
		while ($row = $stu->fetchArray()) {
			$student_names[$row['regnumber']] = $row['fullname'];
		}

		//get registered students for the course
		$et = $db->query("SELECT * FROM registered INNER JOIN student ON registered.student = student.id WHERE form = '$form' AND term = '$term' AND subject = '$subject' AND `group` = '$group' ");
		$regi = [];
		while ($row = $et->fetchArray()) {
			array_push($regi, trim($row['regnumber']));
		}
		?>
		<div class="w3-modal" style="display: block;padding-top: 20px;">
			<div class="w3-modal-content">
				<div class="w3-padding-large bg-info w3-text-white clearfix">Preview file - subject id : <?=$subject." __ ";?> <i class="fa fa-times float-right" onclick="$('#reusable').html('');"></i></div>
				<div class="w3-padding" style="max-height: 400px; overflow-y: auto;">
					<table class="w3-table-all">
						<thead>
							<th>#</th><th>Reg</th><th>Name</th><th>Score</th><th>End Term</th><th>Cont Assmt</th><th>Registration</th>
						</thead>
						<tbody>
							<?php
							$text = file_get_contents("uploads/".$filename);

							$chars = explode("\n", $text);
							for ($i=0; $i < count($chars); $i++) { 
								$row = explode(",", $chars[$i]);
								if (count($row) > 3) {
									if ($row[0] != "Reg number") {
										if (isset($student_names[$row[0]])) {
											$name = $student_names[$row[0]];
										}
										else{
											$name = "<font class='text-danger'>Not found</font>";
										}

										if (in_array($row[0], $regi)) {
											$status = "registered";
										}
										else{
											$status = "<font class='text-danger'>Not registered</font>";
										}
										$score = trim($row[2]) + trim($row[3]);
										echo "<tr><td>$i</td><td>{$row[0]}</td><td>$name</td><td>$score</td><td>{$row[3]}</td><td>{$row[2]}</td><td>$status</td></tr>";
									}
								}
							}
							?>
						</tbody>
					</table>
				</div>
				<div class="w3-padding clearfix w3-border-top">
					<button class="btn btn-info btn-sm" onclick="confirmGradeUpload();">Upload only registered</button> <button class="btn btn-danger btn-sm float-right" onclick="$('#reusable').html('');">Close</button>
				</div>
			</div>
		</div>
		<?php
	}
}
elseif (isset($_FILES['grades_file_extra'], $_POST['subject'], $_SESSION['term'], $_SESSION['form'], $_SESSION['group'], $_SESSION['acayear'])) {
	$filename = $_SESSION['filename'] = $_FILES['grades_file_extra']['name'];
	$subject = $_SESSION['subject'] = $_POST['subject'];
	$term = $_SESSION['term'];
	$form = $_SESSION['form'];
	$group = $_SESSION['group'];
	$year = $_SESSION['acayear'];

	if (move_uploaded_file($_FILES['grades_file_extra']['tmp_name'], "uploads/".$filename)) {
		//get all name and put them in the array
		$stu = $db->query("SELECT * FROM student");
		$student_names = [];
		while ($row = $stu->fetchArray()) {
			$student_names[$row['regnumber']] = $row['fullname'];
		}

		//get registered students for the course
		$et = $db->query("SELECT * FROM registered INNER JOIN student ON registered.student = student.id WHERE form = '$form' AND term = '$term' AND subject = '$subject' ");
		$regi = [];
		while ($row = $et->fetchArray()) {
			array_push($regi, trim($row['regnumber']));
		}
		?>
		<div class="w3-modal" style="display: block;padding-top: 20px;">
			<div class="w3-modal-content">
				<div class="w3-padding-large bg-info w3-text-white clearfix">Preview file - subject id : <?=$subject." __ ";?> <i class="fa fa-times float-right" onclick="$('#reusable').html('');"></i></div>
				<div class="w3-padding" style="max-height: 400px; overflow-y: auto;">
					<table class="w3-table-all">
						<thead>
							<th>#</th><th>Reg</th><th>Name</th><th>Score</th><th>Registration</th>
						</thead>
						<tbody>
							<?php
							$text = file_get_contents("uploads/".$filename);

							$chars = explode("\n", $text);
							for ($i=0; $i < count($chars); $i++) { 
								$row = explode(",", $chars[$i]);
								if (count($row) > 2) {
									if ($row[0] != "Reg") {
										if (isset($student_names[$row[0]])) {
											$name = $student_names[$row[0]];
										}
										else{
											$name = "<font class='text-danger'>Not found</font>";
										}

										if (in_array($row[0], $regi)) {
											$status = "registered";
										}
										else{
											$status = "<font class='text-danger'>Not registered</font>";
										}
										$score = (int)trim($row[2]);
										echo "<tr><td>$i</td><td>{$row[0]}</td><td>$name</td><td>$score</td><td>$status</td></tr>";
									}
								}
							}
							?>
						</tbody>
					</table>
				</div>
				<div class="w3-padding clearfix w3-border-top">
					<button class="btn btn-info btn-sm" onclick="confirmGradeUploadExtra();">Upload only registered</button> <button class="btn btn-danger btn-sm float-right" onclick="$('#reusable').html('');">Close</button>
				</div>
			</div>
		</div>
		<?php
	}
}
elseif (isset($_POST['confirmGradeUpload'])) {
	$filename = $_SESSION['filename'];
	$subject = $_SESSION['subject'];
	$term = $_SESSION['term'];
	$form = $_SESSION['form'];
	$group = $_SESSION['group'];
	$year = $_SESSION['acayear'];

	$stu = $db->query("SELECT * FROM student");
	$student_names = [];
	$student_ids = [];
	while ($row = $stu->fetchArray()) {
		$student_names[$row['regnumber']] = $row['fullname'];
		$student_ids[$row['regnumber']] = $row['id'];
	}

	//get registered students for the course
	$et = $db->query("SELECT * FROM registered INNER JOIN student ON registered.student = student.id WHERE form = '$form' AND term = '$term' AND subject = '$subject' AND `group` = '$group' ");
	$regi = [];
	while ($row = $et->fetchArray()) {
		array_push($regi, trim($row['regnumber']));
	}

	$text = file_get_contents("uploads/".$filename);

	$chars = explode("\n", $text);
	for ($i=0; $i < count($chars); $i++) { 
		$row = explode(",", $chars[$i]);
		if (count($row) > 3) {
			if ($row[0] != "Reg number") {
				
				$studentId = $row[0];
				$end = trim($row[3]);
				$cont1 = trim($row[2]);
				$cont2 = 0;
				
				$score = $end + $cont2 + $cont1;

				if (isset($student_ids[$row[0]])) {
					$student_id = $student_ids[$row[0]];
				}
				else{
					$student_id = 0;
				}

				if (in_array($row[0], $regi)) {
					$status = "registered";
					//delete previous
					$del = $db->query("DELETE FROM scores WHERE student = '$student_id' AND subject = '$subject' AND form = '$form'  AND term = '$term' ");
					$ins = $db->query("INSERT INTO scores (id, student, subject, term, form, score, end_term, ca1, ca2, year, `group`) VALUES (NULL, '$student_id', '$subject', '$term', '$form', '$score', '$end', '$cont1', '$cont2', '$year', '$group')");
				}
				else{
					$status = "<font class='text-danger'>Not registered</font>";

				}
			}
		}
	}

	echo "done";
}
elseif (isset($_POST['confirmGradeUploadExtra'])) {
	$filename = $_SESSION['filename'];
	$subject = $_SESSION['subject'];
	$term = $_SESSION['term'];
	$form = $_SESSION['form'];
	$group = $_SESSION['group'];
	$year = $_SESSION['acayear'];
	$exam = $_SESSION['mode'];

	$stu = $db->query("SELECT * FROM student");
	$student_names = [];
	$student_ids = [];
	while ($row = $stu->fetchArray()) {
		$student_names[$row['regnumber']] = $row['fullname'];
		$student_ids[$row['regnumber']] = $row['id'];
	}

	//get registered students for the course
	$et = $db->query("SELECT * FROM registered INNER JOIN student ON registered.student = student.id WHERE form = '$form' AND term = '$term' AND subject = '$subject' ");
	$regi = [];
	while ($row = $et->fetchArray()) {
		array_push($regi, trim($row['regnumber']));
	}

	$text = file_get_contents("uploads/".$filename);

	$chars = explode("\n", $text);
	for ($i=0; $i < count($chars); $i++) { 
		$row = explode(",", $chars[$i]);
		if (count($row) > 2) {
			if ($row[0] != "Reg") {
				
				$studentId = $row[0];
				$score = (int)trim($row[2]);

				if (isset($student_ids[$row[0]])) {
					$student_id = $student_ids[$row[0]];
				}
				else{
					$student_id = 0;
				}

				if (in_array($row[0], $regi)) {
					$status = "registered";
					//delete previous
					$del = $db->query("DELETE FROM extra_scores WHERE student = '$student_id' AND subject = '$subject' AND form = '$form'  AND term = '$term' ");
					$ins = $db->query("INSERT INTO extra_scores (id, student, subject, term, form, score, exam, year) VALUES (NULL, '$student_id', '$subject', '$term', '$form', '$score','$exam', '$year')");
				}
				else{
					$status = "<font class='text-danger'>Not registered</font>";

				}
			}
		}
	}

	echo "done";
}
elseif (isset($_POST['search_text'], $_POST['searching'])) {
	$text = $db->escapeString($_POST['search_text']);

	$sql = $db->query("SELECT * FROM student WHERE fullname LIKE '%{$text}%' OR regnumber LIKE '%{$text}%' LIMIT 20");
	$i = 0;
	while ($row = $sql->fetchArray()) {
		echo "<div class=\"w3-padding w3-border-bottom w3-hover-light-grey\">{$row['fullname']} ({$row['regnumber']})<button class='btn btn-sm btn-outline-info w3-right' onclick=\"put_on_desk('{$row['id']}');\">Go</button></div>";
		$i++;
	}

	if ($i == 0) {
		echo "<font class='text-danger'>No matching name found</font>";
	}
}
elseif (isset($_POST['studentId'], $_POST['put_on_desk'])) {
	$studentId = $db->escapeString($_POST['studentId']);

	$sql = $db->query("SELECT * FROM student WHERE id = '$studentId'");
	$data = $sql->fetchArray();

	$term = $_SESSION['term'];
	$form = $_SESSION['form'];
	$group = $_SESSION['group'];
	$acayear = $_SESSION['acayear'];

	$default_subjects = [
		[1, 2, 3, 4, 5, 6, 7, 14, 15],
		[3, 4, 2, 5, 1]
	];

	if($form < 3){
		$mysubjects = $default_subjects[0];
	}
	else{
		$mysubjects = $default_subjects[1];
	}

	$reg = $data['regnumber'];

	$fullname = $data['fullname'];
	?>
	<div class="alert alert-info">You are now trying to register subjects to <b><?="$fullname";?></b> for academic year <b><?php echo acayear($acayear);?></b>, form <b><?="$form $group";?></b>, term <b><?="$term";?></b></div>
	<button class="btn btn-sm" onclick="$('#do_me').click();">Cancel</button>
	<br>
	Please tick the selected subjects
	<form id="myForm" onsubmit="return see_selected_subjects(event)">
		<input type="hidden" name="student_name" value="<?="$term";?>">
		<input type="hidden" name="student_id" value="<?="$studentId";?>">
		<input type="hidden" name="register_subjects_multi">
		<?php
		$subject_sql = $db->query("SELECT * FROM subject");
		while ($row = $subject_sql->fetchArray()) {
			if(in_array($row['id'], $mysubjects)){
				$subId = $row['id'];
				$subName = $row['name'];
				?>
				<div class="custom-control custom-checkbox w3-padding w3-border-bottom">
				    <input type="checkbox" class="custom-control-input" id="customCheck<?="$subId";?>" name="<?="$subId";?>" checked>
				    <label class="custom-control-label" for="customCheck<?="$subId";?>"><?="$subName";?></label>
				</div>
				<?php
			}
			else{
				$subId = $row['id'];
				$subName = $row['name'];
				?>
				<div class="custom-control custom-checkbox w3-padding w3-border-bottom">
				    <input type="checkbox" class="custom-control-input" id="customCheck<?="$subId";?>" name="<?="$subId";?>">
				    <label class="custom-control-label" for="customCheck<?="$subId";?>"><?="$subName";?></label>
				</div>
				<?php
			}
		}
		?><br>
		<center><input type="submit" class="btn btn-sm btn-info" name="submit" value="Submit"> &nbsp;<input type="reset" name="" class="btn btn-sm"></center>
	</form>
	<?php
}
elseif (isset($_POST['view_subjects_task'], $_POST['studentId'])) {
	$student_id = $db->escapeString($_POST['studentId']);

	$term = $_SESSION['term'];
	$form = $_SESSION['form'];
	$group = $_SESSION['group'];
	$acayear = $_SESSION['acayear'];

	$sql = $db->query("SELECT * FROM registered WHERE student = '$student_id' AND form = '$form' AND term = '$term' AND year = '$acayear' AND `group` = '$group' ");
	$user_sql = $db->query("SELECT * FROM student WHERE id = '$student_id' ");
	$user_data = $user_sql->fetchArray();
	$student_name = $user_data['fullname'];
	?>
	<div class="alert alert-success">
		Viewing registered subjects for <?="$student_name";?>, academic year <b><?php echo acayear($acayear);?></b>, form <b><?="$form $group";?></b>, term <b><?="$term";?></b>
	</div>
	<table class="w3-table w3-table-all">
		<th>#</th><th>Subject</th><th>Action</th>
		<?php
		$i = 1;
		while ($row = $sql->fetchArray()) {
			echo "<tr id='reg_tr_{$row['id']}'><td>$i</td><td>".subject($row['subject'])."</td><td><a class='pointer text-danger deleteReg' data='{$row['id']}'>Delete</a></td></tr>";
			$i += 1;
		}
		?>
	</table>
	<?php
}
elseif (isset($_POST['deleteReg'])) {
	$id = (int)trim($_POST['deleteReg']);

	$del = $db->query("DELETE FROM registered WHERE id = '$id' ");
	echo "done";
}
elseif (isset($_POST['upload_results_task'], $_POST['studentId'])) {
	$student_id = $db->escapeString($_POST['studentId']);

	$term = $_SESSION['term'];
	$form = $_SESSION['form'];
	$group = $_SESSION['group'];
	$acayear = $_SESSION['acayear'];

	$sql = $db->query("SELECT * FROM registered WHERE student = '$student_id' AND form = '$form' AND term = '$term' AND year = '$acayear' AND `group` = '$group' ");
	$user_sql = $db->query("SELECT * FROM student WHERE id = '$student_id' ");
	$user_data = $user_sql->fetchArray();
	$student_name = $user_data['fullname'];
	?>
	<div class="alert alert-info">
		Uploading results for <?="$student_name";?>, academic year <b><?php echo acayear($acayear);?></b>, form <b><?="$form $group";?></b>, term <b><?="$term";?></b>
	</div>
	Below are subjects that <?="$student_name";?> registered for this term. Please enter respective score<br><br>
	<form id="adding_score">
		<input type="hidden" name="student_id" value="<?="$student_id";?>">
		<input type="hidden" name="student_name" value="<?="$student_name";?>">
		<input type="hidden" name="save_students_grade" value="true">
		<?php
		while ($row = $sql->fetchArray()) {
			echo "<div class='w3-row w3-border-bottom'><div class='w3-col m8 w3-padding-small'><input type='number' min='0' max='60' name='{$row['subject']}' class='form-control' placeholder='".subject($row['subject'])." _ End of Term' required></div><div class='w3-col m4 w3-padding-small'><input type='number' min='0' max='40' name='ca1{$row['subject']}' class='form-control' placeholder='CA' required></div><div class='w3-col m3 w3-padding-small'><input type='number' min='0' max='20' name='ca2{$row['subject']}' class='form-control w3-hide' value='0' placeholder='CA2' required></div></div><br>";
		}
		?>
		<input type="submit" name="submit" value="Save" class="btn btn-info btn-sm">
	</form>
	<?php
}
elseif (isset($_POST['save_students_grade'], $_POST['student_name'], $_POST['student_id'])) {
	$student_name = $db->escapeString($_POST['student_name']);
	$student_id = $db->escapeString($_POST['student_id']);

	$term = $_SESSION['term'];
	$form = $_SESSION['form'];
	$group = $_SESSION['group'];
	$acayear = $_SESSION['acayear'];
	
	$subject_sql = $db->query("SELECT * FROM subject");
	$added = [];
	$already = [];
	while ($row = $subject_sql->fetchArray()) {
		$subId = $row['id'];
		$subName = $row['name'];

		if (isset($_POST[$subId])) {
			//get the grade
			$grade_end = $_POST[$subId];
			$ca1 = $_POST['ca1'.$subId];
			$ca2 = $_POST['ca2'.$subId];

			$grade = $grade_end + $ca1 + $ca2;

			//check if not already available
			$sql_check = $db->query("SELECT * FROM scores WHERE student = '$student_id' AND form = '$form' AND term = '$term' AND year = '$acayear' AND subject = '$subId' AND `group` = '$group'");
			$fg = $sql_check->fetchArray();
			if ($fg) {
				array_push($already, $subName);
			}
			else{
				$ins = $db->query("INSERT INTO `scores`(`id`, `student`, `subject`, `term`, `form`, `score`, `end_term`, `ca1`, `ca2`, `year`, `group`) VALUES (NULL, '$student_id', '$subId', '$term', '$form', '$grade','$grade_end', '$ca1', '$ca2', '$acayear', '$group')");
				array_push($added, $subName);
			}
		}
	}
	?>
	<div class="alert alert-info"><center><i class="fa fa-check text-success fa-3x"></i></center><br>Successfully uploaded marks for <b><?php echo implode(", ", $added);?></b> to <b><?="$student_name";?></b> for academic year <b><?php echo acayear($acayear);?></b>, form <b><?="$form $group";?></b>, term <b><?="$term";?></b></div>
	<?php
	if (count($already) > 0) {
		?>
		<div class="alert alert-danger">
			<b><?php echo implode(", ", $already);?></b> were not inserted since they are already added to this student
		</div>
		<?php
	}
	?>
	<button class="btn btn-sm btn-info" onclick="$('#do_me2').click();">Select another one</button>
	<?php
}
elseif (isset($_POST['edit_students_grade'], $_POST['student_name'], $_POST['student_id'])) {
	$student_name = $db->escapeString($_POST['student_name']);
	$student_id = $db->escapeString($_POST['student_id']);

	$term = $_SESSION['term'];
	$form = $_SESSION['form'];
	$group = $_SESSION['group'];
	$acayear = $_SESSION['acayear'];
	
	$subject_sql = $db->query("SELECT * FROM subject");
	$added = [];
	$already = [];
	while ($row = $subject_sql->fetchArray()) {
		$subId = $row['id'];
		$subName = $row['name'];

		if (isset($_POST[$subId])) {
			//get the grade
			$grade_end = $_POST[$subId];
			$ca1 = $_POST['ca1'.$subId];
			$ca2 = $_POST['ca2'.$subId];

			$grade = $grade_end + $ca1 + $ca2;

			//check if not already available
			$sql_check = $db->query("SELECT * FROM scores WHERE student = '$student_id' AND form = '$form' AND term = '$term' AND year = '$acayear' AND subject = '$subId' AND `group` = '$group'");
			$ini = $sql_check->fetchArray();
			if ($ini) {
				$upd = $db->query("UPDATE scores SET score = '$grade', end_term = '$grade_end', ca1 = '$ca1', ca2 = '$ca2' WHERE student = '$student_id' AND form = '$form' AND term = '$term' AND year = '$acayear' AND subject = '$subId' AND `group` = '$group' ");
				if ($upd) {
					array_push($already, $subName);
				}
				
			}
			else{
				$ins = $db->query("INSERT INTO `scores`(`id`, `student`, `subject`, `term`, `form`, `score`, `end_term`, `ca1`, `ca2`, `year`, `group`) VALUES (NULL, '$student_id', '$subId', '$term', '$form', '$grade','$grade_end', '$ca1', '$ca2', '$acayear', '$group')");
				array_push($added, $subName);
			}
		}
	}
	?>
	<div class="alert alert-info"><center><i class="fa fa-check text-success fa-3x"></i></center><br>Successfully updated marks for <b><?php echo implode(", ", $already);?></b> to <b><?="$student_name";?></b> for academic year <b><?php echo acayear($acayear);?></b>, form <b><?="$form $group";?></b>, term <b><?="$term";?></b></div>
	<?php
	if (count($added) > 0) {
		?>
		<div class="alert alert-danger">
			<b><?php echo implode(", ", $added);?></b> were not updated since they are not added to this student
		</div>
		<?php
	}
}
elseif (isset($_GET['edit_grades'], $_GET['studentId'])) {
	$student_id = $db->escapeString($_GET['studentId']);

	$term = $_SESSION['term'];
	$form = $_SESSION['form'];
	$group = $_SESSION['group'];
	$acayear = $_SESSION['acayear'];

	$sql = $db->query("SELECT * FROM registered WHERE student = '$student_id' AND form = '$form' AND term = '$term' AND year = '$acayear' AND `group` = '$group' ");

	$user_sql = $db->query("SELECT * FROM student WHERE id = '$student_id' ");
	$user_data = $user_sql->fetchArray();
	$student_name = $user_data['fullname'];
	?>
	<div class="alert alert-info">
		Editing results for <?="$student_name";?>, academic year <b><?php echo acayear($acayear);?></b>, form <b><?="$form $group";?></b>, term <b><?="$term";?></b>
	</div>
	Below are subjects that <?="$student_name";?> registered for this term. Please enter correct respective score<br><br>
	<form id="editing_score" onsubmit="return edit_scores(event);">
		<input type="hidden" name="student_id" value="<?="$student_id";?>">
		<input type="hidden" name="student_name" value="<?="$student_name";?>">
		<input type="hidden" name="edit_students_grade" value="true">
		<?php
		while ($row = $sql->fetchArray()) {
			$continuos = score(0, $row['subject'], $student_id) + score(1, $row['subject'], $student_id);
			echo "<b>".subject($row['subject'])."</b><div class='w3-row w3-border-bottom'><div class='w3-col m8 w3-padding-small'><input type='number' min='0' max='60' name='{$row['subject']}' class='form-control' placeholder='".subject($row['subject'])." _ End of Term' value='".score(2, $row['subject'], $student_id)."' required></div><div class='w3-col m4 w3-padding-small'><input type='number' min='0' max='40' name='ca1{$row['subject']}' class='form-control' placeholder='CA1' value='".$continuos."' required></div><div class='w3-col m3 w3-hide w3-padding-small'><input type='number' min='0' max='40' name='ca2{$row['subject']}' class='form-control' placeholder='CA2' value='".score(1, $row['subject'], $student_id)."' required></div></div><br>";
		}
		?>
		<input type="submit" name="submit" value="Save Changes" class="btn btn-info btn-sm">
	</form>
	<?php
}
elseif (isset($_POST['register_subjects_multi'], $_POST['student_name'], $_POST['student_id'])) {
	$student_name = $db->escapeString($_POST['student_name']);
	$student_id = $db->escapeString($_POST['student_id']);

	$term = $_SESSION['term'];
	$form = $_SESSION['form'];
	$group = $_SESSION['group'];
	$acayear = $_SESSION['acayear'];
	
	$subject_sql = $db->query("SELECT * FROM subject");
	$selected = [];
	$already = [];
	while ($row = $subject_sql->fetchArray()) {
		$subId = $row['id'];
		$subName = $row['name'];

		if (isset($_POST[$subId])) {
			//check the subject if not already registered
			$sql_check = $db->query("SELECT COUNT(student) AS countAll FROM registered WHERE student = '$student_id' AND form = '$form' AND term = '$term' AND subject = '$subId' AND year = '$acayear' AND `group` = '$group'");
			$iti = $sql_check->fetchArray();
			if ($iti['countAll'] > 0) {
				array_push($already, $subName);
			}
			else{
				$ins = $db->query("INSERT INTO `registered`(`id`, `term`, `form`, `subject`, `student`, `year`, `group`) VALUES (NULL, '$term', '$form', '$subId', '$student_id', '$acayear', '$group')");
				array_push($selected, $subName);
			}
		}
	}
	?>
	<div class="alert alert-info"><center><i class="fa fa-check text-success fa-3x"></i></center><br>Successfully registered <b><?php echo implode(", ", $selected);?></b> to <b><?="$student_name";?></b> for academic year <b><?php echo acayear($acayear);?></b>, form <b><?="$form $group";?></b>, term <b><?="$term";?></b></div>
	<?php
	if (count($already) > 0) {
		?>
		<div class="alert alert-danger">
			<b><?php echo implode(", ", $already);?></b> were not added since they are already registered to this student
		</div>
		<?php
	}
	?>
	<button class="btn btn-sm btn-info" onclick="$('#do_me').click();">Register another one</button>
	<?php
}
else{
	echo "No specific task found";
}
?>