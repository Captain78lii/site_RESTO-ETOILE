<?php
include($_SERVER['DOCUMENT_ROOT'] . '/db.php');
include($_SERVER['DOCUMENT_ROOT'] . '/header.php');
include($_SERVER['DOCUMENT_ROOT'] . '/commande_functions.php');

// Sécurité : si pas connecté, on renvoie à la connexion
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 1. Récupère les infos de l'utilisateur
$query_user = "SELECT * FROM utilisateurs WHERE id = '$user_id'";
$res_user = mysqli_query($conn, $query_user);
$user_data = mysqli_fetch_assoc($res_user);

// 2. Récupère ses réservations
$query_res = "SELECT * FROM reservations WHERE user_id = '$user_id' ORDER BY date_reservation DESC";
$res_reservations = mysqli_query($conn, $query_res);

// 3. Récupère ses avis
$query_avis = "SELECT * FROM avis WHERE user_id = '$user_id' ORDER BY date_publication DESC";
$res_avis = mysqli_query($conn, $query_avis);

// 4. Récupère ses commandes
$user_id_int = intval($user_id);
$query_commandes = "SELECT * FROM commandes WHERE user_id = $user_id_int ORDER BY date_commande DESC";
$res_commandes = mysqli_query($conn, $query_commandes);

?>

<div class="container">
    <h2 style="color:var(--accent-gold);">Mon Espace Personnel</h2>
    <p>Bienvenue sur votre profil, <strong><?php echo htmlspecialchars($user_data['nom']); ?></strong>.</p>
    <hr style="margin: 20px 0; border: 0; border-top: 1px solid #eee;">

    <div class="form-box" style="max-width: 100%; margin-bottom: 20px;">
        <h3>🧾 Mes Commandes</h3>
        <?php if (mysqli_num_rows($res_commandes) > 0): ?>
            <table class="cart-table" style="margin-top: 10px;">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Détail</th>
                        <th>Total</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($c = mysqli_fetch_assoc($res_commandes)):
                        $color = 'orange';
                        if ($c['statut'] === 'Prête' || $c['statut'] === 'Livrée') $color = 'green';
                        if ($c['statut'] === 'Annulée') $color = 'red';
                        if ($c['statut'] === 'En préparation') $color = '#3498db';

                        $lignes_query = "SELECT * FROM commande_lignes WHERE commande_id = " . intval($c['id']);
                        $lignes_result = mysqli_query($conn, $lignes_query);
                    ?>
                        <tr>
                            <td><?php echo date('d/m/Y à H:i', strtotime($c['date_commande'])); ?></td>
                            <td style="font-size: 0.85rem;">
                                <?php while ($ligne = mysqli_fetch_assoc($lignes_result)): ?>
                                    <div style="margin-bottom: 6px;">
                                        <strong><?php echo intval($ligne['quantite']); ?>x <?php echo htmlspecialchars($ligne['nom_produit']); ?></strong>
                                        <?php $detailOptions = formatOptionsCommande($ligne['options_json']); ?>
                                        <?php if ($detailOptions): ?>
                                            <br><span style="color: #7f8c8d;"><?php echo $detailOptions; ?></span>
                                        <?php endif; ?>
                                    </div>
                                <?php endwhile; ?>
                            </td>
                            <td style="font-weight: bold;"><?php echo number_format($c['total'], 2); ?> €</td>
                            <td style="color:<?php echo $color; ?>; font-weight:bold;"><?php echo htmlspecialchars($c['statut']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucune commande pour le moment.</p>
        <?php endif; ?>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        
        <div class="form-box" style="max-width: 100%; margin: 0;">
            <h3>📅 Mes Réservations</h3>
            <?php if (mysqli_num_rows($res_reservations) > 0): ?>
                <ul style="list-style: none; margin-top: 10px;">
                    <?php while ($r = mysqli_fetch_assoc($res_reservations)): ?>
                        <li style="padding: 10px; border-bottom: 1px solid #eee;">
                            Le <strong><?php echo date('d/m/Y', strtotime($r['date_reservation'])); ?></strong> 
                            à <?php echo $r['heure_reservation']; ?> 
                            (<?php echo $r['nb_personnes']; ?> pers.)
                            <br><small style="color: orange;">Statut : <?php echo $r['statut']; ?></small>
                            
                            <div style="margin-top: 10px; font-size: 0.8rem;">
                                <a href="modifier_reservation.php?id=<?php echo $r['id']; ?>" style="color: #3498db; text-decoration: none; margin-right: 10px;">✏️ Modifier</a>
                                <a href="<?php echo csrf_url('annuler_reservation.php?id=' . $r['id']); ?>"
                                style="color: #e74c3c; text-decoration: none;"
                                onclick="return confirm('Voulez-vous vraiment annuler cette réservation ?');">❌ Annuler</a>
                            </div>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>Aucune réservation pour le moment.</p>
            <?php endif; ?>
        </div>

        <div class="form-box" style="max-width: 100%; margin: 0;">
            <h3>⭐ Mes Avis</h3>
            <?php if (mysqli_num_rows($res_avis) > 0): ?>
                <ul style="list-style: none; margin-top: 10px;">
                    <?php while ($a = mysqli_fetch_assoc($res_avis)): ?>
                        <li style="padding: 10px; border-bottom: 1px solid #eee;">
                            Note : <?php echo str_repeat("⭐", $a['note']); ?>
                            <p style="font-style: italic;">"<?php echo htmlspecialchars($a['commentaire']); ?>"</p>
                            
                            <div style="margin-top: 10px; font-size: 0.8rem;">
                                <a href="modifier_avis.php?id=<?php echo $a['id']; ?>" style="color: #3498db; text-decoration: none; margin-right: 10px;">✏️ Modifier</a>
                                <a href="<?php echo csrf_url('supprimer_avis.php?id=' . $a['id']); ?>"
                                style="color: #e74c3c; text-decoration: none;"
                                onclick="return confirm('Supprimer cet avis ?');">🗑️ Supprimer</a>
                            </div>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>Vous n'avez pas encore laissé d'avis.</p>
            <?php endif; ?>
        </div>

        <div style="text-align: center; margin-top: 30px; padding-bottom: 50px;">
            <a href="<?php echo csrf_url('supprimer_compte.php'); ?>"
            style="color: #c0392b; font-size: 0.9rem;"
            onclick="return confirm('ATTENTION : Voulez-vous vraiment supprimer votre compte et tous vos points de fidélité ?');">
            Supprimer mon compte définitivement
            </a>
        </div>
        <div class="form-box" style="max-width: 100%; margin-top: 20px; text-align: center; background: linear-gradient(135deg, #101f3c, #1e3a8a); color: white;">
            <h3 style="color:var(--accent-gold);">📇 Ma Carte de Fidélité</h3>
            <p style="margin: 10px 0;">Vous avez actuellement :</p>
            <div style="font-size: 2.5rem; font-weight: bold; color: var(--accent-gold);">
                <?php echo $user_data['points_fidelite']; ?> / 10
            </div>
            <p>points de fidélité</p>
            
            <div style="width: 80%; background: #eee; height: 10px; border-radius: 5px; margin: 15px auto; overflow: hidden;">
                <div style="width: <?php echo ($user_data['points_fidelite'] * 10); ?>%; background: var(--brand-red); height: 100%;"></div>
            </div>
            
            <?php if($user_data['points_fidelite'] >= 10): ?>
                <p style="color: #2ecc71; font-weight: bold;">Félicitations ! Votre prochain repas est offert ! 🎁</p>
            <?php else: ?>
                <p style="font-size: 0.8rem;">Encore <?php echo (10 - $user_data['points_fidelite']); ?> commande(s) pour obtenir votre repas gratuit.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>