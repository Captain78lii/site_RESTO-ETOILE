/**
 Panier (Class) + Horaires
 */
document.addEventListener('DOMContentLoaded', () => {
    const monPanier = new Panier();

    checkOpeningStatus();
    initLiveSearch(); // <-- Ligne ajoutée pour lancer la recherche
});


class Panier {
    constructor() {
        this.items = JSON.parse(localStorage.getItem('restoCart')) || [];
        this.init();
    }

    init() {
        // Met à jour le compteur dans le menu dès le chargement
        this.updateCounter();

        // Écouteur sur les boutons "Ajouter au panier" (Page Produits)
        const buttons = document.querySelectorAll('.add-to-cart');
        buttons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                // On récupère les infos via les attributs data-
                const dataset = e.target.dataset;
                this.add(dataset.id, dataset.name, parseFloat(dataset.price));
            });
        });

        // Affichage du tableau (Page Panier)
        const cartTableBody = document.querySelector('#cart-items');
        if (cartTableBody) {
            this.renderCart(cartTableBody);
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
        // On garde tout sauf l'élément qui a l'ID à supprimer
        this.items = this.items.filter(item => item.id !== id);
        this.save();
        
        // Rafraîchir l'affichage
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
        const navLink = document.querySelector('.cart-link'); // J'ai ajouté une classe au lien HTML pour le cibler facilement
        const count = this.items.reduce((sum, item) => sum + item.qty, 0);
        if(navLink) {
            navLink.innerHTML = `Panier <span class="badge">${count}</span>`;
        }
    }

    renderCart(container) {
        container.innerHTML = '';
        
        if (this.items.length === 0) {
            container.innerHTML = '<tr><td colspan="5" style="text-align:center">Votre panier est vide.</td></tr>';
            const totalEl = document.querySelector('#cart-total');
            if(totalEl) totalEl.innerText = '0.00€';
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

        // Mise à jour du total global
        document.querySelector('#cart-total').innerText = `${this.getTotal()}€`;

        // Écouteurs pour suppression dynamique
        // Note: On utilise querySelectorAll à l'intérieur de render pour capter les nouveaux boutons
        container.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const idToDelete = e.target.closest('button').dataset.id; // .closest pour être sûr de cliquer sur le btn
                this.remove(idToDelete);
            });
        });
    }
}

/* --- FONCTION HORAIRES --- */
function checkOpeningStatus() {
    const scheduleElement = document.querySelector('.infos-resto p:nth-child(3)');
    if (!scheduleElement) return;

    const now = new Date();
    const day = now.getDay(); 
    const hour = now.getHours(); 

    let isOpen = (day !== 0 && hour >= 11 && hour < 22);

    const statusBadge = document.createElement('span');
    statusBadge.style.fontWeight = 'bold';
    statusBadge.style.marginLeft = '10px';
    statusBadge.style.padding = '2px 8px';
    statusBadge.style.borderRadius = '4px';
    statusBadge.style.color = '#fff';
    statusBadge.style.backgroundColor = isOpen ? '#2ecc71' : '#e74c3c';
    statusBadge.textContent = isOpen ? " OUVERT" : " FERMÉ";

    scheduleElement.appendChild(statusBadge);
}

/* --- FONCTION RECHERCHE EN DIRECT --- */
function initLiveSearch() {
    const searchInput = document.getElementById('search-input');
    
    // Si la barre de recherche n'existe pas (ex: on est sur l'accueil), on arrête la fonction
    if (!searchInput) return;

    searchInput.addEventListener('input', (e) => {
        // Le texte tapé, mis en minuscules pour faciliter la comparaison
        const searchText = e.target.value.toLowerCase(); 
        
        // On récupère toutes les cartes de produits
        const productCards = document.querySelectorAll('.product-card');

        productCards.forEach(card => {
            // On récupère le titre du plat (h3) et la description (p) s'il y en a une
            const productName = card.querySelector('h3').textContent.toLowerCase();
            const productDesc = card.querySelector('p') ? card.querySelector('p').textContent.toLowerCase() : '';
            
            // Si le texte tapé est dans le titre OU dans la description
            if (productName.includes(searchText) || productDesc.includes(searchText)) {
                card.style.display = ''; // On affiche la carte (retour au style par défaut)
            } else {
                card.style.display = 'none'; // Sinon, on la masque
            }
        });
    });
}