<?php
// Inclure le fichier de connexion à la base de données
include 'bdd-connexion.php';

// Initialiser les variables pour stocker les messages d'erreur ou de succès
$success_msg = "";
$error_msg = "";

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $prix = $_POST['prix'];
    $lien = $_POST['lien'];
    $categorie = $_POST['categorie'];
    
    // Gérer l'upload de l'image
    $image = $_FILES['image']['name'];
    $target_dir = "images/";
    $target_file = $target_dir . basename($image);
    
    // Vérifier que le fichier est une image
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $check = getimagesize($_FILES['image']['tmp_name']);
    if ($check !== false) {
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            try {
                // Préparer la requête d'insertion
                $sql = "INSERT INTO articles (nom, description, prix, lien, categorie, image) 
                        VALUES (:nom, :description, :prix, :lien, :categorie, :image)";
                $stmt = $conn->prepare($sql);
                
                // Lier les paramètres à la requête
                $stmt->bindParam(':nom', $nom);
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':prix', $prix);
                $stmt->bindParam(':lien', $lien);
                $stmt->bindParam(':categorie', $categorie);
                $stmt->bindParam(':image', $image);
                
                // Exécuter la requête
                if ($stmt->execute()) {
                    $success_msg = "L'article a été ajouté avec succès.";
                } else {
                    $error_msg = "Erreur lors de l'ajout de l'article.";
                }
            } catch (PDOException $e) {
                $error_msg = "Erreur : " . $e->getMessage();
            }
        } else {
            $error_msg = "Erreur lors du téléchargement de l'image.";
        }
    } else {
        $error_msg = "Le fichier sélectionné n'est pas une image.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Article</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f6f3;
            color: #333;
            padding: 20px;
        }
        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: auto;
        }
        form h2 {
            margin-bottom: 20px;
            color: #4b6584;
        }
        form label {
            display: block;
            margin-bottom: 8px;
        }
        form input[type="text"],
        form input[type="number"],
        form input[type="url"],
        form input[type="file"],
        form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        form button {
            background-color: #6a89cc;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        form button:hover {
            background-color: #4a69bd;
        }
        .message {
            margin-top: 20px;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }
        .message.success {
            background-color: #2ecc71;
            color: #fff;
        }
        .message.error {
            background-color: #e74c3c;
            color: #fff;
        }
    </style>
</head>
<body>

<form action="" method="post" enctype="multipart/form-data">
    <h2>Ajouter un Article</h2>

    <?php if ($success_msg): ?>
        <div class="message success"><?php echo htmlspecialchars($success_msg); ?></div>
    <?php elseif ($error_msg): ?>
        <div class="message error"><?php echo htmlspecialchars($error_msg); ?></div>
    <?php endif; ?>

    <label for="nom">Nom de l'article :</label>
    <input type="text" id="nom" name="nom" required>

    <label for="description">Description :</label>
    <textarea id="description" name="description"></textarea>

    <label for="prix">Prix :</label>
    <input type="number" id="prix" name="prix" step="0.01">

    <label for="lien">Lien vers le site vendeur :</label>
    <input type="text" id="lien" name="lien">

    <label for="categorie">Catégorie :</label>
    <input type="text" id="categorie" name="categorie">

    <label for="image">Image :</label>
    <input type="file" id="image" name="image" accept="image/*">

    <button type="submit">Ajouter l'article</button>
</form>

</body>
</html>
