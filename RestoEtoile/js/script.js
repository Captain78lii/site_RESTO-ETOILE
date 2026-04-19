/**
 * Panier (Class) + Horaires + Recherche
 */
document.addEventListener('DOMContentLoaded', () => {
    // Initialisation du panier
    const monPanier = new Panier();

    // Lancement des fonctions globales
    checkOpeningStatus();
    initLiveSearch();
});

class Panier {
    constructor() {
        // Chargement des données du localStorage
        this.items = JSON.parse(localStorage.getItem('restoCart')) || [];
        this.init();
    }

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

        // --- GESTION DU BOUTON VIDER ---
        const btnVider = document.getElementById('btn-vider-panier');
        if (btnVider) {
            btnVider.addEventListener('click', () => {
                localStorage.removeItem('restoCart');
                location.reload();
            });
        }

        // --- GESTION DU BOUTON VALIDER ---
        const btnValider = document.getElementById('btn-valider-commande');
        if (btnValider) {
            // On force l'événement avec une fonction fléchée pour garder le contexte 'this'
            btnValider.onclick = (e) => {
                e.preventDefault();
                
                if (this.items.length === 0) {
                    alert("Votre panier est vide !");
                    return;
                }

                fetch('/RestoEtoile/pages/valider_commande.php')
                    .then(response => {
                        if (!response.ok) throw new Error('Erreur serveur');
                        return response.json();
                    })
                    .then(data => {
                        alert(data.message);
                        if (data.status === 'success') {
                            localStorage.removeItem('restoCart');
                            window.location.href = '/RestoEtoile/pages/profil.php';
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        alert("Impossible de contacter le serveur. Vérifiez que le fichier valider_commande.php existe.");
                    });
            };
        }
    }

    add(id, name, price) {
        const existingItem = this.items.find(item => item.id === id);
        if (existingItem) {
            existingItem.qty++;
        } else {
            this.items.push({ id, name, price, qty: 1 });
        }
        this.save();
        this.updateCounter();
        alert(`${name} ajouté au panier !`);
    }

    remove(id) {
        this.items = this.items.filter(item => item.id !== id);
        this.save();
        const cartTableBody = document.querySelector('#cart-items');
        if (cartTableBody) this.renderCart(cartTableBody);
        this.updateCounter();
    }

    save() {
        localStorage.setItem('restoCart', JSON.stringify(this.items));
    }

    getTotal() {
        return this.items.reduce((total, item) => total + (item.price * item.qty), 0).toFixed(2);
    }

    updateCounter() {
        const navLink = document.querySelector('.cart-link');
        const count = this.items.reduce((sum, item) => sum + item.qty, 0);
        if (navLink) {
            navLink.innerHTML = `Panier <span class="badge">${count}</span>`;
        }
    }

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
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${item.name}</td>
                <td>x${item.qty}</td>
                <td>${item.price}€</td>
                <td style="font-weight:bold;">${totalItem}€</td>
                <td><button class="btn-delete" data-id="${item.id}">🗑️</button></td>
            `;
            container.appendChild(row);
        });

        document.querySelector('#cart-total').innerText = `${this.getTotal()}€`;

        container.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const idToDelete = e.target.closest('button').dataset.id;
                this.remove(idToDelete);
            });
        });
    }
}

/**
 * Fonctions hors de la classe pour éviter les erreurs de portée
 */

function checkOpeningStatus() {
    // On cherche un élément qui contient les infos (à adapter selon ton HTML)
    const scheduleElement = document.querySelector('.infos-resto p') || document.querySelector('footer p');
    if (!scheduleElement) return;

    const now = new Date();
    const day = now.getDay(); 
    const hour = now.getHours(); 

    // Ouvert du Lundi (1) au Samedi (6) de 11h à 22h
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