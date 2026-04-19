<?php 
// 1. Inclusion de la base de données et du header
include($_SERVER['DOCUMENT_ROOT'] . '/RestoEtoile/db.php');
include($_SERVER['DOCUMENT_ROOT'] . '/RestoEtoile/header.php'); 
?>

<div class="container">
    <div style="text-align: center; margin-bottom: 2rem;">
        <input type="text" id="search-input" class="search-bar" placeholder="🍔 Rechercher un plat, un ingrédient...">
    </div>

    <h2 class="category-title">Notre Carte Digitale</h2>
    
    <div class="products-grid">
        <?php
        // 2. On récupère les produits depuis la base de données
        $query = "SELECT * FROM produits ORDER BY categorie DESC, prix ASC";
        $result = mysqli_query($conn, $query);

        // 3. Boucle pour afficher chaque produit
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                ?>
                <div class="product-card">
                    <img src="/RestoEtoile/images/<?php echo $row['image_url']; ?>" 
                        alt="<?php echo htmlspecialchars($row['nom']); ?>" 
                        class="product-img">
                    
                    <div class="product-info">
                        <h3><?php echo htmlspecialchars($row['nom']); ?></h3>
                        <p><?php echo htmlspecialchars($row['description']); ?></p>
                        <div class="price-tag"><?php echo number_format($row['prix'], 2); ?>€</div>
                        
                        <button class="btn add-to-cart" 
                                data-id="<?php echo $row['id']; ?>" 
                                data-name="<?php echo htmlspecialchars($row['nom']); ?>" 
                                data-price="<?php echo $row['prix']; ?>">
                            Ajouter
                        </button>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<p style='text-align:center;'>Aucun produit trouvé dans la carte.</p>";
        }
        ?>
    </div>
</div>

<script src="/RestoEtoile/js/script.js"></script>
</body>
</html>