<?php 
include($_SERVER['DOCUMENT_ROOT'] . '/db.php');
include($_SERVER['DOCUMENT_ROOT'] . '/header.php');

$message = "";

// 1. Traitement de l'envoi d'un nouvel avis
if (isset($_POST['publier_avis'])) {
    if (!isset($_SESSION['user_id'])) {
        $message = "<p style='color:red;'>Connectez-vous pour laisser un avis.</p>";
    } else {
        csrf_verify();

        $user_id = intval($_SESSION['user_id']);
        $note = intval($_POST['note']);
        $commentaire = mysqli_real_escape_string($conn, $_POST['commentaire']);

        $query = "INSERT INTO avis (user_id, note, commentaire) VALUES ($user_id, $note, '$commentaire')";
        if (mysqli_query($conn, $query)) {
            $message = "<p style='color:green;'>Merci ! Votre avis a été publié. ⭐</p>";
        }
    }
}

// 2. Récupération avis existants
$query_avis = "SELECT avis.*, utilisateurs.nom FROM avis 
               JOIN utilisateurs ON avis.user_id = utilisateurs.id 
               ORDER BY date_publication DESC";
$result_avis = mysqli_query($conn, $query_avis);
?>

<div class="container">
    <h2 style="text-align:center; color:var(--accent-gold);">Avis de nos clients</h2>

    <?php if (isset($_SESSION['user_id'])): ?>
    <div class="form-box" style="max-width: 600px; margin-bottom: 40px;">
        <h3>Laissez votre avis</h3>
        <?php echo $message; ?>
        <form method="POST">
            <?php echo csrf_field(); ?>
            <div class="form-group">
                <label>Note / 5</label>
                <select name="note" required style="width:100%; padding:10px;">
                    <option value="5">⭐⭐⭐⭐⭐ (Excellent)</option>
                    <option value="4">⭐⭐⭐⭐ (Très bon)</option>
                    <option value="3">⭐⭐⭐ (Moyen)</option>
                    <option value="2">⭐⭐ (Décevant)</option>
                    <option value="1">⭐ (À éviter)</option>
                </select>
            </div>
            <div class="form-group">
                <label>Votre commentaire</label>
                <textarea name="commentaire" rows="4" style="width:100%; border-radius:8px; padding:10px;" required></textarea>
            </div>
            <button type="submit" name="publier_avis" class="btn" style="width:100%;">Publier</button>
        </form>
    </div>
    <?php else: ?>
        <p style="text-align:center;">Veuillez vous <a href="login.php" style="color:var(--accent-gold);">connecter</a> pour laisser un avis.</p>
    <?php endif; ?>

    <div class="avis-liste">
        <?php while ($avis = mysqli_fetch_assoc($result_avis)): ?>
            <div class="product-card" style="margin-bottom: 20px; padding: 20px; width:100%; display:block;">
                <div style="display:flex; justify-content: space-between;">
                    <strong>👤 <?php echo htmlspecialchars($avis['nom']); ?></strong>
                    <span style="color:var(--accent-gold);"><?php echo str_repeat("⭐", $avis['note']); ?></span>
                </div>
                <p style="margin-top:10px; font-style: italic;">"<?php echo htmlspecialchars($avis['commentaire']); ?>"</p>
                <small style="color:gray;">Publié le <?php echo date('d/m/Y', strtotime($avis['date_publication'])); ?></small>
            </div>
        <?php endwhile; ?>
    </div>
</div>

</body>
</html>