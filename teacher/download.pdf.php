<?php
session_start();

$db = new sqlite3("../database.db");
require '../teacher/functions.php';
require 'libs/fpdf.php';
require '../config.php';

class PDF extends FPDF{
	// Load data
	function putPaCenter($text)
	{
	    $pageWidth = $this->GetPageWidth();
	    $this->SetFillColor(102, 255, 102);
	    $this->Cell($pageWidth-20,10,$text, 0, 0, 'C', false);
	}

	function sub_heading($text)
	{
		$pageWidth = $this->GetPageWidth();
	    //$this->SetFillColor(102, 255, 102);
	    $this->Cell($pageWidth-20,10,$text, 'B', 0, 'C', false);
	}


	function print_half_half($text1, $text2)
	{
		$pageWidth = $this->GetPageWidth()-20;
		$forty = ceil((50/100)*$pageWidth);

		//print the rows
		$this->Cell($forty,9,$text1);
		$this->Cell($forty,9,$text2);
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
				$this->Cell(70,9,$array_values[$i],1);
			}
			elseif ($i == (count($array_values) - 1)) {
				$this->Cell(20,9,$array_values[$i],1);
			}
			else{
				$this->Cell(20,9,$array_values[$i],1);
			}
		}
		$this->Ln();
	}

	function print_heading()
	{
		global $db;
		global $all_count;
		$this->Cell(7,6,"#",1);
		$this->Cell(40,6,"Student Name",1);
		$this->Cell(18,6,"Reg",1);
		$left = $this->GetPageWidth() - 60 - 20;
		
		$length = $left / $all_count;
		$all_subjects = $db->query("SELECT * FROM subject");
		while ($row = $all_subjects->fetchArray()) {
			$this->Cell($length,6,substr($row['name'], 0,4).'.',1);
		}
		$this->Ln();
	}
}


if (isset($_SESSION['form'], $_SESSION['term'])) {
	$term = $_SESSION['term'];
	$form = $_SESSION['form'];
	$group = $_SESSION['group'];
	$acayear = $_SESSION['acayear'];

	$all_subjects = $db->query("SELECT count(id) AS countAll FROM subject");
	$dh = $all_subjects->fetchArray();
	$all_count = $dh['countAll'];

	//get year details
	$sql_year = $db->query("SELECT * FROM year WHERE id = '$acayear' ");
	$year_data = $sql_year->fetchArray();
	$year_name = $year_data['name'];
	$fees = $year_data['fees'];
	$uniform = $year_data['uniform'];

	$pdf = new PDF('L');

	$pdf->AddPage();
	$pdf->SetFont('Arial','B',18);
	$pdf->put_image("../img/".$config['logo']);

	$pdf->putPaCenter(strtoupper($config['name'].' - '.$config['intro']));
	$pdf->Ln();
	$pdf->SetFont('Times','',11);
	$pdf->putPaCenter($config['address']);
	$pdf->Ln();
	$pdf->SetFont('Times','',13);
	$sql = $db->query("SELECT DISTINCT student FROM registered WHERE form = '$form' AND term = '$term' AND year = '$acayear' AND `group` = '$group'");
	$enrollment = 0;
	while ($row = $sql->fetchArray()) {
		$enrollment += 1;
	}
	$pdf->sub_heading('Students Registered for Form'.$form.' '.$group.', Term: '.$term.' Academic year '.$year_name." Enrollment No: ".$enrollment);
	$pdf->Ln();
	$pdf->SetFont('Times','',11);
	$pdf->Cell(0,6,'','B','T','L',false);
	$pdf->Ln();
	$pdf->print_heading();
	$all_subjects = $db->query("SELECT * FROM subject");
	$subjects_array = [];
	while ($row = $all_subjects->fetchArray()) {
		array_push($subjects_array, $row['id']);
	}
	$sql = $db->query("SELECT DISTINCT student FROM registered WHERE form = '$form' AND term = '$term' AND year = '$acayear' AND `group` = '$group'");
	
		
	$i = 1;
	while ($row = $sql->fetchArray()) {
		$studentId = $row['student'];
		$stu_sql = $db->query("SELECT * FROM registered join student on registered.student = student.id WHERE student = '$studentId' AND form = '$form' AND term = '$term' AND year = '$acayear' AND `group` = '$group'");
		$reg_subjects = [];
		while ($yd = $stu_sql->fetchArray()) {
			array_push($reg_subjects, $yd['subject']);
			$name = $yd['fullname'];
			$reg = $yd['regnumber'];
		}
		$pdf->Cell(7,6,$i,1);
		$pdf->Cell(40,6,substr($name, 0,15),1);
		$pdf->Cell(18,6,$reg,1);
		
		
		$left = $pdf->GetPageWidth() - 60 - 20;
		
		$length = $left / $all_count;
		for ($h=0; $h < count($subjects_array); $h++) { 
			if (in_array($subjects_array[$h], $reg_subjects)) {
				$pdf->Cell($length,6,substr(subject($subjects_array[$h]), 0,3).'.',1);
			}
			else{
				$pdf->Cell($length,6,'-',1);
			}
		}
		$pdf->Ln();
		$i += 1;
	}

		
		
	
	$pdf->Output();
}
?>