<?php
if ($_POST) {
    $student_id = $_POST['student_id'];
    $name = $_POST['name'];
    $group = $_POST['group'];
    
    // Charger depuis JSON
    $students = [];
    if (file_exists('students.json')) {
        $students = json_decode(file_get_contents('students.json'), true) ?? [];
    }
    
    // Ajouter nouvel étudiant
    $students[] = [
        'student_id' => $student_id,
        'name' => $name,
        'group' => $group
    ];
    
    // Sauvegarder
    file_put_contents('students.json', json_encode($students, JSON_PRETTY_PRINT));
    echo "Étudiant ajouté!";
}
?>

<form method="POST">
    <input type="text" name="student_id" placeholder="ID Étudiant" required>
    <input type="text" name="name" placeholder="Nom Complet" required>
    <input type="text" name="group" placeholder="Groupe" required>
    <button type="submit">Ajouter</button>
</form>