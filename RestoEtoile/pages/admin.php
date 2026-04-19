<?php 
include($_SERVER['DOCUMENT_ROOT'] . '/RestoEtoile/db.php');
include($_SERVER['DOCUMENT_ROOT'] . '/RestoEtoile/header.php');

// Optionnel : Idéalement, il faudrait vérifier ici si l'utilisateur est un "admin"
// Pour l'instant, on affiche la liste pour que tu puisses tester
?>

<div class="container">
    <h2 style="color:var(--brand-red); text-align:center; margin-bottom:20px;">Tableau de Bord Administration</h2>
    
    <div class="form-box" style="max-width: 100%;">
        <h3>📋 Toutes les Réservations</h3>
        <br>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Client</th>
                    <th>Date</th>
                    <th>Heure</th>
                    <th>Personnes</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // On récupère toutes les réservations avec le nom du client
                $query = "SELECT r.*, u.nom FROM reservations r 
                          JOIN utilisateurs u ON r.user_id = u.id 
                          ORDER BY r.date_reservation DESC, r.heure_reservation DESC";
                $result = mysqli_query($conn, $query);

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['nom']) . "</td>";
                        echo "<td>" . date('d/m/Y', strtotime($row['date_reservation'])) . "</td>";
                        echo "<td>" . $row['heure_reservation'] . "</td>";
                        echo "<td>" . $row['nb_personnes'] . "</td>";
                        echo "<td style='color:orange; font-weight:bold;'>" . $row['statut'] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' style='text-align:center;'>Aucune réservation enregistrée.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>