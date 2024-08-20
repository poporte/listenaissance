<?php
session_start();
session_unset(); // Supprime toutes les variables de session
session_destroy(); // Détruit la session

// Redirige l'utilisateur vers la page de connexion après la déconnexion
header("Location: connexion.php");
exit();
?>
