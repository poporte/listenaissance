<?php
session_start();
include 'connexion.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit();
}

if (isset($_POST['article_id'])) {
    $article_id = $_POST['article_id'];
    $user_id = $_SESSION['user_id'];

    try {
        // Vérifier si l'utilisateur a réservé cet article
        $sql = "SELECT * FROM reservations WHERE article_id = :article_id AND user_id = :user_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':article_id', $article_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // Supprimer la réservation
            $sql = "DELETE FROM reservations WHERE article_id = :article_id AND user_id = :user_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':article_id', $article_id);
            $stmt->bindParam(':user_id', $user_id);

            if ($stmt->execute()) {
                echo "Réservation annulée avec succès.";
            } else {
                echo "Erreur lors de l'annulation de la réservation.";
            }
        } else {
            echo "Aucune réservation trouvée pour cet article.";
        }
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
} else {
    echo "Aucun article spécifié.";
}
?>
