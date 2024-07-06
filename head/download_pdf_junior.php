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
				$this->Cell(70,7,$array_values[$i],'LTB','T','B',false);
			}
			elseif ($i == (count($array_values) - 1)) {
				$this->Cell(20,7,$array_values[$i],'LRTB','T','L',false);
			}
			else{
				$this->Cell(20,7,$array_values[$i],'LTB','T','L',false);
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

	//get stamp
	$sql = $db->query("SELECT * FROM stamps WHERE term = '$term' AND year = '$acayear' ");
	$stamp_data = $sql->fetchArray();
	if ($stamp_data) {
		$stamp = "../img/".$stamp_data['file'];
	}

	$pdf = new PDF();

	$sql = $db->query("SELECT DISTINCT student FROM registered WHERE form = '$form' AND term = '$term' AND year = '$acayear' AND `group` = '$group'");
	$students_count = 0;
	while ($r = $sql->fetchArray()) {
		$students_count += 1;
	}
	$aggregate_store = [];
	$sql = $db->query("SELECT DISTINCT student FROM registered WHERE form = '$form' AND term = '$term' AND year = '$acayear' AND `group` = '$group'");
	if ($students_count > 0) {
		$mega = [];
		while ($row = $sql->fetchArray()) {
			$studentId = $row['student'];
			$calculated = aggregate_points($studentId);
			$aggregate_store[$studentId] = $calculated;
			$points = $calculated[2];
			$mega[$studentId] = $points;
		}

		arsort($mega);


		$i = 1;
		$all_students = count($mega);

		$zatsala = [];
		$zeros = [];
		$osamaliza_mayeso = [];

		foreach ($mega as $key => $val) {
    		$studentId = $key;
			$user_sql = $db->query("SELECT * FROM student WHERE id = '$studentId' ");
			$user_data = $user_sql->fetchArray();
			$student_name = $user_data['fullname'];
			$regNumber = $user_data['regnumber'];

			$count_sql = $db->query("SELECT * FROM scores WHERE student = '$studentId' AND form = '$form' AND term = '$term' AND year = '$acayear' AND `group` = '$group'");
			$total_count = 0;
			while ($f = $count_sql->fetchArray()) {
				$total_count += 1;
			}

			$count_sql = $db->query("SELECT * FROM scores WHERE student = '$studentId' AND form = '$form' AND term = '$term' AND year = '$acayear' AND `group` = '$group'");
			$calculated = $aggregate_store[$studentId];
			
			if ($total_count > 5 && has_passed_bool($studentId)) { //checking has written a minimum of six subjects and has passed 
				
				$pdf->AddPage();
				$pdf->SetFont('Arial','B',16);
				$pdf->put_image("logo.png");
				if (isset($stamp)) {
					$pdf->Image($stamp, 150, 246, 50, 40);
				}

				$pdf->putPaCenter(strtoupper('NTONDA COMMUNITY DAY SECONDARY - FOR EXCELLENCE'));
				$pdf->Ln();
				$pdf->SetFont('Times','',11);
				$pdf->putPaCenter("P/BAG 36, MZUZU.  Phone:01310011 / 0310127");
				$pdf->Ln();
				$pdf->putPaCenter("GOVERNMENT OF MALAWI MINISTRY OF EDUCATION CWED");
				$pdf->Ln();
				$pdf->SetFont('Times','',13);
				$pdf->sub_heading('Student School Report');
				$pdf->Ln();
				$pdf->SetFont('Times','',12);
				$pdf->print_half_half("Student Name: ".$student_name, "Reg Number: ".$regNumber);
				$pdf->Ln();
				$pdf->print_half_half("Form: ".$form." - Term:  ".$term, "Stream: ".$group);
				$pdf->Ln();
				$pdf->print_half_half("Position in Class: ".$i, "Enrollment: ".$all_students);
				$pdf->Ln();
				$pdf->print_half_half("Academic year: ".acayear($acayear), "Total Marks: ".$calculated[2]);
				$pdf->Ln();
				$pdf->Ln();
				$pdf->SetFont('Times','B',12);
				$pdf->print_table_row_border_bottom(['Subject name', 'CA', 'ET', 'Aggreg.', 'Position', 'Grade']);
				$pdf->SetFont('Times','',12);
				while ($row = $count_sql->fetchArray()) {
					$pdf->print_table_row([subject($row['subject']), ($row['ca1']+ $row['ca2']), $row['end_term'], $row['score'], position_in_class($row['student'], $row['subject']), ma_pointsShow($row['score'], $form)]);
				}
				$pdf->SetFont('Times','',7);
				$pdf->Ln();
				$pdf->SetFont('Times','B',12);

				$pdf->Cell(0,7,"REMARKS: ".has_passed($studentId),'','T','L',false);
				$pdf->SetFont('Times','',12);
				$pdf->Ln();
				$pdf->Cell(0,7,"Head teacher's signature: _________________________ Class Teacher's signature: __________________________",'','T','L',false);
				
				$pdf->Ln();
				$pdf->Cell(0,7,"Effort: ________________________________________ Behaviour: ______________________________________",'','T','L',false);

                $pdf->Ln();
				$pdf->Cell(1,7,"Next Term Opening: _____________________________                   _________________________________",'','T','L',false);
				$pdf->SetFont('Times','',11);

				$pdf->Ln();
				$pdf->SetFont('Times','',10);
				$pdf->print_third("Grading key", "School Requirements", "Uniform");
				$pdf->print_third("0%-39% = Fk(Fail)", "PTA fees: K".$fees, getUniform($group));
				//$pdf->print_third("50%-59% = C(Pass)", "", "");
				//$pdf->print_third("50%-59% = C(Pass), 60%-69% = C(Good), 70%-79%= B(Very Good)", "", "");
				$pdf->Cell(0,7,"50%-59% = C(Pass), 60%-69% = C(Good), 70%-79%= B(Very Good), 80%-100% = A (Excellent)",'','T','L',false);

				$i += 1;
			}
			else{
				if ($calculated[0] == 0) {
					$zeros[$studentId] = $calculated[0];
				}
				else{
					$zatsala[$studentId] = $calculated[0];
				}
			}
			

		}

		foreach ($zatsala as $key => $val) {
    		$studentId = $key;


    		$papersWritten = getPapers($key);

			if (count($papersWritten) > 5) {
				$user_sql = $db->query("SELECT * FROM student WHERE id = '$studentId' ");
				$user_data = $user_sql->fetchArray();
				$student_name = $user_data['fullname'];
				$regNumber = $user_data['regnumber'];

				$count_sql = $db->query("SELECT COUNT(student) AS countAll FROM scores WHERE student = '$studentId' AND form = '$form' AND term = '$term' AND year = '$acayear' AND `group` = '$group'");
				$df = $count_sql->fetchArray();
				$total_count = $df['countAll'];
				$calculated = $aggregate_store[$studentId];
				
				$count_sql = $db->query("SELECT * FROM scores WHERE student = '$studentId' AND form = '$form' AND term = '$term' AND year = '$acayear' AND `group` = '$group'");
				$pdf->AddPage();
				$pdf->SetFont('Arial','B',16);
				$pdf->put_image("logo.png");
				if (isset($stamp)) {
					$pdf->Image($stamp, 150, 246, 50, 40);
				}
				$pdf->putPaCenter(strtoupper('NTONDA COMMUNITY DAY SECONDARY - FOR EXCELLENCE'));
				$pdf->Ln();
				$pdf->SetFont('Times','',11);
				$pdf->putPaCenter("P/BAG 36, MZUZU.  Phone:01310011 / 0310127");
				//$pdf->putPaCenter("Mulungu amapatsa olimbika");
				$pdf->Ln();
				$pdf->putPaCenter("GOVERNMENT OF MALAWI MINISTRY OF EDUCATION NORTHEN EDUCATION DIVISION");
				$pdf->Ln();
				$pdf->SetFont('Times','',13);
				$pdf->sub_heading('Student School Report');
				$pdf->Ln();
				$pdf->SetFont('Times','',12);
				$pdf->print_half_half("Student Name: ".$student_name, "Reg Number: ".$regNumber);
				$pdf->Ln();
				$pdf->print_half_half("Form: ".$form." - Term:  ".$term, "Stream: ".$group);
				$pdf->Ln();
				$pdf->print_half_half("Position in Class: ".$i, "Enrollment: ".$all_students);
				$pdf->Ln();
				$pdf->print_half_half("Academic year: ".acayear($acayear), "Total Marks: ".$calculated[2]);
				$pdf->Ln();
				$pdf->Ln();
				$pdf->SetFont('Times','B',12);
				$pdf->print_table_row_border_bottom(['Subject name', 'CA', 'ET', 'Aggreg.', 'Position', 'Grade']);
				$pdf->SetFont('Times','',12);
				while ($row = $count_sql->fetchArray()) {
					$pdf->print_table_row([subject($row['subject']), ($row['ca1']+ $row['ca2']), $row['end_term'], $row['score'], position_in_class($row['student'], $row['subject']), ma_pointsShow($row['score'], $form)]);
				}
				$pdf->SetFont('Times','',7);
				$pdf->Ln();
				$pdf->SetFont('Times','B',12);

				$pdf->Cell(0,7,"REMARKS: ".has_passed($studentId),'','T','L',false);
				$pdf->SetFont('Times','',12);
				$pdf->Ln();
				$pdf->Cell(0,7,"Head teacher's signature: _________________________ Class Teacher's signature: __________________________",'','T','L',false);
				
				$pdf->Ln();
				$pdf->Cell(0,7,"Effort: ________________________________________ Behaviour: ___________________________________",'','T','L',false);
				$pdf->Ln();
				$pdf->Cell(0,7,"Next Term Opening: ____________________________                     __________________________________",'','T','L',false);
				$pdf->SetFont('Times','',11);
				$pdf->Ln();
				$pdf->SetFont('Times','',10);
				$pdf->print_third("Grading key", "School Requirements", "Uniform");
				$pdf->print_third("0%-49% = F(Fail)", "PTA fees: K".$fees, getUniform($group));
				$pdf->Cell(0,7,"50%-59% = C(Pass), 60%-69% = C(Good), 70%-79%= B(Very Good), 80%-100% = A (Excellent)",'','T','L',false);

				$i += 1;
			}
			else{
				$osamaliza_mayeso[$studentId] = $val;
			}
		}

		foreach ($osamaliza_mayeso as $key => $val) {
    		$studentId = $key;

			$user_sql = $db->query("SELECT * FROM student WHERE id = '$studentId' ");
			$user_data = $user_sql->fetchArray();
			$student_name = $user_data['fullname'];
			$regNumber = $user_data['regnumber'];

			$count_sql = $db->query("SELECT COUNT(student) AS countAll FROM scores WHERE student = '$studentId' AND form = '$form' AND term = '$term' AND year = '$acayear' AND `group` = '$group'");
			$df = $count_sql->fetchArray();
			$total_count = $df['countAll'];
			$calculated = $aggregate_store[$studentId];
			
			$count_sql = $db->query("SELECT * FROM scores WHERE student = '$studentId' AND form = '$form' AND term = '$term' AND year = '$acayear' AND `group` = '$group'");
			$pdf->AddPage();
			$pdf->SetFont('Arial','B',16);
			$pdf->put_image("logo.png");
			if (isset($stamp)) {
				$pdf->Image($stamp, 150, 246, 50, 40);
			}
			$pdf->putPaCenter(strtoupper('NTONDA COMMUNITY DAY SECONDARY - FOR EXCELLENCE'));
			$pdf->Ln();
			$pdf->SetFont('Times','',11);
			$pdf->putPaCenter("P/BAG 36, MZUZU.  Phone:01310011 / 0310127");
			//$pdf->putPaCenter("Mulungu amapatsa olimbika");
			$pdf->Ln();
			$pdf->putPaCenter("GOVERNMENT OF MALAWI MINISTRY OF EDUCATION NORTHEN EDUCATION DIVISION");
			$pdf->Ln();
			$pdf->SetFont('Times','',13);
			$pdf->sub_heading('Student School Report');
			$pdf->Ln();
			$pdf->SetFont('Times','',12);
			$pdf->print_half_half("Student Name: ".$student_name, "Reg Number: ".$regNumber);
			$pdf->Ln();
			$pdf->print_half_half("Form: ".$form." - Term:  ".$term, "Stream: ".$group);
			$pdf->Ln();
			$pdf->print_half_half("Position in Class: ".$i, "Enrollment: ".$all_students);
			$pdf->Ln();
			$pdf->print_half_half("Academic year: ".acayear($acayear), "Total Marks: ".$calculated[2]);
			$pdf->Ln();
			$pdf->Ln();
			$pdf->SetFont('Times','B',12);
			$pdf->print_table_row_border_bottom(['Subject name', 'CA', 'ET', 'Aggreg.', 'Position', 'Grade']);
			$pdf->SetFont('Times','',12);
			while ($row = $count_sql->fetchArray()) {
				$pdf->print_table_row([subject($row['subject']), ($row['ca1']+ $row['ca2']), $row['end_term'], $row['score'], position_in_class($row['student'], $row['subject']), ma_pointsShow($row['score'], $form)]);
			}
			$pdf->SetFont('Times','',7);
			$pdf->Ln();
			$pdf->SetFont('Times','B',12);

			$pdf->Cell(0,7,"REMARKS: ".has_passed($studentId),'','T','L',false);
			$pdf->SetFont('Times','',12);
			$pdf->Ln();
			$pdf->Cell(0,7,"Head teacher's signature: _________________________ Class Teacher's signature: __________________________",'','T','L',false);
			
			$pdf->Ln();
			$pdf->Cell(0,7,"Effort: ________________________________________ Behaviour: ___________________________________",'','T','L',false);
			$pdf->Ln();
			$pdf->Cell(0,7,"Next Term Opening: ____________________________                     __________________________________",'','T','L',false);
			$pdf->SetFont('Times','',11);
			$pdf->Ln();
			$pdf->SetFont('Times','',10);
			$pdf->print_third("Grading key", "School Requirements", "Uniform");
			$pdf->print_third("0%-49% = F(Fail)", "PTA fees: K".$fees, getUniform($group));
			$pdf->Cell(0,7,"50%-59% = C(Pass), 60%-69% = C(Good), 70%-79%= B(Very Good), 80%-100% = A (Excellent)",'','T','L',false);

			$i += 1;
		}

		foreach ($zeros as $key => $val) {
    		$studentId = $key;

			$user_sql = $db->query("SELECT * FROM student WHERE id = '$studentId' ");
			$user_data = $user_sql->fetchArray();
			$student_name = $user_data['fullname'];
			$regNumber = $user_data['regnumber'];

			$count_sql = $db->query("SELECT COUNT(student) AS countAll FROM scores WHERE student = '$studentId' AND form = '$form' AND term = '$term' AND year = '$acayear' AND `group` = '$group'");
			$df = $count_sql->fetchArray();
			$total_count = $df['countAll'];
			$calculated = $aggregate_store[$studentId];
			
			$count_sql = $db->query("SELECT * FROM scores WHERE student = '$studentId' AND form = '$form' AND term = '$term' AND year = '$acayear' AND `group` = '$group'");
			$pdf->AddPage();
			$pdf->SetFont('Arial','B',16);
			$pdf->put_image("logo.png");
			if (isset($stamp)) {
				$pdf->Image($stamp, 150, 246, 50, 40);
			}
			$pdf->putPaCenter(strtoupper('NTONDA COMMUNITY DAY SECONDARY - FOR EXCELLENCE'));
			$pdf->Ln();
			$pdf->SetFont('Times','',11);
			$pdf->putPaCenter("P/BAG 36, MZUZU.  Phone:01310011 / 0310127");
			//$pdf->putPaCenter("Mulungu amapatsa olimbika");
			$pdf->Ln();
			$pdf->putPaCenter("GOVERNMENT OF MALAWI MINISTRY OF EDUCATION CWED");
			$pdf->Ln();
			$pdf->SetFont('Times','',13);
			$pdf->sub_heading('Student School Report');
			$pdf->Ln();
			$pdf->SetFont('Times','',12);
			$pdf->print_half_half("Student Name: ".$student_name, "Reg Number: ".$regNumber);
			$pdf->Ln();
			$pdf->print_half_half("Form: ".$form." - Term:  ".$term, "Stream: ".$group);
			$pdf->Ln();
			$pdf->print_half_half("Position in Class: ".$i, "Enrollment: ".$all_students);
			$pdf->Ln();
			$pdf->print_half_half("Academic year: ".acayear($acayear), "Total Marks: ".$calculated[2]);
			$pdf->Ln();
			$pdf->Ln();
			$pdf->SetFont('Times','B',12);
			$pdf->print_table_row_border_bottom(['Subject name', 'CA', 'ET', 'Aggreg.', 'Position', 'Grade']);
			$pdf->SetFont('Times','',12);
			while ($row = $count_sql->fetchArray()) {
				$pdf->print_table_row([subject($row['subject']), ($row['ca1']+ $row['ca2']), $row['end_term'], $row['score'], position_in_class($row['student'], $row['subject']), ma_pointsShow($row['score'], $form)]);
			}
			$pdf->SetFont('Times','',7);
			$pdf->Ln();
			$pdf->SetFont('Times','B',12);

			$pdf->Cell(0,7,"REMARKS: ".has_passed($studentId),'','T','L',false);
			$pdf->SetFont('Times','',12);
			$pdf->Ln();
			$pdf->Cell(0,7,"Head teacher's signature: _________________________ Class Teacher's signature: __________________________",'','T','L',false);
			
			$pdf->Ln();
			$pdf->Cell(0,7,"Effort: ________________________________________ Behaviour: ___________________________________",'','T','L',false);
			$pdf->Ln();
			$pdf->Cell(0,7,"Next Term Opening: ____________________________                     __________________________________",'','T','L',false);
			$pdf->SetFont('Times','',11);
			$pdf->Ln();
			$pdf->SetFont('Times','',10);
			$pdf->print_third("Grading key", "School Requirements", "Uniform");
			$pdf->print_third("0%-49% = F(Fail)", "PTA fees: K".$fees, getUniform($group));
			$pdf->Cell(0,7,"50%-59% = C(Pass), 60%-69% = C(Good), 70%-79%= B(Very Good), 80%-100% = A (Excellent)",'','T','L',false);

			

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