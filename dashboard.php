<?php
session_start();

// Check if user is logged in
if(!isset($_SESSION['user_id'])){
    header('Location: login.php');
    exit;
}

$user_name = $_SESSION['user_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI-Powered CV/Resume Builder</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .profile {
            position: relative;
            display: inline-block;
        }
        .profile button {
            background-color: #333;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: bold;
            padding: 8px 15px;
            border-radius: 5px;
            transition: 0.3s;
        }
        .profile button:hover {
            background-color: #555;
        }
        .profile-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            border-radius: 8px;
            z-index: 1;
        }
        .profile-content a {
            color: #333;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }
        .profile-content a:hover {
            background-color: #ddd;
        }
        .profile:hover .profile-content {
            display: block;
        }
        body { margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(-45deg, #00ff88, #84de17, #ff8921, #36de74); background-size: 400% 400%; animation: gradientAnimation 15s ease infinite; color: #333; } /* Top Navigation */ .topnav { display: flex; justify-content: flex-end; padding: 20px 50px; background-color: #333; box-shadow: 0 4px 6px rgba(0,0,0,0.2); } .topnav a { color: white; text-decoration: none; margin-left: 20px; font-weight: bold; transition: 0.3s; } .topnav a:hover { color: #ff4d4d; } /* Hero Section */ .hero { display: flex; flex-direction: column; align-items: center; justify-content: center; height: 80vh; text-align: center; } .hero h1 { font-size: 50px; margin-bottom: 20px; color: #660000; text-shadow: 2px 2px 5px rgba(0,0,0,0.2); } .hero p { font-size: 20px; margin-bottom: 30px; color: #990000; } .hero .btn { padding: 15px 50px; font-size: 22px; color: white; background-color: red; border: none; border-radius: 12px; cursor: pointer; box-shadow: 0 4px 8px rgba(0,0,0,0.3); transition: all 0.3s ease; } .hero .btn:hover { background-color: darkred; transform: scale(1.05); } /* Features Section */ .features { display: flex; justify-content: space-around; flex-wrap: wrap; padding: 50px; background: #dadfdc8a; border-radius: 10px; } .feature-card { background-color: white; border-radius: 12px; width: 250px; text-align: center; padding: 25px 15px; margin: 5px; box-shadow: 0 4px 8px rgba(0,0,0,0.2); transition: transform 0.3s, box-shadow 0.3s; } .feature-card:hover { transform: translateY(-10px); box-shadow: 0 4px 6px rgba(255, 0, 0, 0.751), 0 8px 20px rgba(0, 200, 255, 0.425), 0 12px 30px rgba(102, 255, 0, 0.386); } .feature-card i { font-size: 40px; color: red; margin-bottom: 15px; } .feature-card h3 { margin-bottom: 10px; color: #660000; } .feature-card p { color: #555; } /* Footer */ footer { background-color: #333; color: white; text-align: center; padding: 20px 0; margin-top: 50px; box-shadow: 0 -4px 6px rgba(0,0,0,0.2); } /* Responsive */ @media (max-width: 768px) { .hero h1 { font-size: 36px; } .hero p { font-size: 16px; } .hero .btn { font-size: 18px; padding: 12px 35px; } .features { flex-direction: column; align-items: center; }
    </style>
</head>
<body>

    <!-- Top Navigation -->
    <div class="topnav">
        <div class="profile">
            <button><i class="fa fa-user"></i> Profile</button>
            <div class="profile-content">
                <span style="padding:12px 16px; display:block;"><?php echo htmlspecialchars($user_name); ?></span>
                <a href="logout.php" style="color: #d62727ff;">Logout</a>
            </div>
        </div>
        <a href="about.php">About</a>
    </div>

    <!-- Hero Section -->
    <section class="hero">
        <h1>AI-Powered CV/Resume Builder</h1>
        <p>Create professional resumes in seconds with AI-powered suggestions.</p>
        <button class="btn" id="create-resume">
         Create Resume <i class="fa fa-arrow-right"></i>
        </button>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="feature-card">
            <i class="fa fa-bolt"></i>
            <h3>Fast Resume Creation</h3>
            <p>Generate professional resumes in just a few clicks.</p>
        </div>
        <div class="feature-card">
            <i class="fa fa-robot"></i>
            <h3>AI-Powered Suggestions</h3>
            <p>Get smart career objective and skill suggestions automatically.</p>
        </div>
        <div class="feature-card">
            <i class="fa fa-file-alt"></i>
            <h3>Multiple Templates</h3>
            <p>Choose from multiple modern and clean resume designs.</p>
        </div>
        <div class="feature-card">
            <i class="fa fa-download"></i>
            <h3>Download as PDF</h3>
            <p>Download your resume in PDF format for easy sharing.</p>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 AI-Powered CV/Resume Builder | All rights reserved</p>
    </footer>

</body>
<script>
    const createBtn = document.getElementById('create-resume');

    createBtn.addEventListener('click', () => {
        // User is logged in, go directly to form.html
        window.location.href = 'form.php';
    });
</script>

</html>
