<?php 
$page_simple = true;

include($_SERVER['DOCUMENT_ROOT'] . '/db.php');
include($_SERVER['DOCUMENT_ROOT'] . '/header.php');

if (isset($_POST['register'])) {
    csrf_verify();

    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $password = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);

    $stmt = mysqli_prepare($conn, "INSERT INTO utilisateurs (nom, email, mot_de_passe) VALUES (?, ?, ?)");

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sss", $nom, $email, $password);

        if (mysqli_stmt_execute($stmt)) {
            echo "<p style='color:green; text-align:center; margin-top:20px;'>Inscription réussie ! Vous pouvez maintenant vous connecter.</p>";
        } else {
            // 1062 = email déjà utilisé (clé UNIQUE)
            if (mysqli_errno($conn) == 1062) {
                echo "<p style='color:red; text-align:center; margin-top:20px;'>Erreur : Cet email est déjà utilisé.</p>";
            } else {
                echo "<p style='color:red; text-align:center; margin-top:20px;'>Erreur lors de l'inscription.</p>";
            }
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "<p style='color:red; text-align:center; margin-top:20px;'>Erreur système lors de l'inscription.</p>";
    }
}
?>



    <div class="container">
        <div class="form-box">
            <h2 style="color:var(--accent-gold); text-align:center;">Créer un compte</h2>
            <br>
            <form method="POST" action="register.php">
                <?php echo csrf_field(); ?>
                <div class="form-group">
                    <label>Nom</label>
                    <input type="text" name="nom" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Mot de passe</label>
                    <input type="password" name="mot_de_passe" required>
                </div>
                <button type="submit" class="btn" style="width:100%" name="register">S'inscrire</button>
            </form>
        </div>
    </div>
</body>
</html>