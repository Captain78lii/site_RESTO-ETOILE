/**
 * Panier (Class) + Horaires + Recherche
 */

// Échappe les caractères spéciaux pour pouvoir stocker un cartId (JSON) dans un attribut HTML data-*
function escapeHtmlAttr(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/"/g, '&quot;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}

document.addEventListener('DOMContentLoaded', () => {
    // 🆕 On rend le panier disponible globalement sur la fenêtre (window)
    window.monPanier = new Panier();
    checkOpeningStatus();
    initLiveSearch();
    initAdminProductSearch();
});

class Panier {
    // se lance à la création du panier, récupère le panier sauvegardé ou vide
    constructor() {
        this.items = JSON.parse(localStorage.getItem('restoCart')) || [];
        this.init();
    }

    // met en place tous les boutons
    init() {
        this.updateCounter();

        const buttons = document.querySelectorAll('.add-to-cart');
        buttons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const dataset = e.target.dataset;
                this.add(dataset.id, dataset.name, parseFloat(dataset.price));
            });
        });

        const cartTableBody = document.querySelector('#cart-items');
        if (cartTableBody) {
            this.renderCart(cartTableBody);
        }

        const btnVider = document.getElementById('btn-vider-panier');
        if (btnVider) {
            btnVider.addEventListener('click', () => {
                localStorage.removeItem('restoCart');
                location.reload();
            });
        }

        const btnValider = document.getElementById('btn-valider-commande');
        if (btnValider) {
            btnValider.onclick = (e) => {
                e.preventDefault();
                
                if (this.items.length === 0) {
                    alert("Votre panier est vide !");
                    return;
                }

                fetch('/pages/valider_commande.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': window.CSRF_TOKEN || ''
                    },
                    body: JSON.stringify(this.items)
                })
                    .then(response => {
                        if (!response.ok) throw new Error('Erreur serveur');
                        return response.json();
                    })
                    .then(data => {
                        alert(data.message);
                        if (data.status === 'success') {
                            localStorage.removeItem('restoCart');
                            window.location.href = '/pages/profil.php';
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        alert("Impossible de contacter le serveur. Vérifiez que le fichier valider_commande.php existe.");
                    });
            };
        }
    }

    // 🆕 Modifié pour accepter des options de personnalisation
    add(id, name, price, options = null) {
        // On crée un identifiant unique pour différencier deux Tacos identiques mais avec des viandes ou sauces différentes
        const cartId = options ? `${id}-${JSON.stringify(options)}` : id;

        const existingItem = this.items.find(item => item.cartId === cartId);
        if (existingItem) {
            existingItem.qty++;
        } else {
            this.items.push({ 
                cartId: cartId, // ID unique dans le panier
                id: id,         // ID du produit en BDD
                name: name, 
                price: price, 
                qty: 1,
                options: options // Stockage des viandes, frites et sauces
            });
        }
        this.save();
        this.updateCounter();
        alert(`${name} ajouté au panier !`);
    }

    // 🆕 Modifié pour supprimer via l'identifiant unique cartId
    remove(cartId) {
        this.items = this.items.filter(item => item.cartId !== cartId);
        this.save();
        const cartTableBody = document.querySelector('#cart-items');
        if (cartTableBody) this.renderCart(cartTableBody);
        this.updateCounter();
    }

    // 🆕 Augmente la quantité d'un article du panier
    incrementQty(cartId) {
        const item = this.items.find(i => i.cartId === cartId);
        if (!item) return;
        item.qty++;
        this.save();
        const cartTableBody = document.querySelector('#cart-items');
        if (cartTableBody) this.renderCart(cartTableBody);
        this.updateCounter();
    }

    // 🆕 Diminue la quantité d'un article du panier (le retire si elle tombe à 0)
    decrementQty(cartId) {
        const item = this.items.find(i => i.cartId === cartId);
        if (!item) return;
        if (item.qty > 1) {
            item.qty--;
            this.save();
            const cartTableBody = document.querySelector('#cart-items');
            if (cartTableBody) this.renderCart(cartTableBody);
            this.updateCounter();
        } else {
            this.remove(cartId);
        }
    }

    // écrit le panier actuel dans le localStorage
    save() {
        localStorage.setItem('restoCart', JSON.stringify(this.items));
    }

    // calcule le prix total du panier
    getTotal() {
        return this.items.reduce((total, item) => total + (item.price * item.qty), 0).toFixed(2);
    }

    // met à jour le petit badge "Panier (x)" dans le menu
    updateCounter() {
        const navLink = document.querySelector('.cart-link');
        const count = this.items.reduce((sum, item) => sum + item.qty, 0);
        if (navLink) {
            navLink.innerHTML = `Panier <span class="badge">${count}</span>`;
        }
    }

    // affiche tout le contenu du panier dans le tableau de panier.php
    renderCart(container) {
        container.innerHTML = '';
        if (this.items.length === 0) {
            container.innerHTML = '<tr><td colspan="5" style="text-align:center">Votre panier est vide.</td></tr>';
            const totalEl = document.querySelector('#cart-total');
            if (totalEl) totalEl.innerText = '0.00€';
            return;
        }

        this.items.forEach(item => {
            const totalItem = (item.price * item.qty).toFixed(2);
            
            // 🆕 Formatage de l'affichage HTML des choix du client (Tacos, Sandwich, Burger, Assiette)
            let optionsHtml = '';
            if (item.options) {
                let detailLines = '';

                if (item.options.viandes) {
                    // C'est un Tacos
                    const viandes = item.options.viandes.join(', ');
                    const frites = item.options.frites_cote === 'Oui' ? `🍟 Oui (Sauce: ${item.options.sauce_frites})` : '❌ Non';
                    detailLines += `• 🥩 <strong>Viandes :</strong> ${viandes}<br>`;
                    detailLines += `• 🍟 <strong>Frites à côté :</strong> ${frites}<br>`;
                } else if (item.options.pain) {
                    // C'est un Sandwich
                    const sauces = item.options.sauces.join(', ');
                    const crudites = item.options.crudites.join(', ');
                    detailLines += `• 🥖 <strong>Pain :</strong> ${item.options.pain}<br>`;
                    detailLines += `• 🥫 <strong>Sauces :</strong> ${sauces}<br>`;
                    detailLines += `• 🥗 <strong>Crudités :</strong> ${crudites}<br>`;
                } else if (item.options.type === 'burger') {
                    // C'est un Burger
                    const crudites = item.options.crudites.join(', ');
                    detailLines += `• 🥫 <strong>Sauce :</strong> ${item.options.sauce}<br>`;
                    detailLines += `• 🥗 <strong>Crudités :</strong> ${crudites}<br>`;
                } else if (item.options.type === 'assiette') {
                    // C'est une Assiette
                    detailLines += `• 🌾 <strong>Blé :</strong> ${item.options.ble}<br>`;
                    detailLines += `• 🥗 <strong>Salade :</strong> ${item.options.salade}<br>`;
                    detailLines += `• 🍟 <strong>Frites :</strong> ${item.options.frites}<br>`;
                } else if (item.options.type === 'sauce') {
                    // Barquette frites ou Menu Kids Nuggets : juste une sauce
                    detailLines += `• 🥫 <strong>Sauce :</strong> ${item.options.sauce}<br>`;
                } else if (item.options.type === 'kids_cheese') {
                    // Menu Kids Cheese : sauce + crudités
                    const crudites = item.options.crudites.join(', ');
                    detailLines += `• 🥫 <strong>Sauce :</strong> ${item.options.sauce}<br>`;
                    detailLines += `• 🥗 <strong>Crudités :</strong> ${crudites}<br>`;
                }

                // Suppléments et option Menu, communs aux 4 types de personnalisation
                if (item.options.supplements && item.options.supplements.length > 0) {
                    detailLines += `• ➕ <strong>Suppléments :</strong> ${item.options.supplements.join(', ')}<br>`;
                }
                if (item.options.menu) {
                    detailLines += `• 🥤 <strong>En Menu avec :</strong> ${item.options.menu}<br>`;
                }

                if (detailLines) {
                    optionsHtml = `
                        <div style="font-size: 0.8rem; color: #7f8c8d; margin-top: 5px; line-height: 1.3;">
                            ${detailLines}
                        </div>
                    `;
                }
            }

            // 🆕 Bouton "Modifier" uniquement pour les articles personnalisés (Tacos/Sandwich/Burger/Assiette)
            const boutonModifier = (item.options && item.options.type)
                ? `<a href="/pages/produits.php?edit=${encodeURIComponent(item.cartId)}" class="btn" style="background-color:#3498db; padding:5px 10px; font-size:0.8rem; margin-right:5px; display:inline-block;">✏️</a>`
                : '';

            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <strong>${item.name}</strong>
                    ${optionsHtml} </td>
                <td>
                    <button class="btn-qty" data-cart-id="${escapeHtmlAttr(item.cartId)}" data-action="dec" style="padding:2px 10px;">-</button>
                    <span style="margin:0 8px;">${item.qty}</span>
                    <button class="btn-qty" data-cart-id="${escapeHtmlAttr(item.cartId)}" data-action="inc" style="padding:2px 10px;">+</button>
                </td>
                <td>${item.price}€</td>
                <td style="font-weight:bold;">${totalItem}€</td>
                <td>${boutonModifier}<button class="btn-delete" data-cart-id="${escapeHtmlAttr(item.cartId)}">🗑️</button></td>
            `;
            container.appendChild(row);
        });

        document.querySelector('#cart-total').innerText = `${this.getTotal()}€`;

        container.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const cartIdToDelete = e.target.closest('button').dataset.cartId;
                this.remove(cartIdToDelete);
            });
        });

        container.querySelectorAll('.btn-qty').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const boutonEl = e.target.closest('button');
                const cartId = boutonEl.dataset.cartId;
                if (boutonEl.dataset.action === 'inc') {
                    this.incrementQty(cartId);
                } else {
                    this.decrementQty(cartId);
                }
            });
        });
    }
}

/**
 * Fonctions hors de la classe pour éviter les erreurs de portée
 */
function checkOpeningStatus() {
    const scheduleElement = document.querySelector('.infos-resto p') || document.querySelector('footer p');
    if (!scheduleElement) return;

    const now = new Date();
    const day = now.getDay(); 
    const hour = now.getHours(); 

    let isOpen = (day !== 0 && hour >= 11 && hour < 22);

    const statusBadge = document.createElement('span');
    statusBadge.style.cssText = `
        font-weight: bold;
        margin-left: 10px;
        padding: 2px 8px;
        border-radius: 4px;
        color: #fff;
        background-color: ${isOpen ? '#2ecc71' : '#e74c3c'};
    `;
    statusBadge.textContent = isOpen ? " ● OUVERT" : " ● FERMÉ";

    scheduleElement.appendChild(statusBadge);
}

function initLiveSearch() {
    const searchInput = document.getElementById('search-input');
    if (!searchInput) return;

    searchInput.addEventListener('input', (e) => {
        const searchText = e.target.value.toLowerCase();
        const productCards = document.querySelectorAll('.product-card');

        productCards.forEach(card => {
            const productName = card.querySelector('h3').textContent.toLowerCase();
            const productDesc = card.querySelector('p') ? card.querySelector('p').textContent.toLowerCase() : '';

            if (productName.includes(searchText) || productDesc.includes(searchText)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    });
}

// Enlève les accents pour que "cafe" trouve aussi "café"
function normalizeText(text) {
    const COMBINING_MARKS = new RegExp('[̀-ͯ]', 'g');
    return text.normalize('NFD').replace(COMBINING_MARKS, '').toLowerCase().trim();
}

// Recherche intelligente dans le tableau "Gérer la carte" de l'espace admin
function initAdminProductSearch() {
    const searchInput = document.getElementById('admin-product-search');
    const table = document.getElementById('admin-products-table');
    if (!searchInput || !table) return;

    const rows = Array.from(table.querySelectorAll('tbody tr[data-search]'));
    const noResultsMsg = document.getElementById('admin-product-no-results');
    const countEl = document.getElementById('admin-product-search-count');

    searchInput.addEventListener('input', (e) => {
        // Découpe la recherche en mots-clés : chaque mot doit être trouvé (peu importe l'ordre)
        const keywords = normalizeText(e.target.value).split(/\s+/).filter(Boolean);

        let visibleCount = 0;
        rows.forEach(row => {
            const haystack = normalizeText(row.dataset.search);
            const match = keywords.every(word => haystack.includes(word));
            row.style.display = match ? '' : 'none';
            if (match) visibleCount++;
        });

        if (noResultsMsg) {
            noResultsMsg.style.display = (visibleCount === 0 && rows.length > 0) ? '' : 'none';
        }
        if (countEl) {
            countEl.textContent = keywords.length > 0
                ? `${visibleCount} produit(s) trouvé(s) sur ${rows.length}`
                : '';
        }
    });
}