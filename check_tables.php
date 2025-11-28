<?php
include 'config.php';

try {
    $db = new PDO("mysql:host=localhost;dbname=pawnroject", "root", "");
    
    $tables = ['students', 'attendance_records', 'participation_records', 'sessions', 'courses'];
    
    foreach ($tables as $table) {
        echo "<h3>Table: $table</h3>";
        
        // Structure
        $structure = $db->query("DESCRIBE $table")->fetchAll(PDO::FETCH_ASSOC);
        echo "<strong>Structure:</strong><br>";
        foreach ($structure as $column) {
            echo "• {$column['Field']} ({$column['Type']})";
            if ($column['Key'] == 'PRI') echo " 🔑";
            if ($column['Key'] == 'MUL') echo " 🔗";
            echo "<br>";
        }
        
        // Données
        $data = $db->query("SELECT * FROM $table LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
        echo "<strong>Données (premières lignes):</strong><br>";
        foreach ($data as $row) {
            echo "• " . implode(", ", $row) . "<br>";
        }
        echo "<br>";
    }
    
} catch (PDOException $e) {
    echo "Erreur: " . $e->getMessage();
}
?>