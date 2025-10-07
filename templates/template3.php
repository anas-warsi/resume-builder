<?php
ob_start();
session_start();
if (!isset($_SESSION['user_id'])) die("Please login first!");
$user_id = $_SESSION['user_id']; // <-- Must be set BEFORE you query resumes
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
include "../database.php"; // DB connection

// Include FPDF
require __DIR__ . '/../fpdf/fpdf.php';

// Fetch resume
$resumeQuery = $conn->prepare("SELECT * FROM resumes WHERE user_id = ? ORDER BY id DESC LIMIT 1");
$resumeQuery->bind_param("i", $user_id);
$resumeQuery->execute();
$resumeResult = $resumeQuery->get_result();
$resume = $resumeResult->fetch_assoc();
if (!$resume) die("No resume found. Please create one first.");

// Function to auto-generate Career Objective
function generateCareerObjective($name, $degree, $skills, $role) {
    $skillsList = !empty($skills) ? implode(", ", $skills) : "relevant skills";
    return "I am $name, a $degree graduate, seeking a position as $role. 
    I am passionate about using my skills in $skillsList to contribute to innovative projects and grow professionally.";
}


// Fetch user info
$userQuery = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
$userQuery->bind_param("i", $user_id);
$userQuery->execute();
$userResult = $userQuery->get_result();
$user = $userResult->fetch_assoc();

// Fetch education, experience, certifications, projects
function fetchAll($conn, $table, $resume_id){
    $q = $conn->prepare("SELECT * FROM $table WHERE resume_id=?");
    $q->bind_param("i", $resume_id);
    $q->execute();
    return $q->get_result()->fetch_all(MYSQLI_ASSOC);
}
$educations = fetchAll($conn, "resume_education", $resume['id']);
$experiences = fetchAll($conn, "resume_experience", $resume['id']);
$certifications = fetchAll($conn, "resume_certifications", $resume['id']);
$projects = fetchAll($conn, "resume_projects", $resume['id']);
$skills = !empty($resume['skills']) ? explode(",", $resume['skills']) : array();

// PDF download
if (isset($_GET['download']) && $_GET['download'] === 'pdf') {
    if(ob_get_length()) ob_end_clean();
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',20);
    $pdf->Cell(0,10, $user['name'],0,1,'C');
    
    $pdf->SetFont('Arial','',12);
    $pdf->Cell(0,7, $user['email']." | ".$resume['phone'],0,1,'C');
    if(!empty($resume['link'])) $pdf->Cell(0,7, $resume['link'],0,1,'C');
    $pdf->Ln(5);
    
    // If career_objective is empty, generate it automatically
    if (empty($resume['career_objective'])) {
        $resume['career_objective'] = generateCareerObjective(
            $user['name'], 
            $resume['degree'] ?? "B.Tech in Computer Science", // fallback if degree field is empty
            $skills, 
            "Software Developer" // default role
        );
    }
    // Career Objective
    if(!empty($resume['career_objective'])){
        $pdf->SetFont('Arial','B',14);
        $pdf->SetFillColor(200,200,200);
        $pdf->Cell(0,8,'Career Objective',0,1,'L',true);
        $pdf->SetFont('Arial','',12);
        $pdf->MultiCell(0,6,$resume['career_objective']);
        $pdf->Ln(3);
    }
    
    // Experience
    if(!empty($experiences)){
        $pdf->SetFont('Arial','B',14);
        $pdf->SetFillColor(200,200,200);
        $pdf->Cell(0,8,'Experience',0,1,'L',true);
        $pdf->SetFont('Arial','',12);
        foreach($experiences as $exp){
            $pdf->SetFont('Arial','B',12);
            $pdf->Cell(0,6,$exp['position']." at ".$exp['company'],0,1);
            $pdf->SetFont('Arial','I',11);
            $pdf->Cell(0,6,$exp['date_range'],0,1);
            if(!empty($exp['description'])){
                foreach(preg_split("/[\r\n,]+/", $exp['description']) as $d){
                    if(trim($d)!=="") $pdf->MultiCell(0,5,"- ".trim($d));
                }
            }
            $pdf->Ln(2);
        }
    }

    // Skills
    if(!empty($skills)){
        $pdf->SetFont('Arial','B',14);
        $pdf->SetFillColor(200,200,200);
        $pdf->Cell(0,8,'Skills',0,1,'L',true);
        $pdf->SetFont('Arial','',12);
        $pdf->MultiCell(0,6,implode(" | ", $skills));
        $pdf->Ln(2);
    }
    $skills = !empty($resume['skills']) ? explode(" | ", $resume['skills']) : array();



    // Certifications
    if(!empty($certifications)){
        $pdf->SetFont('Arial','B',14);
        $pdf->SetFillColor(200,200,200);
        $pdf->Cell(0,8,'Certifications',0,1,'L',true);
        $pdf->SetFont('Arial','',12);
        foreach($certifications as $c){
            $pdf->Cell(0,6,$c['certificate_name']." (".$c['year'].")",0,1);
            if(!empty($c['issued_by'])) $pdf->Cell(0,6,"Issued by: ".$c['issued_by'],0,1);
        }
        $pdf->Ln(2);
    }

    // Projects
    if(!empty($projects)){
        $pdf->SetFont('Arial','B',14);
        $pdf->SetFillColor(200,200,200);
        $pdf->Cell(0,8,'Projects',0,1,'L',true);
        $pdf->SetFont('Arial','',12);
        foreach($projects as $p){
            $pdf->Cell(0,6,$p['title']." (".$p['technology'].")",0,1);
            if(!empty($p['description'])){
                foreach(preg_split("/[\r\n,]+/", $p['description']) as $pd){
                    if(trim($pd)!=="") $pdf->MultiCell(0,5,"- ".trim($pd));
                }
            }
            $pdf->Ln(2);
        }
    }

    // Education
    if(!empty($educations)){
        $pdf->SetFont('Arial','B',14);
        $pdf->SetFillColor(200,200,200);
        $pdf->Cell(0,8,'Education',0,1,'L',true);
        $pdf->SetFont('Arial','',12);
        foreach($educations as $e){
            $pdf->Cell(0,6,$e['degree']." at ".$e['college']." (".$e['year'].")",0,1);
        }
    }

    $pdf->Output('D','RESUME.pdf');
    exit;
}

// HTML output for browser
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Resume</title>
<style>
body { font-family: Arial,sans-serif; max-width:800px;margin:0 auto;padding:40px;background:#f5f5f5; }
.resume { background:#fff; padding:40px; box-shadow:0 0 10px rgba(0,0,0,0.1);}
.header {margin-bottom:30px;}
.name{font-size:36px;font-weight:bold;}
.contact-info{color:#666;}
.section-title{font-size:24px;font-weight:bold;margin-top:30px;border-bottom:2px solid #333;}
.skill-tag{background:#007bff;color:white;padding:5px 10px;border-radius:15px;margin-right:5px;display:inline-block;}
</style>
</head>
<body>
<div class="resume">
    <div class="header">
        <div class="name"><?php echo htmlspecialchars($user['name']); ?></div>
        <div class="contact-info"><?php echo htmlspecialchars($user['email']); ?> | <?php echo htmlspecialchars($resume['phone']); ?></div>
        <?php if(!empty($resume['link'])): ?>
            <div><a href="<?php echo htmlspecialchars($resume['link']); ?>" target="_blank"><?php echo htmlspecialchars($resume['link']); ?></a></div>
        <?php endif; ?>
    </div>

    <!-- Career Objective -->
    <?php if(!empty($resume['career_objective'])): ?>
        <div class="section-title">Career Objective</div>
        <p><?php echo htmlspecialchars($resume['career_objective']); ?></p>
    <?php endif; ?>

    <!-- Experience -->
    <?php if(!empty($experiences)): ?>
        <div class="section-title">Experience</div>
        <?php foreach($experiences as $exp): ?>
            <p><strong><?php echo htmlspecialchars($exp['position']); ?> at <?php echo htmlspecialchars($exp['company']); ?></strong></p>
            <p><em><?php echo htmlspecialchars($exp['date_range']); ?></em></p>
            <?php 
            $descItems = preg_split("/[\r\n,]+/", $exp['description']);
            if(!empty($descItems)) echo "<ul>";
            foreach($descItems as $d){
                if(trim($d)!=="") echo '<li>'.htmlspecialchars(trim($d)).'</li>';
            }
            if(!empty($descItems)) echo "</ul>";
            ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Education -->
    <?php if(!empty($educations)): ?>
        <div class="section-title">Education</div>
        <?php foreach($educations as $edu): ?>
            <p><?php echo htmlspecialchars($edu['degree']); ?> at <?php echo htmlspecialchars($edu['college']); ?> (<?php echo htmlspecialchars($edu['year']); ?>)</p>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Projects -->
    <?php if(!empty($projects)): ?>
        <div class="section-title">Projects</div>
        <?php foreach($projects as $p): ?>
            <p><strong><?php echo htmlspecialchars($p['title']); ?> (<?php echo htmlspecialchars($p['technology']); ?>)</strong></p>
            <?php 
            $projDesc = preg_split("/[\r\n,]+/", $p['description']);
            if(!empty($projDesc)) echo "<ul>";
            foreach($projDesc as $pd){
                if(trim($pd)!=="") echo '<li>'.htmlspecialchars(trim($pd)).'</li>';
            }
            if(!empty($projDesc)) echo "</ul>";
            ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Certifications -->
    <?php if(!empty($certifications)): ?>
        <div class="section-title">Certifications</div>
        <?php foreach($certifications as $c): ?>
            <p><?php echo htmlspecialchars($c['certificate_name']); ?> (<?php echo htmlspecialchars($c['year']); ?>)
            <?php if(!empty($c['issued_by'])) echo " - Issued by: ".htmlspecialchars($c['issued_by']); ?></p>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Skills -->
    <?php if(!empty($skills)): ?>
        <div class="section-title">Skills</div>
        <p>
            <?php foreach($skills as $skill): ?>
                <span class="skill-tag"><?php echo htmlspecialchars($skill); ?></span>
            <?php endforeach; ?>
        </p>
    <?php endif; ?>

    <div style="text-align:center;margin-top:20px;">
        <a href="?download=pdf" style="padding:10px 20px;background:#007bff;color:#fff;text-decoration:none;border-radius:5px;">Download PDF</a>
    </div>
</div>
</body>
</html>