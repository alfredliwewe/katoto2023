<?php

$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->put_image("../img/".$config['logo']);
if (isset($stamp)) {
	$pdf->Image($stamp, 150, 246, 50, 40);
}

$pdf->putPaCenter(strtoupper($config['name']." - ".$config['intro']));
$pdf->Ln();
$pdf->SetFont('Times','',11);
$pdf->putPaCenter($config['address']);
$pdf->Ln();
$pdf->putPaCenter($config['information']);
$pdf->Ln();
$pdf->SetFont('Times','',13);
$pdf->sub_heading('Student School Report'); 
$pdf->Ln();
$pdf->SetFont('Times','',12);
$pdf->print_half_half("Student Name: ".$student_names[$key], "Reg Number: ".$regnumbers[$key]);
$pdf->Ln();
$pdf->print_half_half("Form: ".$form." - Term:  ".$term, "Exam name: ".$exam_name);
$pdf->Ln();
$pdf->print_half_half("Position in Class: ".$pre_pos, "Enrollment: ".$all_students);
$pdf->Ln();

if ($form > 2) {
	$pdf->print_half_half("Academic year: ".$year_name, "Points: ".$calculated[0]);
}
else{
	$pdf->print_half_half("Academic year: ".$year_name, "Total Best Six: ".$calculated[2]);
}
$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Times','B',12);
$pdf->print_table_row_border_bottom(['Subject name', 'Score.', 'Position', 'Grade']);
$pdf->SetFont('Times','',12);
foreach ($calculated[1] as $subjectId => $score) {
	$pdf->print_table_row([$subjects[$subjectId], $score, getPosition($subject_scores[$subjectId], $score), ma_pointsShow($score, $form)]);
}
$pdf->SetFont('Times','',7);
$pdf->Ln(3);
$pdf->SetFont('Times','B',12);

$pdf->Cell(0,6,"REMARKS: ".$calculated[4]);
$pdf->SetFont('Times','',12);
$pdf->Ln();
$pdf->Cell(0,6,"Head teacher's signature: _________________________ Class Teacher's signature: __________________________");

$pdf->Ln();
$pdf->Cell(0,6,"Effort: ________________________________________ Behaviour: ____________________________________");
$pdf->Ln();
$pdf->SetFont('Times','B',12);
$pdf->Cell(0,6,"Next Term Opening: ".date('d M Y', strtotime($settings['next_term'])));
$pdf->SetFont('Times','',11);
$pdf->Ln();
$pdf->SetFont('Times','',10);
if ($form > 2) 
{
    $pdf->print_third("Grading key", "0%-39% = 9 points (fail)", "");
	
	$pdf->print_third("40%-44% = 8 points(marginal pass), 45%-49%= 7 points(strong pass)", "", "");
	$pdf->print_third("50%-54% = 6 points (marginal credit), 55%-59%= 5 points (credit)", "", "");
	$pdf->Cell(0,7,"60%-64% = 4 points (credit), 65%-69%= 3 points (Strong Credit), 70%-79% = 2 points (Distinction), 80%-100% = 1 point (Distinction)",'','T','L',false);
	$pdf->Ln(2);
	$pdf->SetFont('Times','B',12);
	$pdf->print_third("SCHOOL REQUIREMENTS:", "", "");
	$pdf->SetFont('Times','I',12);
	$pdf->print_third("UNIFORM:.$uniform", "", "");
	$pdf->print_third("FEES: K".$fees, "","");

	$pdf->AddPage();
              
	$pdf->Ln(2);
	$pdf->print_half_half("VILLAGE: ".$student_village[$key], "CHURCH: ".$student_church[$key]); 
	$pdf->Ln();
	$pdf->print_half_half("GUARDIAN: ".$student_guardian[$key], "PHONE NO:.".$student_lamwa[$key]);          
}
else{
	//ptiny for jce
	$pdf->SetFont('Times','',10);
	$pdf->print_third("Grading key", "0%-39% = F (fail)", "");
	//$pdf->print_third("40%-49% = D(Pass)", "", "");
	//$pdf->print_third("50%-64% = C(Pass), 50%-64% = C(Good), 65%-75%= B(Very Good)", "", "");
	$pdf->Cell(0,7,"40%-49% = D (Pass), 50%-64% = C (Good), 65%-75%= B (Very Good), 76%-100% = A (Excellent)",'','T','L',false);
	$pdf->Ln(2);
	$pdf->SetFont('Times','B',12);
	$pdf->print_third("SCHOOL REQUIREMENTS:", "FEES: K".$fees, "","" );
	$pdf->SetFont('Times','I',12);
	$pdf->print_third("UNIFORM:.$uniform", "", "");

	$pdf->AddPage();
               
    $pdf->SetFont('Times','',10);
	$pdf->Ln(2);
	$pdf->print_half_half("VILLAGE: ".$student_village[$key], "CHURCH: ".$student_church[$key]);     
	$pdf->Ln();
	$pdf->print_half_half("GUARDIAN: ".$student_guardian[$key], "PHONE: ".$student_lamwa[$key]); 
	$pdf->Ln();
}

//write a stamp

