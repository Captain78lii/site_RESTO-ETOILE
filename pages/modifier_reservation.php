<?php 
include($_SERVER['DOCUMENT_ROOT'] . '/db.php');
include($_SERVER['DOCUMENT_ROOT'] . '/header.php');

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: profil.php");
    exit();
}

$id_res = intval($_GET['id']);
$user_id = intval($_SESSION['user_id']);

// Récupère les données actuelles
$res = mysqli_query($conn, "SELECT * FROM reservations WHERE id = $id_res AND user_id = $user_id");
$data = mysqli_fetch_assoc($res);

if (!$data) { header("Location: profil.php"); exit(); }

// Traitement de la modification
if (isset($_POST['update_res'])) {
    csrf_verify();

    $date = mysqli_real_escape_string($conn, $_POST['date_res']);
    $heure = mysqli_real_escape_string($conn, $_POST['heure_res']);
    $personnes = intval($_POST['nb_personnes']);

    $query = "UPDATE reservations SET
              date_reservation = '$date',
              heure_reservation = '$heure',
              nb_personnes = $personnes
              WHERE id = $id_res AND user_id = $user_id";
    
    if (mysqli_query($conn, $query)) {
        header("Location: profil.php?msg=success");
        exit();
    }
}
?>

<div class="container">
    <div class="form-box" style="max-width: 500px;">
        <h3>Modifier ma réservation</h3>
        <form method="POST">
            <?php echo csrf_field(); ?>
            <div class="form-group">
                <label>Date</label>
                <input type="date" name="date_res" value="<?php echo $data['date_reservation']; ?>" required min="<?php echo date('Y-m-d'); ?>">
            </div>
            <div class="form-group">
                <label>Heure</label>
                <select name="heure_res" required style="width:100%; padding:10px; margin-bottom:15px;">
                    <?php 
                    $creneaux = ["11:30", "12:00", "12:30", "13:00", "19:00", "19:30", "20:00", "20:30", "21:00"];
                    foreach($creneaux as $c) {
                        $selected = ($data['heure_reservation'] == $c) ? "selected" : "";
                        echo "<option value='$c' $selected>$c</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label>Nombre de personnes</label>
                <input type="number" name="nb_personnes" value="<?php echo $data['nb_personnes']; ?>" min="1" max="20" required>
            </div>
            <button type="submit" name="update_res" class="btn" style="width:100%;">Enregistrer les modifications</button>
            <p style="text-align:center; margin-top:15px;"><a href="profil.php" style="color:gray;">Annuler</a></p>
        </form>
    </div>
</div>