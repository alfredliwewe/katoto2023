<?php

$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->put_image("../img/".$system->logo);
if (isset($stamp)) {
	$pdf->Image($stamp, 150, 246, 50, 40);
}

$pdf->putPaCenter(strtoupper($system->name." - ".$system->data['moto']));
$pdf->Ln();
$pdf->SetFont('Times','',11);
$pdf->putPaCenter($system->data['address'].".  Phone:".$system->data['phone']);
$pdf->Ln();
$pdf->putPaCenter($system->data['information']);
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
$pdf->print_half_half("Academic year: ".$year_name, "Points: ".$calculated[0]);
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

$pdf->Cell(0,7,"REMARKS: ".$calculated[4],'','T','L',false);
$pdf->SetFont('Times','',12);
$pdf->Ln();
$pdf->Cell(0,7,"Head teacher's signature: _________________________ Class Teacher's signature: __________________________",'','T','L',false);

$pdf->Ln();
$pdf->Cell(0,7,"Effort: ________________________________________ Behaviour: ____________________________________",'','T','L',false);
$pdf->Ln();
$pdf->Cell(0,7,"Next Term Opening: ____________________________                     ___________________________________",'','T','L',false);
$pdf->SetFont('Times','',11);
$pdf->Ln();
$pdf->SetFont('Times','',10);
$pdf->print_third("Grading key", "School Requirements", "Uniform");
$pdf->print_third("0%-39% = 9 points (fail),", "Tuition fees: K".$fees, $uniform);
$pdf->print_third("40%-44% = 8 points(marginal pass), 45%-49%= 7 points(strong pass)", "", "");
$pdf->print_third("50%-54% = 6 points (marginal credit), 55%-59%= 5 points (credit)", "", "");
$pdf->Cell(0,7,"60%-64% = 4 points (credit), 65%-69%= 3 points (Strong Credit), 70%-79% = 2 points (Distinction), 80%-100% = 1 point (Distinction)",'','T','L',false);