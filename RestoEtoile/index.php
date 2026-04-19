
<?php include($_SERVER['DOCUMENT_ROOT'] . '/RestoEtoile/db.php'); ?>
<?php 
// Remplace ton ancien include par celui-ci :
include($_SERVER['DOCUMENT_ROOT'] . '/RestoEtoile/header.php'); ?>




    <header class="hero">
        <div class="container">
            <h1>RESTO ETOILE</h1>
            <p>Fait Maison & Spécialités Turques - Halal</p>
            <br>
            <a href="/RestoEtoile/pages/produits.php" class="btn">VOIR LE MENU</a>

            <div class="infos-resto">
                <p>📍 14 place du Quatorze Juillet - 78260 ACHERES</p>
                <p>📞 09 53 11 80 63</p>
                <p>🕒 Ouvert du Lundi au Samedi : 11h - 22h</p>
            </div>
        </div>
        <section class="container" style="margin-top: 50px;">
            <h2 style="color:var(--brand-red); text-align:center;">Dernières Actualités</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-top: 20px;">
                <?php
                $actus = mysqli_query($conn, "SELECT * FROM actualites ORDER BY date_publication DESC LIMIT 3");
                while($a = mysqli_fetch_assoc($actus)) {
                    echo "<div class='form-box' style='margin:0; max-width:100%;'>";
                    echo "<h3>" . htmlspecialchars($a['titre']) . "</h3>";
                    echo "<p style='font-size:0.9rem; color:gray;'>Publié le " . date('d/m/Y', strtotime($a['date_publication'])) . "</p>";
                    echo "<p style='margin-top:10px;'>" . htmlspecialchars($a['contenu']) . "</p>";
                    echo "</div>";
                }
                ?>
            </div>
        </section>
    </header>

    <script src="js/script.js"></script>
</body>
</html>