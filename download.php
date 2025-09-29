<?php
require 'fpdf/fpdf.php';

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);

// You cannot load full HTML like Dompdf; you need to add text manually
$pdf->Cell(0,10,'My Resume',0,1,'C');
$pdf->Ln();
$pdf->Cell(0,10,'Name: John Doe',0,1);
$pdf->Cell(0,10,'Email: john@example.com',0,1);

// Download the PDF
$pdf->Output('D','My_Resume.pdf');
