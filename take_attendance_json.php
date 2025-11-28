<?php
/**
 * take_attendance_json.php
 * Exercise 2: Take Attendance with JSON Files
 * Advanced Web Programming Tutorial
 */

$page_title = "Take Attendance - JSON Version";
$today = date('Y-m-d');
$attendance_file = "attendance_$today.json";
$students_file = "students.json";

$message = '';
$message_type = '';

// Check if attendance for today already exists
if (file_exists($attendance_file)) {
    $message = "Attendance for today has already been taken.";
    $message_type = 'warning';
} else {
    // Load students from JSON file
    $students = [];
    if (file_exists($students_file)) {
        $students_data = file_get_contents($students_file);
        $students = json_decode($students_data, true) ?? [];
    }

    // Process form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $attendance_data = [];
        
        foreach ($_POST['attendance'] as $student_id => $status) {
            $attendance_data[] = [
                'student_id' => $student_id,
                'status' => $status,
                'timestamp' => date('Y-m-d H:i:s')
            ];
        }
        
        // Save attendance to JSON file
        $json_data = json_encode($attendance_data, JSON_PRETTY_PRINT);
        
        if (file_put_contents($attendance_file, $json_data)) {
            $message = "Attendance saved successfully for $today!";
            $message_type = 'success';
            
            // Clear students array to hide the form
            $students = [];
        } else {
            $message = "Error saving attendance data.";
            $message_type = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
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
            margin: 20px;
            border-radius: 10px;
            text-align: center;
        }
        
        .navbar a {
            text-decoration: none;
            color: #0984e3;
            margin: 0 15px;
            font-weight: bold;
            padding: 8px 15px;
            border-radius: 5px;
            transition: 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .navbar a:hover, .navbar a.active {
            background: #0984e3;
            color: white;
        }
        
        .container {
            max-width: 1000px;
            margin: 20px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        
        .page-title {
            color: var(--primary);
            text-align: center;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .alert {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid var(--secondary);
        }
        
        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border-left: 4px solid var(--warning);
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid var(--danger);
        }
        
        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .attendance-table th {
            background: var(--primary);
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }
        
        .attendance-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
        }
        
        .attendance-table tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        .attendance-table tr:hover {
            background: #e3f2fd;
        }
        
        .status-select {
            padding: 8px 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            width: 120px;
            cursor: pointer;
            transition: border-color 0.3s;
        }
        
        .status-select:focus {
            outline: none;
            border-color: var(--primary);
        }
        
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-family: inherit;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
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
        }
        
        .btn-secondary {
            background: var(--secondary);
            color: white;
        }
        
        .btn-secondary:hover {
            background: #00a085;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: var(--gray);
            font-style: italic;
        }
        
        .file-info {
            background: #e7f3ff;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            border-left: 4px solid var(--primary);
        }
        
        .button-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        
        @media (max-width: 768px) {
            .navbar {
                margin: 10px;
                padding: 10px;
            }
            
            .navbar a {
                margin: 2px;
                padding: 6px 10px;
                font-size: 12px;
            }
            
            .container {
                margin: 10px;
                padding: 20px;
            }
            
            .attendance-table {
                font-size: 14px;
            }
            
            .attendance-table th,
            .attendance-table td {
                padding: 8px 10px;
            }
            
            .button-group {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <a href="index.php"><i class="fas fa-home"></i> Home</a>
        <a href="attendance.php"><i class="fas fa-list"></i> Attendance (MySQL)</a>
        <a href="take_attendance_json.php" class="active"><i class="fas fa-clipboard-check"></i> Take Attendance (JSON)</a>
        <a href="add_student_json.php"><i class="fas fa-user-plus"></i> Add Student (JSON)</a>
        <a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <h1 class="page-title">
            <i class="fas fa-clipboard-check"></i> Take Attendance - JSON Version
        </h1>
        
        <!-- File Information -->
        <div class="file-info">
            <strong><i class="fas fa-info-circle"></i> Today's Date:</strong> <?php echo $today; ?><br>
            <strong><i class="fas fa-file"></i> Attendance File:</strong> <?php echo $attendance_file; ?><br>
            <strong><i class="fas fa-users"></i> Students File:</strong> <?php echo $students_file; ?>
        </div>
        
        <!-- Alert Messages -->
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <i class="fas fa-<?php 
                    if ($message_type === 'success') echo 'check-circle';
                    elseif ($message_type === 'warning') echo 'exclamation-triangle';
                    else echo 'exclamation-circle';
                ?>"></i>
                <span><?php echo htmlspecialchars($message); ?></span>
            </div>
        <?php endif; ?>
        
        <?php if (file_exists($attendance_file)): ?>
            <!-- Show attendance data if already taken -->
            <div class="file-info">
                <h3><i class="fas fa-download"></i> Today's Attendance Data</h3>
                <?php
                $attendance_data = json_decode(file_get_contents($attendance_file), true);
                $present_count = 0;
                $absent_count = 0;
                
                foreach ($attendance_data as $record) {
                    if ($record['status'] === 'present') $present_count++;
                    else $absent_count++;
                }
                ?>
                <p><strong>Present:</strong> <?php echo $present_count; ?> students</p>
                <p><strong>Absent:</strong> <?php echo $absent_count; ?> students</p>
                <p><strong>Total:</strong> <?php echo count($attendance_data); ?> students</p>
                
                <details style="margin-top: 15px;">
                    <summary><strong>View Raw JSON Data</strong></summary>
                    <pre style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin-top: 10px; overflow-x: auto;"><?php 
                        echo htmlspecialchars(file_get_contents($attendance_file)); 
                    ?></pre>
                </details>
            </div>
        <?php elseif (empty($students)): ?>
            <!-- No students message -->
            <div class="no-data">
                <i class="fas fa-users-slash" style="font-size: 3rem; margin-bottom: 15px; display: block; color: #ccc;"></i>
                <h3>No Students Found</h3>
                <p>Please add students first to take attendance.</p>
                <div class="button-group">
                    <a href="add_student_json.php" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Add Students
                    </a>
                    <a href="attendance.php" class="btn btn-secondary">
                        <i class="fas fa-list"></i> View MySQL Attendance
                    </a>
                </div>
            </div>
        <?php else: ?>
            <!-- Attendance Form -->
            <form method="POST" id="attendanceForm">
                <table class="attendance-table">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Group</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $index => $student): ?>
                        <tr>
                            <td style="font-weight: 600;"><?php echo htmlspecialchars($student['student_id']); ?></td>
                            <td><?php echo htmlspecialchars($student['name']); ?></td>
                            <td><?php echo htmlspecialchars($student['group']); ?></td>
                            <td>
                                <select name="attendance[<?php echo htmlspecialchars($student['student_id']); ?>]" 
                                        class="status-select" required>
                                    <option value="present">✅ Present</option>
                                    <option value="absent">❌ Absent</option>
                                </select>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div class="button-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Attendance
                    </button>
                    <button type="button" id="markAllPresent" class="btn btn-secondary">
                        <i class="fas fa-check-circle"></i> Mark All Present
                    </button>
                    <a href="attendance.php" class="btn btn-secondary">
                        <i class="fas fa-database"></i> MySQL Version
                    </a>
                </div>
            </form>
        <?php endif; ?>
        
        <!-- Exercise Information -->
        <div class="file-info" style="margin-top: 30px;">
            <h3><i class="fas fa-book"></i> Exercise 2 Requirements</h3>
            <p><strong>✅ Load students from students.json</strong></p>
            <p><strong>✅ Show list with Present/Absent options</strong></p>
            <p><strong>✅ On submit: Create attendance_YYYY-MM-DD.json file</strong></p>
            <p><strong>✅ Save attendance as array of objects</strong></p>
            <p><strong>✅ Show message if file already exists</strong></p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log("Take Attendance JSON page loaded");
            
            // Mark all students as present
            document.getElementById('markAllPresent')?.addEventListener('click', function() {
                const selects = document.querySelectorAll('.status-select');
                selects.forEach(select => {
                    select.value = 'present';
                });
                
                // Show confirmation
                alert('All students marked as present!');
            });
            
            // Add real-time statistics
            const form = document.getElementById('attendanceForm');
            if (form) {
                const selects = document.querySelectorAll('.status-select');
                const updateStats = () => {
                    let present = 0, absent = 0;
                    selects.forEach(select => {
                        if (select.value === 'present') present++;
                        else absent++;
                    });
                    
                    // You could display these stats in real-time
                    console.log(`Present: ${present}, Absent: ${absent}`);
                };
                
                selects.forEach(select => {
                    select.addEventListener('change', updateStats);
                });
                
                // Initial stats
                updateStats();
            }
            
            // Auto-hide success message after 5 seconds
            const successAlert = document.querySelector('.alert-success');
            if (successAlert) {
                setTimeout(() => {
                    successAlert.style.opacity = '0';
                    setTimeout(() => successAlert.remove(), 300);
                }, 5000);
            }
        });
    </script>
</body>
</html>