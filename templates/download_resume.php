<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
include "../database.php";

if (!isset($_SESSION['user_id'])) die("Please login first!");

$user_id = $_SESSION['user_id'];

// Fetch user + resume
$resumeQuery = $conn->prepare("SELECT * FROM resumes WHERE user_id = ? ORDER BY id DESC LIMIT 1");
$resumeQuery->bind_param("i", $user_id);
$resumeQuery->execute();
$resume = $resumeQuery->get_result()->fetch_assoc();
if (!$resume) die("No resume found.");

$userQuery = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
$userQuery->bind_param("i", $user_id);
$userQuery->execute();
$user = $userQuery->get_result()->fetch_assoc();

// Fetch education
$eduQuery = $conn->prepare("SELECT * FROM resume_education WHERE resume_id = ?");
$eduQuery->bind_param("i", $resume['id']);
$eduQuery->execute();
$educations = $eduQuery->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch experience
$expQuery = $conn->prepare("SELECT * FROM resume_experience WHERE resume_id = ?");
$expQuery->bind_param("i", $resume['id']);
$expQuery->execute();
$experiences = $expQuery->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch certifications
$certQuery = $conn->prepare("SELECT * FROM resume_certifications WHERE resume_id = ?");
$certQuery->bind_param("i", $resume['id']);
$certQuery->execute();
$certifications = $certQuery->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch projects
$projQuery = $conn->prepare("SELECT * FROM resume_projects WHERE resume_id = ?");
$projQuery->bind_param("i", $resume['id']);
$projQuery->execute();
$projects = $projQuery->get_result()->fetch_all(MYSQLI_ASSOC);

// Skills
$skills = !empty($resume['skills']) ? explode(",", $resume['skills']) : array();

// Include FPDF
require __DIR__ . '/../fpdf/fpdf.php';
if (!defined('FPDF_FONTPATH')) define('FPDF_FONTPATH', __DIR__ . '/../fpdf/font/');

$pdf = new FPDF();
$pdf->AddPage();

// Header
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10, $user['name'],0,1,'C');
$pdf->SetFont('Arial','',12);
$pdf->Cell(0,8,'Email: '.$user['email'].' | Phone: '.$resume['phone'],0,1,'C');
if(!empty($resume['link'])) $pdf->Cell(0,8,'Link: '.$resume['link'],0,1,'C');

$pdf->Ln(5);

// Career Objective
if(!empty($resume['career_objective'])){
    $pdf->SetFont('Arial','B',14);
    $pdf->Cell(0,8,'Career Objective',0,1);
    $pdf->SetFont('Arial','',12);
    $pdf->MultiCell(0,8,$resume['career_objective']);
    $pdf->Ln(3);
}

// Experience
if(!empty($experiences)){
    $pdf->SetFont('Arial','B',14);
    $pdf->Cell(0,8,'Experience',0,1);
    $pdf->SetFont('Arial','',12);
    foreach($experiences as $exp){
        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(0,6,$exp['position'].' at '.$exp['company'],0,1);
        $pdf->SetFont('Arial','I',11);
        $pdf->Cell(0,6,$exp['date_range'],0,1);
        $pdf->SetFont('Arial','',12);
        if(!empty($exp['description'])){
            $descItems = preg_split("/[\r\n,]+/", $exp['description']);
            foreach($descItems as $d){
                if(trim($d)!=="") $pdf->MultiCell(0,6,'- '.trim($d));
            }
        }
        $pdf->Ln(2);
    }
}

// Skills
if(!empty($skills)){
    $pdf->SetFont('Arial','B',14);
    $pdf->Cell(0,8,'Skills',0,1);
    $pdf->SetFont('Arial','',12);
    $pdf->MultiCell(0,6,implode(", ",$skills));
    $pdf->Ln(2);
}

// Certifications
if(!empty($certifications)){
    $pdf->SetFont('Arial','B',14);
    $pdf->Cell(0,8,'Certifications',0,1);
    $pdf->SetFont('Arial','',12);
    foreach($certifications as $cert){
        $pdf->Cell(0,6,$cert['certificate_name'].' ('.$cert['year'].')',0,1);
        if(!empty($cert['issued_by'])) $pdf->Cell(0,6,'Issued by: '.$cert['issued_by'],0,1);
        $pdf->Ln(1);
    }
}

// Projects
if(!empty($projects)){
    $pdf->SetFont('Arial','B',14);
    $pdf->Cell(0,8,'Projects',0,1);
    $pdf->SetFont('Arial','',12);
    foreach($projects as $proj){
        $pdf->Cell(0,6,$proj['project_name'].' ('.$proj['technology'].')',0,1);
        if(!empty($proj['description'])){
            $projDescItems = preg_split("/[\r\n,]+/", $proj['description']);
            foreach($projDescItems as $pd){
                if(trim($pd)!=="") $pdf->MultiCell(0,6,'- '.trim($pd));
            }
        }
        $pdf->Ln(1);
    }
}

// Education
if(!empty($educations)){
    $pdf->SetFont('Arial','B',14);
    $pdf->Cell(0,8,'Education',0,1);
    $pdf->SetFont('Arial','',12);
    foreach($educations as $edu){
        $pdf->Cell(0,6,$edu['college'].' - '.$edu['degree'],0,1);
        $pdf->Cell(0,6,'Year: '.$edu['year'],0,1);
        $pdf->Ln(2);
    }
}

// Output PDF
$pdf->Output('D','RESUME.pdf');
exit;
