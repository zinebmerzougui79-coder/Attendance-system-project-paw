<?php
session_start();

// Store user info before destroying session (for display)
$username = $_SESSION['username'] ?? 'User';
$login_time = $_SESSION['login_time'] ?? date('Y-m-d H:i:s');
$logout_time = date('Y-m-d H:i:s');

// Calculate session duration if login time is available
$session_duration = 'N/A';
if (isset($_SESSION['login_time'])) {
    $login_timestamp = strtotime($_SESSION['login_time']);
    $logout_timestamp = strtotime($logout_time);
    $duration_seconds = $logout_timestamp - $login_timestamp;
    
    if ($duration_seconds < 60) {
        $session_duration = $duration_seconds . ' seconds';
    } elseif ($duration_seconds < 3600) {
        $session_duration = floor($duration_seconds / 60) . ' minutes';
    } else {
        $hours = floor($duration_seconds / 3600);
        $minutes = floor(($duration_seconds % 3600) / 60);
        $session_duration = $hours . 'h ' . $minutes . 'm';
    }
}

// Destroy all session data
session_unset();
session_destroy();

// Clear session cookie completely
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout - Attendance System</title>
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
            display: flex;
            flex-direction: column;
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
        
        .navbar a:hover {
            background: #0984e3;
            color: white;
        }
        
        .logout-container {
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 1;
            padding: 20px;
        }
        
        .logout-box {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 40px;
            text-align: center;
            max-width: 500px;
            width: 100%;
            animation: fadeInUp 0.8s ease-out;
            border-top: 5px solid var(--primary);
        }
        
        .logout-icon {
            font-size: 4rem;
            color: var(--primary);
            margin-bottom: 20px;
            animation: pulse 2s infinite;
        }
        
        .logout-title {
            color: var(--primary);
            margin-bottom: 15px;
            font-size: 2.2rem;
        }
        
        .logout-message {
            color: var(--gray);
            margin-bottom: 30px;
            font-size: 1.1rem;
            line-height: 1.6;
        }
        
        .button-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-family: inherit;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--primary-light);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(9, 132, 227, 0.3);
        }
        
        .btn-secondary {
            background: var(--secondary);
            color: white;
        }
        
        .btn-secondary:hover {
            background: #00a085;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 184, 148, 0.3);
        }
        
        .logout-details {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-top: 25px;
            text-align: left;
        }
        
        .detail-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .detail-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        
        .detail-label {
            font-weight: 600;
            color: var(--dark);
        }
        
        .detail-value {
            color: var(--gray);
        }
        
        .countdown {
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--primary);
            margin-top: 20px;
        }
        
        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
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
            
            .logout-box {
                padding: 30px 20px;
                margin: 10px;
            }
            
            .button-group {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
            
            .logout-title {
                font-size: 1.8rem;
            }
            
            .logout-icon {
                font-size: 3rem;
            }
        }
        
        @media (min-width: 769px) and (max-width: 1024px) {
            .logout-box {
                max-width: 450px;
            }
        }
        
        /* Auto logout animation */
        .auto-logout-bar {
            height: 5px;
            background: var(--primary);
            width: 100%;
            border-radius: 5px;
            margin-top: 20px;
            animation: shrink 10s linear forwards;
        }
        
        @keyframes shrink {
            from {
                width: 100%;
            }
            to {
                width: 0%;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <ul>
            <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="attendance.php"><i class="fas fa-list"></i> Attendance List</a></li>
            <li><a href="add-student.php"><i class="fas fa-user-plus"></i> Add Student</a></li>
            <li><a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
            <li><a href="logout.php" class="active"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </nav>

    <!-- Logout Content -->
    <div class="logout-container">
        <div class="logout-box">
            <div class="logout-icon">
                <i class="fas fa-sign-out-alt"></i>
            </div>
            
            <h1 class="logout-title">Logout Successful</h1>
            
            <p class="logout-message">
                You have been successfully logged out of the Student Attendance Management System. 
                Thank you for using our platform.
            </p>
            
            <div class="logout-details">
                <div class="detail-item">
                    <span class="detail-label">User:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($username); ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Session Started:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($login_time); ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Session Ended:</span>
                    <span class="detail-value" id="logoutTime"><?php echo $logout_time; ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Session Duration:</span>
                    <span class="detail-value"><?php echo $session_duration; ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">System:</span>
                    <span class="detail-value">Student Attendance Management</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Security Status:</span>
                    <span class="detail-value" style="color: var(--secondary); font-weight: bold;">
                        <i class="fas fa-shield-check"></i> All session data cleared
                    </span>
                </div>
            </div>
            
            <div class="button-group">
                <a href="index.php" class="btn btn-primary">
                    <i class="fas fa-home"></i> Return to Home
                </a>
                <a href="login.php" class="btn btn-secondary">
                    <i class="fas fa-sign-in-alt"></i> Login Again
                </a>
            </div>
            
            <div class="countdown">
                Redirecting to home page in <span id="countdown">10</span> seconds
            </div>
            
            <div class="auto-logout-bar"></div>
        </div>
    </div>

    <script>
        // Countdown timer for auto-redirect
        let countdown = 10;
        const countdownElement = document.getElementById('countdown');
        
        const countdownInterval = setInterval(() => {
            countdown--;
            countdownElement.textContent = countdown;
            
            if (countdown <= 0) {
                clearInterval(countdownInterval);
                window.location.href = 'index.php';
            }
        }, 1000);
        
        // Clear any remaining user session data from localStorage
        function clearSessionData() {
            // Clear authentication-related data
            localStorage.removeItem('currentUser');
            localStorage.removeItem('authToken');
            localStorage.removeItem('userSession');
            localStorage.removeItem('lastActivity');
            
            // Clear any temporary form data
            const keysToRemove = [];
            for (let i = 0; i < localStorage.length; i++) {
                const key = localStorage.key(i);
                if (key && key.startsWith('temp_')) {
                    keysToRemove.push(key);
                }
            }
            
            keysToRemove.forEach(key => {
                localStorage.removeItem(key);
            });
            
            console.log('Session data cleared successfully');
        }
        
        // Call the function to clear session data
        clearSessionData();
        
        // Add animation to buttons on hover
        document.querySelectorAll('.btn').forEach(button => {
            button.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
            });
            
            button.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
        
        // Add page load analytics
        window.addEventListener('load', function() {
            console.log('Logout page loaded successfully');
            if (typeof gtag !== 'undefined') {
                gtag('event', 'logout', {
                    'event_category': 'authentication',
                    'event_label': 'user_logout'
                });
            }
        });
    </script>
</body>
</html>