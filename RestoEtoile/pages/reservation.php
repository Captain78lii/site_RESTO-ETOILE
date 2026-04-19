<?php 
// 1. Inclusion de la BDD et du header
include($_SERVER['DOCUMENT_ROOT'] . '/RestoEtoile/db.php');
include($_SERVER['DOCUMENT_ROOT'] . '/RestoEtoile/header.php');

$message = "";

// 2. Traitement du formulaire
if (isset($_POST['reserver'])) {
    // On vérifie si l'utilisateur est connecté
    if (!isset($_SESSION['user_id'])) {
        $message = "<p style='color:red;'>Vous devez être connecté pour réserver une table.</p>";
    } else {
        $user_id = $_SESSION['user_id'];
        $date = mysqli_real_escape_string($conn, $_POST['date_res']);
        $heure = mysqli_real_escape_string($conn, $_POST['heure_res']);
        $personnes = intval($_POST['nb_personnes']);

        // Requête d'insertion
        $query = "INSERT INTO reservations (user_id, date_reservation, heure_reservation, nb_personnes) 
                  VALUES ('$user_id', '$date', '$heure', '$personnes')";

        if (mysqli_query($conn, $query)) {
            $message = "<p style='color:green; font-weight:bold;'>Réservation confirmée pour le $date à $heure ! ✨</p>";
        } else {
            $message = "<p style='color:red;'>Erreur : " . mysqli_error($conn) . "</p>";
        }
    }
}
?>

<div class="container">
    <div class="form-box" style="max-width: 600px;">
        <h2 style="color:var(--accent-gold); text-align:center;">Réserver une table</h2>
        <p style="text-align:center; margin-bottom: 20px;">L'Étoile du Gourmet vous accueille avec plaisir.</p>
        
        <?php echo $message; ?>

        <form method="POST" action="reservation.php">
            <div class="form-group">
                <label>Date de visite</label>
                <input type="date" name="date_res" required min="<?php echo date('Y-m-d'); ?>">
            </div>
            
            <div class="form-group">
                <label>Heure</label>
                <select name="heure_res" required style="width:100%; padding:10px; border-radius:5px; border:1px solid #ddd;">
                    <option value="11:30">11:30</option>
                    <option value="12:00">12:00</option>
                    <option value="12:30">12:30</option>
                    <option value="13:00">13:00</option>
                    <option value="19:00">19:00</option>
                    <option value="19:30">19:30</option>
                    <option value="20:00">20:00</option>
                    <option value="20:30">20:30</option>
                    <option value="21:00">21:00</option>
                </select>
            </div>

            <div class="form-group">
                <label>Nombre de personnes</label>
                <input type="number" name="nb_personnes" min="1" max="20" value="2" required>
            </div>

            <button type="submit" name="reserver" class="btn" style="width:100%; margin-top:10px;">Confirmer la réservation</button>
        </form>
    </div>
</div>

</body>
</html>