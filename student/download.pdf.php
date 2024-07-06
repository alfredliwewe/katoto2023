<?php
session_start();

$db = new sqlite3("../database.db");
require '../teacher/functions.php';
require 'libs/fpdf.php';

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
		$this->Cell($forty,9,$text1,'','T','L',false);
		$this->Cell($forty,9,$text2,'',0,'L',false);
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
				$this->Cell(70,9,$array_values[$i],'LB','T','L',false);
			}
			elseif ($i == (count($array_values) - 1)) {
				$this->Cell(20,9,$array_values[$i],'LRB','T','L',false);
			}
			else{
				$this->Cell(20,9,$array_values[$i],'LB','T','L',false);
			}
		}
		$this->Ln();
	}

	function print_heading()
	{
		global $db;
		$this->Cell(10,6,"#",'LRB','T','L',false);
		$this->Cell(50,6,"Student Name",'LRB','T','L',false);
		$left = $this->GetPageWidth() - 60 - 20;
		$all_subjects = $db->query("SELECT * FROM subject");
		$all_count = $all_subjects->num_rows;
		$length = $left / $all_count;
		while ($row = $all_subjects->fetchArray()) {
			$this->Cell($length,6,substr($row['name'], 0,4).'.','LRB','T','L',false);
		}
		$this->Ln();
	}
}


if (isset($_SESSION['form'], $_SESSION['term'])) {
	$term = $_SESSION['term'];
	$form = $_SESSION['form'];
	$group = $_SESSION['group'];
	$acayear = $_SESSION['acayear'];

	//get year details
	$sql_year = $db->query("SELECT * FROM year WHERE id = '$acayear' ");
	$year_data = $sql_year->fetchArray();
	$year_name = $year_data['name'];
	$fees = $year_data['fees'];
	$uniform = $year_data['uniform'];

	$pdf = new PDF('L');

	$pdf->AddPage();
	$pdf->SetFont('Arial','B',18);
	$pdf->put_image("live.gif");

	$pdf->putPaCenter(strtoupper('Kanjerwa Chiyambi Sec School'.$pdf->GetPageWidth()));
	$pdf->Ln();
	$pdf->SetFont('Times','',11);
	$pdf->putPaCenter("P/Bag 23, Mwansambo, Nkhotakota");
	$pdf->Ln();
	$pdf->SetFont('Times','',13);
	$pdf->sub_heading('Student Registered for Form'.$form.' '.$group.', Term: '.$term.' Academic year '.$year_name);
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
	$sql = $db->query("SELECT DISTINCT student FROM registered WHERE form = '$form' AND term = '$term' AND year = '$acayear' ");
	if ($sql->num_rows > 0) {
		
		$i = 1;
		while ($row = $sql->fetchArray()) {
			$studentId = $row['student'];
			$pdf->Cell(10,6,$i,'LRB','T','L',false);
			$pdf->Cell(50,6,student($studentId),'LRB','T','L',false);
			$stu_sql = $db->query("SELECT * FROM registered WHERE student = '$studentId' AND form = '$form' AND term = '$term' AND year = '$acayear' ");
			$reg_subjects = [];
			while ($yd = $stu_sql->fetchArray()) {
				array_push($reg_subjects, $yd['subject']);
			}
			$left = $pdf->GetPageWidth() - 60 - 20;
			$all_subjects = $db->query("SELECT * FROM subject");
			$all_count = $all_subjects->num_rows;
			$length = $left / $all_count;
			for ($h=0; $h < count($subjects_array); $h++) { 
				if (in_array($subjects_array[$h], $reg_subjects)) {
					$pdf->Cell($length,6,substr(subject($subjects_array[$h]), 0,3).'.','LRB','T','L',false);
				}
				else{
					$pdf->Cell($length,6,'-','LRB','T','C',false);
				}
			}
			$pdf->Ln();
			$i += 1;
		}

		
		
	}
	$pdf->Output();
}
?>