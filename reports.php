<?php
include 'config.php';

// Get report data from database
try {
    // Total students
    $total_students = $db->query("SELECT COUNT(*) FROM students")->fetchColumn();
    
    // Overall attendance rate
    $attendance_stats = $db->query("
        SELECT 
            COUNT(*) as total_records,
            SUM(status) as present_count
        FROM attendance_records
    ")->fetch(PDO::FETCH_ASSOC);
    
    $attendance_rate = $attendance_stats['total_records'] > 0 ? 
        round(($attendance_stats['present_count'] / $attendance_stats['total_records']) * 100) : 0;
    
    // Participation rate
    $participation_stats = $db->query("
        SELECT 
            COUNT(*) as total_records,
            SUM(status) as participated_count
        FROM participation_records
    ")->fetch(PDO::FETCH_ASSOC);
    
    $participation_rate = $participation_stats['total_records'] > 0 ? 
        round(($participation_stats['participated_count'] / $participation_stats['total_records']) * 100) : 0;
    
    // At-risk students (3+ absences)
    $at_risk_students = $db->query("
        SELECT 
            s.id,
            s.matricule,
            s.firstname,
            s.lastname,
            s.course,
            COUNT(CASE WHEN ar.status = 0 THEN 1 END) as absences,
            COUNT(CASE WHEN pr.status = 1 THEN 1 END) as participations
        FROM students s
        LEFT JOIN attendance_records ar ON s.id = ar.student_id
        LEFT JOIN participation_records pr ON s.id = pr.student_id
        GROUP BY s.id, s.matricule, s.firstname, s.lastname, s.course
        HAVING COUNT(CASE WHEN ar.status = 0 THEN 1 END) >= 3
        ORDER BY absences DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    $at_risk_count = count($at_risk_students);
    
    // Session-wise attendance
    $session_attendance = [];
    for ($i = 1; $i <= 6; $i++) {
        $session_stats = $db->query("
            SELECT COUNT(*) as present_count
            FROM attendance_records 
            WHERE session_id = $i AND status = 1
        ")->fetch(PDO::FETCH_ASSOC);
        $session_attendance[] = $session_stats['present_count'];
    }
    
    // Student status distribution
    $status_distribution = $db->query("
        SELECT 
            CASE 
                WHEN absences < 3 THEN 'Good (0-2 absences)'
                WHEN absences <= 4 THEN 'Warning (3-4 absences)'
                ELSE 'At Risk (5+ absences)'
            END as status_category,
            COUNT(*) as student_count
        FROM (
            SELECT 
                s.id,
                COUNT(CASE WHEN ar.status = 0 THEN 1 END) as absences
            FROM students s
            LEFT JOIN attendance_records ar ON s.id = ar.student_id
            GROUP BY s.id
        ) as student_absences
        GROUP BY status_category
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    // Initialize status counts
    $good_count = 0;
    $warning_count = 0;
    $danger_count = 0;
    
    foreach ($status_distribution as $status) {
        switch ($status['status_category']) {
            case 'Good (0-2 absences)':
                $good_count = $status['student_count'];
                break;
            case 'Warning (3-4 absences)':
                $warning_count = $status['student_count'];
                break;
            case 'At Risk (5+ absences)':
                $danger_count = $status['student_count'];
                break;
        }
    }
    
} catch (Exception $e) {
    $total_students = $attendance_rate = $participation_rate = $at_risk_count = 0;
    $session_attendance = array_fill(0, 6, 0);
    $at_risk_students = [];
    $good_count = $warning_count = $danger_count = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Attendance System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        
        .page-title {
            color: var(--primary);
            margin-bottom: 30px;
            text-align: center;
            font-size: 2rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--primary);
            margin: 10px 0;
        }
        
        .stat-label {
            color: var(--gray);
            font-size: 1rem;
            font-weight: 500;
        }
        
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .chart-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            padding: 25px;
        }
        
        .chart-title {
            color: var(--primary);
            margin-bottom: 20px;
            font-size: 1.3rem;
            text-align: center;
            border-bottom: 2px solid var(--primary-light);
            padding-bottom: 10px;
        }
        
        .chart-wrapper {
            height: 300px;
            position: relative;
        }
        
        .at-risk-section {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            padding: 25px;
            margin-top: 20px;
        }
        
        .at-risk-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        .at-risk-table th,
        .at-risk-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .at-risk-table th {
            background: var(--primary);
            color: white;
            font-weight: 600;
        }
        
        .at-risk-table tr:nth-child(even) {
            background: #f9f9f9;
        }
        
        .critical {
            color: var(--danger);
            font-weight: bold;
        }
        
        .warning {
            color: var(--warning);
            font-weight: bold;
        }
        
        button {
            padding: 12px 25px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
            font-family: inherit;
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 20px;
        }
        
        button:hover {
            background: var(--primary-light);
        }
        
        .section-title {
            color: var(--primary);
            margin-bottom: 20px;
            font-size: 1.5rem;
            border-bottom: 2px solid var(--primary-light);
            padding-bottom: 10px;
        }
        
        .no-data {
            text-align: center;
            color: var(--gray);
            padding: 40px;
            font-style: italic;
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
            
            .container {
                margin: 10px;
                padding: 15px;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .charts-grid {
                grid-template-columns: 1fr;
            }
            
            .chart-container {
                padding: 15px;
            }
            
            .chart-wrapper {
                height: 250px;
            }
            
            .at-risk-table th,
            .at-risk-table td {
                padding: 8px;
                font-size: 0.9rem;
            }
        }
        
        @media (min-width: 769px) and (max-width: 1024px) {
            .charts-grid {
                grid-template-columns: 1fr;
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
            <li><a href="reports.php" class="active"><i class="fas fa-chart-bar"></i> Reports</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <h1 class="page-title">Attendance Reports & Analytics</h1>
        
        <button id="generateReportBtn">
            <i class="fas fa-sync-alt"></i> Generate Latest Report
        </button>
        
        <div id="reportContent">
            <!-- Report content will be generated here -->
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number" id="totalStudents"><?php echo $total_students; ?></div>
                <div class="stat-label">Total Students</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="attendanceRate"><?php echo $attendance_rate; ?>%</div>
                <div class="stat-label">Overall Attendance Rate</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="participationRate"><?php echo $participation_rate; ?>%</div>
                <div class="stat-label">Participation Rate</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="atRiskStudents"><?php echo $at_risk_count; ?></div>
                <div class="stat-label">At-Risk Students</div>
            </div>
        </div>
        
        <div class="charts-grid">
            <div class="chart-container">
                <h3 class="chart-title">Attendance by Session</h3>
                <div class="chart-wrapper">
                    <canvas id="attendanceChart"></canvas>
                </div>
            </div>
            
            <div class="chart-container">
                <h3 class="chart-title">Student Status Distribution</h3>
                <div class="chart-wrapper">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
        
        <div class="at-risk-section">
            <h2 class="section-title">At-Risk Students (3+ Absences)</h2>
            <div id="atRiskList">
                <?php if (count($at_risk_students) === 0): ?>
                    <p class="no-data">No at-risk students found.</p>
                <?php else: ?>
                    <table class="at-risk-table">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Name</th>
                                <th>Course</th>
                                <th>Absences</th>
                                <th>Participation</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($at_risk_students as $student): 
                                $absences = $student['absences'] ?? 0;
                                $participations = $student['participations'] ?? 0;
                                $status = '';
                                $statusClass = '';
                                
                                if ($absences >= 5) {
                                    $status = 'Critical';
                                    $statusClass = 'critical';
                                } else if ($absences >= 3) {
                                    $status = 'Warning';
                                    $statusClass = 'warning';
                                }
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['matricule']); ?></td>
                                <td><?php echo htmlspecialchars($student['firstname'] . ' ' . $student['lastname']); ?></td>
                                <td><?php echo htmlspecialchars($student['course']); ?></td>
                                <td><?php echo $absences; ?></td>
                                <td><?php echo $participations; ?></td>
                                <td class="<?php echo $statusClass; ?>"><?php echo $status; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Chart instances
        let attendanceChartInstance = null;
        let statusChartInstance = null;
        
        // Generate charts on page load
        document.addEventListener('DOMContentLoaded', function() {
            generateCharts();
            
            // Add event listener to generate report button
            document.getElementById('generateReportBtn').addEventListener('click', function() {
                location.reload(); // Simple refresh to get latest data
                showNotification('Report updated successfully!', 'success');
            });
        });
        
        function generateCharts() {
            generateAttendanceChart();
            generateStatusChart();
        }
        
        function generateAttendanceChart() {
            const ctx = document.getElementById('attendanceChart').getContext('2d');
            const sessionAttendance = <?php echo json_encode($session_attendance); ?>;
            const sessionLabels = ['Session 1', 'Session 2', 'Session 3', 'Session 4', 'Session 5', 'Session 6'];
            
            // Destroy previous chart instance if exists
            if (attendanceChartInstance) {
                attendanceChartInstance.destroy();
            }
            
            attendanceChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: sessionLabels,
                    datasets: [{
                        label: 'Students Present',
                        data: sessionAttendance,
                        backgroundColor: [
                            '#0984e3', '#74b9ff', '#0984e3', 
                            '#74b9ff', '#0984e3', '#74b9ff'
                        ],
                        borderColor: [
                            '#0984e3', '#74b9ff', '#0984e3', 
                            '#74b9ff', '#0984e3', '#74b9ff'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Attendance by Session'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            },
                            title: {
                                display: true,
                                text: 'Number of Students'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Sessions'
                            }
                        }
                    }
                }
            });
        }
        
        function generateStatusChart() {
            const ctx = document.getElementById('statusChart').getContext('2d');
            const goodCount = <?php echo $good_count; ?>;
            const warningCount = <?php echo $warning_count; ?>;
            const dangerCount = <?php echo $danger_count; ?>;
            
            // Destroy previous chart instance if exists
            if (statusChartInstance) {
                statusChartInstance.destroy();
            }
            
            statusChartInstance = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Good (0-2 absences)', 'Warning (3-4 absences)', 'At Risk (5+ absences)'],
                    datasets: [{
                        data: [goodCount, warningCount, dangerCount],
                        backgroundColor: [
                            '#00b894',
                            '#fdcb6e',
                            '#e17055'
                        ],
                        borderColor: [
                            '#00b894',
                            '#fdcb6e',
                            '#e17055'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        title: {
                            display: true,
                            text: 'Student Status Distribution'
                        }
                    }
                }
            });
        }
        
        function showNotification(message, type) {
            // Create notification element
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 15px 20px;
                background: ${type === 'success' ? '#00b894' : '#e17055'};
                color: white;
                border-radius: 5px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                z-index: 1000;
                animation: slideIn 0.3s ease;
            `;
            notification.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check' : 'exclamation'}-circle"></i>
                ${message}
            `;
            
            document.body.appendChild(notification);
            
            // Remove notification after 3 seconds
            setTimeout(() => {
                notification.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 3000);
        }
        
        // Add CSS for animations
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOut {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100%); opacity: 0; }
            }
        `;
        document.head.appendChild(style);

        // Logout functionality
        document.querySelectorAll('.navbar a[href="logout.php"]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                if (confirm('Are you sure you want to logout?')) {
                    window.location.href = 'logout.php';
                }
            });
        });
    </script>
</body>
</html>