<?php
include 'bdd-connexion.php';
session_start();
$success_msg = "";
$error_msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);  // Hash du mot de passe

    try {
        // Vérifier si l'utilisateur existe déjà
        $sql = "SELECT * FROM utilisateurs WHERE email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $error_msg = "Un utilisateur avec cet email existe déjà.";
        } else {
            // Insérer l'utilisateur dans la base de données
            $sql = "INSERT INTO utilisateurs (username, email, password) VALUES (:username, :email, :password)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password);

            if ($stmt->execute()) {
                $sql = "SELECT * FROM utilisateurs WHERE email = :email";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':email', $email);
                $stmt->execute();
        
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header("Location: index.php"); // Rediriger vers une page protégée
                exit();

            } else {
                $error_msg = "Erreur lors de l'inscription.";
            }
        }
    } catch (PDOException $e) {
        $error_msg = "Erreur : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <style>
        /* Styles similaires aux autres pages pour une cohérence visuelle */
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
            max-width: 500px;
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
        form input[type="email"],
        form input[type="password"] {
            width: 95%;
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

<form action="" method="post">
    <h2>Inscription</h2>

    <?php if ($success_msg): ?>
        <div class="message success"><?php echo htmlspecialchars($success_msg); ?></div>
    <?php elseif ($error_msg): ?>
        <div class="message error"><?php echo htmlspecialchars($error_msg); ?></div>
    <?php endif; ?>

    <label for="username">Un nom pour savoir qui tu es :</label>
    <input type="text" id="username" name="username" required>

    <label for="email">Un email pour t'identifier :</label>
    <input type="email" id="email" name="email" required>

    <label for="password">Un mot de passe pour te connecter :</label>
    <input type="password" id="password" name="password" required>
	
	<p><i>Pas d'inquiétude pour tes données, elles restent avec moi</i></p>

    <button type="submit">S'inscrire</button>
</form>

</body>
</html>
