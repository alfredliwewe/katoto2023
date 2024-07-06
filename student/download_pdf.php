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
		$this->Cell($forty,7,$text1,'','T','L',false);
		$this->Cell($forty,7,$text2,'',0,'L',false);
	}

	function put_image($filename)
	{
		//shrink the image to 40% of the page
		$pageWidth = $this->GetPageWidth();
		$forty = ceil((8/100)*$pageWidth);
		$dif = $pageWidth - $forty;
		$x_cord = ceil($dif/2);
		$this->Image($filename,$x_cord,null,($forty));
	}

	function print_table_row($array_values)
	{
		$pageWidth = $this->GetPageWidth()-20;
		
		for ($i=0; $i < count($array_values); $i++) { 
			if ($i == 0) {
				$this->Cell(70,7,$array_values[$i],'LB','T','L',false);
			}
			elseif ($i == (count($array_values) - 1)) {
				$this->Cell(20,7,$array_values[$i],'LRB','T','L',false);
			}
			else{
				$this->Cell(20,7,$array_values[$i],'LB','T','L',false);
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

	//get year details
	$sql_year = $db->query("SELECT * FROM year WHERE id = '$acayear' ");
	$year_data = $sql_year->fetchArray();
	$fees = $year_data['fees'];
	$uniform = $year_data['uniform'];

	$pdf = new PDF();

	$sql = $db->query("SELECT DISTINCT student FROM registered WHERE form = '$form' AND term = '$term' AND year = '$acayear' ");
	$all_students = 0;
	while ($rw = $sql->fetchArray()) {
		$all_students += 1;
	}

	//$all_students = $sql->num_rows;
	if ($all_students > 0) {
		$mega = [];
		while ($row = $sql->fetchArray()) {
			$studentId = $row['student'];
			$calculated = aggregate_points($studentId);
			$points = $calculated[0];
			$mega[$studentId] = $points;
		}

		asort($mega);


		$i = 1;
		$all_students = count($mega);

		$zatsala = [];

		foreach ($mega as $key => $val) {
    		$studentId = $key;
			$user_sql = $db->query("SELECT * FROM student WHERE id = '$studentId' ");
			$user_data = $user_sql->fetchArray();
			$student_name = $user_data['fullname'];
			$regnumber = $user_data['regnumber'];

			$count_sql = $db->query("SELECT COUNT(student) AS countAll FROM scores WHERE student = '$studentId' AND form = '$form' AND term = '$term' AND year = '$acayear' ");
			$dh = $count_sql->fetchArray();
			$total_count = $dh['countAll'];
			$calculated = aggregate_points($studentId);

			$count_sql = $db->query("SELECT * FROM scores WHERE student = '$studentId' AND form = '$form' AND term = '$term' AND year = '$acayear' ");
			
			if ($total_count > 5 && has_passed_bool($studentId)) {
				if ($studentId == $_SESSION['student_id']) {
					$pdf->AddPage();
					$pdf->SetFont('Arial','B',16);
					$pdf->put_image("../head/logo.png");

					$pdf->putPaCenter(strtoupper('KATOTO SECONDARY SCHOOL - STRIVING FOR EXCELLENCE'));
					$pdf->Ln();
					$pdf->SetFont('Times','',11);
					$pdf->putPaCenter("Mulungu amapatsa olimbika");
					$pdf->Ln();
					$pdf->putPaCenter("Education foundation for your excellence - Success comes after hard working");
					$pdf->Ln();
					$pdf->SetFont('Times','',13);
					$pdf->sub_heading('Student School Report');
					$pdf->Ln();
					$pdf->Ln();
					$pdf->SetFont('Times','',12);
					$pdf->print_half_half("Student Name: ".$student_name, "Term: Term ".$term);
					$pdf->Ln();
					$pdf->print_half_half("Form: ".$form, "Stream: ".$group);
					$pdf->Ln();
					$pdf->print_half_half("Position in Class: ".$i, "Enrollment: ".$all_students);
					$pdf->Ln();
					$pdf->print_half_half("Academic year: ".acayear($acayear), "Reg Number: ".$regnumber);

					if ($form > 2) {
						$pdf->Ln();
						$pdf->print_half_half("Total points: ".($calculated[0]), " ");
					}
					else{
						$pdf->Ln();
						$pdf->print_half_half("Total marks: ".($calculated[2]), " ");
					}
					$pdf->Ln();
					$pdf->Ln();
					$pdf->SetFont('Times','B',12);
					$pdf->print_table_row_border_bottom(['Subject name', 'CA1', 'CA2', 'ET', 'Aggreg.', 'Position', 'Grade']);
					$pdf->SetFont('Times','',12);
					while ($row = $count_sql->fetchArray()) {
						$pdf->print_table_row([subject($row['subject']), $row['ca1'], $row['ca2'], $row['end_term'], $row['score'], position_in_class($row['student'], $row['subject']), ma_pointsShow($row['score'], $form)]);
					}
					$pdf->SetFont('Times','',7);
					$pdf->Ln();
					$pdf->SetFont('Times','B',12);

					$pdf->Cell(0,7,"REMARKS: ".has_passed($studentId),'','T','L',false);
					$pdf->SetFont('Times','',12);
					$pdf->Ln();
					$pdf->Cell(0,7,"Head teacher's comment: _________________________ Class Teacher's Comment: __________________________",'','T','L',false);
					
					$pdf->Ln();
					$pdf->Cell(0,7,"Effort: ________________________________________ Behaviour: ___________________________________",'','T','L',false);
					$pdf->Ln();
					$pdf->Cell(0,7,"Next Term Opening: ___________________________________",'','T','L',false);
					$pdf->SetFont('Times','',11);
					$pdf->Ln();
					$pdf->Ln();
					$pdf->SetFont('Times','',10);
					$pdf->print_third("Grading key", "School Requirements", "Uniform");

					if ($form > 2) {
						
						$pdf->print_third("0%-39% = 5, 40%-44% = 8", "School fees: K".$fees, $uniform);
						$pdf->print_third("40%-44% = 8, 45%-49%= 7", "", "");
						$pdf->print_third("50%-54% = 6, 55%-59%= 5", "", "");
						$pdf->Cell(0,7,"60%-64% = 4, 65%-69%= 3, 70%-79% = 2, 80%-100% = 1",'','T','L',false);
					}
					else{
						$pdf->print_third("0%-49% = F", "PTA fees: K".$fees, $uniform);
						$pdf->print_third("50%-59% = C", "", "");
						$pdf->print_third("60%-69% = C, 70%-79%= B", "", "");
						$pdf->Cell(0,7,"80%-100% = A",'','T','L',false);
					}
				}

				$i += 1;
			}
			else{
				$zatsala[$studentId] = $calculated[0];
			}
			

		}

		foreach ($zatsala as $key => $val) {
    		$studentId = $key;
			$user_sql = $db->query("SELECT * FROM student WHERE id = '$studentId' ");
			$user_data = $user_sql->fetchArray();
			$student_name = $user_data['fullname'];
			$regnumber = $user_data['regnumber'];

			$count_sql = $db->query("SELECT COUNT(student) AS countAll FROM scores WHERE student = '$studentId' AND form = '$form' AND term = '$term' AND year = '$acayear' ");
			$ft = $count_sql->fetchArray();
			$total_count = $ft['countAll'];
			$calculated = aggregate_points($studentId);
			$count_sql = $db->query("SELECT * FROM scores WHERE student = '$studentId' AND form = '$form' AND term = '$term' AND year = '$acayear' ");
			
			if ($studentId == $_SESSION['student_id']) {
				$pdf->AddPage();
				$pdf->SetFont('Arial','B',18);
				$pdf->put_image("../head/logo.png");


				$pdf->putPaCenter(strtoupper('KANJERWA SECONDARY SCHOOL'));
				$pdf->Ln();
				$pdf->SetFont('Times','',11);
				$pdf->putPaCenter("P/BAG 36, MZUZU.  Phone:01310011 / 0310127");
				$pdf->Ln();
				$pdf->SetFont('Times','',13);
				$pdf->sub_heading('Student School Report');
				$pdf->Ln();
				$pdf->Ln();
				$pdf->SetFont('Times','',12);
				$pdf->print_half_half("Student Name: ".$student_name, "Term: Term ".$term);
				$pdf->Ln();
				$pdf->print_half_half("Form: ".$form."    Reg: ".$regnumber, "Stream: ".$group);
				$pdf->Ln();
				$pdf->print_half_half("Position in Class: ".$i, "Enrollment: ".$all_students);
				$pdf->Ln();
				$pdf->print_half_half("Academic year: ".acayear($acayear), "Reg Number: ".$regnumber);
				if ($form > 2) {
					$pdf->Ln();
					$pdf->print_half_half("Total points: ".($calculated[0]), " ");
				}
				else{
					$pdf->Ln();
					$pdf->print_half_half("Total marks: ".($calculated[2]), " ");
				}
				$pdf->Ln();
				$pdf->Ln();
				$pdf->print_table_row_border_bottom(['Subject name', 'CA1', 'CA2', 'ET', 'Aggreg.', 'Position', 'Grade']);
				while ($row = $count_sql->fetchArray()) {
					$pdf->print_table_row([subject($row['subject']), $row['ca1'], $row['ca2'], $row['end_term'], $row['score'], position_in_class($row['student'], $row['subject']), ma_pointsShow($row['score'], $form)]);
				}
				$pdf->SetFont('Times','',7);
				$pdf->Ln();
				$pdf->SetFont('Times','B',12);

				$pdf->Cell(0,7,"REMARKS: ".has_passed($studentId),'','T','L',false);
				$pdf->SetFont('Times','',12);
				$pdf->Ln();
				
				$pdf->Cell(0,9,"Head teacher's comment: _________________________ Class Teacher's Comment: __________________________",'','T','L',false);
				$pdf->Ln();
				$pdf->Cell(0,9,"Next Term Opening: ___________________________________",'','T','L',false);
				$pdf->SetFont('Times','',11);
				$pdf->Ln();
				$pdf->print_third("Grading key", "School Requirements", "Uniform");
				$pdf->print_third("0%-39% = 10, 40%-44% = 8", "PTA fees: K".$fees, $uniform);
				$pdf->print_third("40%-44% = 8, 45%-49%= 7", "", "");
				$pdf->print_third("50%-54% = 6, 55%-59%= 5", "", "");
				$pdf->Cell(0,7,"60%-64% = 4, 65%-69%= 3, 70%-79% = 2, 80%-100% = 1",'','T','L',false);
			}
			
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