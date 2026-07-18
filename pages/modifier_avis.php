<?php 
include($_SERVER['DOCUMENT_ROOT'] . '/db.php');
include($_SERVER['DOCUMENT_ROOT'] . '/header.php');

$id_avis = intval($_GET['id']);
$user_id = intval($_SESSION['user_id']);

$res = mysqli_query($conn, "SELECT * FROM avis WHERE id = $id_avis AND user_id = $user_id");
$avis = mysqli_fetch_assoc($res);

if (isset($_POST['update_avis'])) {
    csrf_verify();

    $nouveau_comm = mysqli_real_escape_string($conn, $_POST['commentaire']);
    $nouvelle_note = intval($_POST['note']);

    // re-vérifie user_id, sinon on peut modifier l'avis d'un autre via l'id dans l'URL
    mysqli_query($conn, "UPDATE avis SET commentaire = '$nouveau_comm', note = $nouvelle_note WHERE id = $id_avis AND user_id = $user_id");
    header("Location: profil.php");
    exit();
}
?>
<div class="container">
    <div class="form-box">
        <h3>Modifier mon avis</h3>
        <form method="POST">
            <?php echo csrf_field(); ?>
            <label>Note</label>
            <select name="note" style="width:100%; padding:10px; margin-bottom:15px;">
                <?php for($i=1; $i<=5; $i++): ?>
                    <option value="<?php echo $i; ?>" <?php if($avis['note'] == $i) echo 'selected'; ?>><?php echo $i; ?> ⭐</option>
                <?php endfor; ?>
            </select>
            <label>Commentaire</label>
            <textarea name="commentaire" rows="5" style="width:100%; padding:10px; margin-bottom:15px;"><?php echo htmlspecialchars($avis['commentaire']); ?></textarea>
            <button type="submit" name="update_avis" class="btn">Enregistrer</button>
            <a href="profil.php">Annuler</a>
        </form>
    </div>
</div>