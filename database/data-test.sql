USE paie_me;

-- 5 sociétés
INSERT INTO societes (user_id, raison_sociale, forme_juridique, ice, if_fiscal, rc, tp, cnss, adresse, ville, telephone, email) VALUES
(1, 'TechMaroc Solutions', 'SARL', 'ICE001234567', 'IF123456', 'RC78901', 'TP34567', 'CNSS1001', '12 Rue des Innovateurs', 'Casablanca', '0522123456', 'contact@techmaroc.ma'),
(1, 'MegaBuild Construction', 'SA', 'ICE002345678', 'IF234567', 'RC89012', 'TP45678', 'CNSS1002', '45 Avenue Mohammed V', 'Rabat', '0537123456', 'info@megabuild.ma'),
(1, 'GreenAgri SARL', 'SARL', 'ICE003456789', 'IF345678', 'RC90123', 'TP56789', 'CNSS1003', '88 Rue de la Ferme', 'Marrakech', '0524123456', 'contact@greenagri.ma'),
(1, 'DigitalServices Africa', 'SAS', 'ICE004567890', 'IF456789', 'RC01234', 'TP67890', 'CNSS1004', '33 Boulevard Al Massira', 'Agadir', '0528123456', 'hello@digitalservices.ma'),
(1, 'TransLogistique Group', 'SNC', 'ICE005678901', 'IF567890', 'RC12345', 'TP78901', 'CNSS1005', '7 Rue du Commerce', 'Tanger', '0539123456', 'contact@translogistique.ma');

-- 5 salariés (1 par société)
INSERT INTO salaries (societe_id, matricule, nom_famille, prenom, adresse, date_naissance, date_embauche, cin, cnss, situation_familiale, nb_enfants, poste, type_contrat, salaire_base, indemnite_transport, indemnite_panier, indemnite_representation) VALUES
(1, 'TMS001', 'Benali', 'Karim', '15 Rue Atlas, Casablanca', '1990-03-15', '2022-01-10', 'AB123456', 'CNSS2001', 'marie', 2, 'Développeur Full Stack', 'CDI', 15000.00, 500.00, 780.00, 1500.00),
(2, 'MBC001', 'Alaoui', 'Fatima', '22 Avenue Hassan II, Rabat', '1985-07-22', '2020-06-01', 'CD234567', 'CNSS2002', 'marie', 3, 'Chef de Projet', 'CDI', 22000.00, 500.00, 780.00, 2200.00),
(3, 'GAS001', 'Idrissi', 'Youssef', '8 Rue des Oliviers, Marrakech', '1995-11-08', '2023-03-15', 'EF345678', 'CNSS2003', 'celibataire', 0, 'Ingénieur Agronome', 'CDI', 12000.00, 500.00, 780.00, 0.00),
(4, 'DSA001', 'Bennani', 'Sara', '5 Boulevard Mohammed VI, Agadir', '1992-09-30', '2021-09-01', 'GH456789', 'CNSS2004', 'celibataire', 0, 'Designer UX/UI', 'CDI', 13000.00, 500.00, 780.00, 1000.00),
(5, 'TLG001', 'Ouazzani', 'Hicham', '12 Rue de la Gare, Tanger', '1988-05-12', '2019-11-20', 'IJ567890', 'CNSS2005', 'marie', 4, 'Directeur Logistique', 'CDI', 28000.00, 500.00, 780.00, 2800.00);
