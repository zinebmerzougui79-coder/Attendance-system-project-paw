<?php
include 'config.php';

// Get statistics from database
try {
    $total_students = $db->query("SELECT COUNT(*) FROM students")->fetchColumn();
    
    // Calculate today's attendance
    $today = date('Y-m-d');
    $today_present = $db->query("
        SELECT COUNT(DISTINCT student_id) 
        FROM attendance_records 
        WHERE DATE(recorded_at) = '$today' AND status = 1
    ")->fetchColumn();
    
    // Total sessions (this week)
    $week_start = date('Y-m-d', strtotime('-7 days'));
    $total_sessions = $db->query("
        SELECT COUNT(DISTINCT session_id) 
        FROM attendance_records 
        WHERE DATE(recorded_at) >= '$week_start'
    ")->fetchColumn();
    
    // Average participation rate
    $participation_stats = $db->query("
        SELECT 
            COUNT(*) as total_records,
            SUM(status) as participated_count
        FROM participation_records
    ")->fetch(PDO::FETCH_ASSOC);
    
    $avg_participation = $participation_stats['total_records'] > 0 ? 
        round(($participation_stats['participated_count'] / $participation_stats['total_records']) * 100) : 0;
        
} catch (Exception $e) {
    $total_students = $today_present = $total_sessions = $avg_participation = 0;
}

// Get recent students for activity feed
try {
    $recent_students = $db->query("
        SELECT firstname, lastname, created_at 
        FROM students 
        ORDER BY created_at DESC 
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $recent_students = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Attendance System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* YOUR EXACT ORIGINAL CSS - NO CHANGES */
        :root {
            --primary: #0984e3;
            --primary-light: #74b9ff;
            --secondary: #00b894;
            --warning: #fdcb6e;
            --danger: #e17055;
            --light: #f5f6fa;
            --dark: #2d3436;
            --gray: #636e72;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #74b9ff, #0984e3);
            color: #333;
            min-height: 100vh;
        }
        
        .navbar {
            background: rgba(255,255,255,0.9);
            padding: 15px;
            border-radius: 10px;
            margin: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .navbar ul {
            list-style: none;
            display: flex;
            justify-content: center;
            padding: 0;
            margin: 0;
            flex-wrap: wrap;
        }
        
        .navbar a {
            text-decoration: none;
            color: #0984e3;
            margin: 5px 10px;
            font-weight: bold;
            padding: 10px 15px;
            border-radius: 5px;
            transition: 0.3s;
            display: block;
        }
        
        .navbar a:hover, .navbar a.active {
            background: #0984e3;
            color: white;
        }
        
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        
        .welcome-section {
            text-align: center;
            padding: 40px 20px;
            background: linear-gradient(135deg, #74b9ff, #0984e3);
            color: white;
            border-radius: 10px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }
        
        .welcome-section::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23007bff" fill-opacity="0.1" d="M0,224L48,213.3C96,203,192,181,288,181.3C384,181,480,203,576,202.7C672,203,768,181,864,165.3C960,149,1056,139,1152,149.3C1248,160,1344,192,1392,208L1440,224L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
            background-size: cover;
            background-position: bottom;
        }
        
        .welcome-section h1 {
            font-size: 2.5rem;
            margin-bottom: 15px;
            position: relative;
        }
        
        .welcome-section p {
            font-size: 1.2rem;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
            position: relative;
        }
        
        .dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            padding: 25px;
            text-align: center;
            border-left: 5px solid var(--primary);
            transition: transform 0.3s, box-shadow 0.3s;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: var(--primary);
        }
        
        .stat-card:nth-child(2)::before {
            background: var(--secondary);
        }
        
        .stat-card:nth-child(3)::before {
            background: var(--warning);
        }
        
        .stat-card:nth-child(4)::before {
            background: var(--danger);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        }
        
        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: var(--primary);
        }
        
        .stat-card:nth-child(2) .stat-icon {
            color: var(--secondary);
        }
        
        .stat-card:nth-child(3) .stat-icon {
            color: var(--warning);
        }
        
        .stat-card:nth-child(4) .stat-icon {
            color: var(--danger);
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--primary);
            margin: 10px 0;
        }
        
        .stat-card:nth-child(2) .stat-number {
            color: var(--secondary);
        }
        
        .stat-card:nth-child(3) .stat-number {
            color: var(--warning);
        }
        
        .stat-card:nth-child(4) .stat-number {
            color: var(--danger);
        }
        
        .stat-label {
            color: var(--gray);
            font-size: 1rem;
            font-weight: 500;
        }
        
        .recent-activity {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            padding: 25px;
            margin-top: 20px;
        }
        
        .activity-item {
            padding: 15px;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            transition: background 0.3s;
        }
        
        .activity-item:hover {
            background: #f8f9fa;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-icon {
            background: var(--primary);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        .activity-details {
            flex-grow: 1;
        }
        
        .activity-time {
            color: var(--gray);
            font-size: 0.85rem;
        }
        
        .section-title {
            color: var(--primary);
            margin-bottom: 20px;
            font-size: 1.5rem;
            border-bottom: 2px solid var(--primary-light);
            padding-bottom: 10px;
            display: flex;
            align-items: center;
        }
        
        .section-title i {
            margin-right: 10px;
        }
        
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 30px;
        }
        
        .action-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 15px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .action-btn:hover {
            background: #0a73d1;
            transform: translateY(-2px);
        }
        
        .action-btn:nth-child(2) {
            background: var(--secondary);
        }
        
        .action-btn:nth-child(2):hover {
            background: #00a383;
        }
        
        .action-btn:nth-child(3) {
            background: var(--warning);
        }
        
        .action-btn:nth-child(3):hover {
            background: #f4b84a;
        }
        
        /* Mobile First Responsive */
        @media (max-width: 768px) {
            .navbar ul {
                flex-direction: column;
                align-items: center;
            }
            
            .navbar a {
                margin: 5px 0;
                width: 200px;
                text-align: center;
            }
            
            .welcome-section h1 {
                font-size: 2rem;
            }
            
            .welcome-section p {
                font-size: 1rem;
            }
            
            .container {
                margin: 10px;
                padding: 15px;
            }
            
            .dashboard {
                grid-template-columns: 1fr;
            }
            
            .stat-card {
                padding: 20px;
            }
            
            .quick-actions {
                grid-template-columns: 1fr;
            }
        }
        
        @media (min-width: 769px) and (max-width: 1024px) {
            .dashboard {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <ul>
            <li><a href="index.php" class="active"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="attendance.php"><i class="fas fa-list"></i> Attendance List</a></li>
            <li><a href="add-student.php"><i class="fas fa-user-plus"></i> Add Student</a></li>
            <li><a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
            <li><a href="logout.php" id="logoutBtn"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <div class="welcome-section">
            <h1>Welcome to Student Attendance System</h1>
            <p>Manage student attendance, track participation, and generate reports all in one place.</p>
        </div>

        <div class="dashboard">
            <div class="stat-card">
                <i class="fas fa-users stat-icon"></i>
                <div class="stat-number" id="totalStudents"><?php echo $total_students; ?></div>
                <div class="stat-label">Total Students</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-user-check stat-icon"></i>
                <div class="stat-number" id="todayPresent"><?php echo $today_present; ?></div>
                <div class="stat-label">Present Today</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-calendar-alt stat-icon"></i>
                <div class="stat-number" id="totalSessions"><?php echo $total_sessions; ?></div>
                <div class="stat-label">Sessions This Week</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-chart-line stat-icon"></i>
                <div class="stat-number" id="avgParticipation"><?php echo $avg_participation; ?>%</div>
                <div class="stat-label">Avg Participation</div>
            </div>
        </div>

        <div class="quick-actions">
            <button class="action-btn" onclick="window.location.href='attendance.php'">
                <i class="fas fa-list"></i> Manage Attendance
            </button>
            <button class="action-btn" onclick="window.location.href='add-student.php'">
                <i class="fas fa-user-plus"></i> Add New Student
            </button>
            <button class="action-btn" onclick="window.location.href='reports.php'">
                <i class="fas fa-chart-bar"></i> View Reports
            </button>
        </div>

        <div class="recent-activity">
            <h2 class="section-title"><i class="fas fa-history"></i> Recent Activity</h2>
            <div id="recentActivity">
                <?php if (count($recent_students) === 0): ?>
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div class="activity-details">
                            <div>No students in the system</div>
                            <div class="activity-time">Add students to see activity</div>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($recent_students as $student): ?>
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div class="activity-details">
                            <div><strong><?php echo $student['firstname'] . ' ' . $student['lastname']; ?></strong> was added to the system</div>
                            <div class="activity-time"><?php echo timeAgo($student['created_at']); ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Load dashboard data
        document.addEventListener('DOMContentLoaded', function() {
            // Your original JavaScript functionality
            console.log("PAW Project Homepage Loaded");
            
            // Logout functionality
            document.getElementById('logoutBtn').addEventListener('click', function(e) {
                e.preventDefault();
                if (confirm('Are you sure you want to logout?')) {
                    alert('Logged out successfully!');
                    // In a real application, you would redirect to login page
                    // window.location.href = 'login.php';
                }
            });
        });

        // Helper function to format time ago (for display)
        function timeAgo(timestamp) {
            const now = new Date();
            const past = new Date(timestamp);
            const diff = Math.floor((now - past) / 1000); // difference in seconds
            
            if (diff < 60) return "Just now";
            if (diff < 3600) return Math.floor(diff / 60) + " minutes ago";
            if (diff < 86400) return Math.floor(diff / 3600) + " hours ago";
            if (diff < 2592000) return Math.floor(diff / 86400) + " days ago";
            return "Over a month ago";
        }
    </script>
</body>
</html>

<?php
// PHP helper function for time ago
function timeAgo($datetime) {
    $time = strtotime($datetime);
    $now = time();
    $diff = $now - $time;
    
    if ($diff < 60) return "Just now";
    if ($diff < 3600) return floor($diff / 60) . " minutes ago";
    if ($diff < 86400) return floor($diff / 3600) . " hours ago";
    if ($diff < 2592000) return floor($diff / 86400) . " days ago";
    return "Over a month ago";
}
?>