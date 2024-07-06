<?php
session_start();
$db = new sqlite3("database.db");

if (isset($_POST['phone'], $_POST['password'])) {
	$phone = $db->escapeString($_POST['phone']);
	$password = $db->escapeString(md5($_POST['password']));

	$sql = $db->query("SELECT * FROM student WHERE regnumber = '$phone' AND password = '$password' ");
	$data = $sql->fetchArray();
	if ($data) {
		if ($data['status'] == "deactivated") {
			echo json_encode(['status' => false, 'message' => 'You can\'t login! Inactive. Contact administrator']);
		}
		else{
			$_SESSION['regnumber'] = $data['regnumber'];
			$_SESSION['fullname'] = $data['fullname'];
			$_SESSION['student_id'] = $data['id'];

			echo json_encode(['status' => true, 'link' => 'student/']);
		}
	}
	else{
		$sqll = $db->query("SELECT * FROM staff WHERE phone = '$phone' AND password = '$password' ");
		//call_bootstrap();
		$data = $sqll->fetchArray();
		if ($data) {
			# success...
			$_SESSION['user_id'] = $data['id'];
			$_SESSION['phone'] = $data['phone'];
			$_SESSION['fullname'] = $data['fullname'];
			$_SESSION['role'] = $data['role'];

			echo json_encode(['status' => true, 'link' => $data['role'].'/']);
		}
		else{
			echo json_encode(['status' => false, 'message' => 'Wrong details entered']);
		}
	}
}