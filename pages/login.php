<?php 
$page_simple = true;

include($_SERVER['DOCUMENT_ROOT'] . '/db.php');
include($_SERVER['DOCUMENT_ROOT'] . '/header.php');

if (isset($_POST['login'])) {
    csrf_verify();

    $email = $_POST['email']; // requête préparée, pas besoin d'échapper
    $password = $_POST['mot_de_passe'];

    $stmt = mysqli_prepare($conn, "SELECT * FROM utilisateurs WHERE email = ?");

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if ($user && password_verify($password, $user['mot_de_passe'])) {
            // évite la fixation de session
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nom'] = $user['nom'];
            $_SESSION['user_role'] = $user['role'];
            
            echo "<p style='color:green; text-align:center; margin-top:20px;'>Connexion réussie, bienvenue " . htmlspecialchars($user['nom']) . " !</p>";
            header("refresh:2;url=/index.php");
        } else {
            echo "<p style='color:red; text-align:center; margin-top:20px;'>Email ou mot de passe incorrect.</p>";
        }
    } else {
        echo "<p style='color:red; text-align:center; margin-top:20px;'>Erreur système lors de la connexion.</p>";
    }
}
?>

    <div class="container">
        <div class="form-box">
            <h2 style="color:var(--accent-gold); text-align:center;">Se connecter</h2>
            <br>
            <form method="POST" action="login.php">
                <?php echo csrf_field(); ?>
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