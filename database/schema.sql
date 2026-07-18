-- RestoEtoile — schéma de base de données (structure réelle, exportée depuis MySQL local).
-- Ne contient aucune donnée personnelle : à importer tel quel dans Railway.

CREATE DATABASE IF NOT EXISTS resto_etoile_db
  CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE resto_etoile_db;

CREATE TABLE utilisateurs (
  id INT(11) NOT NULL AUTO_INCREMENT,
  nom VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL,
  mot_de_passe VARCHAR(255) NOT NULL,
  date_inscription TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  points_fidelite INT(11) DEFAULT 0,
  role VARCHAR(20) NOT NULL DEFAULT 'user',
  PRIMARY KEY (id),
  UNIQUE KEY email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE produits (
  id INT(11) NOT NULL AUTO_INCREMENT,
  nom VARCHAR(100) NOT NULL,
  description TEXT DEFAULT NULL,
  prix DECIMAL(10,2) NOT NULL,
  categorie VARCHAR(50) DEFAULT NULL,
  image_url VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE reservations (
  id INT(11) NOT NULL AUTO_INCREMENT,
  user_id INT(11) DEFAULT NULL,
  date_reservation DATE NOT NULL,
  heure_reservation TIME NOT NULL,
  nb_personnes INT(11) NOT NULL,
  statut VARCHAR(20) DEFAULT 'en attente',
  PRIMARY KEY (id),
  KEY user_id (user_id),
  CONSTRAINT reservations_ibfk_1 FOREIGN KEY (user_id) REFERENCES utilisateurs (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE avis (
  id INT(11) NOT NULL AUTO_INCREMENT,
  user_id INT(11) DEFAULT NULL,
  commentaire TEXT NOT NULL,
  note INT(11) DEFAULT NULL CHECK (note BETWEEN 1 AND 5),
  date_publication DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY user_id (user_id),
  CONSTRAINT avis_ibfk_1 FOREIGN KEY (user_id) REFERENCES utilisateurs (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE commandes (
  id INT(11) NOT NULL AUTO_INCREMENT,
  user_id INT(11) NOT NULL,
  total DECIMAL(10,2) NOT NULL,
  statut VARCHAR(20) NOT NULL DEFAULT 'En attente',
  date_commande TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY user_id (user_id),
  CONSTRAINT commandes_ibfk_1 FOREIGN KEY (user_id) REFERENCES utilisateurs (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE commande_lignes (
  id INT(11) NOT NULL AUTO_INCREMENT,
  commande_id INT(11) NOT NULL,
  produit_id INT(11) DEFAULT NULL,
  nom_produit VARCHAR(150) NOT NULL,
  prix_unitaire DECIMAL(10,2) NOT NULL,
  quantite INT(11) NOT NULL,
  options_json TEXT DEFAULT NULL,
  PRIMARY KEY (id),
  KEY commande_id (commande_id),
  KEY produit_id (produit_id),
  CONSTRAINT commande_lignes_ibfk_1 FOREIGN KEY (commande_id) REFERENCES commandes (id) ON DELETE CASCADE,
  CONSTRAINT commande_lignes_ibfk_2 FOREIGN KEY (produit_id) REFERENCES produits (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE actualites (
  id INT(11) NOT NULL AUTO_INCREMENT,
  titre VARCHAR(255) NOT NULL,
  contenu TEXT NOT NULL,
  date_publication DATETIME DEFAULT CURRENT_TIMESTAMP,
  image_url VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Pas de compte pré-rempli (éviter un mot de passe connu dans un repo public).
-- Pour devenir admin après déploiement :
--   1. Inscrivez-vous normalement sur le site (register.php).
--   2. Promouvez ensuite ce compte :
--        UPDATE utilisateurs SET role = 'admin' WHERE email = 'votre-email@exemple.com';
