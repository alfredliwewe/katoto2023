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
	if ($sql->num_rows < 1) {
		
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
elseif (isset($_POST['edit_profile'], $_POST['phone_edit'], $_SESSION['student_id'])) {
	# get all data...

	$user_id = $_SESSION['student_id'];

	$phone = $db->escapeString($_POST['phone_edit']);
	$fullname = $db->escapeString($_POST['fullname_edit']);
	$old_password = $db->escapeString($_POST['old_password']);
	$new_password = $db->escapeString($_POST['new_password']);

	//check the old password
	$old_hash = md5($old_password);
	$sql_check = $db->query("SELECT * FROM student WHERE id = '$user_id' AND password = '$old_hash' ");
	if ($sql_check->num_rows > 0) {
		
		
		$pass_hash = md5($new_password);

		$upd = $db->query("UPDATE student SET regnumber = '$phone', fullname = '$fullname', password = '$pass_hash' WHERE id = '$user_id' ");

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
	if ($sql_check->num_rows > 0) {
		
		
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
elseif (isset($_POST['start_registration'], $_POST['term'], $_POST['form'], $_POST['group'], $_POST['acayear'], $_SESSION['student_id'])) {
	$sql1 = $db->query("SELECT * FROM systemctl WHERE name = 'registration'");
	$data = $sql1->fetchArray();
	if ($data['value'] == "true") {
		$_SESSION['term'] = $term = $db->escapeString($_POST['term']);
		$_SESSION['form'] = $form = $db->escapeString($_POST['form']);
		$_SESSION['group'] = $group = $db->escapeString($_POST['group']);
		$_SESSION['acayear'] = $acayear = $db->escapeString($_POST['acayear']);
		$student_id = $_SESSION['student_id'];
		$fullname = student($student_id);
		?>
		<div class="alert alert-info">Select subjects to register yourself <b>(<?="$fullname";?>)</b> for academic year <b><?php echo acayear($acayear);?></b>, form <b><?="$form $group";?></b>, term <b><?="$term";?></b></div>
		<br>
		<form id="myForm" onsubmit="return see_selected_subjects(event)">
			<input type="hidden" name="student_name" value="<?="$term";?>">
			<input type="hidden" name="student_id" value="<?="$student_id";?>">
			<input type="hidden" name="register_subjects_multi">
			<?php
			$subject_sql = $db->query("SELECT * FROM subject");
			while ($row = $subject_sql->fetchArray()) {
				$subId = $row['id'];
				$subName = $row['name'];
				?>
				<div class="custom-control custom-checkbox w3-padding w3-border-bottom">
				    <input type="checkbox" class="custom-control-input" id="customCheck<?="$subId";?>" name="<?="$subId";?>">
				    <label class="custom-control-label" for="customCheck<?="$subId";?>"><?="$subName";?></label>
				</div>
				<?php
			}
			?><br>
			<center><input type="submit" class="btn btn-sm btn-info" name="submit" value="Submit"> &nbsp;<input type="reset" name="" class="btn btn-sm"></center>
		</form>
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
elseif (isset($_GET['acayear'], $_GET['form'], $_GET['term'])) {
	$year = (int)trim($_GET['acayear']);
	$form = (int)trim($_GET['form']);
	$term = (int)trim($_GET['term']);
	//echo "$year,$form,$term";
	echo "<option value='0'>End of term</option>";
	$read = $db->query("SELECT * FROM exams WHERE year = '$year' AND form = '$form' AND term = '$term' ");
	while ($row = $read->fetchArray()) {
		echo "<option value='{$row['id']}'>{$row['name']}</option>";
	}
}
elseif (isset($_GET['load_all_registered'])) {
	$term = $_SESSION['term'];
	$form = $_SESSION['form'];
	$group = $_SESSION['group'];
	$acayear = $_SESSION['acayear'];

	$sql = $db->query("SELECT DISTINCT student FROM registered WHERE form = '$form' AND term = '$term' AND year = '$acayear' AND `group` = '$group'");
	if ($sql->num_rows > 0) {
		?>

		<div class="alert alert-info">Viewing registered students for academic year <b><?php echo acayear($acayear);?></b>, form <b><?="$form $group";?></b>, term <b><?="$term";?></b></div>
		<div class="w3-responsive">
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
			<a href="download.pdf.php" class="w3-margin btn btn-info btn-sm"><i class="fa fa-arrow-down"></i> Download pdf</a>
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
	$student_id = $db->escapeString($_SESSION['student_id']);

	$term = $_POST['term'];
	$form = $_POST['form'];
	$group = $_POST['group'];
	$acayear = $_POST['acayear'];

	$sql = $db->query("SELECT * FROM registered WHERE student = '$student_id' AND form = '$form' AND term = '$term' AND year = '$acayear' AND `group` = '$group'");
	$d = $sql->fetchArray();
	if ($d) {
		
		$user_sql = $db->query("SELECT * FROM student WHERE id = '$student_id' ");
		$user_data = $user_sql->fetchArray();
		$student_name = $user_data['fullname'];
		?>
		<div class="alert alert-info">
			Viewing registered subjects for <?="$student_name";?>, academic year <b><?php echo acayear($acayear);?></b>, form <b><?="$form $group";?></b>, term <b><?="$term";?></b>
		</div>
		<table class="w3-table w3-table-all">
			<th>#</th><th>Subject</th><th>Action</th>
			<?php
			$i = 1;
			while ($row = $sql->fetchArray()) {
				echo "<tr id='hu{$row['id']}'><td>$i</td><td>".subject($row['subject'])."</td><td><i class=\"fa fa-times w3-hover-text-red pointer\" onclick=\"delete_registered_subject('{$row['id']}', '".subject($row['subject'])."')\"></i></td></tr>";
				$i += 1;
			}
			?>
		</table>
		<?php
	}
	else{
		?>
		<div class="alert alert-danger">You didn't register any subjects for this term</div>
		<?php
	}
}
elseif (isset($_POST['delete_registered_subject'], $_POST['id'])) {
	$reg_id = (int)trim($_POST['id']);

	$del = $db->query("DELETE FROM registered WHERE id = '$reg_id' ");
	echo "done";
}
elseif (isset($_POST['start_upload_service'])) {
	$_SESSION['term'] = $term = $db->escapeString($_POST['term']);
	$_SESSION['form'] = $form = $db->escapeString($_POST['form']);
	$_SESSION['group'] = $group = $db->escapeString($_POST['group']);
	$year = $_SESSION['acayear'] = $acayear = $db->escapeString($_POST['acayear']);
	$_SESSION['mode'] = $mode = (int)$_POST['mode'];

	$student_id = $_SESSION['student_id'];

	$check_sql = $db->query("SELECT * FROM registered WHERE student = '$student_id' AND form = '$form' AND term = '$term' AND year = '$acayear' ");
	$rows = [];
	while($df = $check_sql->fetchArray()){
		array_push($rows, $df);
	}

	if (count($rows) > 0) {
		
		$data = getData("ordered", [
			'form' => $form,
			'term' => $term,
			'year' => $year,
			'student' => $student_id,
			'exam' => $_POST['mode']
		]);

		$student_data = getData("student", ['id' => $student_id]);

		if ($data != null) {
			?>
			<div class="alert alert-info">Viewing results for academic year <b><?php echo acayear($acayear);?></b>, form <b><?="$form $group";?></b>, term <b><?="$term";?></b></div>
			<table class="w3-table w3-table-all" border="1">
				<thead>
					<th>Position</th>
					<th>Student Name</th>
					<?php if($form > 2){?><th>Aggregate</th><?php } ?>
					<th>Subjects</th>
					<th>Average</th>
				</thead>
				<tbody>
					<?php
						echo "<tr><td>{$data['position']}</td><td>{$student_data['fullname']}</td>";
						if($form > 2){
							echo "<td>".$data['points']."</td>";
						}
						echo "<td>{$data['subjects']}</td><td>".$data['average']."</td></tr>";
					?>
				</tbody>
			</table><br>
			<p>Download school report</p>
			<a href="download_pdf.php?mode=<?=$mode;?>" target="_blank" class='btn btn-sm btn-info'><i class="fa fa-arrow-down"></i> Download</a>
			<?php
		}
		else{
			?>
			<div class="alert alert-danger">
				Results have not been uploaded yet
			</div>
			<?php
		}
	}
	else{
		?>
		<div class="alert alert-danger">
			You didn't register any subject for this term! As a result there are no results for this term!!
		</div>
		<?php
	}
}
elseif (isset($_POST['view_subjects_task'], $_POST['studentId'])) {
	$student_id = $db->escapeString($_POST['studentId']);

	$term = $_SESSION['term'];
	$form = $_SESSION['form'];
	$group = $_SESSION['group'];
	$acayear = $_SESSION['acayear'];

	$sql = $db->query("SELECT * FROM registered WHERE student = '$student_id' AND form = '$form' AND term = '$term' AND year = '$acayear' AND `group` = '$group'");
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
			echo "<tr><td>$i</td><td>".subject($row['subject'])."</td><td>$i</td></tr>";
			$i += 1;
		}
		?>
	</table>
	<?php
}
elseif (isset($_POST['upload_results_task'], $_POST['studentId'])) {
	$student_id = $db->escapeString($_POST['studentId']);

	$term = $_SESSION['term'];
	$form = $_SESSION['form'];
	$group = $_SESSION['group'];
	$acayear = $_SESSION['acayear'];

	$sql = $db->query("SELECT * FROM registered WHERE student = '$student_id' AND form = '$form' AND term = '$term' AND year = '$acayear' AND `group` = '$group'");
	$user_sql = $db->query("SELECT * FROM student WHERE id = '$student_id' ");
	$user_data = $user_sql->fetchArray();
	$student_name = $user_data['fullname'];
	?>
	<div class="alert alert-info">
		Uploading results for <?="$student_name";?>, academic year <b><?php echo acayear($acayear);?></b>, form <b><?="$form $group";?></b>, term <b><?="$term";?></b>
	</div>
	Below are subjects that <?="$student_name";?> registered for this term. Please enter respective score<br><br>
	<form id="adding_score" onsubmit="return save_scores(event);">
		<input type="hidden" name="student_id" value="<?="$student_id";?>">
		<input type="hidden" name="student_name" value="<?="$student_name";?>">
		<input type="hidden" name="save_students_grade" value="true">
		<?php
		while ($row = $sql->fetchArray()) {
			echo "<div class='w3-row w3-border-bottom'><div class='w3-col m6 w3-padding-small'><input type='number' min='0' max='60' name='{$row['subject']}' class='form-control' placeholder='".subject($row['subject'])." _ End of Term' required></div><div class='w3-col m3 w3-padding-small'><input type='number' min='0' max='20' name='ca1{$row['subject']}' class='form-control' placeholder='CA1' required></div><div class='w3-col m3 w3-padding-small'><input type='number' min='0' max='20' name='ca2{$row['subject']}' class='form-control' placeholder='CA2' required></div></div><br>";
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
			if ($sql_check->num_rows > 0) {
				array_push($already, $subName);
			}
			else{
				$ins = $db->query("INSERT INTO `scores`(`id`, `student`, `subject`, `term`, `form`, `score`, `end_term`, `ca1`, `ca2`, `year`, `group`) VALUES (NULL, '$student_id', '$subId', '$term', '$form', '$grade','$grade_end', '$ca1', '$ca2', '$acayear', '$group')");
				array_push($added, $subName);
			}
		}
	}
	?>
	<div class="alert alert-info"><center><i class="fa fa-check text-success fa-3x"></i></center><br>Successfully uploaded marks for <b><?php echo implode(", ", $added);?></b> to <b><?="$student_name";?></b> for academic year <b><?php echo acayear($acayear);?></b>, form <b><?="$form $group";?></b>, term <b><?="$term";?></b><br><button class="btn btn-info btn-sm" onclick="edit_grades('<?="$student_id";?>')"><i class="fa fa-pen-alt"></i> Edit</button></div>
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
			if ($sql_check->num_rows > 0) {
				$upd = $db->query("UPDATE scores SET score = '$grade', end_term = '$grade_end', ca1 = '$ca1', ca2 = '$ca2' WHERE student = '$student_id' AND form = '$form' AND term = '$term' AND year = '$acayear' AND subject = '$subId' AND `group` = '$group'");
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
	<div class="alert alert-info"><center><i class="fa fa-check text-success fa-3x"></i></center><br>Successfully updated marks for <b><?php echo implode(", ", $already);?></b> to <b><?="$student_name";?></b> for academic year <b><?php echo acayear($acayear);?></b>, form <b><?="$form $group";?></b>, term <b><?="$term";?></b><br><button class="btn btn-info btn-sm" onclick="edit_grades('<?="$student_id";?>')"><i class="fa fa-pen-alt"></i> Edit</button></div>
	<?php
	if (count($already) > 0) {
		?>
		<div class="alert alert-danger">
			<b><?php echo implode(", ", $added);?></b> were not updated since they are not added to this student
		</div>
		<?php
	}
	?>
	<button class="btn btn-sm btn-info" onclick="$('#do_me2').click();">Select another one</button>
	<?php
}
elseif (isset($_GET['edit_grades'], $_GET['studentId'])) {
	$student_id = $db->escapeString($_GET['studentId']);

	$term = $_SESSION['term'];
	$form = $_SESSION['form'];
	$group = $_SESSION['group'];
	$acayear = $_SESSION['acayear'];

	$sql = $db->query("SELECT * FROM registered WHERE student = '$student_id' AND form = '$form' AND term = '$term' AND year = '$acayear' AND `group` = '$group'");

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
			echo "<b>".subject($row['subject'])."</b><div class='w3-row w3-border-bottom'><div class='w3-col m6 w3-padding-small'><input type='number' min='0' max='60' name='{$row['subject']}' class='form-control' placeholder='".subject($row['subject'])." _ End of Term' value='".score(2, $row['subject'], $student_id)."' required></div><div class='w3-col m3 w3-padding-small'><input type='number' min='0' max='20' name='ca1{$row['subject']}' class='form-control' placeholder='CA1' value='".score(0, $row['subject'], $student_id)."' required></div><div class='w3-col m3 w3-padding-small'><input type='number' min='0' max='20' name='ca2{$row['subject']}' class='form-control' placeholder='CA2' value='".score(1, $row['subject'], $student_id)."' required></div></div><br>";
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
			$sql_check = $db->query("SELECT * FROM registered WHERE student = '$student_id' AND form = '$form' AND term = '$term' AND subject = '$subId' AND year = '$acayear' AND `group` = '$group'");
			$tf = $sql_check->fetchArray();
			if ($tf) {
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
}
else{
	echo "No specific task found";
}
?>