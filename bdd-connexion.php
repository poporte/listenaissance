<?php
  $dbhost = 'localhost';
  $dbport=3306;
  $db = 'naissance';
  $dbuser = 'dbu998323';
  $dbpasswd = 'rtss33PLESK';

try {
    $conn = new PDO('mysql:host='.$dbhost.';port='.$dbport.';dbname='.$db.'', $dbuser, $dbpasswd);
	$conn->exec("SET CHARACTER SET utf8");
	
    // Activer le mode d'erreur PDO pour lancer des exceptions
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
}

?>