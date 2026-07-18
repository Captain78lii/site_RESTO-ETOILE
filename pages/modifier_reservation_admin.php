<?php 
include($_SERVER['DOCUMENT_ROOT'] . '/db.php');
include($_SERVER['DOCUMENT_ROOT'] . '/header.php');

// Sécurité : Seul l'administrateur peut modifier n'importe quelle réservation
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo "<div class='container'><p style='color:red; text-align:center; font-weight:bold; margin-top:50px;'>Accès refusé.</p></div>";
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: /pages/admin.php");
    exit();
}

$id_res = intval($_GET['id']);

// 1. Récupération des informations actuelles de la réservation avec le nom du client
$query = "SELECT r.*, u.nom FROM reservations r 
          JOIN utilisateurs u ON r.user_id = u.id 
          WHERE r.id = $id_res";
$result = mysqli_query($conn, $query);
$res_data = mysqli_fetch_assoc($result);

if (!$res_data) {
    echo "<div class='container'><p style='color:red; text-align:center; font-weight:bold; margin-top:50px;'>Réservation introuvable.</p></div>";
    exit();
}

$message = "";

// 2. Traitement du formulaire
if (isset($_POST['modifier_reservation'])) {
    csrf_verify();

    $date = mysqli_real_escape_string($conn, $_POST['date_res']);
    $heure = mysqli_real_escape_string($conn, $_POST['heure_res']);
    $personnes = intval($_POST['nb_personnes']);
    $statut = mysqli_real_escape_string($conn, $_POST['statut']);

    $update_query = "UPDATE reservations SET 
                        date_reservation = '$date', 
                        heure_reservation = '$heure', 
                        nb_personnes = $personnes,
                        statut = '$statut'
                     WHERE id = $id_res";

    if (mysqli_query($conn, $update_query)) {
        $message = "<p style='color:green; font-weight:bold; text-align:center;'>La réservation a bien été modifiée ! 🌟 Redirection...</p>";
        header("refresh:2;url=/pages/admin.php");
    } else {
        $message = "<p style='color:red;'>Erreur de mise à jour : " . mysqli_error($conn) . "</p>";
    }
}
?>

<div class="container">
    <div class="form-box" style="max-width: 600px; margin-top: 40px;">
        <h2 style="color:var(--brand-red); text-align:center;">✏️ Modifier la réservation</h2>
        <p style="text-align: center; color: gray; margin-bottom: 20px;">Pour le client : <strong><?php echo htmlspecialchars($res_data['nom']); ?></strong></p>
        
        <?php echo $message; ?>

        <form method="POST">
            <?php echo csrf_field(); ?>
            <div class="form-group">
                <label>Date de visite</label>
                <input type="date" name="date_res" required value="<?php echo $res_data['date_reservation']; ?>">
            </div>
            
            <div class="form-group">
                <label>Heure</label>
                <select name="heure_res" required style="width:100%; padding:12px; border-radius:8px; border:1px solid #ddd; background:#f9f9f9; margin-bottom:15px;">
                    <?php 
                    $creneaux = ["11:30", "12:00", "12:30", "13:00", "19:00", "19:30", "20:00", "20:30", "21:00"];
                    foreach($creneaux as $c) {
                        $selected = ($res_data['heure_reservation'] === $c) ? 'selected' : '';
                        echo "<option value='$c' $selected>$c</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label>Nombre de personnes</label>
                <input type="number" name="nb_personnes" required min="1" max="50" value="<?php echo $res_data['nb_personnes']; ?>">
            </div>

            <div class="form-group">
                <label>Statut</label>
                <select name="statut" style="width:100%; padding:12px; border-radius:8px; border:1px solid #ddd; background:#f9f9f9; margin-bottom:15px;">
                    <option value="En attente" <?php if($res_data['statut'] === 'En attente') echo 'selected'; ?>>En attente</option>
                    <option value="Confirmé" <?php if($res_data['statut'] === 'Confirmé') echo 'selected'; ?>>Confirmé</option>
                    <option value="Annulé" <?php if($res_data['statut'] === 'Annulé') echo 'selected'; ?>>Annulé</option>
                </select>
            </div>
            
            <button type="submit" name="modifier_reservation" class="btn" style="width:100%; margin-bottom: 10px;">Enregistrer les modifications</button>
            <a href="/pages/admin.php" class="btn" style="width:100%; background-color: #7f8c8d; text-align: center; display: block; text-decoration: none; line-height: 2.4;">Annuler</a>
        </form>
    </div>
</div>

</body>
</html>