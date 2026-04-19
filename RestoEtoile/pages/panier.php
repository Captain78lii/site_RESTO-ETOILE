<?php include($_SERVER['DOCUMENT_ROOT'] . '/RestoEtoile/header.php'); ?>



    <div class="container">
        <h1>Votre Panier</h1>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Quantité</th>
                    <th>Prix unitaire</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="cart-items">
                </tbody>
        </table>

        <div style="text-align: right; font-size: 1.5rem; color: var(--accent-gold);">
            <strong>Total à payer : <span id="cart-total">0.00€</span></strong>
        </div>
        
        <div style="text-align: right; margin-top: 20px;">
            <button id="btn-vider-panier" class="btn" style="background-color:#c0392b; margin-right:10px;">
                Vider le panier
            </button>
            
            <button id="btn-valider-commande" class="btn">
                Valider la commande
            </button>
        </div>
    </div>
    <script src="/RestoEtoile/js/script.js"></script>
</body>
</html>