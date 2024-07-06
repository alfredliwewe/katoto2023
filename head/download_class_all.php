<?php
session_start();

$db = new sqlite3('../database.db');
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

	function stripped_table()
	{
		global $bookid;
		$this->SetFillColor(224,235,255);
		$w = array(40, 35, 40, 45);
		$array_tasks = ['User Name','Booking Id', 'Mobile', 'Email', 'From / To', 'Comment', 'Status', 'Cost', 'Package Features', 'Package Description'];
		$fill = false;
		$pageWidth = $this->GetPageWidth()-20;
		$forty = ceil((40/100)*$pageWidth);
		$sixty = ceil((60/100)*$pageWidth);
		for ($i=0; $i < count($array_tasks); $i++) {
			if ($i == 0) { // checking first row to print top bar
				$this->Cell($forty,9,$array_tasks[$i],'LT','T','L',$fill);
				$this->Cell($sixty,9,real_data($array_tasks[$i], $bookid),'LRT',0,'L',$fill);
			}
			elseif($i == (count($array_tasks)-1)) { // checking if its last row to print bottom bar
	        	$this->Cell($forty,9,$array_tasks[$i],'LB','T','L',$fill);
				$this->Cell($sixty,9,"Yokha data",'LRB',0,'L',$fill);
	        }
			else{
				$this->Cell($forty,9,$array_tasks[$i],'L','T','L',$fill);
				$this->Cell($sixty,9,"Yokha data",'LR',0,'L',$fill);
			}
	        
	        
			$this->Ln();
	        $fill = !$fill;
		}
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
		$pageWidth = $this->GetPageWidth()-10;
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

	function print_table_row_border_bottom($array_values)
	{
		$pageWidth = $this->GetPageWidth()-20;
		
		for ($i=0; $i < count($array_values); $i++) { 
			if ($i == 0) {
				$this->Cell(70,9,$array_values[$i],'LTB','T','L',false);
			}
			elseif ($i == (count($array_values) - 1)) {
				$this->Cell(20,9,$array_values[$i],'LRTB','T','L',false);
			}
			else{
				$this->Cell(20,9,$array_values[$i],'LTB','T','L',false);
			}
		}
		$this->Ln();
	}

	function print_line()
	{
		$this->Cell(10,9,"#",'LT','T','L',false);
		$this->Cell(45,9,"Student Name",'LT','T','L',false);
		$this->Cell(21,9,"Reg",'LT','T','L',false);
		$this->Cell(16,9,"Points",'LT','T','L',false);
		global $db;
		global $subjects_count;

		$sql = $db->query("SELECT * FROM subject");
		$cell_width = 180 / $subjects_count;
		$i = 1;
		while ($row = $sql->fetchArray()) {
			if ($i == $subjects_count) {
				$this->Cell($cell_width,9,substr($row['name'], 0,3).".",'LRT','T','L',false);
			}
			else{
				$this->Cell($cell_width,9,substr($row['name'], 0,3).".",'LT','T','L',false);
			}
			$i += 1;
		}
	}

	function print_line_data($number, $name, $regnumber, $points, $studentId)
	{
		$this->Cell(10,9,$number,'LT','T','L',false);
		$this->Cell(45,9,$name,'LT','T','L',false);
		$this->Cell(21,9,$regnumber,'LT','T','L',false);
		$this->Cell(16,9,$points,'LT','T','L',false);
		global $db;
		global $form;
		global $term;
		global $acayear;
		global $group;
		global $subjects_count;


		$sql = $db->query("SELECT * FROM subject");
		$cell_width = 180 / $subjects_count;
		$i = 1;
		while ($row = $sql->fetchArray()) {
			$subId = $row['id'];
			$get_score_sql = $db->query("SELECT * FROM scores WHERE subject = '$subId' AND student = '$studentId' AND form = '$form' AND term = '$term' AND year = '$acayear'");
			$data = $get_score_sql->fetchArray();
			if ($data) {
				
				$score_mark = $data['score'];
			}
			else{
				$score_mark = "-";
			}
			if ($i == $subjects_count) {
				$this->Cell($cell_width,9,$score_mark,'LRT','T','L',false);
			}
			else{
				$this->Cell($cell_width,9,$score_mark,'LT','T','L',false);
			}
			$i += 1;
		}
	}

	function print_line_data_bottom($number, $name, $regnumber, $points, $scores)
	{
		$this->Cell(10,9,$number,'LTB','T','L',false);
		$this->Cell(45,9,$name,'LTB','T','L',false);
		$this->Cell(21,9,$regnumber,'LTB','T','L',false);
		$this->Cell(16,9,$points,'LTB','T','L',false);
		global $subjects;
		$subjects_count = count($subjects);

		
		$cell_width = 180 / $subjects_count;
		$i = 1;
		foreach ($subjects as $subId => $subject_name) {
			if (isset($scores[$subId])) {
				
				$score_mark = $scores[$subId];
			}
			else{
				$score_mark = "-";
			}
			
			$this->Cell($cell_width,9,$score_mark,1,'T','L',false);
		}
	}


	function print_third($text1, $text2, $text3)
	{
		$pageWidth = $this->GetPageWidth()-20;

		$third = $pageWidth / 3;

		$this->Cell($third,9,$text1,'','T','L',false);
		$this->Cell($third,9,$text2,'','T','L',false);
		$this->Cell($third,9,$text3,'','T','L',false);
		$this->Ln();
	}
}


if (isset($_SESSION['form'], $_SESSION['term'])) {
	$term = $_SESSION['term'];
	$form = $_SESSION['form'];
	$group = $_SESSION['group'];
	$acayear = $_SESSION['acayear'];

	$sql = $db->query("SELECT * FROM subject");
	$subjects_count = 0;
	$subjects = [];

	while ($r = $sql->fetchArray()) {
		$subjects_count++;
		$subjects[$r['id']] = $r['name'];
	}

	if ($form < 3) {
		header("location: download_class_junior_all.php");
	}

	$pdf = new PDF('L');
	$pdf->AddPage();
	$pdf->SetFont('Arial','B',18);
	$pdf->put_image("logo.png");

	$pdf->putPaCenter(strtoupper('KATOTO SECONDARY SCHOOL'));
	$pdf->Ln();
	$pdf->SetFont('Times','',11);
	$pdf->putPaCenter("P/BAG 36, MZUZU.");
	$pdf->Ln();
	$pdf->SetFont('Times','',13);

	$students_name = [];
	$students_reg = [];
	$sql = $db->query("SELECT DISTINCT student, student.fullname, student.regnumber FROM registered JOIN student ON registered.student = student.id WHERE form = '$form' AND term = '$term' AND year = '$acayear' AND `group` != 'OPEN'");
	$enrollment = 0;
	while ($r = $sql->fetchArray()) {
		$students_reg[$r['student']] = $r['regnumber'];
		$students_name[$r['student']] = $r['fullname'];
		$enrollment += 1;
	}

	$pageWidth = $pdf->GetPageWidth()-20;
	$pdf->sub_heading('Form '.$form.' End of Term '.$term.' Student Ranking Report - '.acayear($acayear).' - Enrollment: '.$enrollment);
	$pdf->Ln();
	$pdf->Ln();
	$pdf->print_line();
	$pdf->Ln();
	$pdf->SetFont('Times','',12);

	if ($enrollment > 0) {
		$mega = [];
		$aggregate_store = [];
		foreach ($students_name as $studentId => $student_name) {
			$calculated = aggregate_points1($studentId);
			$points = $calculated[0];
			$mega[$studentId] = $points;
			$aggregate_store[$studentId] = $calculated;
		}

		asort($mega);


		$i = 1;
		$all_students = count($mega);
		$zatsala = [];
		foreach ($mega as $key => $val) {
    		$studentId = $key;
			$student_name = $students_name[$key];
			$regnumber = $students_reg[$key];

			$calculated = $aggregate_store[$studentId];
			$total_count = count($calculated[1]);
			
			
			if ($total_count < 6) {
				$zatsala[$studentId] = $calculated[0];
			}
			else{
				if ($calculated[3] == true) {
					$pdf->print_line_data_bottom($i, $student_name, $regnumber, $calculated[0], $calculated[1]);
					
					$pdf->Ln();
					$i += 1;
				}
				else{
					$zatsala[$studentId] = $calculated[0];
				}	
			}
		}
		
		$osamaliza_mayeso = [];
		$zeros = [];

		foreach ($zatsala as $key => $val) {
    		$studentId = $key;
			$student_name = $students_name[$key];
			$regnumber = $students_reg[$key];

			$calculated = $aggregate_store[$studentId];
			$total_count = count($calculated[1]);

			if ($total_count < 6) {
				$osamaliza_mayeso[$key] = $val;
			}
			else{
				if ($val == 0) {
					$zeros[$key] = $val;
				}
				else{
					
					$pdf->print_line_data_bottom($i, $student_name, $regnumber, $calculated[0], $calculated[1]);
					$pdf->Ln();
					$i += 1;
				}
			}
		}

		foreach ($osamaliza_mayeso as $key => $val) {
    		$studentId = $key;
			$student_name = $students_name[$key];
			$regnumber = $students_reg[$key];

			$calculated = $aggregate_store[$studentId];
			$total_count = count($calculated[1]);

			$pdf->print_line_data_bottom($i, $student_name, $regnumber, $calculated[0], $calculated[1]);
			$pdf->Ln();
			$i += 1;			
		}

		foreach ($zeros as $key => $val) {
    		$studentId = $key;

			$student_name = $students_name[$key];
			$regnumber = $students_reg[$key];

			$calculated = $aggregate_store[$studentId];
			$total_count = count($calculated[1]);

			$pdf->print_line_data_bottom($i, $student_name, $regnumber, $calculated[0], $calculated[1]);
			$pdf->Ln();
			$i += 1;
			
		}

		$pdf->Output();
		
	}
	else{
		?>
		<div class="alert alert-danger">
			There is no student for academic year <b><?php echo acayear($acayear);?></b>, form <b><?="$form $group";?></b>, term <b><?="$term";?></b>
		</div>
		<?php
	}
}
?>