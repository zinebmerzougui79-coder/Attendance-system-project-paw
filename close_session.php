<?php
include 'config.php';

if ($_POST) {
    $session_id = $_POST['session_id'];
    
    try {
        $stmt = $db->prepare("UPDATE sessions SET status = 'closed' WHERE id = ?");
        $stmt->execute([$session_id]);
        echo "Session fermée!";
    } catch (PDOException $e) {
        echo "Erreur: " . $e->getMessage();
    }
}

// Afficher les sessions ouvertes
$sessions = $db->query("SELECT * FROM sessions WHERE status = 'open'")->fetchAll();
?>

<form method="POST">
    <select name="session_id" required>
        <option value="">Choisir une session</option>
        <?php foreach ($sessions as $session): ?>
            <option value="<?= $session['id'] ?>">
                Session <?= $session['id'] ?> - <?= $session['course_name'] ?> (<?= $session['session_date'] ?>)
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Fermer Session</button>
</form>