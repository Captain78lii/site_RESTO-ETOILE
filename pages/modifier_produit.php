<?php
include($_SERVER['DOCUMENT_ROOT'] . '/db.php');
include($_SERVER['DOCUMENT_ROOT'] . '/header.php');
include($_SERVER['DOCUMENT_ROOT'] . '/upload_functions.php');

// Sécurité : Seul l'administrateur connecté peut accéder à cette page
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo "<div class='container'><p style='color:red; text-align:center; font-weight:bold; margin-top:50px;'>Accès refusé. Vous devez être administrateur.</p></div>";
    exit();
}

// Vérification de la présence de l'identifiant du produit dans l'URL
if (!isset($_GET['id'])) {
    header("Location: /pages/admin.php");
    exit();
}

$id_prod = intval($_GET['id']);

// 1. Récupération des informations actuelles du produit
$query = "SELECT * FROM produits WHERE id = $id_prod";
$result = mysqli_query($conn, $query);
$prod = mysqli_fetch_assoc($result);

// Si le produit n'existe pas en BDD
if (!$prod) {
    echo "<div class='container'><p style='color:red; text-align:center; font-weight:bold; margin-top:50px;'>Produit introuvable.</p></div>";
    exit();
}

$message = "";

// 2. Traitement du formulaire de modification lors de la soumission
if (isset($_POST['modifier_produit'])) {
    csrf_verify();

    $nom = mysqli_real_escape_string($conn, $_POST['nom_plat']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $prix = floatval($_POST['prix']);
    $categorie = mysqli_real_escape_string($conn, $_POST['categorie']);

    // Conserve l'image actuelle si l'admin n'en envoie pas une nouvelle
    $resultat_image = handle_image_upload('image_file', $prod['image_url']);

    if (is_array($resultat_image)) {
        $message = "<p style='color:red;'>Erreur : " . htmlspecialchars($resultat_image['erreur']) . "</p>";
    } else {
        $image_url = mysqli_real_escape_string($conn, $resultat_image);

        $update_query = "UPDATE produits SET
                            nom = '$nom',
                            description = '$description',
                            prix = $prix,
                            categorie = '$categorie',
                            image_url = '$image_url'
                         WHERE id = $id_prod";

        if (mysqli_query($conn, $update_query)) {
            $message = "<p style='color:green; font-weight:bold; text-align:center;'>Le produit a bien été mis à jour ! ✨ Redirection en cours...</p>";
            // Redirection vers l'administration après 2 secondes
            header("refresh:2;url=/pages/admin.php");
        } else {
            $message = "<p style='color:red;'>Erreur lors de la mise à jour : " . mysqli_error($conn) . "</p>";
        }
    }
}
?>

<div class="container">
    <div class="form-box" style="max-width: 600px; margin-top: 40px;">
        <h2 style="color:var(--brand-red); text-align:center;">✏️ Modifier le produit</h2>
        <br>
        
        <?php echo $message; ?>

        <form method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <div class="form-group">
                <label>Nom du plat / boisson</label>
                <input type="text" name="nom_plat" required value="<?php echo htmlspecialchars($prod['nom']); ?>">
            </div>
            <div class="form-group">
                <label>Description (Ingrédients)</label>
                <input type="text" name="description" required value="<?php echo htmlspecialchars($prod['description']); ?>">
            </div>
            <div class="form-group">
                <label>Prix (€)</label>
                <input type="number" step="0.01" name="prix" required value="<?php echo htmlspecialchars($prod['prix']); ?>">
            </div>
            <div class="form-group">
                <label>Catégorie (Dossier Parent)</label>
                <select name="categorie" style="width:100%; padding:12px; border-radius:8px; border:1px solid #ddd; background:#f9f9f9; margin-bottom:15px;">
                    <option value="Tacos" <?php echo (isset($prod['categorie']) && $prod['categorie'] === 'Tacos') ? 'selected' : ''; ?>>
                        🌮 Tacos Maison
                    </option>
                    <option value="Sandwichs" <?php echo (isset($prod['categorie']) && $prod['categorie'] === 'Sandwichs') ? 'selected' : ''; ?>>
                        🥖 Sandwichs & Paninis
                    </option>
                    <option value="Burgers" <?php echo (isset($prod['categorie']) && $prod['categorie'] === 'Burgers') ? 'selected' : ''; ?>>
                        🍔 Burgers
                    </option>
                    <option value="Assiettes" <?php echo (isset($prod['categorie']) && $prod['categorie'] === 'Assiettes') ? 'selected' : ''; ?>>
                        🍽️ Assiettes
                    </option>
                    <option value="Barquettes" <?php echo (isset($prod['categorie']) && $prod['categorie'] === 'Barquettes') ? 'selected' : ''; ?>>
                        🍟 Barquettes & Accompagnements
                    </option>
                    <option value="Boissons" <?php echo (isset($prod['categorie']) && $prod['categorie'] === 'Boissons') ? 'selected' : ''; ?>>
                        🥤 Boissons
                    </option>
                    <option value="Desserts" <?php echo (isset($prod['categorie']) && $prod['categorie'] === 'Desserts') ? 'selected' : ''; ?>>
                        🍰 Desserts
                    </option>
                    <option value="Kids" <?php echo (isset($prod['categorie']) && $prod['categorie'] === 'Kids') ? 'selected' : ''; ?>>
                        👶 Menu Kids
                    </option>
                </select>
            </div>
            <div class="form-group">
                <label>Image actuelle</label>
                <br>
                <img src="/images/<?php echo htmlspecialchars($prod['image_url']); ?>" style="width:100px; height:100px; object-fit:cover; border-radius:8px; margin:10px 0;">
            </div>
            <div class="form-group">
                <label>Remplacer l'image (laisser vide pour conserver l'image actuelle)</label>
                <input type="file" name="image_file" accept="image/jpeg,image/png,image/gif,image/webp">
            </div>

            <button type="submit" name="modifier_produit" class="btn" style="width:100%; margin-bottom: 10px;">Enregistrer les modifications</button>
            <a href="/pages/admin.php" class="btn" style="width:100%; background-color: #7f8c8d; text-align: center; display: block; text-decoration: none; line-height: 2.4;">Annuler</a>
        </form>
    </div>
</div>

</body>
</html>