<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit();
} else {
    $utilisateur_id = $_SESSION["user_id"];
}

// Inclure le fichier de connexion à la base de données
include 'bdd-connexion.php';

// Classe Article
require_once('class/article.php');

// Gérer la réservation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reserver'])) {
    $article_id = $_POST['article_id'];
    $message_reservation = $_POST['message_reservation'];

    // Mettre à jour la réservation dans la base de données
    $sql = "UPDATE articles SET est_bloque = TRUE, utilisateur_id = :utilisateur_id WHERE id = :article_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':utilisateur_id', $utilisateur_id, PDO::PARAM_INT);
    $stmt->bindParam(':article_id', $article_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        //echo "Article réservé avec succès!";
    } else {
        echo "Erreur lors de la réservation de l'article.";
    }

    $sql2 = "INSERT INTO reservations (id_utilisateur, id_article, message) 
                        VALUES (:utilisateur_id,:article_id, :message_reservation)";
                $stmt2 = $conn->prepare($sql2);
                $stmt2->bindParam(':utilisateur_id', $utilisateur_id, PDO::PARAM_INT);
                $stmt2->bindParam(':article_id', $article_id, PDO::PARAM_INT);
                $stmt2->bindParam(':message_reservation', $message_reservation, PDO::PARAM_STR);

    if ($stmt2->execute()) {
        //echo "Article réservé avec succès!";
    } else {
        echo "Erreur lors de la réservation de l'article.";
    }      
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['annuler'])) {
    $article_id = $_POST['article_id'];

    // Mettre à jour la réservation dans la base de données
    $sql3 = "UPDATE articles SET est_bloque = FALSE, utilisateur_id = 0 WHERE id = :article_id";
    $stmt3 = $conn->prepare($sql3);
    $stmt3->bindParam(':article_id', $article_id, PDO::PARAM_INT);

    if ($stmt3->execute()) {
        //echo "Article réservé avec succès!";
    } else {
        echo "Erreur lors de la réservation de l'article.";
    }

    $sql4 = "DELETE FROM reservations WHERE id_article = :article_id";
                $stmt4 = $conn->prepare($sql4);
                $stmt4->bindParam(':article_id', $article_id, PDO::PARAM_INT);

    if ($stmt4->execute()) {
        //echo "Article réservé avec succès!";
    } else {
        echo "Erreur lors de la réservation de l'article.";
    }
}

// Initialiser une liste pour stocker les articles
$articles = [];

// Requête pour sélectionner tous les articles
//$sql5 = "SELECT * FROM articles";
$sql5 = "SELECT articles.id, articles.nom, articles.description, articles.prix, articles.lien, articles.image, articles.categorie, articles.est_bloque, utilisateurs.id as utilisateur_id, utilisateurs.username FROM articles left join utilisateurs on utilisateur_id = utilisateurs.id where articles.privee = false order by categorie, nom";
$stmt5 = $conn->prepare($sql5);
$stmt5->execute();

// Récupérer les résultats sous forme de tableau associatif
$result = $stmt5->fetchAll(PDO::FETCH_ASSOC);

// Vérifier s'il y a des résultats
if ($stmt5->rowCount() > 0) {
    // Parcourir chaque ligne de résultats
    foreach ($result as $row) {
        // Créer un nouvel objet Article avec les données de la ligne
        $article = new Article(
            $row['id'],
            $row['nom'],
            $row['description'],
            $row['prix'],
            $row['lien'],
            $row['categorie'],
            $row['image'],
            $row['est_bloque'],
            $row['utilisateur_id'],
            $row['username']
        );

        // Ajouter l'objet Article à la liste des articles
        $articles[] = $article;
    }
} else {
    echo "Aucun article trouvé.";
}
?>

<!DOCTYPE html>
<!-- Pas la peine de farfouiller le code, le prénom n'y est pas :P -->
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste de naissance Antho & Lolo</title>
    <link href="style/index.css" rel="stylesheet">
</head>
<body>
    <header>
        <h1>Notre liste de naissance</h1>
    </header>
    <main>
        <section>
			<div class='description'>
				<p>Notre petite princesse devrait pointer le bout de son nez à la mi-octobre. Comme vous nous demandez souvent ce qui nous ferait plaisir ou ce dont nous aurions besoin, avec cette liste, vous êtes sûrs de viser juste !</p>  
				<p>Nous y avons rassemblé tous nos essentiels et coups de cœur (♥) pour les mois à venir.</p>  
				<p>Nous serions ravis de partager un moment avec vous après la naissance pour recevoir votre présent. Pour ceux qui sont plus éloignés, les livreurs pourront nous trouver au : 1 Place du Pradeau, 33000 Bordeaux.</p>
				<p>Anthony et Lorène</p>
			</div>
        </section>
        <section>
        <?php
    
        echo "<div class='article-list'>";
        // Afficher les articles
            foreach ($articles as $article) {
                echo "<div class='article'>";
                    echo "<h2>" . htmlspecialchars($article->nom) . "</h2>";
                    echo "<div class='article-content'>";
                        echo "<div>";
                            echo "<img src='images/" . htmlspecialchars($article->image) . "' alt='" . htmlspecialchars($article->nom) . "'>";
                        echo "</div>";
                        echo "<div>";
                            echo "<p>" . htmlspecialchars($article->description) . "</p>";
                            /*if (!$article->est_bloque)
								echo "<p>Prix : " . htmlspecialchars(number_format($article->prix, 2)) . " €</p>";
							 else
								echo "<p>Prix : <span class='blur'> xx.xx</span></p>";
				*/
							echo "<p>Prix : " . htmlspecialchars(number_format($article->prix, 2)) . " €</p>";
                            echo "<p>Catégorie : " . htmlspecialchars($article->categorie) . "</p>";
                            if (substr_compare($article->lien,"http",0,4)==0) 
                                echo "<a href='" . htmlspecialchars($article->lien) . "' target='_blank'>Lien web</a>";
                            else
                                echo "<p>Magasin : ".htmlspecialchars($article->lien) ."</p>";
                            
                        echo "</div>";
                    echo "</div>";
                    
                    echo "<div class= 'box'>";
                        if (!$article->est_bloque) {
                            echo "<form action='' method='post'>";
                            echo "<input type='hidden' name='article_id' value='" . $article->id . "'>";
                            echo "<label for='message_reservation'>Message de réservation :</label>";
                            echo "<textarea id='message_reservation' name='message_reservation'></textarea>";
                            echo "<button type='submit' name='reserver'>Réserver cet article</button>";
                            echo "</form>";
                        } else {
                            echo "<p><strong>Réservé par ".$article->username."</strong></p>";
                            if ($article->utilisateur_id == $utilisateur_id){
                                echo "<form action='' method='post'>";
                                    echo "<input type='hidden'  name='article_id'' value='". $article->id ."'>";
                                    echo "<button type='submit' name='annuler'>Annuler la réservation</button>";
                                echo "</form>";
                            }
                        }
                    echo "</div>";
                echo "</div>";
            }
        echo "</div>";
        ?>
        </section>
    </main>
    <footer>
        <a href="deconnexion.php">Se déconnecter</a>
    </footer>
</body>
</html>
