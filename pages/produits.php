<?php 
include($_SERVER['DOCUMENT_ROOT'] . '/db.php');
include($_SERVER['DOCUMENT_ROOT'] . '/header.php'); 
?>

<div class="container">
    <div style="text-align: center; margin-bottom: 2rem;">
        <input type="text" id="search-input" class="search-bar" placeholder="🍔 Rechercher un plat, un ingrédient...">
    </div>

    <h2 class="category-title">Notre Carte Digitale</h2>
    
    <div class="categories-nav" style="display: flex; justify-content: center; gap: 10px; margin-bottom: 30px; flex-wrap: wrap;">
        <button class="btn-category active" onclick="filtrerCategorie('tous', this)" style="padding: 10px 18px; border-radius: 25px; border: 2px solid #e74c3c; background: #e74c3c; color: white; font-weight: bold; cursor: pointer; transition: 0.3s;">
            ✨ Tout voir
        </button>
        <button class="btn-category" onclick="filtrerCategorie('tacos', this)" style="padding: 10px 18px; border-radius: 25px; border: 2px solid #ddd; background: white; font-weight: bold; cursor: pointer; transition: 0.3s;">
            🌮 Tacos
        </button>
        <button class="btn-category" onclick="filtrerCategorie('sandwich', this)" style="padding: 10px 18px; border-radius: 25px; border: 2px solid #ddd; background: white; font-weight: bold; cursor: pointer; transition: 0.3s;">
            🥖 Sandwichs & Paninis
        </button>
        <button class="btn-category" onclick="filtrerCategorie('burger', this)" style="padding: 10px 18px; border-radius: 25px; border: 2px solid #ddd; background: white; font-weight: bold; cursor: pointer; transition: 0.3s;">
            🍔 Burgers
        </button>
        <button class="btn-category" onclick="filtrerCategorie('assiette', this)" style="padding: 10px 18px; border-radius: 25px; border: 2px solid #ddd; background: white; font-weight: bold; cursor: pointer; transition: 0.3s;">
            🍽️ Assiettes
        </button>
        <button class="btn-category" onclick="filtrerCategorie('barquette', this)" style="padding: 10px 18px; border-radius: 25px; border: 2px solid #ddd; background: white; font-weight: bold; cursor: pointer; transition: 0.3s;">
            🍟 Barquettes & Accompagnements
        </button>
        <button class="btn-category" onclick="filtrerCategorie('boisson-dessert', this)" style="padding: 10px 18px; border-radius: 25px; border: 2px solid #ddd; background: white; font-weight: bold; cursor: pointer; transition: 0.3s;">
            🥤 Desserts & Boissons
        </button>
        <button class="btn-category" onclick="filtrerCategorie('kids', this)" style="padding: 10px 18px; border-radius: 25px; border: 2px solid #ddd; background: white; font-weight: bold; cursor: pointer; transition: 0.3s;">
            👶 Menu Kids
        </button>
    </div>

    <div class="products-grid">
        <?php
        $query = "SELECT * FROM produits ORDER BY categorie DESC, prix ASC";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $nom_lowercase = mb_strtolower($row['nom'], 'UTF-8');
                $is_tacos = (strpos($nom_lowercase, 'tacos') !== false || strpos($nom_lowercase, 'grill') !== false);

                $categorie_bd = isset($row['categorie']) ? mb_strtolower($row['categorie'], 'UTF-8') : '';

                // catégorie BD prioritaire, mots-clés du nom en secours seulement
                // (sinon "Panini Nutella" finissait classé "sandwich" à cause de "panini")
                $categorie_produit = 'autre';

                if (strpos($categorie_bd, 'tacos') !== false) {
                    $categorie_produit = 'tacos';
                } elseif (strpos($categorie_bd, 'assiette') !== false) {
                    $categorie_produit = 'assiette';
                } elseif (strpos($categorie_bd, 'burger') !== false) {
                    $categorie_produit = 'burger';
                } elseif (strpos($categorie_bd, 'sandwich') !== false) {
                    $categorie_produit = 'sandwich';
                } elseif (strpos($categorie_bd, 'barquette') !== false || strpos($categorie_bd, 'accompagnement') !== false) {
                    $categorie_produit = 'barquette';
                } elseif (strpos($categorie_bd, 'boisson') !== false || strpos($categorie_bd, 'dessert') !== false) {
                    $categorie_produit = 'boisson-dessert';
                } elseif (strpos($categorie_bd, 'kids') !== false) {
                    $categorie_produit = 'kids';
                } elseif ($is_tacos) {
                    $categorie_produit = 'tacos';
                } elseif (strpos($nom_lowercase, 'assiette') !== false) {
                    $categorie_produit = 'assiette';
                } elseif (strpos($nom_lowercase, 'burger') !== false || strpos($nom_lowercase, 'duo') !== false || strpos($nom_lowercase, 'cheese') !== false) {
                    $categorie_produit = 'burger';
                } elseif (strpos($nom_lowercase, 'sandwich') !== false || strpos($nom_lowercase, 'panini') !== false || strpos($nom_lowercase, 'kebab') !== false) {
                    $categorie_produit = 'sandwich';
                } elseif (strpos($nom_lowercase, 'barquette') !== false || strpos($nom_lowercase, 'frites') !== false || strpos($nom_lowercase, 'blé') !== false) {
                    $categorie_produit = 'barquette';
                } elseif (strpos($nom_lowercase, 'boisson') !== false || strpos($nom_lowercase, 'tiramisu') !== false || strpos($nom_lowercase, 'coca') !== false) {
                    $categorie_produit = 'boisson-dessert';
                } elseif (strpos($nom_lowercase, 'kids') !== false || strpos($nom_lowercase, 'enfant') !== false) {
                    $categorie_produit = 'kids';
                }

                // barquettes frites + kids nuggets : juste le choix de la sauce
                $besoin_sauce_seule = ($categorie_produit === 'barquette' && strpos($nom_lowercase, 'frites') !== false)
                    || ($categorie_produit === 'kids' && strpos($nom_lowercase, 'nuggets') !== false);

                // kids cheese : sauce + crudités, pas de menu (boisson déjà incluse)
                $is_kids_cheese = ($categorie_produit === 'kids' && strpos($nom_lowercase, 'cheese') !== false);
                ?>
                <div class="product-card" data-category="<?php echo $categorie_produit; ?>">
                    <img src="/images/<?php echo $row['image_url']; ?>" 
                         alt="<?php echo htmlspecialchars($row['nom']); ?>" 
                         class="product-img">
                    
                    <div class="product-info">
                        <h3><?php echo htmlspecialchars($row['nom']); ?></h3>
                        <p><?php echo htmlspecialchars($row['description']); ?></p>
                        <div class="price-tag"><?php echo number_format($row['prix'], 2); ?>€</div>
                        
                        <?php if ($is_tacos): ?>
                            <button class="btn" data-id="<?php echo $row['id']; ?>" data-price="<?php echo $row['prix']; ?>"
                                    onclick="ouvrirModalTacos(<?php echo $row['id']; ?>, '<?php echo addslashes(htmlspecialchars($row['nom'])); ?>', <?php echo $row['prix']; ?>)">
                                Personnaliser 🌮
                            </button>
                        <?php elseif ($categorie_produit === 'sandwich'): ?>
                            <button class="btn" data-id="<?php echo $row['id']; ?>" data-price="<?php echo $row['prix']; ?>"
                                    onclick="ouvrirModalSandwich(<?php echo $row['id']; ?>, '<?php echo addslashes(htmlspecialchars($row['nom'])); ?>', <?php echo $row['prix']; ?>)">
                                Personnaliser 🥖
                            </button>
                        <?php elseif ($categorie_produit === 'burger'): ?>
                            <button class="btn" data-id="<?php echo $row['id']; ?>" data-price="<?php echo $row['prix']; ?>"
                                    onclick="ouvrirModalBurger(<?php echo $row['id']; ?>, '<?php echo addslashes(htmlspecialchars($row['nom'])); ?>', <?php echo $row['prix']; ?>)">
                                Personnaliser 🍔
                            </button>
                        <?php elseif ($categorie_produit === 'assiette'): ?>
                            <button class="btn" data-id="<?php echo $row['id']; ?>" data-price="<?php echo $row['prix']; ?>"
                                    onclick="ouvrirModalAssiette(<?php echo $row['id']; ?>, '<?php echo addslashes(htmlspecialchars($row['nom'])); ?>', <?php echo $row['prix']; ?>)">
                                Personnaliser 🍽️
                            </button>
                        <?php elseif ($is_kids_cheese): ?>
                            <button class="btn" data-id="<?php echo $row['id']; ?>" data-price="<?php echo $row['prix']; ?>"
                                    onclick="ouvrirModalKidsCheese(<?php echo $row['id']; ?>, '<?php echo addslashes(htmlspecialchars($row['nom'])); ?>', <?php echo $row['prix']; ?>)">
                                Personnaliser 🧒
                            </button>
                        <?php elseif ($besoin_sauce_seule): ?>
                            <button class="btn" data-id="<?php echo $row['id']; ?>" data-price="<?php echo $row['prix']; ?>"
                                    onclick="ouvrirModalSauce(<?php echo $row['id']; ?>, '<?php echo addslashes(htmlspecialchars($row['nom'])); ?>', <?php echo $row['prix']; ?>)">
                                Choisir la sauce 🥫
                            </button>
                        <?php else: ?>
                            <button class="btn add-to-cart" 
                                    data-id="<?php echo $row['id']; ?>" 
                                    data-name="<?php echo htmlspecialchars($row['nom']); ?>" 
                                    data-price="<?php echo $row['prix']; ?>">
                                Ajouter
                            </button>
                        <?php endif; ?>
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

<?php
// boissons dispo pour l'option "Menu" dans les popups
$boissons_options = '';
$boissons_query = "SELECT nom FROM produits WHERE categorie = 'Boissons' ORDER BY nom ASC";
$boissons_result = mysqli_query($conn, $boissons_query);
if ($boissons_result) {
    while ($b = mysqli_fetch_assoc($boissons_result)) {
        $boissons_options .= '<option value="' . htmlspecialchars($b['nom']) . '">' . htmlspecialchars($b['nom']) . '</option>';
    }
}

function render_supplements_block($prefix, $include_viande = true) {
    $items = [
        'Cheddar' => 0.50,
        'Œuf' => 0.50,
        'Olives' => 0.50,
        'Chèvre' => 1.00,
        'Piment' => 0.50,
        'Boursin' => 1.00,
    ];
    if ($include_viande) {
        $items['Supplément Viande'] = 2.00;
    }
    $html = '<div class="form-group" style="margin-bottom: 15px;">';
    $html .= '<label style="font-weight:bold; display:block; margin-bottom:10px;">➕ Suppléments :</label>';
    $html .= '<div class="supplements-grid" style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">';
    foreach ($items as $nom => $prix) {
        $prix_str = number_format($prix, 2);
        $html .= '<label><input type="checkbox" name="supplements[]" value="' . htmlspecialchars($nom) . '"> ' . htmlspecialchars($nom) . ' (+' . $prix_str . '€)</label>';
    }
    $html .= '</div></div>';
    return $html;
}

function render_menu_block($prefix, $boissons_options) {
    $html = '<div class="form-group" style="margin-bottom: 15px;">';
    $html .= '<label style="font-weight:bold; display:flex; align-items:center; gap:8px; cursor:pointer;">';
    $html .= '<input type="checkbox" name="menu_actif" id="menu_actif_' . $prefix . '" onchange="toggleMenuBoisson(\'' . $prefix . '\')"> 🍟🥤 En faire un Menu (+1,00€) avec une boisson';
    $html .= '</label>';
    $html .= '<select name="menu_boisson" id="menu_boisson_' . $prefix . '" style="display:none; width:100%; padding:10px; border-radius:8px; border:1px solid #ddd; margin-top:10px;">';
    $html .= $boissons_options;
    $html .= '</select>';
    $html .= '</div>';
    return $html;
}
?>

<div id="modal-tacos" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:1000; justify-content:center; align-items:center;">
    <div class="modal-content" style="background:#fff; padding:30px; border-radius:15px; max-width:500px; width:90%; max-height:90vh; overflow-y:auto; box-shadow: 0 4px 20px rgba(0,0,0,0.2);">
        <h3 style="color:var(--brand-red); margin-bottom:5px;">🌮 Personnalise ton Tacos</h3>
        <p style="font-size:0.9rem; color:gray; margin-bottom:20px;">(Frites et sauce fromagère incluses à l'intérieur de base)</p>

        <form id="form-tacos">
            <div class="form-group">
                <label style="font-weight:bold; display:block; margin-bottom:10px;">Choisissez vos viandes (<span id="nb-viandes-requis">1</span> max) :</label>
                <div class="viandes-grid" style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
                    <label><input type="checkbox" name="viandes[]" value="Kebab"> Kebab</label>
                    <label><input type="checkbox" name="viandes[]" value="Escalope"> Escalope</label>
                    <label><input type="checkbox" name="viandes[]" value="Chicken Paprika"> Chicken Paprika</label>
                    <label><input type="checkbox" name="viandes[]" value="Chicken Curry"> Chicken Curry</label>
                    <label><input type="checkbox" name="viandes[]" value="Merguez"> Merguez</label>
                    <label><input type="checkbox" name="viandes[]" value="Steak Haché"> Steak Haché</label>
                    <label><input type="checkbox" name="viandes[]" value="Adana"> Adana</label>
                    <label><input type="checkbox" name="viandes[]" value="Kofte"> Kofte</label>
                    <label><input type="checkbox" name="viandes[]" value="Nugget"> Nugget</label>
                    <label><input type="checkbox" name="viandes[]" value="Cordon Bleu"> Cordon Bleu</label>
                </div>
            </div>

            <hr style="margin:20px 0; border:0; border-top:1px solid #eee;">

            <div class="form-group">
                <label style="font-weight:bold; display:block; margin-bottom:10px;">🍟 Voulez-vous des frites à côté ?</label>
                <select name="frites_cote" id="frites_cote" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ddd;" onchange="toggleSauceFrites()">
                    <option value="Non">Non, sans frites à côté</option>
                    <option value="Oui">Oui, avec frites à côté</option>
                </select>
            </div>

            <div id="sec-sauce-frites" class="form-group" style="display:none; margin-top:15px;">
                <label style="font-weight:bold; display:block; margin-bottom:10px;">🥫 Choix de la sauce pour vos frites :</label>
                <select name="sauce_frites" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ddd;">
                    <option value="Mayo">Mayonnaise</option>
                    <option value="Ketchup">Ketchup</option>
                    <option value="Algérienne">Algérienne</option>
                    <option value="Algérienne">Biggy</option>
                    <option value="Algérienne">Moutarde</option>
                    <option value="Algérienne">Andalouse</option>
                    <option value="Algérienne">Barbecue</option>
                    <option value="Samouraï">Samouraï</option>
                    <option value="Harissa">Harissa</option>
                    <option value="Sauce Blanche">Sauce Blanche</option>
                </select>
            </div>

            <hr style="margin:20px 0; border:0; border-top:1px solid #eee;">

            <?php echo render_supplements_block('tacos', true); ?>
            <?php echo render_menu_block('tacos', $boissons_options); ?>

            <br>
            <button type="submit" class="btn" style="width:100%;">Ajouter au panier 🛒</button>
            <button type="button" class="btn" style="width:100%; background:#7f8c8d; margin-top:10px;" onclick="fermerModal()">Annuler</button>
        </form>
    </div>
</div>

<div id="modal-sandwich" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:1000; justify-content:center; align-items:center;">
    <div class="modal-content" style="background:#fff; padding:30px; border-radius:15px; max-width:500px; width:90%; max-height:90vh; overflow-y:auto; box-shadow: 0 4px 20px rgba(0,0,0,0.2);">
        <h3 id="sandwich-title" style="color:var(--brand-red); margin-bottom:5px;">🥖 Personnalise ton Sandwich</h3>
        <p style="font-size:0.9rem; color:gray; margin-bottom:20px;">(Servi avec frites à l'intérieur ou à côté)</p>

        <form id="form-sandwich">
            <div class="form-group" style="margin-bottom: 15px;">
                <label style="font-weight:bold; display:block; margin-bottom:10px;">🥖 Choix du Pain :</label>
                <select name="pain_choix" id="pain_choix" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ddd;">
                    <option value="Classique">Pain Classique (Baguette/Rond)</option>
                    <option value="Tortilla">Tortilla (Galette/Wrap)</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label style="font-weight:bold; display:block; margin-bottom:10px;">🥫 Choix des sauces (2 max) :</label>
                <div class="sauces-grid" style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
                    <label><input type="checkbox" name="sauces_sandwich[]" value="Algérienne"> Algérienne</label>
                    <label><input type="checkbox" name="sauces_sandwich[]" value="Samouraï"> Samouraï</label>
                    <label><input type="checkbox" name="sauces_sandwich[]" value="Blanche"> Blanche</label>
                    <label><input type="checkbox" name="sauces_sandwich[]" value="Mayo"> Mayonnaise</label>
                    <label><input type="checkbox" name="sauces_sandwich[]" value="Ketchup"> Ketchup</label>
                    <label><input type="checkbox" name="sauces_sandwich[]" value="Harissa"> Harissa</label>
                    <label><input type="checkbox" name="sauces_sandwich[]" value="Andalouse"> Andalouse</label>
                    <label><input type="checkbox" name="sauces_sandwich[]" value="Curry"> Curry</label>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label style="font-weight:bold; display:block; margin-bottom:10px;">🥗 Crudités (Salade / Tomate / Oignon) :</label>
                <div style="display:flex; gap:15px;">
                    <label><input type="checkbox" name="crudites[]" value="Salade" checked> Salade</label>
                    <label><input type="checkbox" name="crudites[]" value="Tomate" checked> Tomate</label>
                    <label><input type="checkbox" name="crudites[]" value="Oignon" checked> Oignon</label>
                </div>
            </div>

            <?php echo render_supplements_block('sandwich', true); ?>
            <?php echo render_menu_block('sandwich', $boissons_options); ?>

            <br>
            <button type="submit" class="btn" style="width:100%;">Ajouter au panier 🛒</button>
            <button type="button" class="btn" style="width:100%; background:#7f8c8d; margin-top:10px;" onclick="fermerModalSandwich()">Annuler</button>
        </form>
    </div>
</div>

<div id="modal-burger" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:1000; justify-content:center; align-items:center;">
    <div class="modal-content" style="background:#fff; padding:30px; border-radius:15px; max-width:500px; width:90%; max-height:90vh; overflow-y:auto; box-shadow: 0 4px 20px rgba(0,0,0,0.2);">
        <h3 id="burger-title" style="color:var(--brand-red); margin-bottom:5px;">🍔 Personnalise ton Burger</h3>
        <p style="font-size:0.9rem; color:gray; margin-bottom:20px;">(Sans suppléments, le prix ne change pas)</p>

        <form id="form-burger">
            <div class="form-group" style="margin-bottom: 15px;">
                <label style="font-weight:bold; display:block; margin-bottom:10px;">🥫 Choix de la sauce :</label>
                <select name="sauce_burger" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ddd;">
                    <option value="Algérienne">Algérienne</option>
                    <option value="Samouraï">Samouraï</option>
                    <option value="Blanche">Blanche</option>
                    <option value="Mayo">Mayonnaise</option>
                    <option value="Ketchup">Ketchup</option>
                    <option value="Harissa">Harissa</option>
                    <option value="Andalouse">Andalouse</option>
                    <option value="Curry">Curry</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label style="font-weight:bold; display:block; margin-bottom:10px;">🥗 Crudités (Salade / Tomate / Oignon) :</label>
                <div style="display:flex; gap:15px;">
                    <label><input type="checkbox" name="crudites_burger[]" value="Salade" checked> Salade</label>
                    <label><input type="checkbox" name="crudites_burger[]" value="Tomate" checked> Tomate</label>
                    <label><input type="checkbox" name="crudites_burger[]" value="Oignon" checked> Oignon</label>
                </div>
            </div>

            <?php echo render_supplements_block('burger', false); ?>
            <?php echo render_menu_block('burger', $boissons_options); ?>

            <br>
            <button type="submit" class="btn" style="width:100%;">Ajouter au panier 🛒</button>
            <button type="button" class="btn" style="width:100%; background:#7f8c8d; margin-top:10px;" onclick="fermerModalBurger()">Annuler</button>
        </form>
    </div>
</div>

<div id="modal-assiette" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:1000; justify-content:center; align-items:center;">
    <div class="modal-content" style="background:#fff; padding:30px; border-radius:15px; max-width:500px; width:90%; max-height:90vh; overflow-y:auto; box-shadow: 0 4px 20px rgba(0,0,0,0.2);">
        <h3 id="assiette-title" style="color:var(--brand-red); margin-bottom:5px;">🍽️ Personnalise ton Assiette</h3>
        <p style="font-size:0.9rem; color:gray; margin-bottom:20px;">(Le blé, la salade et les frites n'affectent pas le prix)</p>

        <form id="form-assiette">
            <div class="form-group" style="margin-bottom: 15px;">
                <label style="font-weight:bold; display:block; margin-bottom:10px;">🍚 Accompagnements :</label>
                <div style="display:flex; flex-direction:column; gap:10px;">
                    <label><input type="checkbox" name="ble_assiette" checked> Avec blé</label>
                    <label><input type="checkbox" name="salade_assiette" checked> Avec salade</label>
                    <label><input type="checkbox" name="frites_assiette" checked> Avec frites</label>
                </div>
            </div>

            <?php echo render_supplements_block('assiette', true); ?>
            <?php echo render_menu_block('assiette', $boissons_options); ?>

            <br>
            <button type="submit" class="btn" style="width:100%;">Ajouter au panier 🛒</button>
            <button type="button" class="btn" style="width:100%; background:#7f8c8d; margin-top:10px;" onclick="fermerModalAssiette()">Annuler</button>
        </form>
    </div>
</div>

<div id="modal-sauce" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:1000; justify-content:center; align-items:center;">
    <div class="modal-content" style="background:#fff; padding:30px; border-radius:15px; max-width:500px; width:90%; max-height:90vh; overflow-y:auto; box-shadow: 0 4px 20px rgba(0,0,0,0.2);">
        <h3 id="sauce-title" style="color:var(--brand-red); margin-bottom:5px;">🥫 Choisis ta sauce</h3>

        <form id="form-sauce">
            <div class="form-group" style="margin-bottom: 15px;">
                <label style="font-weight:bold; display:block; margin-bottom:10px;">Choix de la sauce :</label>
                <select name="sauce_simple" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ddd;">
                    <option value="Algérienne">Algérienne</option>
                    <option value="Samouraï">Samouraï</option>
                    <option value="Blanche">Blanche</option>
                    <option value="Mayo">Mayonnaise</option>
                    <option value="Ketchup">Ketchup</option>
                    <option value="Harissa">Harissa</option>
                    <option value="Andalouse">Andalouse</option>
                    <option value="Curry">Curry</option>
                </select>
            </div>

            <br>
            <button type="submit" class="btn" style="width:100%;">Ajouter au panier 🛒</button>
            <button type="button" class="btn" style="width:100%; background:#7f8c8d; margin-top:10px;" onclick="fermerModalSauce()">Annuler</button>
        </form>
    </div>
</div>

<div id="modal-kids-cheese" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:1000; justify-content:center; align-items:center;">
    <div class="modal-content" style="background:#fff; padding:30px; border-radius:15px; max-width:500px; width:90%; max-height:90vh; overflow-y:auto; box-shadow: 0 4px 20px rgba(0,0,0,0.2);">
        <h3 id="kids-cheese-title" style="color:var(--brand-red); margin-bottom:5px;">🧒 Personnalise ton Menu Kids</h3>
        <p style="font-size:0.9rem; color:gray; margin-bottom:20px;">(Frites et Capri Sun déjà inclus dans le prix)</p>

        <form id="form-kids-cheese">
            <div class="form-group" style="margin-bottom: 15px;">
                <label style="font-weight:bold; display:block; margin-bottom:10px;">🥫 Choix de la sauce :</label>
                <select name="sauce_kids" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ddd;">
                    <option value="Algérienne">Algérienne</option>
                    <option value="Samouraï">Samouraï</option>
                    <option value="Blanche">Blanche</option>
                    <option value="Mayo">Mayonnaise</option>
                    <option value="Ketchup">Ketchup</option>
                    <option value="Harissa">Harissa</option>
                    <option value="Andalouse">Andalouse</option>
                    <option value="Curry">Curry</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label style="font-weight:bold; display:block; margin-bottom:10px;">🥗 Crudités (Salade / Tomate / Oignon) :</label>
                <div style="display:flex; gap:15px;">
                    <label><input type="checkbox" name="crudites_kids[]" value="Salade" checked> Salade</label>
                    <label><input type="checkbox" name="crudites_kids[]" value="Tomate" checked> Tomate</label>
                    <label><input type="checkbox" name="crudites_kids[]" value="Oignon" checked> Oignon</label>
                </div>
            </div>

            <br>
            <button type="submit" class="btn" style="width:100%;">Ajouter au panier 🛒</button>
            <button type="button" class="btn" style="width:100%; background:#7f8c8d; margin-top:10px;" onclick="fermerModalKidsCheese()">Annuler</button>
        </form>
    </div>
</div>

<script>
let produitEnCours = { id: null, nom: "", prix: 0 };
let maxViandesPermis = 1;
let sandwichEnCours = { id: null, nom: "", prix: 0 };
let burgerEnCours = { id: null, nom: "", prix: 0 };
let assietteEnCours = { id: null, nom: "", prix: 0 };
let sauceEnCours = { id: null, nom: "", prix: 0 };
let kidsCheeseEnCours = { id: null, nom: "", prix: 0 };

// si actif, on modifie un article du panier au lieu d'en ajouter un
let editEnCours = { actif: false, cartId: null };

// partagé par les 4 popups de personnalisation
const SUPPLEMENTS_PRIX = {
    "Cheddar": 0.50,
    "Œuf": 0.50,
    "Olives": 0.50,
    "Chèvre": 1.00,
    "Piment": 0.50,
    "Boursin": 1.00,
    "Supplément Viande": 2.00
};
const PRIX_MENU = 1.00;

function toggleMenuBoisson(prefix) {
    const checkbox = document.getElementById('menu_actif_' + prefix);
    const select = document.getElementById('menu_boisson_' + prefix);
    select.style.display = checkbox.checked ? 'block' : 'none';
}

// form doit contenir "supplements[]" (présent dans les 4 popups)
function getSupplementsEtMenu(form) {
    const supplements = Array.from(form.querySelectorAll('input[name="supplements[]"]:checked')).map(cb => cb.value);
    const supplementsTotal = supplements.reduce((sum, s) => sum + (SUPPLEMENTS_PRIX[s] || 0), 0);
    const menuCheckbox = form.querySelector('input[name="menu_actif"]');
    const menuActif = menuCheckbox ? menuCheckbox.checked : false;
    const menuBoisson = menuActif ? form.querySelector('select[name="menu_boisson"]').value : null;
    const menuTotal = menuActif ? PRIX_MENU : 0;
    return { supplements, supplementsTotal, menuActif, menuBoisson, menuTotal };
}

function resetSupplementsEtMenu(form) {
    form.querySelectorAll('input[name="supplements[]"]').forEach(cb => cb.checked = false);
    const menuCheckbox = form.querySelector('input[name="menu_actif"]');
    const menuSelect = form.querySelector('select[name="menu_boisson"]');
    if (menuCheckbox) menuCheckbox.checked = false;
    if (menuSelect) menuSelect.style.display = 'none';
}

// pré-coche les choix déjà faits, pour modifier un article du panier
function preRemplirSupplementsEtMenu(form, options) {
    if (options.supplements) {
        options.supplements.forEach(nomSupplement => {
            const cb = form.querySelector('input[name="supplements[]"][value="' + nomSupplement + '"]');
            if (cb) cb.checked = true;
        });
    }
    if (options.menu) {
        const prefix = form.id.replace('form-', '');
        const menuCheckbox = document.getElementById('menu_actif_' + prefix);
        const menuSelect = document.getElementById('menu_boisson_' + prefix);
        if (menuCheckbox && menuSelect) {
            menuCheckbox.checked = true;
            menuSelect.style.display = 'block';
            menuSelect.value = options.menu;
        }
    }
}

// ?edit=<cartId> dans l'URL -> ouvre la popup pré-remplie avec l'article du panier
function chargerArticlePourModification() {
    const params = new URLSearchParams(window.location.search);
    const cartId = params.get('edit');
    if (!cartId) return;

    const panier = JSON.parse(localStorage.getItem('restoCart')) || [];
    const item = panier.find(i => i.cartId === cartId);
    if (!item || !item.options || !item.options.type) return;

    const bouton = document.querySelector('button[data-id="' + item.id + '"][data-price]');
    const prixBase = bouton ? parseFloat(bouton.dataset.price) : item.price;

    if (item.options.type === 'tacos') {
        ouvrirModalTacos(item.id, item.name, prixBase);
        item.options.viandes.forEach(v => {
            const cb = document.querySelector('input[name="viandes[]"][value="' + v + '"]');
            if (cb) cb.checked = true;
        });
        document.getElementById('frites_cote').value = item.options.frites_cote;
        toggleSauceFrites();
        if (item.options.frites_cote === 'Oui') {
            document.querySelector('select[name="sauce_frites"]').value = item.options.sauce_frites;
        }
        preRemplirSupplementsEtMenu(document.getElementById('form-tacos'), item.options);
    } else if (item.options.type === 'sandwich') {
        ouvrirModalSandwich(item.id, item.name, prixBase);
        if (item.options.pain !== 'Pain Panini') {
            document.getElementById('pain_choix').value = item.options.pain;
        }
        item.options.sauces.forEach(s => {
            const cb = document.querySelector('input[name="sauces_sandwich[]"][value="' + s + '"]');
            if (cb) cb.checked = true;
        });
        document.querySelectorAll('input[name="crudites[]"]').forEach(cb => {
            cb.checked = item.options.crudites.includes(cb.value);
        });
        preRemplirSupplementsEtMenu(document.getElementById('form-sandwich'), item.options);
    } else if (item.options.type === 'burger') {
        ouvrirModalBurger(item.id, item.name, prixBase);
        document.querySelector('select[name="sauce_burger"]').value = item.options.sauce;
        document.querySelectorAll('input[name="crudites_burger[]"]').forEach(cb => {
            cb.checked = item.options.crudites.includes(cb.value);
        });
        preRemplirSupplementsEtMenu(document.getElementById('form-burger'), item.options);
    } else if (item.options.type === 'assiette') {
        ouvrirModalAssiette(item.id, item.name, prixBase);
        document.querySelector('input[name="ble_assiette"]').checked = item.options.ble === 'Oui';
        document.querySelector('input[name="salade_assiette"]').checked = item.options.salade === 'Oui';
        document.querySelector('input[name="frites_assiette"]').checked = item.options.frites === 'Oui';
        preRemplirSupplementsEtMenu(document.getElementById('form-assiette'), item.options);
    } else if (item.options.type === 'sauce') {
        ouvrirModalSauce(item.id, item.name, prixBase);
        document.querySelector('select[name="sauce_simple"]').value = item.options.sauce;
    } else if (item.options.type === 'kids_cheese') {
        ouvrirModalKidsCheese(item.id, item.name, prixBase);
        document.querySelector('select[name="sauce_kids"]').value = item.options.sauce;
        document.querySelectorAll('input[name="crudites_kids[]"]').forEach(cb => {
            cb.checked = item.options.crudites.includes(cb.value);
        });
    } else {
        return;
    }

    editEnCours = { actif: true, cartId: cartId };
    // évite de recharger la modification si la page est rafraîchie
    history.replaceState({}, '', '/pages/produits.php');
}

function finaliserAjoutOuModification(id, nom, prix, options, fermerModaleFn) {
    if (!window.monPanier) return;

    if (editEnCours.actif) {
        // add() démarre toujours à 1, donc on réapplique la quantité d'origine
        const ancienItem = window.monPanier.items.find(i => i.cartId === editEnCours.cartId);
        const qteAConserver = ancienItem ? ancienItem.qty : 1;
        const cartIdCible = options ? `${id}-${JSON.stringify(options)}` : String(id);
        const existeDeja = window.monPanier.items.some(i => i.cartId === cartIdCible && i.cartId !== editEnCours.cartId);

        window.monPanier.remove(editEnCours.cartId);
        window.monPanier.add(id, nom, prix, options);

        if (!existeDeja && qteAConserver > 1) {
            const nouvelItem = window.monPanier.items.find(i => i.cartId === cartIdCible);
            if (nouvelItem) {
                nouvelItem.qty = qteAConserver;
                window.monPanier.save();
            }
        }

        editEnCours = { actif: false, cartId: null };
        window.location.href = '/pages/panier.php';
        return;
    }

    window.monPanier.add(id, nom, prix, options);
    fermerModaleFn();
}

function ouvrirModalSandwich(id, nom, prix) {
    sandwichEnCours = { id: id, nom: nom, prix: parseFloat(prix) };

    document.getElementById('sandwich-title').innerText = `🥖 Personnalise ton ${nom}`;

    // panini = pain unique, pas de choix
    const divPain = document.getElementById('pain_choix').closest('.form-group');
    if (nom.toLowerCase().includes('panini')) {
        divPain.style.display = 'none';
    } else {
        divPain.style.display = 'block';
    }

    document.getElementById('modal-sandwich').style.display = 'flex';
}

const sauceCheckboxes = document.querySelectorAll('input[name="sauces_sandwich[]"]');
sauceCheckboxes.forEach(cb => {
    cb.addEventListener('change', () => {
        const cochees = document.querySelectorAll('input[name="sauces_sandwich[]"]:checked').length;
        if (cochees > 2) {
            cb.checked = false;
            alert("Vous pouvez choisir jusqu'à 2 sauces maximum !");
        }
    });
});

function fermerModalSandwich() {
    document.getElementById('modal-sandwich').style.display = 'none';
    sauceCheckboxes.forEach(cb => cb.checked = false);
    document.querySelectorAll('input[name="crudites[]"]').forEach(cb => cb.checked = true); // STO par défaut
    document.getElementById('pain_choix').value = 'Classique';
    resetSupplementsEtMenu(document.getElementById('form-sandwich'));
    editEnCours = { actif: false, cartId: null };
}

document.getElementById('form-sandwich').addEventListener('submit', function(e) {
    e.preventDefault();

    const form = e.target;

    const saucesSelectionnees = [];
    document.querySelectorAll('input[name="sauces_sandwich[]"]:checked').forEach(cb => {
        saucesSelectionnees.push(cb.value);
    });

    const cruditesSelectionnees = [];
    document.querySelectorAll('input[name="crudites[]"]:checked').forEach(cb => {
        cruditesSelectionnees.push(cb.value);
    });

    const painChoisi = sandwichEnCours.nom.toLowerCase().includes('panini') ? 'Pain Panini' : document.getElementById('pain_choix').value;

    const extra = getSupplementsEtMenu(form);
    const prixFinal = sandwichEnCours.prix + extra.supplementsTotal + extra.menuTotal;

    const sandwichPersonnalise = {
        id: sandwichEnCours.id,
        nom: sandwichEnCours.nom,
        prix: prixFinal,
        options: {
            type: 'sandwich',
            pain: painChoisi,
            sauces: saucesSelectionnees.length > 0 ? saucesSelectionnees : ['Aucune sauce'],
            crudites: cruditesSelectionnees.length > 0 ? cruditesSelectionnees : ['Sans crudités'],
            supplements: extra.supplements,
            menu: extra.menuBoisson
        }
    };

    finaliserAjoutOuModification(
        sandwichPersonnalise.id,
        sandwichPersonnalise.nom,
        sandwichPersonnalise.prix,
        sandwichPersonnalise.options,
        fermerModalSandwich
    );
});



function ouvrirModalTacos(id, nom, prix) {
    produitEnCours = { id: id, nom: nom, prix: parseFloat(prix) };

    if (nom.toLowerCase().includes("3 viandes") || nom.toLowerCase().includes("triple")) {
        maxViandesPermis = 3;
    } else if (nom.toLowerCase().includes("2 viandes") || nom.toLowerCase().includes("double")) {
        maxViandesPermis = 2;
    } else {
        maxViandesPermis = 1;
    }

    document.getElementById('nb-viandes-requis').innerText = maxViandesPermis;
    document.getElementById('modal-tacos').style.display = 'flex';
}

const checkboxes = document.querySelectorAll('input[name="viandes[]"]');
checkboxes.forEach(cb => {
    cb.addEventListener('change', () => {
        const cochees = document.querySelectorAll('input[name="viandes[]"]:checked').length;
        if (cochees > maxViandesPermis) {
            cb.checked = false;
            alert("Vous ne pouvez pas choisir plus de " + maxViandesPermis + " viande(s) pour ce Tacos !");
        }
    });
});

function toggleSauceFrites() {
    const frites = document.getElementById('frites_cote').value;
    const secSauce = document.getElementById('sec-sauce-frites');
    if (frites === 'Oui') {
        secSauce.style.display = 'block';
    } else {
        secSauce.style.display = 'none';
    }
}

function fermerModal() {
    document.getElementById('modal-tacos').style.display = 'none';
    checkboxes.forEach(cb => cb.checked = false);
    document.getElementById('frites_cote').value = 'Non';
    toggleSauceFrites();
    resetSupplementsEtMenu(document.getElementById('form-tacos'));
    editEnCours = { actif: false, cartId: null };
}

document.getElementById('form-tacos').addEventListener('submit', function(e) {
    e.preventDefault();

    const form = e.target;

    const viandesSelectionnees = [];
    document.querySelectorAll('input[name="viandes[]"]:checked').forEach(cb => {
        viandesSelectionnees.push(cb.value);
    });

    if (viandesSelectionnees.length < maxViandesPermis) {
        alert("Veuillez choisir exactement " + maxViandesPermis + " viande(s) pour composer votre Tacos !");
        return;
    }

    const fritesCote = document.getElementById('frites_cote').value;
    const sauceFrites = fritesCote === 'Oui' ? document.querySelector('select[name="sauce_frites"]').value : 'Aucune';

    const extra = getSupplementsEtMenu(form);
    const prixFinal = produitEnCours.prix + extra.supplementsTotal + extra.menuTotal;

    const produitPersonnalise = {
        id: produitEnCours.id,
        nom: produitEnCours.nom,
        prix: prixFinal,
        quantite: 1,
        options: {
            type: 'tacos',
            viandes: viandesSelectionnees,
            frites_cote: fritesCote,
            sauce_frites: sauceFrites,
            supplements: extra.supplements,
            menu: extra.menuBoisson
        }
    };

    finaliserAjoutOuModification(
        produitPersonnalise.id,
        produitPersonnalise.nom,
        produitPersonnalise.prix,
        produitPersonnalise.options,
        fermerModal
    );
});

// popup burger
function ouvrirModalBurger(id, nom, prix) {
    burgerEnCours = { id: id, nom: nom, prix: parseFloat(prix) };
    document.getElementById('burger-title').innerText = `🍔 Personnalise ton ${nom}`;
    document.getElementById('modal-burger').style.display = 'flex';
}

function fermerModalBurger() {
    document.getElementById('modal-burger').style.display = 'none';
    const form = document.getElementById('form-burger');
    form.querySelectorAll('input[name="crudites_burger[]"]').forEach(cb => cb.checked = true);
    form.querySelector('select[name="sauce_burger"]').selectedIndex = 0;
    resetSupplementsEtMenu(form);
    editEnCours = { actif: false, cartId: null };
}

document.getElementById('form-burger').addEventListener('submit', function(e) {
    e.preventDefault();

    const form = e.target;
    const sauceChoisie = form.querySelector('select[name="sauce_burger"]').value;

    const cruditesSelectionnees = [];
    form.querySelectorAll('input[name="crudites_burger[]"]:checked').forEach(cb => {
        cruditesSelectionnees.push(cb.value);
    });

    const extra = getSupplementsEtMenu(form);
    const prixFinal = burgerEnCours.prix + extra.supplementsTotal + extra.menuTotal;

    const burgerPersonnalise = {
        id: burgerEnCours.id,
        nom: burgerEnCours.nom,
        prix: prixFinal,
        options: {
            type: 'burger',
            sauce: sauceChoisie,
            crudites: cruditesSelectionnees.length > 0 ? cruditesSelectionnees : ['Sans crudités'],
            supplements: extra.supplements,
            menu: extra.menuBoisson
        }
    };

    finaliserAjoutOuModification(
        burgerPersonnalise.id,
        burgerPersonnalise.nom,
        burgerPersonnalise.prix,
        burgerPersonnalise.options,
        fermerModalBurger
    );
});

// popup assiette
function ouvrirModalAssiette(id, nom, prix) {
    assietteEnCours = { id: id, nom: nom, prix: parseFloat(prix) };
    document.getElementById('assiette-title').innerText = `🍽️ Personnalise ton ${nom}`;
    document.getElementById('modal-assiette').style.display = 'flex';
}

function fermerModalAssiette() {
    document.getElementById('modal-assiette').style.display = 'none';
    const form = document.getElementById('form-assiette');
    form.querySelector('input[name="ble_assiette"]').checked = true;
    form.querySelector('input[name="salade_assiette"]').checked = true;
    form.querySelector('input[name="frites_assiette"]').checked = true;
    resetSupplementsEtMenu(form);
    editEnCours = { actif: false, cartId: null };
}

document.getElementById('form-assiette').addEventListener('submit', function(e) {
    e.preventDefault();

    const form = e.target;
    const ble = form.querySelector('input[name="ble_assiette"]').checked ? 'Oui' : 'Non';
    const salade = form.querySelector('input[name="salade_assiette"]').checked ? 'Oui' : 'Non';
    const frites = form.querySelector('input[name="frites_assiette"]').checked ? 'Oui' : 'Non';

    const extra = getSupplementsEtMenu(form);
    const prixFinal = assietteEnCours.prix + extra.supplementsTotal + extra.menuTotal;

    const assiettePersonnalisee = {
        id: assietteEnCours.id,
        nom: assietteEnCours.nom,
        prix: prixFinal,
        options: {
            type: 'assiette',
            ble: ble,
            salade: salade,
            frites: frites,
            supplements: extra.supplements,
            menu: extra.menuBoisson
        }
    };

    finaliserAjoutOuModification(
        assiettePersonnalisee.id,
        assiettePersonnalisee.nom,
        assiettePersonnalisee.prix,
        assiettePersonnalisee.options,
        fermerModalAssiette
    );
});

// popup sauce seule : barquettes frites + kids nuggets
function ouvrirModalSauce(id, nom, prix) {
    sauceEnCours = { id: id, nom: nom, prix: parseFloat(prix) };
    document.getElementById('sauce-title').innerText = `🥫 Choisis ta sauce pour ${nom}`;
    document.getElementById('modal-sauce').style.display = 'flex';
}

function fermerModalSauce() {
    document.getElementById('modal-sauce').style.display = 'none';
    document.querySelector('select[name="sauce_simple"]').selectedIndex = 0;
    editEnCours = { actif: false, cartId: null };
}

document.getElementById('form-sauce').addEventListener('submit', function(e) {
    e.preventDefault();

    const form = e.target;
    const sauceChoisie = form.querySelector('select[name="sauce_simple"]').value;

    const options = {
        type: 'sauce',
        sauce: sauceChoisie
    };

    finaliserAjoutOuModification(
        sauceEnCours.id,
        sauceEnCours.nom,
        sauceEnCours.prix,
        options,
        fermerModalSauce
    );
});

// popup kids cheese : sauce + crudités, prix fixe (boisson déjà incluse)
function ouvrirModalKidsCheese(id, nom, prix) {
    kidsCheeseEnCours = { id: id, nom: nom, prix: parseFloat(prix) };
    document.getElementById('kids-cheese-title').innerText = `🧒 Personnalise ton ${nom}`;
    document.getElementById('modal-kids-cheese').style.display = 'flex';
}

function fermerModalKidsCheese() {
    document.getElementById('modal-kids-cheese').style.display = 'none';
    const form = document.getElementById('form-kids-cheese');
    form.querySelector('select[name="sauce_kids"]').selectedIndex = 0;
    form.querySelectorAll('input[name="crudites_kids[]"]').forEach(cb => cb.checked = true);
    editEnCours = { actif: false, cartId: null };
}

document.getElementById('form-kids-cheese').addEventListener('submit', function(e) {
    e.preventDefault();

    const form = e.target;
    const sauceChoisie = form.querySelector('select[name="sauce_kids"]').value;
    const cruditesSelectionnees = Array.from(form.querySelectorAll('input[name="crudites_kids[]"]:checked')).map(cb => cb.value);

    const options = {
        type: 'kids_cheese',
        sauce: sauceChoisie,
        crudites: cruditesSelectionnees.length > 0 ? cruditesSelectionnees : ['Sans crudités']
    };

    finaliserAjoutOuModification(
        kidsCheeseEnCours.id,
        kidsCheeseEnCours.nom,
        kidsCheeseEnCours.prix,
        options,
        fermerModalKidsCheese
    );
});

function filtrerCategorie(categorieCible, boutonClike) {
    const boutons = document.querySelectorAll('.btn-category');
    boutons.forEach(btn => {
        btn.style.background = 'white';
        btn.style.color = 'black';
        btn.style.borderColor = '#ddd';
    });

    if (boutonClike) {
        boutonClike.style.background = '#e74c3c';
        boutonClike.style.color = 'white';
        boutonClike.style.borderColor = '#e74c3c';
    }

    const cartes = document.querySelectorAll('.product-card');
    cartes.forEach(carte => {
        const categorieCarte = carte.getAttribute('data-category');

        if (categorieCible === 'tous' || categorieCarte === categorieCible) {
            carte.style.display = '';
        } else {
            carte.style.display = 'none';
        }
    });
}

chargerArticlePourModification();
</script>

<script src="/js/script.js"></script>
</body>
</html>