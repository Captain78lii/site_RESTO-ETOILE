<?php
include($_SERVER['DOCUMENT_ROOT'] . '/db.php');
include($_SERVER['DOCUMENT_ROOT'] . '/header.php');
include($_SERVER['DOCUMENT_ROOT'] . '/commande_functions.php');
include($_SERVER['DOCUMENT_ROOT'] . '/upload_functions.php');

// SÉCURITÉ : Vérifie si l'utilisateur est connecté ET s'il est bien admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo "<div class='container' style='text-align:center; margin-top:50px;'>";
    echo "<h2 style='color:var(--brand-red);'>⛔ Accès Refusé</h2>";
    echo "<p>Vous n'avez pas l'autorisation d'accéder à cette page.</p>";
    echo "<br><a href='/index.php' class='btn'>Retour à l'accueil</a>";
    echo "</div>";
    exit(); // Arrête le chargement du reste de la page
}

if (isset($_GET['action']) && isset($_GET['id'])) {
    csrf_verify();

    $id_res = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action === 'confirmer') {
        $update_query = "UPDATE reservations SET statut = 'Confirmé' WHERE id = $id_res";
        mysqli_query($conn, $update_query);
    } elseif ($action === 'annuler') {
        $update_query = "UPDATE reservations SET statut = 'Annulé' WHERE id = $id_res";
        mysqli_query($conn, $update_query);
    }
    
    // Redirection pour nettoyer l'URL et rafraîchir la page
    header("Location: /pages/admin.php");
    exit();
}

// 🆕 CHANGEMENT DE STATUT D'UNE COMMANDE
if (isset($_GET['action_commande']) && isset($_GET['id_commande'])) {
    csrf_verify();

    $id_commande = intval($_GET['id_commande']);
    $statuts_valides = ['En attente', 'En préparation', 'Prête', 'Livrée', 'Annulée'];
    $nouveau_statut = $_GET['action_commande'];

    if (in_array($nouveau_statut, $statuts_valides, true)) {
        $stmt = mysqli_prepare($conn, "UPDATE commandes SET statut = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $nouveau_statut, $id_commande);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    header("Location: /pages/admin.php");
    exit();
}

// 🆕 TRAITEMENT DE LA SUPPRESSION DÉFINITIVE D'UNE RÉSERVATION PAR L'ADMIN
if (isset($_GET['delete_reservation'])) {
    csrf_verify();

    $id_res = intval($_GET['delete_reservation']);

    $delete_query = "DELETE FROM reservations WHERE id = $id_res";
    if (mysqli_query($conn, $delete_query)) {
        header("Location: /pages/admin.php?msg=res_supprimee");
        exit();
    }
}

// 🆕 TRAITEMENT DE LA SUPPRESSION D'UN PRODUIT
if (isset($_GET['delete_product'])) {
    csrf_verify();

    $id_prod = intval($_GET['delete_product']);

    $delete_query = "DELETE FROM produits WHERE id = $id_prod";
    if (mysqli_query($conn, $delete_query)) {
        header("Location: /pages/admin.php?msg=supprime");
        exit();
    }
}
?>

<div class="container">
    <h2 style="color:var(--brand-red); text-align:center; margin-bottom:20px;">Tableau de Bord Administration</h2>
    
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'res_supprimee'): ?>
        <div style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 15px; border-radius: 8px; text-align: center; margin-bottom: 20px; font-weight: bold;">
            La réservation a bien été supprimée définitivement ! 🗑️
        </div>
    <?php endif; ?>

    <div class="form-box" style="max-width: 100%;">
        <h3>🧾 Commandes</h3>
        <br>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Client</th>
                    <th>Date</th>
                    <th>Détail</th>
                    <th>Total</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $ordre_statuts = ['En attente' => 'En préparation', 'En préparation' => 'Prête', 'Prête' => 'Livrée'];

                $query_cmd = "SELECT c.*, u.nom FROM commandes c
                              JOIN utilisateurs u ON c.user_id = u.id
                              ORDER BY c.date_commande DESC";
                $result_cmd = mysqli_query($conn, $query_cmd);

                if (mysqli_num_rows($result_cmd) > 0) {
                    while ($cmd = mysqli_fetch_assoc($result_cmd)) {
                        $color = 'orange';
                        if ($cmd['statut'] === 'Prête' || $cmd['statut'] === 'Livrée') $color = 'green';
                        if ($cmd['statut'] === 'Annulée') $color = 'red';
                        if ($cmd['statut'] === 'En préparation') $color = '#3498db';

                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($cmd['nom']) . "</td>";
                        echo "<td>" . date('d/m/Y H:i', strtotime($cmd['date_commande'])) . "</td>";

                        echo "<td style='font-size:0.8rem;'>";
                        $lignes_cmd = mysqli_query($conn, "SELECT * FROM commande_lignes WHERE commande_id = " . intval($cmd['id']));
                        while ($ligne = mysqli_fetch_assoc($lignes_cmd)) {
                            echo "<div style='margin-bottom:4px;'><strong>" . intval($ligne['quantite']) . "x " . htmlspecialchars($ligne['nom_produit']) . "</strong>";
                            $detail = formatOptionsCommande($ligne['options_json']);
                            if ($detail) echo "<br><span style='color:#7f8c8d;'>" . $detail . "</span>";
                            echo "</div>";
                        }
                        echo "</td>";

                        echo "<td>" . number_format($cmd['total'], 2) . " €</td>";
                        echo "<td style='color:$color; font-weight:bold;'>" . htmlspecialchars($cmd['statut']) . "</td>";

                        echo "<td>";
                        if (isset($ordre_statuts[$cmd['statut']])) {
                            $prochain_statut = $ordre_statuts[$cmd['statut']];
                            $lien_progression = csrf_url("/pages/admin.php?action_commande=" . urlencode($prochain_statut) . "&id_commande=" . $cmd['id']);
                            echo "<a href='" . $lien_progression . "' class='btn' style='background-color:#2ecc71; padding:5px 10px; font-size:0.8rem; margin-right:5px;'>➡️ " . htmlspecialchars($prochain_statut) . "</a>";
                        }
                        if ($cmd['statut'] !== 'Livrée' && $cmd['statut'] !== 'Annulée') {
                            $lien_annulation = csrf_url("/pages/admin.php?action_commande=Annulée&id_commande=" . $cmd['id']);
                            echo "<a href='" . $lien_annulation . "' class='btn' style='background-color:#e74c3c; padding:5px 10px; font-size:0.8rem;' onclick=\"return confirm('Annuler cette commande ?');\">❌ Annuler</a>";
                        }
                        echo "</td>";

                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' style='text-align:center;'>Aucune commande enregistrée.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

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
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Récupère toutes les réservations avec le nom du client
                $query = "SELECT r.*, u.nom FROM reservations r 
                        JOIN utilisateurs u ON r.user_id = u.id 
                        ORDER BY r.date_reservation DESC, r.heure_reservation DESC";
                $result = mysqli_query($conn, $query);

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        // Définition de la couleur du statut
                        $color = 'orange';
                        if ($row['statut'] === 'Confirmé') $color = 'green';
                        if ($row['statut'] === 'Annulé') $color = 'red';

                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['nom']) . "</td>";
                        echo "<td>" . date('d/m/Y', strtotime($row['date_reservation'])) . "</td>";
                        echo "<td>" . htmlspecialchars($row['heure_reservation']) . "</td>";
                        echo "<td>" . intval($row['nb_personnes']) . "</td>";
                        echo "<td style='color:$color; font-weight:bold;'>" . htmlspecialchars($row['statut']) . "</td>";
                        
                        // BOUTONS D'ACTION POUR L'ADMIN
                        echo "<td>";
                        
                        // 1. Actions rapides de statut (uniquement si en attente)
                        if ($row['statut'] === 'En attente') {
                            echo "<a href='" . csrf_url("/pages/admin.php?action=confirmer&id=" . $row['id']) . "' class='btn' style='background-color:#2ecc71; padding:5px 10px; font-size:0.8rem; margin-right:5px;'>✔ Confirmer</a>";
                            echo "<a href='" . csrf_url("/pages/admin.php?action=annuler&id=" . $row['id']) . "' class='btn' style='background-color:#e74c3c; padding:5px 10px; font-size:0.8rem; margin-right:5px;' onclick=\"return confirm('Annuler cette réservation ?');\">❌ Annuler</a>";
                        }

                        // 2. Bouton Modifier
                        echo "<a href='/pages/modifier_reservation_admin.php?id=" . $row['id'] . "' class='btn' style='background-color:#3498db; padding:5px 10px; font-size:0.8rem; margin-right:5px;'>✏️ Modifier</a>";

                        // 3. Bouton Supprimer définitivement
                        echo "<a href='" . csrf_url("/pages/admin.php?delete_reservation=" . $row['id']) . "' class='btn' style='background-color:#95a5a6; padding:5px 10px; font-size:0.8rem;' onclick=\"return confirm('Supprimer définitivement cette réservation de la base de données ?');\">🗑️ Supprimer</a>";
                        
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' style='text-align:center;'>Aucune réservation enregistrée.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="form-box" style="max-width: 600px; margin-top: 40px;">
        <h3>🍔 Ajouter un produit à la carte</h3>
        <br>
        
        <?php
        // Traitement de l'ajout du produit
        if (isset($_POST['ajouter_produit'])) {
            csrf_verify();

            $nom_plat = mysqli_real_escape_string($conn, $_POST['nom_plat']);
            $description = mysqli_real_escape_string($conn, $_POST['description']);
            $prix = floatval($_POST['prix']);
            $categorie = mysqli_real_escape_string($conn, $_POST['categorie']);

            $resultat_image = handle_image_upload('image_file');

            if (is_array($resultat_image)) {
                echo "<p style='color:red;'>Erreur : " . htmlspecialchars($resultat_image['erreur']) . "</p><br>";
            } elseif (!$resultat_image) {
                echo "<p style='color:red;'>Veuillez sélectionner une image pour le produit.</p><br>";
            } else {
                $image_url = mysqli_real_escape_string($conn, $resultat_image);

                $insert_prod = "INSERT INTO produits (nom, description, prix, categorie, image_url)
                                VALUES ('$nom_plat', '$description', $prix, '$categorie', '$image_url')";

                if (mysqli_query($conn, $insert_prod)) {
                    echo "<p style='color:green; font-weight:bold; text-align:center;'>Le produit a bien été ajouté ! ✨</p><br>";
                } else {
                    echo "<p style='color:red;'>Erreur : " . mysqli_error($conn) . "</p><br>";
                }
            }
        }
        ?>

        <form method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <div class="form-group">
                <label>Nom du plat / boisson</label>
                <input type="text" name="nom_plat" required placeholder="Ex: Sandwich Kebab">
            </div>
            <div class="form-group">
                <label>Description (Ingrédients)</label>
                <input type="text" name="description" required placeholder="Ex: Salade, tomates, oignons, sauce blanche">
            </div>
            <div class="form-group">
                <label>Prix (€)</label>
                <input type="number" step="0.01" name="prix" required placeholder="Ex: 7.50">
            </div>
            <div class="form-group">
                <label>Catégorie (Dossier Parent)</label>
                <select name="categorie" style="width:100%; padding:12px; border-radius:8px; border:1px solid #ddd; background:#f9f9f9; margin-bottom:15px;">
                    <option value="Tacos">🌮 Tacos Maison</option>
                    <option value="Sandwichs">🥖 Sandwichs & Paninis</option>
                    <option value="Burgers">🍔 Burgers</option>
                    <option value="Assiettes">🍽️ Assiettes</option>
                    <option value="Barquettes">🍟 Barquettes & Accompagnements</option>
                    <option value="Boissons">🥤 Boissons</option>
                    <option value="Desserts">🍰 Desserts</option>
                    <option value="Kids">👶 Menu Kids</option>
                </select>
            </div>
            <div class="form-group">
                <label>Image du produit</label>
                <input type="file" name="image_file" accept="image/jpeg,image/png,image/gif,image/webp" required>
            </div>
            <button type="submit" name="ajouter_produit" class="btn" style="width:100%;">Ajouter le produit</button>
        </form>
    </div>

    <div class="form-box" style="max-width: 100%; margin-top: 40px;">
        <h3>🗑️ Gérer la carte (Supprimer un produit)</h3>
        <br>

        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'supprime'): ?>
            <p style="color: green; font-weight: bold; text-align: center; margin-bottom: 15px;">Le produit a bien été retiré de la carte ! ❌</p>
        <?php endif; ?>

        <div style="text-align: center; margin-bottom: 20px;">
            <input type="text" id="admin-product-search" class="search-bar" placeholder="🔍 Rechercher un produit (nom, catégorie, prix...)">
            <p id="admin-product-search-count" style="margin-top: 8px; color: var(--text-dark); font-size: 0.9rem;"></p>
        </div>

        <table class="cart-table" id="admin-products-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Nom</th>
                    <th>Catégorie</th>
                    <th>Prix</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Récupère tous les produits de la base de données
                $prod_query = "SELECT * FROM produits ORDER BY categorie DESC, nom ASC";
                $prod_result = mysqli_query($conn, $prod_query);

                if (mysqli_num_rows($prod_result) > 0) {
                    while ($prod = mysqli_fetch_assoc($prod_result)) {
                        $search_data = htmlspecialchars(
                            $prod['nom'] . ' ' . $prod['categorie'] . ' ' . $prod['description'] . ' ' . number_format($prod['prix'], 2)
                        );
                        echo "<tr data-search='" . $search_data . "'>";
                        echo "<td><img src='/images/" . htmlspecialchars($prod['image_url']) . "' style='width:50px; height:50px; object-fit:cover; border-radius:5px;'></td>";
                        echo "<td><strong>" . htmlspecialchars($prod['nom']) . "</strong></td>";
                        echo "<td>" . htmlspecialchars($prod['categorie']) . "</td>";
                        echo "<td>" . number_format($prod['prix'], 2) . " €</td>";

                        echo "<td>";
                        // Bouton Modifier
                        echo "<a href='/pages/modifier_produit.php?id=" . $prod['id'] . "' class='btn' style='background-color:#3498db; padding:5px 10px; font-size:0.8rem; margin-right:5px;'>Modifier</a>";

                        // Bouton Retirer
                        echo "<a href='" . csrf_url("/pages/admin.php?delete_product=" . $prod['id']) . "' class='btn' style='background-color:#e74c3c; padding:5px 10px; font-size:0.8rem;' onclick=\"return confirm('Voulez-vous vraiment retirer " . addslashes($prod['nom']) . " de la carte ?');\">Retirer</a>";
                        echo "</td>";

                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' style='text-align:center;'>Aucun produit sur la carte actuellement.</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <p id="admin-product-no-results" style="display:none; text-align:center; margin-top:15px; color: var(--text-dark);">Aucun produit ne correspond à votre recherche.</p>
    </div>

</div>

<script src="/js/script.js"></script>
</body>
</html>