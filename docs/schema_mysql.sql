-- Schéma MySQL pour EcoRide (importable via phpMyAdmin)
-- Ajustez le charset/collation si nécessaire.

CREATE TABLE configuration (
    id INT AUTO_INCREMENT NOT NULL,
    PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE parametre (
    id INT AUTO_INCREMENT NOT NULL,
    configuration_id INT NOT NULL,
    propriete VARCHAR(50) NOT NULL,
    valeur VARCHAR(50) NOT NULL,
    INDEX IDX_parametre_configuration (configuration_id),
    PRIMARY KEY(id),
    CONSTRAINT FK_parametre_configuration FOREIGN KEY (configuration_id) REFERENCES configuration (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE utilisateur (
    id INT AUTO_INCREMENT NOT NULL,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    email VARCHAR(180) NOT NULL,
    password VARCHAR(255) NOT NULL,
    telephone VARCHAR(50) DEFAULT NULL,
    adresse VARCHAR(255) DEFAULT NULL,
    date_naissance DATE DEFAULT NULL,
    photo LONGTEXT DEFAULT NULL,
    pseudo VARCHAR(50) DEFAULT NULL,
    slug VARCHAR(100) NOT NULL,
    UNIQUE INDEX UNIQ_utilisateur_email (email),
    UNIQUE INDEX UNIQ_utilisateur_slug (slug),
    PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE role (
    id INT AUTO_INCREMENT NOT NULL,
    libelle VARCHAR(50) NOT NULL,
    UNIQUE INDEX UNIQ_role_libelle (libelle),
    PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE utilisateur_role (
    utilisateur_id INT NOT NULL,
    role_id INT NOT NULL,
    INDEX IDX_utilisateur_role_user (utilisateur_id),
    INDEX IDX_utilisateur_role_role (role_id),
    PRIMARY KEY(utilisateur_id, role_id),
    CONSTRAINT FK_utilisateur_role_user FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE,
    CONSTRAINT FK_utilisateur_role_role FOREIGN KEY (role_id) REFERENCES role (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE marque (
    id INT AUTO_INCREMENT NOT NULL,
    libelle VARCHAR(50) NOT NULL,
    UNIQUE INDEX UNIQ_marque_libelle (libelle),
    PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE voiture (
    id INT AUTO_INCREMENT NOT NULL,
    marque_id INT NOT NULL,
    proprietaire_id INT NOT NULL,
    modele VARCHAR(50) NOT NULL,
    immatriculation VARCHAR(50) NOT NULL,
    energie VARCHAR(50) NOT NULL,
    couleur VARCHAR(50) NOT NULL,
    date_premiere_immatriculation VARCHAR(50) NOT NULL,
    UNIQUE INDEX UNIQ_voiture_immatriculation (immatriculation),
    INDEX IDX_voiture_marque (marque_id),
    INDEX IDX_voiture_proprietaire (proprietaire_id),
    PRIMARY KEY(id),
    CONSTRAINT FK_voiture_marque FOREIGN KEY (marque_id) REFERENCES marque (id),
    CONSTRAINT FK_voiture_proprietaire FOREIGN KEY (proprietaire_id) REFERENCES utilisateur (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE covoiturage (
    id INT AUTO_INCREMENT NOT NULL,
    voiture_id INT NOT NULL,
    date_depart DATE NOT NULL,
    heure_depart TIME NOT NULL,
    lieu_depart VARCHAR(50) NOT NULL,
    date_arrivee DATE NOT NULL,
    heure_arrivee TIME NOT NULL,
    lieu_arrivee VARCHAR(50) NOT NULL,
    statut VARCHAR(50) NOT NULL,
    nb_place INT NOT NULL,
    prix_personne DOUBLE PRECISION NOT NULL,
    slug VARCHAR(150) NOT NULL,
    INDEX IDX_covoiturage_voiture (voiture_id),
    UNIQUE INDEX UNIQ_covoiturage_slug (slug),
    PRIMARY KEY(id),
    CONSTRAINT FK_covoiturage_voiture FOREIGN KEY (voiture_id) REFERENCES voiture (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE covoiturage_participant (
    covoiturage_id INT NOT NULL,
    utilisateur_id INT NOT NULL,
    INDEX IDX_covoiturage_participant_covoiturage (covoiturage_id),
    INDEX IDX_covoiturage_participant_utilisateur (utilisateur_id),
    PRIMARY KEY(covoiturage_id, utilisateur_id),
    CONSTRAINT FK_covoiturage_participant_covoiturage FOREIGN KEY (covoiturage_id) REFERENCES covoiturage (id) ON DELETE CASCADE,
    CONSTRAINT FK_covoiturage_participant_utilisateur FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE avis (
    id INT AUTO_INCREMENT NOT NULL,
    utilisateur_id INT NOT NULL,
    covoiturage_id INT DEFAULT NULL,
    commentaire LONGTEXT NOT NULL,
    note VARCHAR(50) NOT NULL,
    statut VARCHAR(50) NOT NULL,
    INDEX IDX_avis_utilisateur (utilisateur_id),
    INDEX IDX_avis_covoiturage (covoiturage_id),
    PRIMARY KEY(id),
    CONSTRAINT FK_avis_utilisateur FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id),
    CONSTRAINT FK_avis_covoiturage FOREIGN KEY (covoiturage_id) REFERENCES covoiturage (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Données de base (seed) inspirées des profils et trajets du front EcoRide
-- ---------------------------------------------------------------------------
SET NAMES utf8mb4;

INSERT INTO role (id, libelle) VALUES
  (1, 'ADMIN'),
  (2, 'CONDUCTEUR'),
  (3, 'PASSAGER');

INSERT INTO utilisateur (id, nom, prenom, email, password, telephone, adresse, date_naissance, photo, pseudo, slug) VALUES
  (101, 'Alexandre', 'Martin', 'alex.martin@example.com', 'password', '0612345678', '12 Rue de Rivoli, 75004 Paris', '1988-04-15', 'img/alex.jpg', 'Alex_75', 'alex-75'),
  (102, 'Marie', 'Lambert', 'marie.lambert@example.com', 'password', '0623456789', '8 Quai du Rhône, 69002 Lyon', '1990-02-11', 'img/marie.jpg', 'MarieL', 'mariel'),
  (103, 'Pierre', 'Tremblay', 'pierre.tremblay@example.com', 'password', '0634567890', '5 Rue de Strasbourg, 35000 Rennes', '1985-09-23', 'img/pierre.jpg', 'PierreT', 'pierret'),
  (104, 'Sophie', 'Martin', 'sophie.martin@example.com', 'password', '0645678901', '22 Rue Nationale, 59000 Lille', '1992-07-30', 'img/sophie.jpg', 'SophieM', 'sophiem'),
  (105, 'Cristiano', 'Ronaldo', 'cristiano.ronaldo@example.com', 'password', '0645678981', '7 Av. du Prado, 13008 Marseille', '1984-02-05', 'img/ronaldo.jpg', 'C.Ronaldo7', 'c-ronaldo7'),
  (106, 'Julie', 'Dupont', 'julie.dupont@example.com', 'password', '0656789123', "14 Cours de l'Intendance, 33000 Bordeaux", '1991-03-18', 'img/julie.png', 'JulieEco', 'julieeco'),
  (107, 'Léo', 'Martinez', 'leo.martinez@example.com', 'password', '0678912345', '10 Rue Espariat, 13100 Aix-en-Provence', '1989-12-02', 'img/leo.png', 'LeoSud', 'leosud'),
  (108, 'Nina', 'Leroy', 'nina.leroy@example.com', 'password', '0689123456', '3 Rue du Faisan, 67000 Strasbourg', '1993-05-27', 'img/nina.png', 'NinaZen', 'ninazen'),
  (109, 'Ross', 'Admin', 'ross@example.com', 'password', '0600000000', '1 Rue Symfony, 75000 Paris', '1990-01-01', NULL, 'Ross', 'ross');

INSERT INTO utilisateur_role (utilisateur_id, role_id) VALUES
  (101, 2), (102, 2), (103, 2), (104, 2),
  (105, 2), (106, 2), (107, 2), (108, 2);

INSERT INTO marque (id, libelle) VALUES
  (1, 'Tesla'),
  (2, 'Renault'),
  (3, 'Peugeot'),
  (4, 'Citroën'),
  (5, 'Volkswagen'),
  (6, 'Ferrari'),
  (7, 'Toyota'),
  (8, 'Hyundai');

INSERT INTO voiture (id, marque_id, proprietaire_id, modele, immatriculation, energie, couleur, date_premiere_immatriculation) VALUES
  (1, 1, 101, 'Model 3', 'AA-101-AA', 'Electrique', 'Blanc', '2023-01-15'),
  (2, 2, 102, 'Zoé', 'BB-102-BB', 'Electrique', 'Bleu', '2022-06-10'),
  (3, 3, 102, '308', 'CC-102-CC', 'Essence', 'Gris', '2021-04-20'),
  (4, 4, 103, 'C4', 'DD-103-DD', 'Essence', 'Gris', '2020-09-09'),
  (5, 5, 103, 'Golf', 'EE-103-EE', 'Diesel', 'Noir', '2019-03-12'),
  (6, 7, 104, 'Prius', 'FF-104-FF', 'Hybride', 'Vert', '2021-11-01'),
  (7, 6, 105, '458', 'GG-105-GG', 'Essence', 'Rouge', '2018-07-07'),
  (8, 8, 106, 'Kona', 'HH-106-HH', 'Electrique', 'Bleu', '2022-02-14'),
  (9, 3, 107, '508', 'II-107-II', 'Diesel', 'Gris', '2020-12-05'),
  (10, 5, 108, 'ID.4', 'JJ-108-JJ', 'Electrique', 'Noir', '2023-04-18');

INSERT INTO covoiturage (id, voiture_id, date_depart, heure_depart, lieu_depart, date_arrivee, heure_arrivee, lieu_arrivee, statut, nb_place, prix_personne, slug) VALUES
  (1, 1, '2025-10-15', '08:00:00', 'Paris', '2025-10-15', '12:30:00', 'Lyon', 'ouvert', 2, 15, 'paris-lyon-20251015-1'),
  (2, 2, '2025-10-20', '14:00:00', 'Lyon', '2025-10-20', '17:30:00', 'Marseille', 'ouvert', 1, 20, 'lyon-marseille-20251020-2'),
  (3, 3, '2025-10-22', '09:00:00', 'Marseille', '2025-10-22', '11:30:00', 'Nice', 'ouvert', 3, 12, 'marseille-nice-20251022-3'),
  (4, 4, '2025-10-25', '09:00:00', 'Nantes', '2025-10-25', '10:30:00', 'Rennes', 'ouvert', 2, 8, 'nantes-rennes-20251025-4'),
  (5, 5, '2025-10-28', '16:00:00', 'Rennes', '2025-10-28', '19:30:00', 'Paris', 'ouvert', 3, 25, 'rennes-paris-20251028-5'),
  (6, 6, '2025-10-19', '13:00:00', 'Lille', '2025-10-19', '14:45:00', 'Amiens', 'ouvert', 1, 10, 'lille-amiens-20251019-6'),
  (7, 7, '2025-10-30', '12:00:00', 'Paris', '2025-10-30', '21:45:00', 'Marseille', 'ouvert', 1, 7, 'paris-marseille-20251030-7'),
  (8, 8, '2025-11-02', '09:15:00', 'Bordeaux', '2025-11-02', '12:00:00', 'Toulouse', 'ouvert', 2, 14, 'bordeaux-toulouse-20251102-8'),
  (9, 8, '2025-11-05', '15:00:00', 'Toulouse', '2025-11-05', '18:10:00', 'Montpellier', 'ouvert', 1, 16, 'toulouse-montpellier-20251105-9'),
  (10, 9, '2025-11-08', '07:30:00', 'Nice', '2025-11-08', '10:20:00', 'Aix-en-Provence', 'ouvert', 3, 18, 'nice-aix-en-provence-20251108-10'),
  (11, 9, '2025-11-09', '13:45:00', 'Aix-en-Provence', '2025-11-09', '18:30:00', 'Lyon', 'ouvert', 2, 22, 'aix-en-provence-lyon-20251109-11'),
  (12, 10, '2025-11-12', '08:10:00', 'Strasbourg', '2025-11-12', '10:00:00', 'Metz', 'ouvert', 4, 11, 'strasbourg-metz-20251112-12'),
  (13, 10, '2025-11-13', '14:30:00', 'Metz', '2025-11-13', '17:10:00', 'Reims', 'ouvert', 2, 13, 'metz-reims-20251113-13'),
  (14, 2, '2025-10-15', '09:30:00', 'Paris', '2025-10-15', '13:45:00', 'Lyon', 'ouvert', 2, 18, 'paris-lyon-20251015-14'),
  (15, 4, '2025-10-15', '11:15:00', 'Paris', '2025-10-15', '15:30:00', 'Lyon', 'ouvert', 1, 20, 'paris-lyon-20251015-15'),
  (16, 8, '2025-10-15', '07:45:00', 'Paris', '2025-10-15', '12:10:00', 'Lyon', 'ouvert', 3, 17, 'paris-lyon-20251015-16');
