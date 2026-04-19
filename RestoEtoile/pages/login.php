<?php 
$page_simple = true; // On indique que c'est une page simple

// 1. Inclusion de la base de données et du header
include($_SERVER['DOCUMENT_ROOT'] . '/RestoEtoile/db.php');
include($_SERVER['DOCUMENT_ROOT'] . '/RestoEtoile/header.php');

// 2. Traitement du formulaire de connexion
if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['mot_de_passe'];

    // On cherche l'utilisateur dans la base de données
    $query = "SELECT * FROM utilisateurs WHERE email = '$email'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    // On vérifie si l'utilisateur existe ET si le mot de passe correspond
    if ($user && password_verify($password, $user['mot_de_passe'])) {
        // On stocke les infos dans la session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_nom'] = $user['nom'];
        
        echo "<p style='color:green; text-align:center; margin-top:20px;'>Connexion réussie, bienvenue " . htmlspecialchars($user['nom']) . " !</p>";
        // Optionnel : Redirection vers l'accueil après 2 secondes
        header("refresh:2;url=/RestoEtoile/index.php");
    } else {
        echo "<p style='color:red; text-align:center; margin-top:20px;'>Email ou mot de passe incorrect.</p>";
    }
}
?>

    <div class="container">
        <div class="form-box">
            <h2 style="color:var(--accent-gold); text-align:center;">Se connecter</h2>
            <br>
            <form method="POST" action="login.php">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="votre@email.com" required>
                </div>
                <div class="form-group">
                    <label>Mot de passe</label>
                    <input type="password" name="mot_de_passe" placeholder="******" required>
                </div>
                <button type="submit" name="login" class="btn" style="width:100%">Entrer</button>
            </form>
            <p style="text-align:center; margin-top:1rem;">
                Pas encore de compte ? <a href="register.php" style="color:var(--accent-gold)">S'inscrire</a>
            </p>
        </div>
    </div>
</body>
</html>