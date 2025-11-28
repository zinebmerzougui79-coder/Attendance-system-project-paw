<?php
include 'config.php';

if ($_POST) {
    $course_name = $_POST['course_name'];
    $group_name = $_POST['group_name'];
    $professor = $_POST['professor'] ?? 'Unknown';
    
    try {
        $stmt = $db->prepare("INSERT INTO sessions (course_name, group_name, session_date, opened_by) VALUES (?, ?, CURDATE(), ?)");
        $stmt->execute([$course_name, $group_name, $professor]);
        
        $session_id = $db->lastInsertId();
        echo "Session créée! ID: $session_id";
    } catch (PDOException $e) {
        echo "Erreur: " . $e->getMessage();
    }
}
?>

<form method="POST">
    <input type="text" name="course_name" placeholder="Nom du cours" required>
    <input type="text" name="group_name" placeholder="Nom du groupe" required>
    <input type="text" name="professor" placeholder="Professeur">
    <button type="submit">Créer Session</button>
</form>