<?php
session_start();
include "database.php"; // Your DB connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Check login
    if (!isset($_SESSION['user_id'])) {
        die("Please login first!");
    }
    $user_id = $_SESSION['user_id'];

    // 2. Sanitize form inputs
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $link     = $_POST['link'];
    $phone    = $_POST['phone'];
    $career   = $_POST['career'];
    $skills   = isset($_POST['skills']) ? $_POST['skills'] : '';

    // Arrays for dynamic fields
    $schools     = isset($_POST['school']) ? $_POST['school'] : array();
    $degrees     = isset($_POST['degree']) ? $_POST['degree'] : array();
    $completions = isset($_POST['completion']) ? $_POST['completion'] : array();

    $companies   = isset($_POST['company']) ? $_POST['company'] : array();
    $positions   = isset($_POST['position']) ? $_POST['position'] : array();
    $dates       = isset($_POST['date']) ? $_POST['date'] : array();
    $exp_descs   = isset($_POST['experience_desc']) ? $_POST['experience_desc'] : array();

    $cert_names  = isset($_POST['cert_name']) ? $_POST['cert_name'] : array();
    $cert_orgs   = isset($_POST['cert_org']) ? $_POST['cert_org'] : array();
    $cert_years  = isset($_POST['cert_date']) ? $_POST['cert_date'] : array();

    $proj_names  = isset($_POST['project_name']) ? $_POST['project_name'] : array();
    $proj_techs  = isset($_POST['project_tech']) ? $_POST['project_tech'] : array();
    $proj_descs  = isset($_POST['project_desc']) ? $_POST['project_desc'] : array();

    // 3. Insert resume
    $stmt = $conn->prepare("INSERT INTO resumes (user_id, career_objective, skills, link, phone, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    if (!$stmt) { die("Prepare failed: ".$conn->error); }
    $stmt->bind_param("issss", $user_id, $career, $skills, $link, $phone);
    $stmt->execute();
    $resume_id = $conn->insert_id;

    // 4. Insert education
    $stmtEdu = $conn->prepare("INSERT INTO resume_education (resume_id, degree, college, year) VALUES (?, ?, ?, ?)");
    if (!$stmtEdu) { die("Prepare failed: ".$conn->error); }
    foreach ($degrees as $i => $degree) {
        if (!empty($degree)) {
            $school     = $schools[$i];
            $completion = $completions[$i];
            $stmtEdu->bind_param("isss", $resume_id, $degree, $school, $completion);
            $stmtEdu->execute();
        }
    }

    // 5. Insert experience
    $stmtExp = $conn->prepare("INSERT INTO resume_experience (resume_id, company, position, date_range, description) VALUES (?, ?, ?, ?, ?)");
    if (!$stmtExp) { die("Prepare failed: ".$conn->error); }
    foreach ($companies as $i => $company) {
        if (!empty($company)) {
            $stmtExp->bind_param("issss", $resume_id, $company, $positions[$i], $dates[$i], $exp_descs[$i]);
            $stmtExp->execute();
        }
    }

    // 6. Insert certifications
    $stmtCert = $conn->prepare("INSERT INTO resume_certifications (resume_id, certificate_name, issued_by, year) VALUES (?, ?, ?, ?)");
    if (!$stmtCert) { die("Prepare failed: ".$conn->error); }
    foreach ($cert_names as $i => $cert) {
        if (!empty($cert)) {
            $stmtCert->bind_param("isss", $resume_id, $cert, $cert_orgs[$i], $cert_years[$i]);
            $stmtCert->execute();
        }
    }

    // 7. Insert projects
    $stmtProj = $conn->prepare("INSERT INTO resume_projects (resume_id, title, technology, description) VALUES (?, ?, ?, ?)");
    if (!$stmtProj) { die("Prepare failed: ".$conn->error); }
    foreach ($proj_names as $i => $pname) {
        if (!empty($pname)) {
            $stmtProj->bind_param("isss", $resume_id, $pname, $proj_techs[$i], $proj_descs[$i]);
            $stmtProj->execute();
        }
    }

    // 8. Redirect to template3.php with resume_id
    echo "<script>alert('Resume created successfully!'); window.location.href='templates/template3.php?resume_id={$resume_id}';</script>";
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Your Resume</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            margin:0;
            font-family: Arial, sans-serif;
            background: #dde9f7;
            background-repeat: no-repeat;
            background-size: cover;
        }
        .topnav {
            display:flex;
            justify-content:flex-end;
            background:#333;
            padding:10px;
        }
        .topnav a {
            color:white;
            text-decoration:none;
            margin-left:15px;
        }
        .form-container {
            max-width:700px;
            margin:50px auto;
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(15px);
            padding:30px;
            border-radius:15px;
            box-shadow:0 8px 32px rgba(0,0,0,0.1);
        }
        .form-container h2 {
            text-align:center;
            margin-bottom:20px;
            color:#333;
        }
        .form-group {
            margin-bottom:15px;
        }
        .form-group label {
            display:block;
            margin-bottom:5px;
            color:#333;
        }
        .form-group input, .form-group textarea {
            width:100%;
            padding:10px;
            border-radius:8px;
            border:1px solid #ccc;
            font-size:16px;
        }
        .form-group textarea {
            resize: vertical;
        }
        .btn {
            padding:12px 25px;
            font-size:18px;
            border:none;
            border-radius:8px;
            cursor:pointer;
            background:#04AA6D;
            color:white;
            width:100%;
            margin-top:10px;
        }
        .education-row {
    display: flex;
    gap: 10px;
    margin-top: 10px;
    align-items: center;
}
.education-row input {
    flex: 1;
    padding: 8px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 14px;
}
.remove-edu {
    background: none;
    border: none;
    color: red;
    font-size: 20px;
    cursor: pointer;
    padding: 0 5px;
}
#add-education {
    background: #04AA6D;
    color: white;
    border: none;
    padding: 6px 12px;
    font-size: 14px;
    border-radius: 6px;
    cursor: pointer;
    margin-top: 10px;
}
#add-education:hover {
    opacity: 0.8;
}
.remove-edu:hover {
    opacity: 0.6;
}
.skills-input-container {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    padding: 5px;
    border: 1px solid #ccc;
    border-radius: 6px;
    min-height: 40px;
}
.skills-input-container input {
    border: none;
    outline: none;
    padding: 5px;
    flex: 150px 1 0;
}
.skill-tag {
    background: #007bff;
    color: white;
    padding: 5px 10px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    font-size: 14px;
}
.skill-tag span {
    margin-left: 5px;
    cursor: pointer;
    font-weight: bold;
}
.skill-tag span:hover {
    opacity: 0.7;
}
* { box-sizing: border-box; }

/* container card */
.experience-row {
  margin-top: 10px;
  padding: 10px;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  background: #fff;
}

/* TOP ROW: three inputs + remove button in one line */
.exp-top {
  display: flex;
  gap: 10px;
  margin-top: 10px;
  align-items: center;
}

/* inputs in the top row share space */
.exp-top input {
  flex: 1;
  width: 127px;
  padding: 8px;
  border-radius: 6px;
  border: 1px solid #ccc;
  font-size: 14px;
  background: #fafafa;
}

/* small remove button at the end of top row */
.remove-exp {
  background: none;
  border: none;
  color: #d33;
  font-size: 20px;
  cursor: pointer;
  padding: 0 6px;
  line-height: 1;
}

/* description textarea full width, on next line */
.experience-row textarea {
  width: 100%;
  margin-top: 8px;
  padding: 8px;
  border-radius: 6px;
  border: 1px solid #ccc;
  font-size: 14px;
  resize: vertical;
  background: #fafafa;
}

/* add button styling */
#add-experience {
  background: #04AA6D;
  color: white;
  border: none;
  padding: 8px 12px;
  border-radius: 6px;
  cursor: pointer;
  margin-top: 10px;
}
#add-experience:hover {
    opacity: 0.8;
}

.cert-row {
    display: flex;
    gap: 10px;
    margin-top: 10px;
    align-items: center;
}
.cert-row input {
    flex: 1;
    padding: 8px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 14px;
}
.remove-cert {
    background: none;
    border: none;
    color: red;
    font-size: 20px;
    cursor: pointer;
    padding: 0 5px;
}
#add-cert {
    background: #04AA6D;
    color: white;
    border: none;
    padding: 6px 12px;
    font-size: 14px;
    border-radius: 6px;
    cursor: pointer;
    margin-top: 10px;
}
#add-cert:hover {
    opacity: 0.8;
}
.remove-cert:hover {
    opacity: 0.6;
}
.project-row {
    display: flex;
    flex-direction: column;
    gap: 5px;
    margin-top: 10px;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 8px;
    position: relative;
}
.project-row input, .project-row textarea {
    width: 100%;
    padding: 8px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 14px;
}
.remove-project {
    position: absolute;
    top: 5px;
    right: 5px;
    background: none;
    border: none;
    color: red;
    font-size: 18px;
    cursor: pointer;
}
#add-project {
    background: #04AA6D;
    color: white;
    border: none;
    padding: 6px 12px;
    font-size: 14px;
    border-radius: 6px;
    cursor: pointer;
    margin-top: 10px;
}
#add-project:hover {
    opacity: 0.8;
}
.remove-project:hover {
    opacity: 0.6;
}
    </style>
</head>
<body>

    <!-- Top Navigation -->
    <div class="topnav">
        <a href="index.html">Dashboard</a>
        <a href="about.php">About</a>
    </div>

    <!-- Resume Form -->
    <div class="form-container">
        <h2>Create Your Resume</h2>
        <form id="resume-form" method="POST" action="form.php">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" name="name" id="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div class="form-group">
                <label for="link">LinkedIn URL</label>
                <input type="url" name="link" id="LinkedIn" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" name="phone" id="phone" required>
            </div>
            <div class="form-group" id="education-section">
                <label>Education</label>
            <div class="education-row">
                <input type="text" name="school[]" placeholder="University / School" required>
                <input type="text" name="degree[]" placeholder="Degree / Course" required>
                <input type="text" name="completion[]" placeholder="Completion Date" required>
            <button type="button" class="remove-edu" title="Remove">&times;</button>
    </div>
    <button type="button" id="add-education" title="Add Education">+ Add Education</button>
    </div>
            <div class="form-group" id="skills-section">
               <label>Skills</label>
            <div class="skills-input-container">
               <input type="text" name="skill-input" id="skill-input" placeholder="Type a skill and press Enter">
               <input type="hidden" name="skills" id="skills-hidden">
            </div>
            </div>
            <div class="form-group">
                <label for="objective">Career Objective</label>
                <textarea id="objective" name="career" rows="3"></textarea>
            </div>
            <label>Experience</label>

            <div id="experience-container">
            <div class="experience-row">
            <div class="exp-top">
                <input type="text" name="company[]" placeholder="Company / Industry" required>
                <input type="text" name="position[]" placeholder="Position" required>
                <input type="text" name="date[]" placeholder="From - To / Present" required>
                <button type="button" class="remove-exp" title="Remove">&times;</button>
            </div>
            <textarea name="experience_desc[]" placeholder="Experience Description / What you gained" rows="3"></textarea>
            </div>
            </div>

            <button type="button" id="add-experience">+ Add Experience</button><br><br>
            <div class="form-group" id="certification-section">
                 <label>Certifications</label>
            <div class="cert-row">
                 <input type="text" name="cert_name[]" placeholder="Certification Name" required>
                 <input type="text" name="cert_org[]" placeholder="Issuing Organization" required>
                 <input type="text" name="cert_date[]" placeholder="Date / Year" required>
            <button type="button" class="remove-cert" title="Remove">&times;</button>
            </div>
            <button type="button" id="add-cert" title="Add Certification">+ Add Certification</button>
            </div>
            <div class="form-group" id="project-section">
        <label>Projects</label>
        <div class="project-row">
        <input type="text" name="project_name[]" placeholder="Project Name" required>
        <input type="text" name="project_tech[]" placeholder="Technologies Used" required>
        <textarea name="project_desc[]" placeholder="Project Description" rows="2" required></textarea>
        <button type="button" class="remove-project" title="Remove">&times;</button>
        </div>

        <button type="button" id="add-project" title="Add Project">+ Add Project</button>
        </div>
            <button type="submit" class="btn">Generate Resume</button>
        </form>
        </div>

    <script>
        const form = document.getElementById('resume-form');
        form.addEventListener('submit', (e) => {
            document.getElementById('skills-hidden').value = skills.join(',');
            // backend integration will handle PDF/save later
        });
        const addEduBtn = document.getElementById('add-education');
const eduSection = document.getElementById('education-section');

addEduBtn.addEventListener('click', () => {
    const newRow = document.createElement('div');
    newRow.classList.add('education-row');
    
    newRow.innerHTML = `
        <input type="text" name="school[]" placeholder="University / School" required>
        <input type="text" name="degree[]" placeholder="Degree / Course" required>
        <input type="text" name="completion[]" placeholder="Completion Date" required>
        <button type="button" class="remove-edu" title="Remove">&times;</button>
    `;
    
    eduSection.insertBefore(newRow, addEduBtn);

    // Remove functionality
    newRow.querySelector('.remove-edu').addEventListener('click', () => {
        newRow.remove();
    });
});
const skillInput = document.getElementById('skill-input');
const skillsContainer = document.querySelector('.skills-input-container');

let skills = [];

function addSkill(skill){
    skill = skill.trim();
    if(skill && !skills.includes(skill)){
        skills.push(skill);
        

        const tag = document.createElement('div');
        tag.classList.add('skill-tag');
        tag.innerHTML = `${skill} <span>&times;</span>`;
        
        // remove functionality
        tag.querySelector('span').addEventListener('click', () => {
            skills = skills.filter(s => s !== skill);
            tag.remove();
        });

        skillsContainer.insertBefore(tag, skillInput);
        skillInput.value = '';
    }
}

// add skill on enter
skillInput.addEventListener('keydown', (e) => {
    if(e.key === 'Enter'){
        e.preventDefault();
        addSkill(skillInput.value);
    }
});
const addExpBtn = document.getElementById('add-experience');
const expContainer = document.getElementById('experience-container');

addExpBtn.addEventListener('click', () => {
  const newRow = document.createElement('div');
  newRow.className = 'experience-row';

  newRow.innerHTML = `
    <div class="exp-top">
      <input type="text" name="company[]" placeholder="Company / Industry" required>
      <input type="text" name="position[]" placeholder="Position" required>
      <input type="text" name="date[]" placeholder="From - To / Present" required>
      <button type="button" class="remove-exp" title="Remove">&times;</button>
    </div>
    <textarea name="experience_desc[]" placeholder="Experience Description / What you gained" rows="3"></textarea>
  `;

  // attach remove handler BEFORE inserting (safe)
  newRow.querySelector('.remove-exp').addEventListener('click', () => {
    newRow.remove();
  });

  expContainer.appendChild(newRow);
});

// also add remove handlers for any pre-existing remove buttons (initial row)
document.querySelectorAll('#experience-container .remove-exp').forEach(btn => {
  btn.addEventListener('click', (e) => {
    btn.closest('.experience-row').remove();
  });
});

const addCertBtn = document.getElementById('add-cert');
const certSection = document.getElementById('certification-section');

addCertBtn.addEventListener('click', () => {
    const newRow = document.createElement('div');
    newRow.classList.add('cert-row');

    newRow.innerHTML = `
        <input type="text" name="cert_name[]" placeholder="Certification Name" required>
        <input type="text" name="cert_org[]" placeholder="Issuing Organization" required>
        <input type="text" name="cert_date[]" placeholder="Date / Year" required>
        <button type="button" class="remove-cert" title="Remove">&times;</button>
    `;

    certSection.insertBefore(newRow, addCertBtn);

    // remove functionality
    newRow.querySelector('.remove-cert').addEventListener('click', () => {
        newRow.remove();
    });
});
const addProjectBtn = document.getElementById('add-project');
const projectSection = document.getElementById('project-section');

addProjectBtn.addEventListener('click', () => {
    const newRow = document.createElement('div');
    newRow.classList.add('project-row');

    newRow.innerHTML = `
        <input type="text" name="project_name[]" placeholder="Project Name" required>
        <input type="text" name="project_tech[]" placeholder="Technologies Used" required>
        <textarea name="project_desc[]" placeholder="Project Description" rows="2" required></textarea>
        <button type="button" class="remove-project" title="Remove">&times;</button>
    `;

    projectSection.insertBefore(newRow, addProjectBtn);

    // remove functionality
    newRow.querySelector('.remove-project').addEventListener('click', () => {
        newRow.remove();
    });
});
    </script>

</body>
</html>
