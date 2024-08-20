<?php
include 'bdd-connexion.php';
session_start();

$error_msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        // Rechercher l'utilisateur dans la base de données
        $sql = "SELECT * FROM utilisateurs WHERE email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Mot de passe correct, démarrer une session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: index.php"); // Rediriger vers une page protégée
            exit();
        } else {
            $error_msg = "Email ou mot de passe incorrect.";
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
    <title>Connexion</title>
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
      
        .register-btn {
            background-color: #82ccdd;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
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
        .message.error {
            background-color: #e74c3c;
            color: #fff;
        }
    </style>
</head>
<body>

<form action="" method="post">
    <h2>Connexion</h2>

    <?php if ($error_msg): ?>
        <div class="message error"><?php echo htmlspecialchars($error_msg); ?></div>
    <?php endif; ?>
	
	<p>Pour accéder à la liste de naissance, il est nécessaire de te connecter ! </p>

    <label for="email">Ton email :</label>
    <input type="email" id="email" name="email" required>

    <label for="password">Ton mot de passe :</label>
    <input type="password" id="password" name="password" required>

    <button type="submit">Se connecter</button>
	<p><i>Si tu n'as pas de compte, c'est ici que ça se passe <i></p>
	
	<a href="inscription.php" class="register-btn">Créer un compte</a>
</form>

</body>
</html>
