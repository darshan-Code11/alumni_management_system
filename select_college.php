<?php
session_start();
require_once __DIR__ . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $college = trim($_POST['college_name']);
    if (!empty($college)) {
        $_SESSION['current_college'] = $college;
        header("Location: index.php");
        exit;
    }
}
$pageTitle = "Select Your College - AlumniConnect";
?>
<?php include 'includes/header.php'; ?>
<div class="hero position-relative overflow-hidden" style="min-height: 80vh; display:flex; align-items:center;">
    <div class="blob-shape bg-warning" style="width:400px; height:400px; top:-100px; left:-100px; animation-delay:0s;"></div>
    <div class="blob-shape bg-info" style="width:300px; height:300px; bottom:-50px; right:10%; animation-delay:2s;"></div>
    
    <div class="container position-relative z-1 text-center">
        <h1 class="display-4 fw-bold text-white mb-4">Welcome to <span class="text-warning">AlumniConnect</span></h1>
        <p class="lead text-white opacity-75 mb-5 fs-5">Please enter the name of your college to continue and connect with your specific alumni network.</p>
        
        <div class="card p-5 mx-auto bg-glass shadow-lg" style="max-width: 500px; border-radius: 20px;">
            <form method="POST">
                <div class="mb-4">
                    <label class="form-label fw-bold text-start w-100 text-dark">Select Your College</label>
                    <select name="college_name" class="form-select form-select-lg border-2 shadow-sm" required autofocus>
                        <option value="" disabled selected>-- Choose your college --</option>
                        <option value="Kristu Jayanti University">Kristu Jayanti University</option>
                        <option value="Ramaiah Institute of Technology">Ramaiah Institute of Technology</option>
                        <option value="M.S. Ramaiah College of Arts, Science and Commerce">M.S. Ramaiah College of Arts, Science and Commerce</option>
                        <option value="ISBR Business School">ISBR Business School</option>
                        <option value="Faculty of Engineering and Technology, M.S. Ramaiah University of Applied Sciences">Faculty of Engineering and Technology, M.S. Ramaiah University of Applied Sciences</option>
                        <option value="International Institute of Information Technology, Bangalore">International Institute of Information Technology, Bangalore</option>
                        <option value="Christ University">Christ University</option>
                        <option value="Jain Deemed-to-be University, Bangalore">Jain Deemed-to-be University, Bangalore</option>
                        <option value="Alliance University, Bangalore">Alliance University, Bangalore</option>
                        <option value="Swami Vivekananda Yoga Anusandhana Samsthana">Swami Vivekananda Yoga Anusandhana Samsthana</option>
                        <option value="IIM Bangalore - Indian Institute of Management">IIM Bangalore - Indian Institute of Management</option>
                        <option value="Indian Institute of Science">Indian Institute of Science</option>
                        <option value="R.V. College of Engineering">R.V. College of Engineering</option>
                        <option value="Dayananda Sagar College">Dayananda Sagar College </option>
                        <option value="New Horizon College, Kasturinagar">New Horizon College, Kasturinagar</option>
                        <option value="Global University">Global University</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-warning btn-lg w-100 fw-bold rounded-pill text-white shadow-sm" style="background: linear-gradient(135deg, var(--emerald) 0%, var(--emerald-dark) 100%); border:none;">
                    Enter Alumni Network <i class="fas fa-arrow-right ms-2"></i>
                </button>
            </form>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
