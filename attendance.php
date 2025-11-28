<?php
include 'config.php';

// Handle attendance updates
if ($_POST['action'] ?? '' === 'update_attendance') {
    $student_id = $_POST['student_id'];
    $session_index = $_POST['session_index'];
    $type = $_POST['type']; // 'attendance' or 'participation'
    $status = $_POST['status'];
    
    try {
        if ($type === 'attendance') {
            $stmt = $db->prepare("INSERT INTO attendance_records (student_id, session_id, status, recorded_at) 
                                 VALUES (?, ?, ?, NOW()) 
                                 ON DUPLICATE KEY UPDATE status = ?");
            $stmt->execute([$student_id, $session_index + 1, $status, $status]);
        } else {
            $stmt = $db->prepare("INSERT INTO participation_records (student_id, session_id, status, recorded_at) 
                                 VALUES (?, ?, ?, NOW()) 
                                 ON DUPLICATE KEY UPDATE status = ?");
            $stmt->execute([$student_id, $session_index + 1, $status, $status]);
        }
        echo json_encode(['success' => true]);
        exit;
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
}

// Get all students with their attendance and participation data
try {
    $students = $db->query("
        SELECT s.*,
               GROUP_CONCAT(CASE WHEN ar.session_id = 1 THEN ar.status ELSE NULL END) as a1,
               GROUP_CONCAT(CASE WHEN ar.session_id = 2 THEN ar.status ELSE NULL END) as a2,
               GROUP_CONCAT(CASE WHEN ar.session_id = 3 THEN ar.status ELSE NULL END) as a3,
               GROUP_CONCAT(CASE WHEN ar.session_id = 4 THEN ar.status ELSE NULL END) as a4,
               GROUP_CONCAT(CASE WHEN ar.session_id = 5 THEN ar.status ELSE NULL END) as a5,
               GROUP_CONCAT(CASE WHEN ar.session_id = 6 THEN ar.status ELSE NULL END) as a6,
               GROUP_CONCAT(CASE WHEN pr.session_id = 1 THEN pr.status ELSE NULL END) as p1,
               GROUP_CONCAT(CASE WHEN pr.session_id = 2 THEN pr.status ELSE NULL END) as p2,
               GROUP_CONCAT(CASE WHEN pr.session_id = 3 THEN pr.status ELSE NULL END) as p3,
               GROUP_CONCAT(CASE WHEN pr.session_id = 4 THEN pr.status ELSE NULL END) as p4,
               GROUP_CONCAT(CASE WHEN pr.session_id = 5 THEN pr.status ELSE NULL END) as p5,
               GROUP_CONCAT(CASE WHEN pr.session_id = 6 THEN pr.status ELSE NULL END) as p6
        FROM students s
        LEFT JOIN attendance_records ar ON s.id = ar.student_id
        LEFT JOIN participation_records pr ON s.id = pr.student_id
        GROUP BY s.id
        ORDER BY s.firstname, s.lastname
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $students = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Attendance Page</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
/* YOUR EXACT ORIGINAL CSS - NO CHANGES */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    background: linear-gradient(135deg, #74b9ff, #0984e3);
    margin: 0;
    padding: 0;
    min-height: 100vh;
}

.navbar {
    background: rgba(255,255,255,0.9);
    padding: 15px;
    margin: 20px;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.navbar a {
    margin: 0 15px;
    text-decoration: none;
    color: #0984e3;
    font-weight: bold;
    padding: 8px 15px;
    border-radius: 5px;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.navbar a:hover {
    background: #0984e3;
    color: white;
    transform: translateY(-2px);
}

.navbar a.active {
    background: #0984e3;
    color: white;
}

.container {
    width: 95%;
    max-width: 1200px;
    margin: 20px auto;
    padding: 25px;
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

h2 {
    text-align: center;
    color: #0984e3;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

/* Controls */
.controls {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin: 20px 0;
    align-items: center;
    background: #f8f9fa;
    padding: 15px;
    border-radius: 10px;
}

.search-box {
    flex: 1;
    min-width: 200px;
    padding: 10px 15px;
    border: 2px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
}

.search-box:focus {
    outline: none;
    border-color: #0984e3;
}

.btn {
    background: #0984e3;
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 5px;
}

.btn:hover {
    background: #065aa1;
    transform: translateY(-2px);
}

.btn.highlight {
    background: #f39c12;
}

.btn.highlight:hover {
    background: #e67e22;
}

.btn.reset {
    background: #e74c3c;
}

.btn.reset:hover {
    background: #c0392b;
}

.btn.report {
    background: #27ae60;
}

.btn.report:hover {
    background: #219a52;
}

/* Table */
.table-container {
    overflow-x: auto;
    margin: 20px 0;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
    min-width: 1000px;
}

th, td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: center;
}

th {
    background: #0984e3;
    color: white;
    font-weight: 600;
    position: sticky;
    top: 0;
}

/* Status Colors */
tr.green {
    background: #d4edda;
}

tr.yellow {
    background: #fff3cd;
}

tr.red {
    background: #f8d7da;
}

tr.highlighted {
    animation: pulse 2s infinite;
    border: 2px solid #f39c12;
}

@keyframes pulse {
    0% { background-color: #fff7dd; }
    50% { background-color: #ffeaa7; }
    100% { background-color: #fff7dd; }
}

tr:hover {
    background: #d0e7ff !important;
    cursor: pointer;
    transition: background 0.3s ease;
}

/* Checkbox Styles */
input[type="checkbox"] {
    transform: scale(1.2);
    cursor: pointer;
}

input[type="checkbox"]:checked {
    accent-color: #27ae60;
}

.attendCheck:checked {
    accent-color: #27ae60;
}

.partCheck:checked {
    accent-color: #3498db;
}

/* Messages */
.sort-message {
    background: #d1ecf1;
    color: #0c5460;
    padding: 12px;
    border-radius: 6px;
    margin: 10px 0;
    border-left: 4px solid #0c5460;
    display: flex;
    align-items: center;
    gap: 8px;
}

.message-cell {
    font-size: 12px;
    font-weight: 600;
    max-width: 200px;
}

/* Report Section */
#reportSection {
    margin-top: 20px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 10px;
    border-left: 4px solid #0984e3;
}

.report-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

.stat-item {
    background: white;
    padding: 15px;
    border-radius: 8px;
    text-align: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.stat-value {
    font-size: 1.5em;
    font-weight: bold;
    color: #0984e3;
    display: block;
}

.stat-label {
    color: #7f8c8d;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Mobile Responsive */
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
        width: 98%;
        margin: 10px auto;
        padding: 15px;
    }

    .controls {
        flex-direction: column;
        align-items: stretch;
    }

    .search-box {
        min-width: unset;
    }

    .btn {
        justify-content: center;
    }

    table {
        font-size: 12px;
    }

    th, td {
        padding: 6px 4px;
    }
}
</style>
</head>
<body>

<nav class="navbar">
    <a href="index.php"><i class="fas fa-home"></i> Home</a>
    <a href="attendance.php" class="active"><i class="fas fa-list"></i> Attendance</a>
    <a href="add-student.php"><i class="fas fa-user-plus"></i> Add Student</a>
    <a href="reports.php"><i class="fas fa-chart-bar"></i> Report</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</nav>

<div class="container">
    <h2><i class="fas fa-list"></i> Attendance Management</h2>

    <div class="controls">
        <input type="text" class="search-box" id="searchInput" placeholder="🔍 Search by First or Last Name">
        <button class="btn" id="sortAbsencesBtn"><i class="fas fa-sort-amount-down"></i> Sort by Absences (Asc)</button>
        <button class="btn" id="sortParticipationBtn"><i class="fas fa-sort-amount-down-alt"></i> Sort by Participation (Desc)</button>
        <button class="btn highlight" id="highlightBtn"><i class="fas fa-star"></i> Highlight Excellent Students</button>
        <button class="btn reset" id="resetBtn"><i class="fas fa-sync"></i> Reset Colors</button>
        <button class="btn report" id="showReportBtn"><i class="fas fa-chart-bar"></i> Show Report</button>
    </div>

    <div id="sortMsg" class="sort-message" style="display: none;">
        <i class="fas fa-info-circle"></i>
        <span id="sortMessageText"></span>
    </div>

    <div class="table-container">
        <table id="attendanceTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th colspan="6">Attendance (Sessions 1-6)</th>
                    <th colspan="6">Participation (Sessions 1-6)</th>
                    <th>Absences</th>
                    <th>Participations</th>
                    <th>Message</th>
                </tr>
                <tr style="background: #2c3e50;">
                    <th colspan="3"></th>
                    <th>S1</th><th>S2</th><th>S3</th><th>S4</th><th>S5</th><th>S6</th>
                    <th>P1</th><th>P2</th><th>P3</th><th>P4</th><th>P5</th><th>P6</th>
                    <th colspan="3"></th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($students) === 0): ?>
                    <tr>
                        <td colspan="17" style="text-align: center; padding: 40px; color: #7f8c8d;">
                            <i class="fas fa-users" style="font-size: 3em; margin-bottom: 10px; display: block;"></i>
                            No students found. <a href="add-student.php" style="color: #0984e3;">Add some students</a> to get started.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($students as $index => $student): 
                        // Calculate attendance and participation arrays
                        $attendance = [
                            $student['a1'] ?? '0',
                            $student['a2'] ?? '0', 
                            $student['a3'] ?? '0',
                            $student['a4'] ?? '0',
                            $student['a5'] ?? '0',
                            $student['a6'] ?? '0'
                        ];
                        
                        $participation = [
                            $student['p1'] ?? '0',
                            $student['p2'] ?? '0',
                            $student['p3'] ?? '0',
                            $student['p4'] ?? '0',
                            $student['p5'] ?? '0',
                            $student['p6'] ?? '0'
                        ];
                        
                        $absences = array_sum(array_map(function($a) { return $a === '0' ? 1 : 0; }, $attendance));
                        $participations = array_sum($participation);
                        
                        $msg = "";
                        $rowClass = "";
                        
                        if ($absences < 3) {
                            $msg = "Good attendance – Excellent participation";
                            $rowClass = "green";
                        } else if ($absences < 5) {
                            $msg = "Warning – attendance low – You need to participate more";
                            $rowClass = "yellow";
                        } else {
                            $msg = "Excluded – too many absences – You need to participate more";
                            $rowClass = "red";
                        }
                    ?>
                    <tr class="<?php echo $rowClass; ?>" data-id="<?php echo $student['id']; ?>" data-index="<?php echo $index; ?>">
                        <td style="font-weight: 600;"><?php echo $student['matricule']; ?></td>
                        <td><?php echo htmlspecialchars($student['firstname']); ?></td>
                        <td><?php echo htmlspecialchars($student['lastname']); ?></td>
                        
                        <?php foreach ($attendance as $i => $a): ?>
                        <td><input type="checkbox" class="attendCheck" data-index="<?php echo $i; ?>" 
                                   data-student-id="<?php echo $student['id']; ?>" 
                                   <?php echo $a === '1' ? 'checked' : ''; ?>></td>
                        <?php endforeach; ?>
                        
                        <?php foreach ($participation as $i => $p): ?>
                        <td><input type="checkbox" class="partCheck" data-index="<?php echo $i; ?>" 
                                   data-student-id="<?php echo $student['id']; ?>" 
                                   <?php echo $p === '1' ? 'checked' : ''; ?>></td>
                        <?php endforeach; ?>
                        
                        <td style="font-weight: 700; color: <?php echo $absences > 4 ? '#e74c3c' : ($absences > 2 ? '#f39c12' : '#27ae60'); ?>">
                            <?php echo $absences; ?>
                        </td>
                        <td style="font-weight: 700; color: #0984e3;"><?php echo $participations; ?></td>
                        <td class="message-cell"><?php echo $msg; ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div id="reportSection" style="display:none;">
        <h3 style="color: #2d3436; margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-chart-pie"></i> Attendance Report
        </h3>
        <div class="report-stats" id="reportStats"></div>
        <div style="background: white; padding: 15px; border-radius: 8px; margin-top: 15px;">
            <canvas id="attendanceChart" height="150"></canvas>
        </div>
    </div>
</div>

<script>
// Update attendance or participation in database
function updateRecord(studentId, sessionIndex, type, status) {
    $.post('attendance.php', {
        action: 'update_attendance',
        student_id: studentId,
        session_index: sessionIndex,
        type: type,
        status: status ? 1 : 0
    }, function(response) {
        if (!response.success) {
            console.error('Error updating record:', response.error);
            alert('Error updating record. Please try again.');
        }
    }, 'json');
}

// Checkbox change handler
$(document).on("change", ".attendCheck, .partCheck", function(){
    const studentId = $(this).data('student-id');
    const sessionIndex = $(this).data('index');
    const type = $(this).hasClass('attendCheck') ? 'attendance' : 'participation';
    const status = $(this).is(':checked');
    
    console.log("📝 Updating", type, "for student", studentId, "session", sessionIndex, "status:", status);
    
    updateRecord(studentId, sessionIndex, type, status);
    
    // Refresh the page to show updated counts
    setTimeout(() => {
        location.reload();
    }, 300);
});

// Hover row highlight
$(document).on("mouseenter", "#attendanceTable tbody tr", function(){
    $(this).css("background", "#d0e7ff");
});

$(document).on("mouseleave", "#attendanceTable tbody tr", function(){
    // Restore original color based on class
    const rowClass = $(this).attr('class');
    if (rowClass.includes('green')) {
        $(this).css("background", "#d4edda");
    } else if (rowClass.includes('yellow')) {
        $(this).css("background", "#fff3cd");
    } else if (rowClass.includes('red')) {
        $(this).css("background", "#f8d7da");
    } else {
        $(this).css("background", "");
    }
});

// Click row message
$(document).on("click", "#attendanceTable tbody tr", function(e){
    // Don't trigger if clicking on checkboxes
    if ($(e.target).is('input[type="checkbox"]')) return;
    
    const studentId = $(this).data('id');
    const studentIndex = $(this).data('index');
    const firstname = $(this).find('td:eq(1)').text();
    const lastname = $(this).find('td:eq(2)').text();
    const absences = $(this).find('td:eq(15)').text();
    const participations = $(this).find('td:eq(16)').text();
    
    alert(`Student: ${firstname} ${lastname}\nStudent ID: ${studentId}\nAbsences: ${absences}\nParticipations: ${participations}`);
});

// Highlight excellent students
$("#highlightBtn").click(() => {
    $("#attendanceTable tbody tr").removeClass("highlighted");
    
    $("#attendanceTable tbody tr").each(function() {
        const absences = parseInt($(this).find('td:eq(15)').text());
        if (absences < 3) {
            $(this).addClass("highlighted");
            $(this).fadeOut(100).fadeIn(500);
        }
    });
});

// Reset colors
$("#resetBtn").click(() => {
    $("#attendanceTable tbody tr").removeClass("highlighted");
    $("#sortMsg").hide();
});

// Search functionality
$("#searchInput").on("keyup", function() {
    const val = $(this).val().toLowerCase();
    $("#attendanceTable tbody tr").filter(function() {
        const fname = $(this).find("td:eq(1)").text().toLowerCase();
        const lname = $(this).find("td:eq(2)").text().toLowerCase();
        $(this).toggle(fname.includes(val) || lname.includes(val));
    });
});

// Sort by absences
$("#sortAbsencesBtn").click(() => {
    let rows = $("#attendanceTable tbody tr").get();
    
    rows.sort((a, b) => {
        const aAbs = parseInt($(a).find('td:eq(15)').text());
        const bAbs = parseInt($(b).find('td:eq(15)').text());
        return aAbs - bAbs;
    });
    
    $.each(rows, (i, tr) => $("#attendanceTable tbody").append(tr));
    
    $("#sortMessageText").text("Currently sorted by Absences (ascending)");
    $("#sortMsg").show();
});

// Sort by participation
$("#sortParticipationBtn").click(() => {
    let rows = $("#attendanceTable tbody tr").get();
    
    rows.sort((a, b) => {
        const aPart = parseInt($(a).find('td:eq(16)').text());
        const bPart = parseInt($(b).find('td:eq(16)').text());
        return bPart - aPart;
    });
    
    $.each(rows, (i, tr) => $("#attendanceTable tbody").append(tr));
    
    $("#sortMessageText").text("Currently sorted by Participation (descending)");
    $("#sortMsg").show();
});

// Show report
$("#showReportBtn").click(() => {
    let total = 0, present = 0, part = 0, totalAbsences = 0;
    
    $("#attendanceTable tbody tr").each(function() {
        if ($(this).find('td').length > 1) { // Skip empty row
            total++;
            present += 6 - parseInt($(this).find('td:eq(15)').text()); // 6 sessions - absences
            part += parseInt($(this).find('td:eq(16)').text());
            totalAbsences += parseInt($(this).find('td:eq(15)').text());
        }
    });
    
    // Update report stats
    $("#reportStats").html(`
        <div class="stat-item">
            <span class="stat-value">${total}</span>
            <span class="stat-label">Total Students</span>
        </div>
        <div class="stat-item">
            <span class="stat-value">${present}</span>
            <span class="stat-label">Total Presence</span>
        </div>
        <div class="stat-item">
            <span class="stat-value">${part}</span>
            <span class="stat-label">Total Participation</span>
        </div>
        <div class="stat-item">
            <span class="stat-value">${totalAbsences}</span>
            <span class="stat-label">Total Absences</span>
        </div>
    `);
    
    $("#reportSection").show();
    
    // Create chart
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Presence', 'Participation', 'Absences'],
            datasets: [{
                label: 'Session Statistics',
                data: [present, part, totalAbsences],
                backgroundColor: ['#27ae60', '#3498db', '#e74c3c'],
                borderColor: ['#219a52', '#2980b9', '#c0392b'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Sessions'
                    }
                }
            }
        }
    });
});

// Initialize
$(document).ready(() => {
    console.log("🚀 Attendance page loaded");
});
</script>

</body>
</html>