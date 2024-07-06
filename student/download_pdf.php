<?php
session_start();

$db = new sqlite3("../database.db");
require '../teacher/functions.php';
require '../head/libs/fpdf.php';
require '../config.php';

class PDF extends FPDF{
	// Load data
	function putPaCenter($text)
	{
	    $pageWidth = $this->GetPageWidth();
	    $this->SetFillColor(102, 255, 102);
	    $this->Cell($pageWidth-20,8,$text, 0, 0, 'C', false);
	}

	function sub_heading($text)
	{
		$pageWidth = $this->GetPageWidth();
	    //$this->SetFillColor(102, 255, 102);
	    $this->Cell($pageWidth-20,8,$text, 'B', 0, 'C', false);
	}


	function print_half_half($text1, $text2)
	{
		$pageWidth = $this->GetPageWidth()-20;
		$forty = ceil((50/100)*$pageWidth);

		//print the rows
		$this->Cell($forty,6,$text1);
		$this->Cell($forty,6,$text2);
	}

	function put_image($filename)
	{
		//shrink the image to 40% of the page
		$pageWidth = $this->GetPageWidth();
		$forty = ceil((10/100)*$pageWidth);
		$dif = $pageWidth - $forty;
		$x_cord = ceil($dif/2);
		$this->Image($filename,$x_cord,null,($forty));
	}

	function print_table_row($array_values)
	{
		$pageWidth = $this->GetPageWidth()-20;
		
		for ($i=0; $i < count($array_values); $i++) { 
			if ($i == 0) {
				$this->Cell(70,7,$array_values[$i],1);
			}
			elseif ($i == (count($array_values) - 1)) {
				$this->Cell(35,7,$array_values[$i],1);
			}
			else{
				$this->Cell(35,7,$array_values[$i],1);
			}
		}
		$this->Ln();
	}

	function print_table_row_border_bottom($array_values)
	{
		$pageWidth = $this->GetPageWidth()-20;
		
		for ($i=0; $i < count($array_values); $i++) { 
			if ($i == 0) {
				$this->Cell(70,7,$array_values[$i],1);
			}
			elseif ($i == (count($array_values) - 1)) {
				$this->Cell(35,7,$array_values[$i],1);
			}
			else{
				$this->Cell(35,7,$array_values[$i],1);
			}
		}
		$this->Ln();
	}

	function print_third($text1, $text2, $text3)
	{
		$pageWidth = $this->GetPageWidth()-20;

		$third = $pageWidth / 3;

		$this->Cell($third,9,$text1);
		$this->Cell($third,9,$text2);
		$this->Cell($third,9,$text3);
		$this->Ln();
	}
}

$years = [];
$r = $db->query("SELECT * FROM year");
while ($row = $r->fetchArray()) {
	$years[$row['id']] = $row['name'];
}


$subjects = [];
$subject_scores = [];
$r = $db->query("SELECT * FROM subject");
while ($row = $r->fetchArray()) {
	$subjects[$row['id']] = $row['name'];
	$subject_scores[$row['id']] = [];
}

$term = $_SESSION['term'];
$form = $_SESSION['form'];
$group = $_SESSION['group'];
$year = $_SESSION['acayear'];
$mode = $_GET['mode'];

$student_id = $_SESSION['student_id'];

$exam_name = $db->query("SELECT * FROM exams WHERE id = '$mode' ")->fetchArray()['name'];


$sql_year = $db->query("SELECT * FROM year WHERE id = '$year' ");
$year_data = $sql_year->fetchArray();
$fees = $year_data['fees'];
$uniform = $year_data['uniform'];
$year_name = $year_data['name'];

//get stamp
$sql = $db->query("SELECT * FROM stamps WHERE term = '$term' AND year = '$year' ");
$stamp_data = $sql->fetchArray();
if ($stamp_data) {
	$stamp = "../img/".$stamp_data['file'];
}

$pdf = new PDF();

$data = getData("ordered", [
	'form' => $form,
	'term' => $term,
	'year' => $year,
	'student' => $student_id,
	'exam' => $_GET['mode']
]);

$student_data = getData("student", ['id' => $student_id]);

//match all from the head
$scores = [];
$subject_scores = [];
$r = $db->query("SELECT * FROM extra_scores WHERE form = '$form' AND term = '$term' AND year = '$year' AND student = '$student_id' AND exam = '$mode' ");
while ($d = $r->fetchArray()) {
	$scores[$d['subject']] = $d['score'];

	$read = $db->query("SELECT * FROM extra_scores WHERE form = '$form' AND term = '$term' AND year = '$year' AND exam = '$mode' AND subject = '{$d['subject']}' ");
	$subs = [];
	while ($row = $read->fetchArray()) {
		array_push($subs, $row['score']);
	}
	$subject_scores[$d['subject']] = $subs;
}

$calculated = getAggregate($scores);
$key = $student_id;
$student_names = [
	$student_id => $student_data['fullname']
];

$regnumbers = [
	$student_id => $student_data['regnumber']
];

$student_village = [
	$student_id => $student_data['village']
];

$student_church = [
	$student_id => $student_data['church']
];

$student_guardian = [
	$student_id => $student_data['guardian']
];
$student_lamwa = [
	$student_id => $student_data['lamwa']
];

$pre_pos = $data['position'];

$read = $db->query("SELECT DISTINCT student FROM extra_scores WHERE form = '$form' AND term = '$term' AND year = '$year' AND exam = '$mode' ");
$all_students = 0;
while ($r = $read->fetchArray()) {
	$all_students += 1;
}

//echo "Student id: $student_id<br>";

if ($mode != 0) {
	require '../head/page_extra.php';
}

$pdf->Output();
?>