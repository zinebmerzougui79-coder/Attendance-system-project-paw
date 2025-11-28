<?php
include 'config.php';

// Handle form submission
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matricule = $_POST['matricule'] ?? '';
    $firstname = $_POST['firstname'] ?? '';
    $lastname = $_POST['lastname'] ?? '';
    $course = $_POST['course'] ?? '';
    
    // Validate required fields
    if (empty($matricule) || empty($firstname) || empty($lastname) || empty($course)) {
        $error_message = 'Please fill in all required fields.';
    } else {
        // Validate that matricule contains only numbers
        if (!preg_match('/^[0-9]+$/', $matricule)) {
            $error_message = "Student ID must contain only numbers (0-9).";
        } else {
            try {
                // Check if student ID already exists
                $check_stmt = $db->prepare("SELECT id FROM students WHERE matricule = ?");
                $check_stmt->execute([$matricule]);
                
                if ($check_stmt->fetch()) {
                    $error_message = "Student with ID \"$matricule\" already exists.";
                } else {
                    // Insert new student
                    $insert_stmt = $db->prepare("
                        INSERT INTO students (matricule, firstname, lastname, course, created_at) 
                        VALUES (?, ?, ?, ?, NOW())
                    ");
                    $insert_stmt->execute([$matricule, $firstname, $lastname, $course]);
                    
                    $success_message = "Student \"$firstname $lastname\" added successfully!";
                    
                    // Clear form fields
                    $_POST = [];
                }
            } catch (Exception $e) {
                $error_message = "Error adding student: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student - Attendance System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* YOUR EXACT ORIGINAL CSS - NO CHANGES */
        :root {
            --primary: #0984e3;
            --primary-light: #74b9ff;
            --secondary: #00b894;
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
            max-width: 600px;
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
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--dark);
        }
        
        .form-input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        .form-input:focus {
            outline: none;
            border-color: var(--primary);
        }
        
        .form-input.error {
            border-color: var(--danger);
        }
        
        .error-message {
            color: var(--danger);
            font-size: 0.85rem;
            margin-top: 5px;
            display: none;
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
            width: 100%;
        }
        
        .btn-primary:hover {
            background: var(--primary-light);
        }
        
        .btn-primary:disabled {
            background: #ccc;
            cursor: not-allowed;
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
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid var(--danger);
        }
        
        .hidden {
            display: none;
        }
        
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-link a {
            color: var(--primary);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
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
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <a href="index.php"><i class="fas fa-home"></i> Home</a>
        <a href="attendance.php"><i class="fas fa-list"></i> Attendance</a>
        <a href="add-student.php" class="active"><i class="fas fa-user-plus"></i> Add Student</a>
        <a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <h1 class="page-title"><i class="fas fa-user-plus"></i> Add New Student</h1>
        
        <!-- Alert Messages -->
        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <span><?php echo htmlspecialchars($success_message); ?></span>
            </div>
        <?php endif; ?>
        
        <?php if ($error_message): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo htmlspecialchars($error_message); ?></span>
            </div>
        <?php endif; ?>
        
        <!-- Simple Form -->
        <form id="studentForm" method="POST" action="">
            <div class="form-group">
                <label for="matricule" class="form-label">Student ID *</label>
                <input type="text" id="matricule" name="matricule" class="form-input" 
                       placeholder="Enter student ID (numbers only)" required 
                       pattern="[0-9]+"
                       title="Please enter numbers only (0-9)"
                       value="<?php echo htmlspecialchars($_POST['matricule'] ?? ''); ?>">
                <div class="error-message" id="matriculeError">Student ID must contain only numbers (0-9)</div>
            </div>
            
            <div class="form-group">
                <label for="firstname" class="form-label">First Name *</label>
                <input type="text" id="firstname" name="firstname" class="form-input" 
                       placeholder="Enter first name" required
                       value="<?php echo htmlspecialchars($_POST['firstname'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="lastname" class="form-label">Last Name *</label>
                <input type="text" id="lastname" name="lastname" class="form-input" 
                       placeholder="Enter last name" required
                       value="<?php echo htmlspecialchars($_POST['lastname'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="course" class="form-label">Course *</label>
                <input type="text" id="course" name="course" class="form-input" 
                       placeholder="Enter course name" required
                       value="<?php echo htmlspecialchars($_POST['course'] ?? ''); ?>">
            </div>
            
            <button type="submit" class="btn btn-primary" id="submitBtn">
                <i class="fas fa-save"></i> Add Student
            </button>
        </form>
        
        <div class="back-link">
            <a href="attendance.php"><i class="fas fa-arrow-left"></i> Back to Attendance List</a>
        </div>
    </div>

    <script>
        // Auto-hide success message after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            console.log("Add Student page loaded");
            
            const successAlert = document.querySelector('.alert-success');
            if (successAlert) {
                setTimeout(() => {
                    successAlert.style.display = 'none';
                }, 5000);
            }
            
            const matriculeInput = document.getElementById('matricule');
            const matriculeError = document.getElementById('matriculeError');
            const submitBtn = document.getElementById('submitBtn');
            
            // Real-time validation for matricule
            matriculeInput.addEventListener('input', function() {
                const value = this.value;
                
                // Remove any non-numeric characters
                const numericValue = value.replace(/[^0-9]/g, '');
                if (value !== numericValue) {
                    this.value = numericValue;
                }
                
                // Show/hide error message
                if (value.length > 0 && !/^[0-9]+$/.test(value)) {
                    matriculeError.style.display = 'block';
                    this.classList.add('error');
                    submitBtn.disabled = true;
                } else {
                    matriculeError.style.display = 'none';
                    this.classList.remove('error');
                    submitBtn.disabled = false;
                }
            });
            
            // Prevent non-numeric input
            matriculeInput.addEventListener('keypress', function(e) {
                const char = String.fromCharCode(e.keyCode || e.which);
                if (!/^[0-9]$/.test(char)) {
                    e.preventDefault();
                    return false;
                }
            });
            
            // Prevent paste of non-numeric characters
            matriculeInput.addEventListener('paste', function(e) {
                const pasteData = e.clipboardData.getData('text');
                if (!/^[0-9]+$/.test(pasteData)) {
                    e.preventDefault();
                    // Optionally, you can show a message here
                    matriculeError.style.display = 'block';
                    this.classList.add('error');
                    submitBtn.disabled = true;
                }
            });
            
            // Client-side validation on form submit
            const studentForm = document.getElementById('studentForm');
            studentForm.addEventListener('submit', function(e) {
                const matricule = document.getElementById('matricule').value;
                const firstname = document.getElementById('firstname').value;
                const lastname = document.getElementById('lastname').value;
                const course = document.getElementById('course').value;
                
                // Check required fields
                if (!matricule || !firstname || !lastname || !course) {
                    e.preventDefault();
                    alert('Please fill in all required fields.');
                    return false;
                }
                
                // Check matricule format
                if (!/^[0-9]+$/.test(matricule)) {
                    e.preventDefault();
                    matriculeError.style.display = 'block';
                    matriculeInput.classList.add('error');
                    matriculeInput.focus();
                    alert('Student ID must contain only numbers (0-9).');
                    return false;
                }
                
                return true;
            });
        });
    </script>
</body>
</html>