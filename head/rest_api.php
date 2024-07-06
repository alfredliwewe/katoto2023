<?php
session_start();

$db = new sqlite3("../database.db");
require '../teacher/functions.php';

$years = [];
$r = $db->query("SELECT * FROM year");
while ($row = $r->fetchArray()) {
	$years[$row['id']] = $row['name'];
}

if (isset($_POST['add_student'], $_POST['reg_number'], $_POST['fullname'], $_POST['village'],$_POST['church'])) {
	$reg_number = $db->escapeString($_POST['reg_number']);
	$fullname = $db->escapeString($_POST['fullname']);
	$village = $db->escapeString($_POST['village']);
	$church = $db->escapeString($_POST['church']);
	$guardian = $db->escapeString($_POST['guardian']);
                  
	$password = md5("1234");
                   
	//check reg number first
	$sql = $db->query("SELECT * FROM student WHERE regnumber = '$reg_number'");
	$data = $sql->fetchArray();
	if ($data) {
		echo json_encode(['status' => false, 'message' => '<b>'.$reg_number.'</b> is already registered']);
	}
	else{
		$res = $db->query("INSERT INTO `student`(`id`, `regnumber`, `fullname`, `password`, `status`,`village`,`church`,`guardian`) VALUES (NULL, '$reg_number', '$fullname', '$password', 'active','$village','$church','$guardian')");

		if ($res) {
			echo json_encode(['status' => true, 'message' => 'Successfully registered '.$fullname.' as a new student']);
		}
		else{
			echo json_encode(['status' => false, 'message' => $db->error]);
		}
	}
}
elseif (isset($_GET['activateStudent'])) {
	$studentId = (int)trim($_GET['activateStudent']);
	$upd = $db->query("UPDATE student SET status = 'active' WHERE id = '$studentId' ");
	//echo "done";
}
elseif (isset($_FILES['students_file'])) {
	$filename = $_SESSION['filename'] = $_FILES['students_file']['name'];

	if (move_uploaded_file($_FILES['students_file']['tmp_name'], "uploads/".$filename)) {
		$file_contents = file_get_contents("uploads/".$filename);

		$chars = explode("\n", $file_contents);

		?>
		<div class="w3-modal" style="display: block;">
			<div class="w3-modal-content shadow w3-round-large" style="width: 500px;">
				<div class="w3-padding-large bg-info rounded-top">
					Upload names <i class="fa fa-times w3-right text-danger pointer" onclick="$('#reusable').html('')"></i>
				</div>
				<div class="w3-padding-large rounded-bottom">
					<table class="w3-table-all">
						<thead>
							<th>#</th>
							<th>Reg</th>
							<th>Name</th>
						</thead>
						<tbody>
							<?php
							for ($i=0; $i < count($chars); $i++) { 
								$row = explode(",", $chars[$i]);
								if (count($row) > 1) {
									echo "<tr><td>".($i+1)."</td><td>{$row[0]}</td><td>{$row[1]}</td></tr>";
								}
							}
							?>
						</tbody>
					</table>
					<br>
					<p>
						<button class="btn btn-info btn-sm" onclick="confirmStudentsUpload();">Upload</button>
					</p>

				</div>
			</div>
		</div>
		<?php
	}
}
elseif (isset($_POST['confirmStudentsUpload'], $_SESSION['filename'])) {
	$filename = "uploads/".$_SESSION['filename'];

	$file_contents = file_get_contents($filename);
	$chars = explode("\n", $file_contents);

	$query = "INSERT INTO student (id, regnumber, fullname, password, status) VALUES ";
	$password = md5("0000");

	$values = [];

	for ($i=0; $i < count($chars); $i++) { 
		$row = explode(",", $chars[$i]);
		if (count($row) > 1) {
			$reg = $db->escapeString($row[0]); 
			$name = $db->escapeString($row[1]);
			$inner = "(NULL, '$reg', '$name', '$password', 'active')";

			$db->query($query.$inner);
			//array_push($values, $inner);
		}
	}

	/*$res = $db->query($query.implode(", ", $values));
	if ($res) {
		file_put_contents("sql.sql", $query.implode(", ", $values));
		echo "New students are now uploaded";
	}
	else{
		echo $db->lastErrorMsg();
	} */
	echo "New students are now uploaded";
}
elseif (isset($_POST['deleteZero'])) {
	$del = $db->query("DELETE FROM scores WHERE score = '0' ");
	if ($del) {
		echo "Successfully removed the zero grades";
	}
	else{
		echo $db->lastErrorMsg();
	}
}
elseif (isset($_POST['deleteStamp'])) {
	$id = (int)trim($_POST['deleteStamp']);

	$del = $db->query("DELETE FROM stamps WHERE id = '$id' ");
	echo "done";
}
elseif (isset($_FILES['stampImage'])) {
	$filename = $_FILES['stampImage']['name'];

	if(move_uploaded_file($_FILES['stampImage']['tmp_name'], "../img/".$filename)){
		$year = $db->escapeString($_POST['acayear']);
		$term = $db->escapeString($_POST['term']);

		$delete = $db->query("DELETE FROM stamps WHERE year = '$year' AND term = '$term' ");

		$ins = $db->query("INSERT INTO stamps (id, year, term, file) VALUES (NULL, '$year', '$term', '$filename')");
		if ($ins) {
			header("location: index.php?successStamp");
		}
		else{
			echo $db->error;
		}
	}
	else{
		echo "File not uploaded";
	}
}
elseif (isset($_GET['deactivateStudent'])) {
	$studentId = (int)trim($_GET['deactivateStudent']);
	$upd = $db->query("UPDATE student SET status = 'deactivated' WHERE id = '$studentId' ");
	if ($upd) {
		//echo "done";
	}
	else{
		echo $db->error;
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
	$data = $sql_check->fetchArray();
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
elseif (isset($_POST['edit_academic_year'])) {
	# we are editing academic year...
	$year_id = (int)trim($_POST['edit_academic_year']);
	$year_name = $db->escapeString($_POST['academic_year2']);
	$fees = $db->escapeString($_POST['fees_year2']);
	$uniform = $db->escapeString($_POST['uniform2']);

	//now updating
	$upd = $db->query("UPDATE year SET name = '$year_name', fees = '$fees', uniform = '$uniform' WHERE id = '$year_id' ");

	if ($upd) {
		echo "Successfully edited the academic year!!";
	}
	else{
		echo "Failed to edit the academic year!! ".$db->error;
	}
}
elseif (isset($_GET['year_id_edit'])) {
	$year_id = (int)trim($_GET['year_id_edit']);

	$sql = $db->query("SELECT * FROM year WHERE id = '$year_id' ");
	$data = $sql->fetchArray();
	if ($data) {
		# proceed...
		$year_name = $data['name'];
		$fees = $data['fees'];
		$uniform = $data['uniform'];
		?>
		<form id="edit_academic_form" onsubmit="edit_this_year(event)">
			<div id="aca_result"></div>
			<p>Year name<input type="text" name="academic_year2" class="form-control" placeholder="Academic year name.." value="<?="$year_name";?>" required></p>
			<p>Fees<input type="text" name="fees_year2" class="form-control" placeholder="School fees.." value="<?="$fees";?>" required></p>
			<p>Uniform
				<textarea name="uniform2" class="form-control" placeholder="School uniform.." required><?="$uniform";?></textarea>
			</p>
			<input type="hidden" name="edit_academic_year" value="<?="$year_id";?>">
			<center>
				<button class="btn btn-sm btn-info">Edit year</button>
			</center>
		</form>
		<?php
	}
	else{
		?>
		<div class="alert alert-danger">There is no year matching to the id you have sent to us</div>
		<?php
	}
}
elseif (isset($_GET['reload_all_teachers'])) {
	$read = $db->query("SELECT * FROM staff WHERE role != 'head'");
	$i = 1;
	while ($row = $read->fetchArray()) {
		echo "<tr><td>$i</td><td>{$row['phone']}</td><td>{$row['fullname']}</td><td><a class='btn btn-info btn-sm' onclick=\"edit_teacher('{$row['id']}')\"><i class='fa fa-pen-alt'></i> Edit</a> <a class='btn btn-danger btn-sm' onclick=\"delete_teacher('{$row['id']}', '{$row['fullname']}')\"><i class='fa fa-trash'></i> Delete</a></td></tr>";
		$i += 1;
	}
}
elseif (isset($_POST['delete_teacher_id'])) {
	$teacher_id = (int)trim($_POST['delete_teacher_id']);

	$del = $db->query("DELETE FROM staff WHERE id = '$teacher_id' ");
}
elseif (isset($_POST['switch_control'], $_POST['tab'], $_POST['val'])) {
	$tab = $db->escapeString($_POST['tab']);
	$val = $db->escapeString($_POST['val']);

	if ($tab == "tab1") {
		$doer = "registration";

		$upd = $db->query("UPDATE systemctl SET value = '$val' WHERE name = '$doer' ");

		echo "Successfully set registration status to ".$val;
	}
	elseif ($tab == "tab2") {
		$doer = "exam_uploading";

		$upd = $db->query("UPDATE systemctl SET value = '$val' WHERE name = '$doer' ");

		echo "Successfully set examinations uploading status to ".$val;
	}
	else{
		echo " We don't know what to do";
	}
}
elseif (isset($_POST['show_teacher'])) {
	$teacher = $db->escapeString($_POST['show_teacher']);

	$sql = $db->query("SELECT * FROM staff WHERE id = '$teacher' ");
	$data = $sql->fetchArray();
	echo json_encode(['name' => $data['fullname'], 'phone' => $data['phone']]);
}
elseif (isset($_GET['reload_years'])) {
	$sql = $db->query("SELECT * FROM year");
	$i = 1;
	while ($row = $sql->fetchArray()) {
		echo "<tr><td>$i</td><td>{$row['name']}</td><td>{$row['fees']}</td><td>{$row['uniform']}</td><td><a class='btn btn-info btn-sm' onclick=\"edit_teachjher('{$row['id']}')\"><i class='fa fa-pen-alt'></i> Edit</a></td></tr>";
		$i += 1;
	}
}
elseif (isset($_POST['edit_subject_id'], $_POST['edit_subject_name'])) {
	$subject_id = (int)trim($_POST['edit_subject_id']);

	$subject_name = $db->escapeString($_POST['edit_subject_name']);

	$upd = $db->query("UPDATE subject SET name = '$subject_name' WHERE id = '$subject_id' ");

	if ($upd) {
		echo "Successfully edited the subject name to <b>".$subject_name."</b>";
	}
	else{
		echo $db->error;
	}
}
elseif (isset($_POST['create_academic_year'], $_POST['academic_year'])) {
	$year = $db->escapeString($_POST['academic_year']);
	$fees = $db->escapeString($_POST['fees_year']);
	$uniform = $db->escapeString($_POST['uniform']);

	//check first
	$sql = $db->query("SELECT * FROM year WHERE name = '$year'");
	$data = $sql->fetchArray();
	if ($data) {
		echo json_encode(['status' => false, 'message' => '<b>'.$year.'</b> is already added as a year']);
	}
	else{
		$ins = $db->query("INSERT INTO year (id, name, `fees`, `uniform`) VALUES (NULL, '$year', '$fees', '$uniform')");
		if ($ins) {
			echo json_encode(['status' => true, 'message' => 'Successfully added <b>'.$year.'</b> is added as a new year']);
		}
		else{
			echo json_encode(['status' => $db->error]);
		}
	}
}
elseif (isset($_POST['create_subject'], $_POST['new_subject'])) {
	$subject = $db->escapeString($_POST['new_subject']);

	//check first
	$sql = $db->query("SELECT * FROM subject WHERE name = '$subject'");
	$data = $sql->fetchArray();
	if ($data) {
		echo json_encode(['status' => false, 'message' => '<b>'.$subject.'</b> is already added as a subject']);
	}
	else{
		$ins = $db->query("INSERT INTO subject (id, name) VALUES (NULL, '$subject')");
		if ($ins) {
			echo json_encode(['status' => true, 'message' => 'Successfully added <b>'.$subject.'</b> is added as a new subject']);
		}
		else{
			echo json_encode(['status' => false, 'message' => $db->error]);
		}
	}
}
elseif (isset($_GET['reload_subjects'])) {
	$sql = $db->query("SELECT * FROM subject");
	$i = 1;
	while ($row = $sql->fetchArray()) {
		echo "<tr><td>$i</td><td>{$row['name']}</td><td><a class='btn btn-info btn-sm' onclick=\"edit_subject('{$row['id']}', '{$row['name']}')\"><i class='fa fa-pen-alt'></i> Edit</a></td></tr>";
		$i += 1;
	}
}
elseif (isset($_POST['phone_number_edit'], $_POST['fullname_edit'], $_POST['edit_teacher'])) {
	$teacher_id = (int)trim($_POST['edit_teacher']);

	$phone_number = $db->escapeString($_POST['phone_number_edit']);
	$fullname = $db->escapeString($_POST['fullname_edit']);

	$upd = $db->query("UPDATE staff SET phone = '$phone_number', fullname = '$fullname' WHERE id = '$teacher_id' ");

	if ($upd) {
		echo json_encode(['status' => true, 'message' => 'Successfully edited the teacher']);
	}
	else{
		echo json_encode(['status' => false, 'message' => $db->error]);
	}
}
elseif (isset($_POST['add_teacher'], $_POST['phone_number'], $_POST['fullname'])) {
	$phone_number = $db->escapeString($_POST['phone_number']);
	$fullname = $db->escapeString($_POST['fullname']);

	$password = md5("1234");

	//check reg number first
	$sql = $db->query("SELECT * FROM staff WHERE phone = '$phone_number'");
	$data = $sql->fetchArray();
	if ($data) {
		echo json_encode(['status' => false, 'message' => '<b>'.$phone_number.'</b> is already registered']);
	}
	else{
		$res = $db->query("INSERT INTO `staff`(`id`, `phone`, `fullname`, `password`, `role`) VALUES (NULL, '$phone_number', '$fullname', '$password', 'teacher')");

		if ($res) {
			echo json_encode(['status' => true, 'message' => 'Successfully registered '.$fullname.' as a new teacher']);
		}
		else{
			echo json_encode(['status' => false, 'message' => $db->error]);
		}
	}
}
elseif (isset($_POST['name'], $_POST['acayear'], $_POST['form'], $_POST['term'])) {
	$name = $db->escapeString($_POST['name']);
	$year = $db->escapeString($_POST['acayear']);
	$form = $db->escapeString($_POST['form']);
	$term = $db->escapeString($_POST['term']);

	//check first
	$r = $db->query("SELECT * FROM exams WHERE name = '$name' AND year = '$year' AND form = '$form' AND term = '$term' ")->fetchArray();
	if ($r) {
		echo "$name is already registered";
	}
	else{
		$ins = $db->query("INSERT INTO exams(id, name, year, form, term) VALUES (NULL, '$name', '$year', '$form', '$term')");
		if ($ins) {
			echo "Extra examinations created!";
		}
		else{
			echo $db->lastErrorMsg();
		}
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
elseif (isset($_GET['reloadExtraExams'])) {
	$r = $db->query("SELECT * FROM exams");
	$i = 1;
	while ($row = $r->fetchArray()) {
		echo "<tr><td>$i</td><td>{$row['name']}</td><td>".$years[$row['year']]."</td><td>{$row['term']}</td><td>{$row['form']}</td><td><button class='btn btn-danger btn-sm deleteExam' data='{$row['id']}'>Delete</button></td></tr>";
		$i += 1;
	}
}
elseif (isset($_GET['reload_all_students'])) {
	$read = $db->query("SELECT * FROM student");
	$i = 1;
	while ($row = $read->fetchArray()) {
		$row_id = $row['id'];
		echo "<tr><td>$i</td><td>{$row['regnumber']}</td><td>{$row['fullname']}</td><td>{$row['status']}</td><td id='row$row_id'>";
		if ($row['status'] == "deactivated") {
			echo "<button class='btn btn-sm btn-info' onclick=\"activateStudent('$row_id')\">Activate</button>";
		}
		else{
			echo "<button class='btn btn-sm btn-danger' onclick=\"deactivateStudent('$row_id')\">Deactivate</button>";
		}
		echo "</td></tr>";
		$i += 1;
	}
}
elseif (isset($_GET['extraExcel'])) {
	$term = $_SESSION['term'];
	$form = $_SESSION['form'];
	$group = $_SESSION['group'];
	$year = $_SESSION['acayear'];
	$mode = $_SESSION['mode'];

	$exam_name = $db->query("SELECT * FROM exams WHERE id = '$mode' ")->fetchArray()['name'];

	$mega = [];
	$student_names = [];
	$regnumbers = [];
	$scores_store = [];
	$aggregate_store = [];

	if ($form > 2) {
		
		$read = $db->query("SELECT DISTINCT student, student.regnumber, student.fullname FROM extra_scores INNER JOIN student ON extra_scores.student = student.id WHERE form = '$form' AND term = '$term' AND year = '$year' AND exam = '$mode' ");
		while ($row = $read->fetchArray()) {
			$studentId = $row['student'];
			$student_names[$row['student']] = $row['fullname'];
			$regnumbers[$row['student']] = $row['regnumber'];

			if ($group != "OPEN" AND $group != "EVENING") {
				if(strpos(strtolower($row['regnumber']), "op")){
				}
				else{
					$scores = [];
					$r = $db->query("SELECT * FROM extra_scores WHERE form = '$form' AND term = '$term' AND year = '$year' AND student = '$studentId' AND exam = '$mode' ");
					while ($d = $r->fetchArray()) {
						$scores[$d['subject']] = $d['score'];
					}
					$scores_store[$studentId] = $scores;
					$calculated = getAggregate($scores);
					$mega[$studentId] = $calculated[0];
					$aggregate_store[$studentId] = $calculated;
				}
			}
			else{
				if(strpos(strtolower($row['regnumber']), "op")){
					$scores = [];
					$r = $db->query("SELECT * FROM extra_scores WHERE form = '$form' AND term = '$term' AND year = '$year' AND student = '$studentId' AND exam = '$mode' ");
					while ($d = $r->fetchArray()) {
						$scores[$d['subject']] = $d['score'];
					}
					$scores_store[$studentId] = $scores;
					$calculated = getAggregate($scores);
					$mega[$studentId] = $calculated[0];
					$aggregate_store[$studentId] = $calculated;
				}
			}
		}

		asort($mega);
	}
	else{
		$read = $db->query("SELECT DISTINCT student, student.regnumber, student.fullname FROM extra_scores INNER JOIN student ON extra_scores.student = student.id WHERE form = '$form' AND term = '$term' AND year = '$year' AND exam = '$mode' ");
		while ($row = $read->fetchArray()) {
			$studentId = $row['student'];
			$student_names[$row['student']] = $row['fullname'];
			$regnumbers[$row['student']] = $row['regnumber'];

			if ($group != "OPEN" AND $group != "EVENING") {
				if(strpos(strtolower($row['regnumber']), "op")){
				}
				else{
					$scores = [];
					$r = $db->query("SELECT * FROM extra_scores WHERE form = '$form' AND term = '$term' AND year = '$year' AND student = '$studentId' AND exam = '$mode' ");
					while ($d = $r->fetchArray()) {
						$scores[$d['subject']] = $d['score'];
					}
					$scores_store[$studentId] = $scores;
					$calculated = getAggregate($scores);
					$mega[$studentId] = $calculated[2];
					$aggregate_store[$studentId] = $calculated;
				}
			}
			else{
				if(strpos(strtolower($row['regnumber']), "op")){
					$scores = [];
					$r = $db->query("SELECT * FROM extra_scores WHERE form = '$form' AND term = '$term' AND year = '$year' AND student = '$studentId' AND exam = '$mode' ");
					while ($d = $r->fetchArray()) {
						$scores[$d['subject']] = $d['score'];
					}
					$scores_store[$studentId] = $scores;
					$calculated = getAggregate($scores);
					$mega[$studentId] = $calculated[2];
					$aggregate_store[$studentId] = $calculated;
				}
			}
		}

		arsort($mega);
	}

	$subjects = [];
	$r = $db->query("SELECT * FROM subject");
	while ($row = $r->fetchArray()) {
		$subjects[$row['id']] = $row['name'];
	}

	$text = "Position,Name,Points,Reg,Status";
	foreach ($subjects as $key => $value) {
		$text .= ",".substr($value, 0,5);
	}

	$i = 1;
	$failed = [];
	$previous = 0;
	$pre_pos = 1;
	foreach ($mega as $key => $value) {
		$calculated = $aggregate_store[$key];
		$myScore = $calculated[1];
		if ($calculated[3]) {
			$status = "PASS";

			if($value == $previous){
				
			}
			else{
				$pre_pos = $i;
			}

			$text .= "\n$pre_pos,".trim(trim(trim($student_names[$key]))).",$value,".trim(trim(trim($regnumbers[$key]))).",PASS";
			
			foreach ($subjects as $subjectId => $subjectName) {
				if (isset($myScore[$subjectId])) {
					$text .= ",".$myScore[$subjectId];
				}
				else{
					$text .= ",";
				}
			}
			
			$i += 1;
			$previous = $value;
		}
		else{
			$failed[$key] = $value;
			$status = "FAIL";
		}

	}

	$osamaliza_mayeso = [];
	foreach ($failed as $key => $value) {
		$calculated = $aggregate_store[$key];
		$myScore = $calculated[1];
		if (count($calculated[1]) > 5) {

			if($value == $previous){
				
			}
			else{
				$pre_pos = $i;
			}


			$text .= "\n$pre_pos,".trim(trim(trim($student_names[$key]))).",$value,".trim(trim(trim($regnumbers[$key]))).",FAIL";

			foreach ($subjects as $subjectId => $subjectName) {
				if (isset($myScore[$subjectId])) {
					$text .= ",".$myScore[$subjectId];
				}
				else{
					$text .= ",";
				}
			}
			$i += 1;
			$previous = $value;
		}
		else{
			$osamaliza_mayeso[$key] = $value;
		}
	}

	foreach ($osamaliza_mayeso as $key => $value) {
		$calculated = $aggregate_store[$key];
		$myScore = $calculated[1];

		if($value == $previous){
				
		}
		else{
			$pre_pos = $i;
		}


		$text .= "\n$pre_pos,".trim(trim(trim($student_names[$key]))).",$value,".trim(trim(trim($regnumbers[$key]))).",FAIL";

		foreach ($subjects as $subjectId => $subjectName) {
			if (isset($myScore[$subjectId])) {
				$text .= ",".$myScore[$subjectId];
			}
			else{
				$text .= ",";
			}
		}
		$i += 1;
		$previous = $value;
	}
	$filename = "uploads/$exam_name form $form term $term.csv";
	file_put_contents($filename, $text);

	header("location: $filename");
}
elseif (isset($_POST['start_upload_service'], $_POST['term'], $_POST['form'], $_POST['group'], $_POST['acayear'])) {
	if ($_POST['mode'] == "0") {
		$sql1 = $db->query("SELECT * FROM systemctl WHERE name = 'exam_uploading'");
		$data = $sql1->fetchArray();
		if ($data['value'] == "true") {
			$_SESSION['term'] = $term = $db->escapeString($_POST['term']);
			$_SESSION['form'] = $form = $db->escapeString($_POST['form']);
			$_SESSION['group'] = $group = $db->escapeString($_POST['group']);
			$_SESSION['acayear'] = $acayear = $db->escapeString($_POST['acayear']);

			$sql = $db->query("SELECT DISTINCT student FROM registered WHERE form = '$form' AND term = '$term' AND year = '$acayear' AND `group` = '$group' ");
			$dat = $sql->fetchArray();
			$sql = $db->query("SELECT DISTINCT student FROM registered WHERE form = '$form' AND term = '$term' AND year = '$acayear' AND `group` = '$group' ");
			
			if ($dat) {
				$mega = [];
				$aggregate_store = [];
				while ($row = $sql->fetchArray()) {
					$studentId = $row['student'];
					$calculated = aggregate_points($studentId);
					$points = getAverage($calculated[1]);
					$aggregate_store[$studentId] = $calculated;
					$mega[$studentId] = $points;
				}

				arsort($mega);

				
				?>
				<div class="alert alert-info">Viewing results for academic year <b><?php echo acayear($acayear);?></b>, form <b><?="$form $group";?></b>, term <b><?="$term";?></b><br><br><button class="btn btn-info btn-sm" onclick="$('#upload_container').removeClass('m12').addClass('m8');$('#upload_view1').show();"><i class="fa fa-arrow-left"></i> Choose another class</button></div>
				<p>Download school reports for this class</p>
				<a href="download_pdf.php" target="_blank" class='btn btn-sm btn-info'>Download</a> &nbsp; <a href="download_class.php" target="_blank" class='btn btn-sm btn-info'><i class="fa fa-arrow-down"></i> Download Class Scores</a> <a href="download_class_all.php" target="_blank" class='btn btn-sm btn-info'><i class="fa fa-arrow-down"></i> All form <?=$form;?>'s</a> <a href="download_class_all_x.php" target="_blank" class='btn btn-sm btn-success'><i class="fa fa-arrow-down"></i> All form <?=$form;?>'s excel</a><br><br>
				<table class="w3-table w3-table-all" border="1">
					<th>#</th><th>Student Name</th><?php if((int)$form > 2){ echo "<th>Aggregate</th>";}?><th>Status</th><th>Subjects</th><th>Average</th><th>Action</th>
					<?php
					$i = 1;
					$zatsala = [];
					$zeros = [];
					foreach ($mega as $key => $val) {
			    		$studentId = $key;
						$user_sql = $db->query("SELECT * FROM student WHERE id = '$studentId' ");
						$user_data = $user_sql->fetchArray();
						$student_name = $user_data['fullname'];

						$count_sql = $db->query("SELECT COUNT(student) AS countAll FROM registered WHERE student = '$studentId' AND form = '$form' AND term = '$term' AND year = '$acayear' AND `group` = '$group' ");
						$fh = $count_sql->fetchArray();
						$total_count = $fh['countAll'];

						$calculated = $aggregate_store[$studentId];

						if ($calculated[0] < 6) {
							$zatsala[$studentId] = $calculated[0];
						}
						else{
							if ($calculated[3]) {
								echo "<tr><td>$i</td><td>$student_name</td>";
								if((int)$form > 2){ 
									echo "<td>".$calculated[0]."</td>";
								}
								echo "<td>PASS</td><td>$total_count</td><td>".getAverage($calculated[1])."</td><td><button class='btn btn-sm btn-info' onclick=\"upload_results('$studentId');\"> View</button></td></tr>";
								$i += 1;
							}
							else{
								$zatsala[$studentId] = $calculated[0];
							}
						}
					}
					if (count($zatsala) > 0) {

						$osamaliza_mayeso = [];
						
						foreach ($zatsala as $key => $val) {
				    		$studentId = $key;
							$user_sql = $db->query("SELECT * FROM student WHERE id = '$studentId' ");
							$user_data = $user_sql->fetchArray();
							$student_name = $user_data['fullname'];

							$papersWritten = getPapers($key);

							if (count($papersWritten) > 5 && $val != 0) {
								

								$count_sql = $db->query("SELECT COUNT(student) AS countAll FROM registered WHERE student = '$studentId' AND form = '$form' AND term = '$term' AND year = '$acayear' AND `group` = '$group' ");
								$fg = $count_sql->fetchArray();
								$total_count = $fg['countAll'];
								$calculated = $aggregate_store[$studentId];

								
								echo "<tr><td>$i</td><td>$student_name</td>";
									if((int)$form > 2){ 
										echo "<td>".$calculated[0]."</td>";
									}
									echo "<td>FAIL</td><td>$total_count</td><td>".getAverage($calculated[1])."</td><td><button class='btn btn-sm btn-info' onclick=\"upload_results('$studentId');\"> View</button></td></tr>";
								$i += 1;
							}
							else{
								if($val == 0){
									$zeros[$key] = $val;
								}
								else{
									$osamaliza_mayeso[$key] = $val;
								}
							}
							
						}

						foreach ($osamaliza_mayeso as $key => $val) {
				    		$studentId = $key;
							$user_sql = $db->query("SELECT * FROM student WHERE id = '$studentId' ");
							$user_data = $user_sql->fetchArray();
							$student_name = $user_data['fullname'];

							
							$count_sql = $db->query("SELECT COUNT(student) AS countAll FROM registered WHERE student = '$studentId' AND form = '$form' AND term = '$term' AND year = '$acayear' AND `group` = '$group' ");
							$fg = $count_sql->fetchArray();
							$total_count = $fg['countAll'];
							$calculated = $aggregate_store[$studentId];;

							
							echo "<tr><td>$i</td><td>$student_name</td>";
								if((int)$form > 2){ 
									echo "<td>".$calculated[0]."</td>";
								}
								echo "<td>FAIL</td><td>$total_count</td><td>".getAverage($calculated[1])."</td><td><button class='btn btn-sm btn-info' onclick=\"upload_results('$studentId');\"> View</button></td></tr>";
							$i += 1;
							
							
						}

						foreach ($zeros as $key => $val) {
				    		$studentId = $key;
							$user_sql = $db->query("SELECT * FROM student WHERE id = '$studentId' ");
							$user_data = $user_sql->fetchArray();
							$student_name = $user_data['fullname'];

							
							$count_sql = $db->query("SELECT COUNT(student) AS countAll FROM registered WHERE student = '$studentId' AND form = '$form' AND term = '$term' AND year = '$acayear' AND `group` = '$group' ");
							$fg = $count_sql->fetchArray();
							$total_count = $fg['countAll'];
							$calculated = $aggregate_store[$studentId];

							
							echo "<tr><td>$i</td><td>$student_name</td>";
								if((int)$form > 2){ 
									echo "<td>".$calculated[0]."</td>";
								}
								echo "<td>FAIL</td><td>$total_count</td><td>".getAverage($calculated[1])."</td><td><button class='btn btn-sm btn-info' onclick=\"upload_results('$studentId');\"> View</button></td></tr>";
							$i += 1;
							
							
						}
					}
					?>
				</table><br>
				
				<?php
				
			}
			else{
				?>
				<div class="alert alert-danger">
					There is no student for academic year <b><?php echo acayear($acayear);?></b>, form <b><?="$form $group";?></b>, term <b><?="$term";?></b>
					<br><br>
					<button class="btn btn-info btn-sm" onclick="$('#upload_container').removeClass('m12').addClass('m8');$('#upload_view1').show();">Choose another class</button>
				</div>
				<?php
			}
		}
		else{
			?>
			<div class="alert alert-danger">
				<center>
					<i class="fa fa-lock"></i><br>
					<p>Exam uploading is closed! Please contact the headteacher or system administrator</p>
				</center>
			</div>
			<?php
		}
	}
	else{
		//$db->query("DELETE FROM extra_scores WHERE score = '0'");
		$_SESSION['term'] = $term = $db->escapeString($_POST['term']);
		$_SESSION['form'] = $form = $db->escapeString($_POST['form']);
		$_SESSION['group'] = $group = $db->escapeString($_POST['group']);
		$year = $_SESSION['acayear'] = $acayear = $db->escapeString($_POST['acayear']);
		$mode = $_SESSION['mode'] = (int)trim($_POST['mode']);
		$exam_data = $db->query("SELECT * FROM exams WHERE id = '$mode' ")->fetchArray();

		db_delete("ordered", [
			'year' => $year,
			'form' => $form,
			'term' => $term,
			'exam' => $mode
		]);
		?>
		<h1>Exam name: <b><?=$exam_data['name'];?></b></h1>
		<div class="alert alert-info">
			Results for <b><?php echo acayear($acayear);?></b>, form <b><?="$form $group";?></b>, term <b><?="$term";?></b>
			<br><br>
			<button class="btn btn-info btn-sm" onclick="$('#upload_container').removeClass('m12').addClass('m8');$('#upload_view1').show();">Choose another class</button>
		</div>
		<div class="w3-padding">
			<a href="download_extra.php" target="_blank" class="btn btn-info">Downdlokkd reports</a> <a href="rest_api.php?extraExcel" class="btn btn-success"><i class="far fa-file-excel"></i> Scores Excel</a>
		</div>
		<div class="table-responsive">
			<table class="table table-striped">
				<thead>
					<th>Position</th><th>Student Name</th>
					<?php if($form > 2){?><th>Aggregate</th><?php }else{ ?><th>Total</th><?php } ?>
					<th>Reg</th><th>Status</th>
				</thead>
				<tbody>
					<?php
					$mega = [];

					$student_names = [];
					$regnumbers = [];
					$scores_store = [];
					$aggregate_store = [];

					
					if ($form > 2) {
						
						$read = $db->query("SELECT DISTINCT student, student.regnumber, student.fullname FROM extra_scores INNER JOIN student ON extra_scores.student = student.id WHERE form = '$form' AND term = '$term' AND year = '$year' AND exam = '$mode'");
						
						while ($row = $read->fetchArray()) {
							$studentId = $row['student'];
							$student_names[$row['student']] = $row['fullname'];
							$regnumbers[$row['student']] = $row['regnumber'];

							if ($group != "OPEN" AND $group != "EVENING") {
								if(strpos(strtolower($row['regnumber']), "op")){
								}
								else{
									$scores = [];
									$r = $db->query("SELECT * FROM extra_scores WHERE form = '$form' AND term = '$term' AND year = '$year' AND student = '$studentId' AND exam = '$mode' ");
									while ($d = $r->fetchArray()) {
										$scores[$d['subject']] = $d['score'];
									}
									if (count($scores) < 6) {
										$scores = addExtra($scores, $studentId);
									}
									$scores_store[$studentId] = $scores;
									$calculated = getAggregate($scores);
									$mega[$studentId] = $calculated[0];
									$aggregate_store[$studentId] = $calculated;
								}
							}
							else{
								if(strpos(strtolower($row['regnumber']), "op")){
									$scores = [];
									$r = $db->query("SELECT * FROM extra_scores WHERE form = '$form' AND term = '$term' AND year = '$year' AND student = '$studentId' AND exam = '$mode' ");
									while ($d = $r->fetchArray()) {
										$scores[$d['subject']] = $d['score'];
									}
									if (count($scores) < 6) {
										$scores = addExtra($scores, $studentId);
									}
									$scores_store[$studentId] = $scores;
									$calculated = getAggregate($scores);
									$mega[$studentId] = $calculated[0];
									$aggregate_store[$studentId] = $calculated;
								}
							}
						}

						asort($mega);
					}
					else{
						$read = $db->query("SELECT DISTINCT student, student.regnumber, student.fullname FROM extra_scores INNER JOIN student ON extra_scores.student = student.id WHERE form = '$form' AND term = '$term' AND year = '$year' AND exam = '$mode'");
						
						while ($row = $read->fetchArray()) {
							$studentId = $row['student'];
							$student_names[$row['student']] = $row['fullname'];
							$regnumbers[$row['student']] = $row['regnumber'];

							if ($group != "OPEN" AND $group != "EVENING") {
								if(strpos(strtolower($row['regnumber']), "op")){
								}
								else{
									$scores = [];
									$r = $db->query("SELECT * FROM extra_scores WHERE form = '$form' AND term = '$term' AND year = '$year' AND student = '$studentId' AND exam = '$mode' ");
									while ($d = $r->fetchArray()) {
										$scores[$d['subject']] = $d['score'];
									}
									if (count($scores) < 6) {
										$scores = addExtra($scores, $studentId);
									}
									$scores_store[$studentId] = $scores;
									$calculated = getAggregate($scores);
									$mega[$studentId] = $calculated[2];
									$aggregate_store[$studentId] = $calculated;
								}
							}
							else{
								if(strpos(strtolower($row['regnumber']), "op")){
									$scores = [];
									$r = $db->query("SELECT * FROM extra_scores WHERE form = '$form' AND term = '$term' AND year = '$year' AND student = '$studentId' AND exam = '$mode' ");
									while ($d = $r->fetchArray()) {
										$scores[$d['subject']] = $d['score'];
									}
									if (count($scores) < 6) {
										$scores = addExtra($scores, $studentId);
									}
									$scores_store[$studentId] = $scores;
									$calculated = getAggregate($scores);
									$mega[$studentId] = $calculated[2];
									$aggregate_store[$studentId] = $calculated;
								}
							}
						}

						arsort($mega);
					}

					/**
					 *Also save progress for students reports 
					 */
					$values = [];
					$sql_ref = "INSERT INTO ordered (`id`, `year`, `form`, `term`, `exam`, `position`, `student`, `reg`, `points`, `comments`, `remarks`, `status`, `subjects`, `average`) VALUES ";

					$i = 1;
					$failed = [];
					$previous = 0;
					$pre_pos = 1;
					foreach ($mega as $key => $value) {
						$calculated = $aggregate_store[$key];
						$reg = $regnumbers[$key];
						
						if ($calculated[3]) {
							$status = "PASS";

							if($value == $previous){
							}
							else{
								$pre_pos = $i;
							}

							if ($form > 2) {
								echo "<tr><td>$pre_pos</td><td>".$student_names[$key]."</td><td>$value</td><td>".$regnumbers[$key]."</td><td>$status</td></tr>";
							}
							else{
								echo "<tr><td>$pre_pos</td><td>".$student_names[$key]."</td><td>".$calculated[2]."</td><td>".$regnumbers[$key]."</td><td>$status</td></tr>";
							}
							
							$previous = $value;
							$count = count($calculated[1]);
							$average = (int)(array_sum(array_values($calculated[1]))/$count);

							//add to values
							array_push($values, "(NULL, '$year','$form','$term','$mode', '$i','$key','$reg','$value','','','$status','$count','$average')");

							$i += 1;
						}
						else{
							$failed[$key] = $value;
							$status = "FAIL";
						}

					}

					$status = "FAIL";
					$osamaliza_mayeso = [];
					foreach ($failed as $key => $value) {
						$calculated = $aggregate_store[$key];
						$reg = $regnumbers[$key];

						if (count($calculated[1]) > 5) {

							if($value == $previous){
							}
							else{
								$pre_pos = $i;
							}

							if ($form > 2) {
								echo "<tr><td>$i</td><td>".$student_names[$key]."</td><td>$value</td><td>".$regnumbers[$key]."</td><td>$status</td></tr>";
							}
							else{
								echo "<tr><td>$i</td><td>".$student_names[$key]."</td><td>".$calculated[2]."</td><td>".$regnumbers[$key]."</td><td>$status</td></tr>";
							}
							
							$previous = $value;

							$count = count($calculated[1]);
							$average = (int)(array_sum(array_values($calculated[1]))/$count);

							//add to values
							array_push($values, "(NULL, '$year','$form','$term','$mode','$i','$key','$reg','$value','','','FAILED','$count','$average')");

							$i += 1;
						}
						else{
							$osamaliza_mayeso[$key] = $value;
						}
					}

					foreach ($osamaliza_mayeso as $key => $value) {
						$reg = $regnumbers[$key];

						if($value == $previous){
						}
						else{
							$pre_pos = $i;
						}
						if ($form > 2) {
							echo "<tr><td>$i</td><td>".$student_names[$key]."</td><td>$value</td><td>".$regnumbers[$key]."</td><td>$status</td></tr>";
						}
						else{
							echo "<tr><td>$i</td><td>".$student_names[$key]."</td><td>".$calculated[2]."</td><td>".$regnumbers[$key]."</td><td>$status</td></tr>";
						}
						
						$previous = $value;

						$count = count($calculated[1]);
							$average = (int)(array_sum(array_values($calculated[1]))/$count);

						//add to values
						array_push($values, "(NULL, '$year','$form','$term','$mode','$i','$key','$reg','$value','','','FAILED','$count','$average')");

						$i += 1;
					}

					$chuncks = array_chunk($values, 80);
					foreach ($chuncks as $chunck) {
						if (count($chunck) > 0) {
							$db->query($sql_ref." ".implode(", ", $chunck));
						}
					}
					?>
				</tbody>
			</table>
		</div>
		<?php
	}
}
elseif (isset($_GET['open_student_editor'])) {
	$data = getData("student", ['id' => (int)$_GET['open_student_editor']]);
	?>
	<div class="drawer">
		<div class="drawer-container p-3">
			<font class="block text-lg">Edit Student - <?=$_GET['open_student_editor'];?></font>

			<form id="edit_student_form" class="pt-3">
				<div id="edit_student_result"></div>
				<input type="hidden" name="student_id" value="<?=$data['id'];?>">
				<p>
					<input type="text" name="reg_number_edit" class="form-control" placeholder="Enter reg number..." value="<?=$data['regnumber'];?>" required>
				</p>
				<p>
					<input type="text" name="fullname" class="form-control" placeholder="Fullname.." value="<?=$data['fullname'];?>" required>
				</p>
				<p><input type="text" name="village" class="form-control" placeholder="village..." value="<?=$data['village'];?>"></p>
				<p><input type="text" name="church" class="form-control" placeholder="church.." value="<?=$data['church'];?>" ></p>
				<p><input type="text" name="guardian" class="form-control" placeholder="guardian.." value="<?=$data['guardian'];?>" ></p>
				<p><input type="text" name="phone" class="form-control" placeholder="Phone.." value="<?=$data['lamwa'];?>" ></p>

				<input type="hidden" name="add_student" value="true">
				
				<button class="btn btn-info px-3" type="submit">Save Changes</button>
			</form>
		</div>
	</div>
	<?php
}
elseif(isset($_POST['student_id'], $_POST['reg_number_edit'], $_POST['fullname'], $_POST['village'], $_POST['church'], $_POST['guardian'])){
	db_update("student", [
		'regnumber' => $_POST['reg_number_edit'],
		'fullname' => $_POST['fullname'],
		'village' =>  $_POST['village'],
		'church' => $_POST['church'],
		'guardian' => $_POST['guardian'],
		'lamwa' => $_POST['phone'],
	], ['id' => $_POST['student_id']]);

	echo "Successfully updated student";
}
elseif (isset($_POST['deleteStudents'])) {
	$students = explode(",", trim($_POST['deleteStudents']));

	foreach ($students as $student) {
		$student = (int)$student;
		$del = $db->query("DELETE FROM student WHERE id = '$student' ");
		$del2  = $db->query("DELETE FROM registered WHERE student = '$student' ");
		$del3 = $db->query("DELETE FROM scores WHERE student = '$student' ");
	}

	echo "Successfully deleted students";
}
elseif (isset($_GET['reload_students'])) {
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
}
else{
	echo "No specific task found";
}
?>