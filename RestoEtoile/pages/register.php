<?php 
$page_simple = true; // On indique que c'est une page simple

// 1. D'abord la connexion
include($_SERVER['DOCUMENT_ROOT'] . '/RestoEtoile/db.php');

// 2. Ensuite le header
include($_SERVER['DOCUMENT_ROOT'] . '/RestoEtoile/header.php');

//  Traitement du formulaire quand on clique sur "S'inscrire"
if (isset($_POST['register'])) {
    $nom = mysqli_real_escape_string($conn, $_POST['nom']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    // On sécurise le mot de passe avec password_hash
    $password = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT); 

    $query = "INSERT INTO utilisateurs (nom, email, mot_de_passe) VALUES ('$nom', '$email', '$password')";
    
    if (mysqli_query($conn, $query)) {
        echo "<p style='color:green; text-align:center;'>Inscription réussie !</p>";
    } else {
        echo "<p style='color:red; text-align:center;'>Erreur : " . mysqli_error($conn) . "</p>";
    }
}
?>



    <div class="container">
        <div class="form-box">
            <h2 style="color:var(--accent-gold); text-align:center;">Créer un compte</h2>
            <br>
            <form method="POST" action="register.php">
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