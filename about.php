<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>About — Resume Builder</title>
<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
  :root{
    --bg1: #0f1724;
    --glass: rgba(255,255,255,0.06);
    --glass-border: rgba(255,255,255,0.12);
    --muted: #cbd5e1;
    --accent: #7c3aed;
    --card-bg: rgba(255,255,255,0.05);
  }
  body{
    margin:0;
    min-height:100vh;
    background: linear-gradient(135deg,#07102a 0%, #0f1724 100%);
    font-family: Inter, "Segoe UI", Roboto, Arial, sans-serif;
    color:var(--muted);
    padding:40px 20px;
  }
  .container{ max-width:1100px; margin:auto; }

  .hero{text-align:center; margin-bottom:40px;}
  .hero h1{font-size:32px; color:#fff; margin-bottom:10px;}
  .hero p{font-size:15px; max-width:700px; margin:auto; opacity:0.9;}

  .section{ margin-bottom:50px; }
  .section h2{
    font-size:22px;
    color:#fff;
    margin-bottom:16px;
    border-left:4px solid var(--accent);
    padding-left:10px;
  }
  .grid{
    display:grid;
    grid-template-columns: repeat(auto-fit, minmax(250px,1fr));
    gap:40px;
  }

  .card{
    background:var(--card-bg);
    border:1px solid var(--glass-border);
    border-radius:14px;
    padding:18px;
    margin-bottom:16px;
    backdrop-filter: blur(10px) saturate(120%);
  }
  .card h3{margin:0 0 8px; color:#fff; font-size:16px;}
  .card p{margin:0; font-size:14px; line-height:1.5;}

  ul{margin:0; padding-left:0; list-style:none;}
  ul li{
    display:flex;
    align-items:center;
    margin-bottom:10px;
    font-size:14px;
  }
  ul li i{
    color:var(--accent);
    margin-right:10px;
    min-width:18px;
    text-align:center;
  }

  .footer{text-align:center;margin-top:50px;font-size:13px;color:var(--muted);}
  .footer a{color:var(--accent);text-decoration:none;}
</style>
</head>
<body>
<div class="container">

  <!-- Hero -->
  <div class="hero">
    <h1>About AI-Powered CV/Resume Builder</h1>
    <p>
      A minor project developed to help students and professionals create resumes quickly, with modern features like PDF export and auto-generated career objectives.
    </p>
  </div>

  <!-- Project Overview -->
  <div class="section">
    <h2>Project Overview</h2>
    <div class="card">
      <p>
        This Resume Builder helps students and professionals create resumes quickly and download them in PDF format.  
        Users can add and edit education, skills, experience, and projects.  
        It also includes auto-generated career objectives and AI-powered suggestions (future scope).
      </p>
    </div>
  </div>

  <!-- Motivation / Purpose -->
  <div class="section">
    <h2>Motivation / Purpose</h2>
    <div class="card">
      <p>
        The project was built to simplify resume creation for college students and freshers who may not have design skills.  
        It is developed as part of a minor project to demonstrate integration of web development and document generation.
      </p>
    </div>
  </div>

  <!-- Features -->
  <div class="section">
    <h2>Features</h2>
    <div class="card">
      <ul>
        <li><i class="fas fa-file-alt"></i> Easy resume creation with input forms</li>
        <li><i class="fas fa-file-pdf"></i> PDF download option</li>
        <li><i class="fas fa-robot"></i> Auto career objective generation</li>
        <li><i class="fas fa-desktop"></i> User-friendly interface</li>
        <li><i class="fas fa-lock"></i> Secure login & session management</li>
      </ul>
    </div>
  </div>

  <!-- Tech Cards -->
   <div class="section">
    <h2>Technology</h2>
   </div>
    <div class="grid">
      <div class="card">
        <div class="icon"><i class="fab fa-html5"></i></div>
        <h3>Frontend</h3>
        <p>Built using HTML, CSS, and JavaScript to design responsive forms and preview layouts.</p>
      </div>

      <div class="card">
        <div class="icon"><i class="fas fa-code"></i></div>
        <h3>Backend</h3>
        <p>PHP powers the server-side logic including authentication, session handling, and data storage.</p>
      </div>

      <div class="card">
        <div class="icon"><i class="fas fa-file-pdf"></i></div>
        <h3>PDF Export</h3>
        <p>FPDF library generates professional resume PDFs from the stored data.</p>
      </div>

      <div class="card">
        <div class="icon"><i class="fas fa-database"></i></div>
        <h3>Database</h3>
        <p>MySQL / MariaDB stores user profiles, resumes, and related details in structured tables.</p>
      </div>

      <div class="card">
        <div class="icon"><i class="fas fa-laptop-code"></i></div>
        <h3>Editor</h3>
        <p>The project was developed using VS Code. It helped in writing clean, efficient PHP, HTML, CSS, and JavaScript code, making development faster and organized.</p>
      </div>

    </div>

  <!-- Team / Credits -->
  <div class="section">
    <h2>Team / Credits</h2>
    <div class="card">
      <p>Developed by <strong>ANAS WARSI</strong>, Diploma Student at Govt. Polytechnic Shahjahanpur</p>
    </div>
  </div>

  <!-- Future Enhancements -->
  <div class="section">
    <h2>Future Enhancements</h2>
    <div class="card">
      <ul>
        <li><i class="fas fa-robot"></i> AI-powered full resume suggestions</li>
        <li><i class="fas fa-layer-group"></i> Multiple resume templates</li>
        <li><i class="fas fa-file-word"></i> Export to Word/Docx</li>
        <li><i class="fas fa-briefcase"></i> Integration with job portals</li>
      </ul>
    </div>
  </div>

  <!-- Contact -->
  <div class="section">
    <h2>Contact</h2>
    <div class="card">
      <p>Email: <a href="mailto:anaswarsi51@gmail.com">anaswarsi51@gmail.com</a></p>
      <p>GitHub: <a href="https://github.com/anas-warsi" target="_blank">github.com/anas-warsi</a></p>
    </div>
  </div>

  <!-- Footer -->
  <div class="footer">
    <p>Made with ❤️ using PHP & MySQL. | <a href="dashboard.php">Back to Home</a></p>
  </div>

</div>
</body>
</html>
