<?php
session_start();

$db = new sqlite3("../database.db");
require '../teacher/functions.php';
require 'libs/fpdf.php';
require '../objects.php';

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
	$system = new System($db);
	$term = $_SESSION['term'];
	$form = $_SESSION['form'];
	$group = $_SESSION['group'];
	$acayear = $_SESSION['acayear'];
	if ((int)$form < 3) {
		header("location: download_pdf_junior.php");
	}
	//get year details
	$sql_year = $db->query("SELECT * FROM year WHERE id = '$acayear' ");
	$year_data = $sql_year->fetchArray();
	$fees = $year_data['fees'];
	$uniform = $year_data['uniform'];
	$year_name = $year_data['name'];

	//get stamp
	$sql = $db->query("SELECT * FROM stamps WHERE term = '$term' AND year = '$acayear' ");
	$stamp_data = $sql->fetchArray();
	if ($stamp_data) {
		$stamp = "../img/".$stamp_data['file'];
	}

	$pdf = new PDF();
	$students_name = [];
	$students_reg = [];
	$sql = $db->query("SELECT DISTINCT student, student.fullname, student.regnumber FROM registered JOIN student ON registered.student = student.id WHERE form = '$form' AND term = '$term' AND year = '$acayear' AND `group` = '$group'");
	$c = 0;
	while ($r = $sql->fetchArray()) {
		$students_reg[$r['student']] = $r['regnumber'];
		$students_name[$r['student']] = $r['fullname'];
		$c += 1;
	}
	$aggregate_store = [];
	$sql = $db->query("SELECT DISTINCT student FROM registered WHERE form = '$form' AND term = '$term' AND year = '$acayear' AND `group` = '$group'");
	if ($c > 0) {
		$mega = [];
		while ($row = $sql->fetchArray()) {
			$studentId = $row['student'];
			$calculated = aggregate_points($studentId);
			$aggregate_store[$studentId] = $calculated;
			$points = $calculated[0];
			$mega[$studentId] = $points;
		}

		asort($mega);


		$i = 1;
		$all_students = count($mega);

		$zatsala = [];
		$osamaliza_mayeso = [];
		$zeros = [];
		foreach ($mega as $key => $val) {
    		$studentId = $key;
		
			$student_name = $students_name[$key];
			$regNumber = $students_reg[$key];
			
			$calculated = $aggregate_store[$studentId];
			$total_count = count($calculated[1]);
			
			if ($calculated[3] == true) { //checking has written a minimum of six subjects and has passed 
				$count_sql = $db->query("SELECT * FROM scores WHERE student = '$studentId' AND form = '$form' AND term = '$term' AND year = '$acayear' AND `group` = '$group'");
				require 'show_page.php';
				$i += 1;
			}
			elseif ($total_count < 6) {
				$osamaliza_mayeso[$studentId] = $calculated[0];
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
			$student_name = $students_name[$key];
			$regNumber = $students_reg[$key];

			$calculated = $aggregate_store[$studentId];
			$total_count = count($calculated[1]);
			$count_sql = $db->query("SELECT * FROM scores WHERE student = '$studentId' AND form = '$form' AND term = '$term' AND year = '$acayear' AND `group` = '$group'");
			require 'show_page.php';
			$i += 1;

		}

		foreach ($osamaliza_mayeso as $key => $val) {
    		$studentId = $key;
			$student_name = $students_name[$key];
			$regNumber = $students_reg[$key];

			$calculated = $aggregate_store[$studentId];
			$total_count = count($calculated[1]);
			$count_sql = $db->query("SELECT * FROM scores WHERE student = '$studentId' AND form = '$form' AND term = '$term' AND year = '$acayear' AND `group` = '$group'");
			require 'show_page.php';
			$i += 1;
		}

		foreach ($zeros as $key => $val) {
    		$studentId = $key;
			$student_name = $students_name[$key];
			$regNumber = $students_reg[$key];

			$calculated = $aggregate_store[$studentId];
			$total_count = count($calculated[1]);
			$count_sql = $db->query("SELECT * FROM scores WHERE student = '$studentId' AND form = '$form' AND term = '$term' AND year = '$acayear' AND `group` = '$group'");
			require 'show_page.php';

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