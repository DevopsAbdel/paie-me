<?php
/**
 * Migration script — exécuter après chaque pull pour mettre à jour la base
 * Usage : php database/migrate.php
 */

$p = new PDO("mysql:host=127.0.0.1;dbname=paie_me;charset=utf8mb4", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

function colExists(PDO $p, string $table, string $col): bool {
    return (bool) $p->query("SHOW COLUMNS FROM `$table` LIKE '$col'")->fetch();
}

function addCol(PDO $p, string $table, string $def): void {
    $col = explode(" ", $def)[0];
    if (!colExists($p, $table, $col)) {
        $p->exec("ALTER TABLE `$table` ADD COLUMN $def");
        echo "   + colonne $col ($table)\n";
    }
}

$count = 0;

// === rubriques_gains : colonnes ===
addCol($p, 'rubriques_gains', 'is_global TINYINT(1) NOT NULL DEFAULT 0 AFTER societe_id');
addCol($p, 'rubriques_gains', 'categorie VARCHAR(50) DEFAULT NULL AFTER valeur_defaut');
addCol($p, 'rubriques_gains', 'affectation VARCHAR(20) DEFAULT NULL AFTER imposable');
addCol($p, 'rubriques_gains', 'plafond_dgi VARCHAR(200) DEFAULT NULL AFTER affectation');
addCol($p, 'rubriques_gains', 'plafond_cnss VARCHAR(200) DEFAULT NULL AFTER plafond_dgi');
addCol($p, 'rubriques_gains', 'justificatifs VARCHAR(500) DEFAULT NULL AFTER plafond_cnss');

if (!colExists($p, 'rubriques_gains', 'societe_id') || $p->query("SHOW COLUMNS FROM rubriques_gains LIKE 'societe_id'")->fetch()['Null'] !== 'YES') {
    $p->exec("ALTER TABLE rubriques_gains MODIFY COLUMN societe_id INT UNSIGNED DEFAULT NULL");
    echo "   + societe_id nullable (rubriques_gains)\n";
}

// === rubriques_retenues : colonnes ===
addCol($p, 'rubriques_retenues', 'is_global TINYINT(1) NOT NULL DEFAULT 0 AFTER societe_id');

if (!colExists($p, 'rubriques_retenues', 'societe_id') || $p->query("SHOW COLUMNS FROM rubriques_retenues LIKE 'societe_id'")->fetch()['Null'] !== 'YES') {
    $p->exec("ALTER TABLE rubriques_retenues MODIFY COLUMN societe_id INT UNSIGNED DEFAULT NULL");
    echo "   + societe_id nullable (rubriques_retenues)\n";
}

// === Barème IR 2025 ===
$check = $p->query("SELECT COUNT(*) FROM bareme_ir WHERE deduction = 333.33")->fetchColumn();
if (!$check) {
    $p->exec("TRUNCATE TABLE bareme_ir");
    $p->exec("INSERT INTO bareme_ir (min, max, taux, deduction, type) VALUES
        (0.00, 3333.33, 0, 0, 'mensuel'),
        (3333.34, 5000.00, 10, 333.33, 'mensuel'),
        (5000.01, 6666.67, 20, 833.33, 'mensuel'),
        (6666.68, 8333.33, 30, 1500.00, 'mensuel'),
        (8333.34, 15000.00, 34, 1833.33, 'mensuel'),
        (15000.01, 999999.99, 37, 2283.33, 'mensuel'),
        (0.00, 40000.00, 0, 0, 'annuel'),
        (40001.00, 60000.00, 10, 4000.00, 'annuel'),
        (60001.00, 80000.00, 20, 10000.00, 'annuel'),
        (80001.00, 100000.00, 30, 18000.00, 'annuel'),
        (100001.00, 180000.00, 34, 22000.00, 'annuel'),
        (180000.01, 9999999.99, 37, 27400.00, 'annuel')");
    echo "   + barème IR 2025 mis à jour\n";
}

// === Rubriques gains globales ===
$existing = $p->query("SELECT COUNT(*) FROM rubriques_gains WHERE is_global = 1 AND code = '501'")->fetchColumn();
if (!$existing) {
    $p->exec("INSERT IGNORE INTO rubriques_gains (societe_id, is_global, code, libelle, type_montant, valeur_defaut, categorie, imposable, affectation, compte, plafond_dgi, plafond_cnss, justificatifs, source, source_maj, nature_edi, base_anciennete, au_prorata) VALUES
        (NULL,1,'501','Prime de rendement','proportionnel',10.00,'Gain standard',1,'61711000','61711000','Imposable','Imposable','Contrat de travail / avenant définissant les objectifs et critères de rendement','Contrat de travail','2025-10-01','REND',1,0),
        (NULL,1,'502','Prime d''objectifs','proportionnel',5.00,'Gain standard',1,'61711000','61711000','Imposable','Imposable','Contrat de travail / avenant définissant les objectifs','Contrat de travail','2025-10-01','OBJEC',1,0),
        (NULL,1,'503','Prime d''assiduité','fixe',300.00,'Gain standard',1,'61711000','61711000','Imposable','Imposable','Règlement intérieur ou contrat définissant les conditions de présence','Règlement intérieur / contrat','2025-10-01','ASSID',1,0),
        (NULL,1,'504','Prime de nuit','fixe',250.00,'Gain standard',1,'61711000','61711000','Imposable','Imposable','Planning / pointage justifiant les heures de nuit effectuées','Convention collective / contrat','2025-10-01','NUIT',1,0),
        (NULL,1,'505','13ème mois (prorata)','proportionnel',8.33,'Gain standard',1,'61711000','61711000','Imposable','Imposable','Convention collective ou usage d''entreprise','Convention collective','2025-10-01','13EME',0,1),
        (NULL,1,'330','Indemnité de transport urbain','fixe',500,'Transport & Déplacement',0,'61713000','61713000','500.00 DH / mois','500.00 DH / mois','Lieu de travail situé au milieu urbain de la ville','Arrêté n° 1314-25','2025-10-01',NULL,0,0),
        (NULL,1,'331','Indemnité de représentation','proportionnel',10,'Spécifiques à certains emplois',0,'61713000','61713000','10% du salaire de base','10% du salaire de base','Poste de direction, d''encadrement supérieur ou équivalent','Arrêté n° 1314-25','2025-10-01',NULL,0,0),
        (NULL,1,'334','Indemnité kilométrique','fixe',0,'Transport & Déplacement',0,'61713000','61713000','3 DH / KM','3 DH / KM','Carnet de bord, carte grise au nom du salarié, trajet < 50 KM','Arrêté n° 1314-25','2025-10-01',NULL,0,0),
        (NULL,1,'337','Indemnité de tournée','fixe',1500,'Transport & Déplacement',0,'61713000','61713000','1 500.00 DH / mois','1 500.00 DH / mois','Périmètre de déplacement limité à 50 KM, planning de tournée','Arrêté n° 1314-25','2025-10-01',NULL,0,0),
        (NULL,1,'339','Indemnité de déplacement justifiée','fixe',0,'Transport & Déplacement',0,'61713000','61713000','Nourriture (10x SMIG hor.), Hébergement (30x SMIG hor.)','Totalement exonéré si justifié','Pièces justificatives (factures, tickets, ordre de mission)','Arrêté n° 1314-25','2025-10-01',NULL,0,0),
        (NULL,1,'340','Indemnité de déplacement forfaitaire ponctuelle','fixe',0,'Transport & Déplacement',0,'61713000','61713000','Nourriture (10x SMIG hor.), Hébergement (30x SMIG hor.)','Repas: 171 DH/j, Hébergement: 513 DH/nuit','Ordre de mission stipulant la nature ponctuelle','Arrêté n° 1314-25','2025-10-01',NULL,0,0),
        (NULL,1,'341','Indemnité de déplacement forfaitaire régulière','fixe',5000,'Transport & Déplacement',0,'61713000','61713000','<= 5000 DH et <= Salaire de base','Exonération dans la limite de 100% du S.B. (max 5000 DH/mois)','Déplacements professionnels hors périmètre urbain (> 50 km)','Arrêté n° 1314-25','2025-10-01',NULL,0,0),
        (NULL,1,'342','Indemnité de transport hors urbain','fixe',750,'Transport & Déplacement',0,'61713000','61713000','750.00 DH / mois','750.00 DH / mois','Lieu de travail situé en dehors du milieu urbain','Arrêté n° 1314-25','2025-10-01',NULL,0,0),
        (NULL,1,'343','Prime d''outillage','fixe',100,'Spécifiques à certains emplois',0,'61713000','61713000','100 DH / mois','119.70 DH / 26 jours de travail','Le salarié doit être propriétaire de ses propres équipements','Arrêté n° 1314-25','2025-10-01',NULL,0,0),
        (NULL,1,'344','Prime de salissure','fixe',210,'Spécifiques à certains emplois',0,'61713000','61713000','210 DH / mois','239.40 DH / 26 jours de travail','Travaux salissants / insalubres (bleu de travail requis)','Arrêté n° 1314-25','2025-10-01',NULL,0,0),
        (NULL,1,'345','Prime d''usure de vêtements / Tenue','fixe',0,'Spécifiques à certains emplois',0,'61713000','61713000','Frais réels ou barème interne','Exonéré si port obligatoire pour le service','Obligation contractuelle ou règlement intérieur','Arrêté n° 1314-25','2025-10-01',NULL,0,0),
        (NULL,1,'346','Indemnité de panier / Panier de nuit','fixe',0,'Spécifiques à certains emplois',0,'61713000','61713000','2x SMIG horaire par jour','Exonération selon plafond légal en vigueur','Horaires de nuit ou travail continu sans coupure','Arrêté n° 1314-25','2025-10-01',NULL,0,0),
        (NULL,1,'347','Indemnité de pénibilité','fixe',0,'Spécifiques à certains emplois',0,'61713000','61713000','Selon convention collective','Exonéré sous réserve d''un cadre réglementé','Attestation de conditions de travail pénibles','Arrêté n° 1314-25','2025-10-01',NULL,0,0),
        (NULL,1,'348','Indemnité de risque / Danger','fixe',0,'Spécifiques à certains emplois',0,'61713000','61713000','Selon barème sectoriel','Exonéré si le risque est inhérent à la fonction','Fiche de poste, rapport d''évaluation des risques','Arrêté n° 1314-25','2025-10-01',NULL,0,0),
        (NULL,1,'349','Indemnité d''astreinte','fixe',0,'Spécifiques à certains emplois',0,'61713000','61713000','Selon convention collective','Exonéré si liée à des interventions urgentes hors horaires','Planning d''astreinte et rapports d''intervention','Arrêté n° 1314-25','2025-10-01',NULL,0,0),
        (NULL,1,'350','Indemnité de garde','fixe',0,'Spécifiques à certains emplois',0,'61713000','61713000','Barème interne conventionné','Exonéré dans le cadre médical ou de sécurité','Registre des gardes effectuées','Arrêté n° 1314-25','2025-10-01',NULL,0,0),
        (NULL,1,'351','Voiture de fonction ou de service','fixe',0,'Transport & Déplacement',0,'61713000','61713000','Charges supportées par l''entreprise','Totalement exonéré','Usage strictement professionnel ou convention d''affectation','Arrêté n° 1314-25','2025-10-01',NULL,0,0),
        (NULL,1,'352','Indemnité de voyage à l''étranger','fixe',0,'Transport & Déplacement',0,'61713000','61713000','Frais réels justifiés','Frais réels sur justificatifs ou barème officiel','Ordre de mission international, billets, factures hôtel','Arrêté n° 1314-25','2025-10-01',NULL,0,0),
        (NULL,1,'353','Indemnité de déménagement / mutation','fixe',0,'Transport & Déplacement',0,'61713000','61713000','Frais réels sur factures','Exonéré si requis par l''employeur','Décision de mutation, factures du déménageur','Arrêté n° 1314-25','2025-10-01',NULL,0,0),
        (NULL,1,'354','Allocations familiales additionnelles','fixe',0,'Caractère Social & Familial',0,'61712000','61712000','Plafond légal CNSS','Totalement exonéré','Livret de famille, attestation de non-paiement par ailleurs','Arrêté n° 1314-25','2025-10-01',NULL,0,0),
        (NULL,1,'355','Allocation de naissance','fixe',0,'Caractère Social & Familial',0,'61712000','61712000','Barème interne raisonnable','Exonéré si ponctuel','Extrait d''acte de naissance du nouveau-né','Arrêté n° 1314-25','2025-10-01',NULL,0,0),
        (NULL,1,'356','Allocation de mariage','fixe',0,'Caractère Social & Familial',0,'61712000','61712000','Barème social de l''entreprise','Exonéré si ponctuel','Acte de mariage adoulé ou officiel','Arrêté n° 1314-25','2025-10-01',NULL,0,0),
        (NULL,1,'357','Allocation de décès / Obsèques','fixe',0,'Caractère Social & Familial',0,'61712000','61712000','Frais réels ou forfait social','Totalement exonéré','Certificat de décès du conjoint ou d''un ascendant/descendant direct','Arrêté n° 1314-25','2025-10-01',NULL,0,0),
        (NULL,1,'358','Prime de scolarité / Rentrée scolaire','fixe',400,'Caractère Social & Familial',0,'61712000','61712000','Plafond par enfant/an','Exonéré si attribué aux enfants à charge','Certificat de scolarité annuel','Arrêté n° 1314-25','2025-10-01',NULL,0,0),
        (NULL,1,'359','Bons d''achat / Cadeaux de fin d''année','fixe',0,'Caractère Social & Familial',0,'61712000','61712000','Plafond annuel (ex: 10% SMIG)','Exonéré dans la limite du plafond social','Distribution générale à l''occasion de fêtes (Aïd, Achoura, etc.)','Arrêté n° 1314-25','2025-10-01',NULL,0,0),
        (NULL,1,'360','Indemnité de caisse (responsabilité pécuniaire)','fixe',190,'Spécifiques à certains emplois',0,'61713000','61713000','190 DH / mois','239.40 DH / 26 jours de travail','Poste de caissier ou manipulation effective de fonds','Arrêté n° 1314-25','2025-10-01',NULL,0,0),
        (NULL,1,'361','Subvention de cantine / Titres repas','fixe',0,'Caractère Social & Familial',0,'61712000','61712000','Plafond par ticket / jour','Exonéré selon la quote-part patronale réglementaire','Factures du prestataire de restauration ou émetteur de titres','Arrêté n° 1314-25','2025-10-01',NULL,0,0),
        (NULL,1,'362','Prise en charge des frais médicaux','fixe',0,'Caractère Social & Familial',0,'61712000','61712000','Sur dossier médical','Exonéré si géré par le fonds social / mutuelle','Décompte AMO/Mutuelle et ordonnances restées à charge','Arrêté n° 1314-25','2025-10-01',NULL,0,0),
        (NULL,1,'363','Aide aux vacances / Estivage','fixe',0,'Caractère Social & Familial',0,'61712000','61712000','Plafond annuel fixe','Exonéré si géré via les œuvres sociales (COS)','Factures d''organismes de vacances ou convention COS','Arrêté n° 1314-25','2025-10-01',NULL,0,0),
        (NULL,1,'364','Secours exceptionnel / Social','fixe',0,'Caractère Social & Familial',0,'61712000','61712000','Forfait ponctuel motivé','Exonéré si situation de précarité avérée','Dossier d''assistante sociale ou justificatifs de force majeure','Arrêté n° 1314-25','2025-10-01',NULL,0,0),
        (NULL,1,'365','Bourses d''études pour les enfants','fixe',0,'Caractère Social & Familial',0,'61712000','61712000','Selon mérite et critères sociaux','Exonéré si versé directement à l''établissement','Facture de l''école/université, attestation de réussite','Arrêté n° 1314-25','2025-10-01',NULL,0,0),
        (NULL,1,'366','Indemnité légale de licenciement','fixe',0,'Rupture & Fin de Contrat',0,'61715000','61715000','Barème du Code du Travail','Totalement exonérée de CNSS et DGI','Lettre de licenciement, PV de l''inspecteur du travail / tribunal','Code du Travail / Arrêté n° 1314-25','2025-10-01',NULL,0,0),
        (NULL,1,'367','Indemnité de licenciement abusive','fixe',0,'Rupture & Fin de Contrat',0,'61715000','61715000','Fixée par tribunal ou conciliation','Exonérée selon la limite légale ou judiciaire','Jugement définitif ou PV de conciliation légalisé','Code du Travail / Arrêté n° 1314-25','2025-10-01',NULL,0,0),
        (NULL,1,'368','Indemnité de départ volontaire / Retraite','fixe',0,'Rupture & Fin de Contrat',0,'61715000','61715000','Plafonds selon barème légal','Exonérée sous conditions de l''accord DGI/CNSS','Convention de départ volontaire signée et légalisée','Code du Travail / Arrêté n° 1314-25','2025-10-01',NULL,0,0),
        (NULL,1,'369','Indemnité de préavis (dispensé)','fixe',0,'Rupture & Fin de Contrat',0,'61715000','61715000','Montant correspondant aux salaires','Assujettie sauf cas spécifiques d''exonération globale','Lettre de dispense de préavis','Code du Travail / Arrêté n° 1314-25','2025-10-01',NULL,0,0),
        (NULL,1,'370','Prime de fin de carrière','fixe',0,'Rupture & Fin de Contrat',0,'61715000','61715000','Selon convention collective','Exonérée si assimilée à l''indemnité de départ','Notification de mise à la retraite','Code du Travail / Arrêté n° 1314-25','2025-10-01',NULL,0,0),
        (NULL,1,'371','Indemnité compensatrice de logement','fixe',0,'Rupture & Fin de Contrat',0,'61715000','61715000','Frais réels ou barème','Exonérée si intégrée aux dommages et intérêts','Protocole d''accord transactionnel','Code du Travail / Arrêté n° 1314-25','2025-10-01',NULL,0,0),
        (NULL,1,'372','Indemnité de non-concurrence','fixe',0,'Rupture & Fin de Contrat',0,'61715000','61715000','Fixée par contrat','Exonérée si qualifiée de dommages et intérêts','Clause contractuelle et reçu pour solde de tout compte','Code du Travail / Arrêté n° 1314-25','2025-10-01',NULL,0,0),
        (NULL,1,'373','Indemnité de clientèle (VRP)','fixe',0,'Rupture & Fin de Contrat',0,'61715000','61715000','Selon préjudice commercial','Exonérée selon le Code du Travail','Calcul de la perte de clientèle validé par expert/tribunal','Code du Travail / Arrêté n° 1314-25','2025-10-01',NULL,0,0),
        (NULL,1,'374','Indemnité de reconversion professionnelle','fixe',0,'Rupture & Fin de Contrat',0,'61715000','61715000','Prise en charge de la formation','Exonérée si versée au centre de formation','Facture du centre de formation, plan de sauvegarde de l''emploi','Code du Travail / Arrêté n° 1314-25','2025-10-01',NULL,0,0),
        (NULL,1,'375','Indemnité de chômage technique / Partiel','fixe',0,'Rupture & Fin de Contrat',0,'61715000','61715000','Selon autorisations réglementaires','Exonérée en période de crise majeure','Autorisation du gouverneur ou décision ministérielle','Code du Travail / Arrêté n° 1314-25','2025-10-01',NULL,0,0),
        (NULL,1,'376','Indemnité transactionnelle globale','fixe',0,'Rupture & Fin de Contrat',0,'61715000','61715000','Limite des dommages légaux','Exonérée à hauteur des plafonds légaux','Protocole de transaction enregistré auprès des autorités','Code du Travail / Arrêté n° 1314-25','2025-10-01',NULL,0,0),
        (NULL,1,'377','Prime de tutorat / Fin de projet','fixe',0,'Rupture & Fin de Contrat',0,'61713000','61713000','Forfait contractuel','Exonéré si lié à un transfert d''outils de fin de contrat','Rapport de fin de mission validé par l''entreprise','Code du Travail / Arrêté n° 1314-25','2025-10-01',NULL,0,0);");
    echo "   + rubriques gains globales insérées\n";
} else {
    // Mettre à jour les métadonnées si colonnes existent mais données vides
    $needUpdate = $p->query("SELECT COUNT(*) FROM rubriques_gains WHERE is_global = 1 AND code BETWEEN '330' AND '377' AND categorie IS NULL AND societe_id IS NULL")->fetchColumn();
    if ($needUpdate > 0) {
        $p->exec("UPDATE rubriques_gains SET categorie='Transport & Déplacement', affectation='61713000', compte='61713000', plafond_dgi='500.00 DH / mois', plafond_cnss='500.00 DH / mois', justificatifs='Lieu de travail situé au milieu urbain de la ville', source='Arrêté n° 1314-25', source_maj='2025-10-01' WHERE code='330' AND societe_id IS NULL");
        $p->exec("UPDATE rubriques_gains SET categorie='Spécifiques à certains emplois', affectation='61713000', compte='61713000', plafond_dgi='10% du salaire de base', plafond_cnss='10% du salaire de base', justificatifs='Poste de direction, d''encadrement supérieur ou équivalent', source='Arrêté n° 1314-25', source_maj='2025-10-01' WHERE code='331' AND societe_id IS NULL");
        $p->exec("UPDATE rubriques_gains SET categorie='Transport & Déplacement', affectation='61713000', compte='61713000', plafond_dgi='3 DH / KM', plafond_cnss='3 DH / KM', justificatifs='Carnet de bord, carte grise au nom du salarié, trajet < 50 KM', source='Arrêté n° 1314-25', source_maj='2025-10-01' WHERE code='334' AND societe_id IS NULL");
        $p->exec("UPDATE rubriques_gains SET categorie='Transport & Déplacement', affectation='61713000', compte='61713000', plafond_dgi='1 500.00 DH / mois', plafond_cnss='1 500.00 DH / mois', justificatifs='Périmètre de déplacement limité à 50 KM, planning de tournée', source='Arrêté n° 1314-25', source_maj='2025-10-01' WHERE code='337' AND societe_id IS NULL");
        $p->exec("UPDATE rubriques_gains SET categorie='Transport & Déplacement', affectation='61713000', compte='61713000', plafond_dgi='Nourriture (10x SMIG hor.), Hébergement (30x SMIG hor.)', plafond_cnss='Totalement exonéré si justifié', justificatifs='Pièces justificatives (factures, tickets, ordre de mission)', source='Arrêté n° 1314-25', source_maj='2025-10-01' WHERE code='339' AND societe_id IS NULL");
        $p->exec("UPDATE rubriques_gains SET categorie='Transport & Déplacement', affectation='61713000', compte='61713000', plafond_dgi='Nourriture (10x SMIG hor.), Hébergement (30x SMIG hor.)', plafond_cnss='Repas: 171 DH/j, Hébergement: 513 DH/nuit', justificatifs='Ordre de mission stipulant la nature ponctuelle', source='Arrêté n° 1314-25', source_maj='2025-10-01' WHERE code='340' AND societe_id IS NULL");
        $p->exec("UPDATE rubriques_gains SET categorie='Transport & Déplacement', affectation='61713000', compte='61713000', plafond_dgi='<= 5000 DH et <= Salaire de base', plafond_cnss='Exonération dans la limite de 100% du S.B. (max 5000 DH/mois)', justificatifs='Déplacements professionnels hors périmètre urbain (> 50 km)', source='Arrêté n° 1314-25', source_maj='2025-10-01' WHERE code='341' AND societe_id IS NULL");
        $p->exec("UPDATE rubriques_gains SET categorie='Transport & Déplacement', affectation='61713000', compte='61713000', plafond_dgi='750.00 DH / mois', plafond_cnss='750.00 DH / mois', justificatifs='Lieu de travail situé en dehors du milieu urbain', source='Arrêté n° 1314-25', source_maj='2025-10-01' WHERE code='342' AND societe_id IS NULL");
        $p->exec("UPDATE rubriques_gains SET categorie='Spécifiques à certains emplois', affectation='61713000', compte='61713000', plafond_dgi='100 DH / mois', plafond_cnss='119.70 DH / 26 jours de travail', justificatifs='Le salarié doit être propriétaire de ses propres équipements', source='Arrêté n° 1314-25', source_maj='2025-10-01' WHERE code='343' AND societe_id IS NULL");
        $p->exec("UPDATE rubriques_gains SET categorie='Spécifiques à certains emplois', affectation='61713000', compte='61713000', plafond_dgi='210 DH / mois', plafond_cnss='239.40 DH / 26 jours de travail', justificatifs='Travaux salissants / insalubres (bleu de travail requis)', source='Arrêté n° 1314-25', source_maj='2025-10-01' WHERE code='344' AND societe_id IS NULL");
        $p->exec("UPDATE rubriques_gains SET categorie='Spécifiques à certains emplois', affectation='61713000', compte='61713000', plafond_dgi='Frais réels ou barème interne', plafond_cnss='Exonéré si port obligatoire pour le service', justificatifs='Obligation contractuelle ou règlement intérieur', source='Arrêté n° 1314-25', source_maj='2025-10-01' WHERE code='345' AND societe_id IS NULL");
        $p->exec("UPDATE rubriques_gains SET categorie='Spécifiques à certains emplois', affectation='61713000', compte='61713000', plafond_dgi='2x SMIG horaire par jour', plafond_cnss='Exonération selon plafond légal en vigueur', justificatifs='Horaires de nuit ou travail continu sans coupure', source='Arrêté n° 1314-25', source_maj='2025-10-01' WHERE code='346' AND societe_id IS NULL");
        $p->exec("UPDATE rubriques_gains SET categorie='Spécifiques à certains emplois', affectation='61713000', compte='61713000', plafond_dgi='Selon convention collective', plafond_cnss='Exonéré sous réserve d''un cadre réglementé', justificatifs='Attestation de conditions de travail pénibles', source='Arrêté n° 1314-25', source_maj='2025-10-01' WHERE code='347' AND societe_id IS NULL");
        $p->exec("UPDATE rubriques_gains SET categorie='Spécifiques à certains emplois', affectation='61713000', compte='61713000', plafond_dgi='Selon barème sectoriel', plafond_cnss='Exonéré si le risque est inhérent à la fonction', justificatifs='Fiche de poste, rapport d''évaluation des risques', source='Arrêté n° 1314-25', source_maj='2025-10-01' WHERE code='348' AND societe_id IS NULL");
        $p->exec("UPDATE rubriques_gains SET categorie='Spécifiques à certains emplois', affectation='61713000', compte='61713000', plafond_dgi='Selon convention collective', plafond_cnss='Exonéré si liée à des interventions urgentes hors horaires', justificatifs='Planning d''astreinte et rapports d''intervention', source='Arrêté n° 1314-25', source_maj='2025-10-01' WHERE code='349' AND societe_id IS NULL");
        $p->exec("UPDATE rubriques_gains SET categorie='Spécifiques à certains emplois', affectation='61713000', compte='61713000', plafond_dgi='Barème interne conventionné', plafond_cnss='Exonéré dans le cadre médical ou de sécurité', justificatifs='Registre des gardes effectuées', source='Arrêté n° 1314-25', source_maj='2025-10-01' WHERE code='350' AND societe_id IS NULL");
        $p->exec("UPDATE rubriques_gains SET categorie='Transport & Déplacement', affectation='61713000', compte='61713000', plafond_dgi='Charges supportées par l''entreprise', plafond_cnss='Totalement exonéré', justificatifs='Usage strictement professionnel ou convention d''affectation', source='Arrêté n° 1314-25', source_maj='2025-10-01' WHERE code='351' AND societe_id IS NULL");
        $p->exec("UPDATE rubriques_gains SET categorie='Transport & Déplacement', affectation='61713000', compte='61713000', plafond_dgi='Frais réels justifiés', plafond_cnss='Frais réels sur justificatifs ou barème officiel', justificatifs='Ordre de mission international, billets, factures hôtel', source='Arrêté n° 1314-25', source_maj='2025-10-01' WHERE code='352' AND societe_id IS NULL");
        $p->exec("UPDATE rubriques_gains SET categorie='Transport & Déplacement', affectation='61713000', compte='61713000', plafond_dgi='Frais réels sur factures', plafond_cnss='Exonéré si requis par l''employeur', justificatifs='Décision de mutation, factures du déménageur', source='Arrêté n° 1314-25', source_maj='2025-10-01' WHERE code='353' AND societe_id IS NULL");
        $p->exec("UPDATE rubriques_gains SET categorie='Caractère Social & Familial', affectation='61712000', compte='61712000', plafond_dgi='Plafond légal CNSS', plafond_cnss='Totalement exonéré', justificatifs='Livret de famille, attestation de non-paiement par ailleurs', source='Arrêté n° 1314-25', source_maj='2025-10-01' WHERE code='354' AND societe_id IS NULL");
        $p->exec("UPDATE rubriques_gains SET categorie='Caractère Social & Familial', affectation='61712000', compte='61712000', plafond_dgi='Plafond interne raisonnable', plafond_cnss='Exonéré si ponctuel', justificatifs='Extrait d''acte de naissance du nouveau-né', source='Arrêté n° 1314-25', source_maj='2025-10-01' WHERE code='355' AND societe_id IS NULL");
        $p->exec("UPDATE rubriques_gains SET categorie='Caractère Social & Familial', affectation='61712000', compte='61712000', plafond_dgi='Barème social de l''entreprise', plafond_cnss='Exonéré si ponctuel', justificatifs='Acte de mariage adoulé ou officiel', source='Arrêté n° 1314-25', source_maj='2025-10-01' WHERE code='356' AND societe_id IS NULL");
        $p->exec("UPDATE rubriques_gains SET categorie='Caractère Social & Familial', affectation='61712000', compte='61712000', plafond_dgi='Frais réels ou forfait social', plafond_cnss='Totalement exonéré', justificatifs='Certificat de décès du conjoint ou d''un ascendant/descendant direct', source='Arrêté n° 1314-25', source_maj='2025-10-01' WHERE code='357' AND societe_id IS NULL");
        $p->exec("UPDATE rubriques_gains SET categorie='Caractère Social & Familial', affectation='61712000', compte='61712000', plafond_dgi='Plafond par enfant/an', plafond_cnss='Exonéré si attribué aux enfants à charge', justificatifs='Certificat de scolarité annuel', source='Arrêté n° 1314-25', source_maj='2025-10-01' WHERE code='358' AND societe_id IS NULL");
        $p->exec("UPDATE rubriques_gains SET categorie='Caractère Social & Familial', affectation='61712000', compte='61712000', plafond_dgi='Plafond annuel (ex: 10% SMIG)', plafond_cnss='Exonéré dans la limite du plafond social', justificatifs='Distribution générale à l''occasion de fêtes (Aïd, Achoura, etc.)', source='Arrêté n° 1314-25', source_maj='2025-10-01' WHERE code='359' AND societe_id IS NULL");
        $p->exec("UPDATE rubriques_gains SET categorie='Spécifiques à certains emplois', affectation='61713000', compte='61713000', plafond_dgi='190 DH / mois', plafond_cnss='239.40 DH / 26 jours de travail', justificatifs='Poste de caissier ou manipulation effective de fonds', source='Arrêté n° 1314-25', source_maj='2025-10-01' WHERE code='360' AND societe_id IS NULL");
        $p->exec("UPDATE rubriques_gains SET categorie='Caractère Social & Familial', affectation='61712000', compte='61712000', plafond_dgi='Plafond par ticket / jour', plafond_cnss='Exonéré selon la quote-part patronale réglementaire', justificatifs='Factures du prestataire de restauration ou émetteur de titres', source='Arrêté n° 1314-25', source_maj='2025-10-01' WHERE code='361' AND societe_id IS NULL");
        $p->exec("UPDATE rubriques_gains SET categorie='Caractère Social & Familial', affectation='61712000', compte='61712000', plafond_dgi='Sur dossier médical', plafond_cnss='Exonéré si géré par le fonds social / mutuelle', justificatifs='Décompte AMO/Mutuelle et ordonnances restées à charge', source='Arrêté n° 1314-25', source_maj='2025-10-01' WHERE code='362' AND societe_id IS NULL");
        $p->exec("UPDATE rubriques_gains SET categorie='Caractère Social & Familial', affectation='61712000', compte='61712000', plafond_dgi='Plafond annuel fixe', plafond_cnss='Exonéré si géré via les œuvres sociales (COS)', justificatifs='Factures d''organismes de vacances ou convention COS', source='Arrêté n° 1314-25', source_maj='2025-10-01' WHERE code='363' AND societe_id IS NULL");
        $p->exec("UPDATE rubriques_gains SET categorie='Caractère Social & Familial', affectation='61712000', compte='61712000', plafond_dgi='Forfait ponctuel motivé', plafond_cnss='Exonéré si situation de précarité avérée', justificatifs='Dossier d''assistante sociale ou justificatifs de force majeure', source='Arrêté n° 1314-25', source_maj='2025-10-01' WHERE code='364' AND societe_id IS NULL");
        $p->exec("UPDATE rubriques_gains SET categorie='Caractère Social & Familial', affectation='61712000', compte='61712000', plafond_dgi='Selon mérite et critères sociaux', plafond_cnss='Exonéré si versé directement à l''établissement', justificatifs='Facture de l''école/université, attestation de réussite', source='Arrêté n° 1314-25', source_maj='2025-10-01' WHERE code='365' AND societe_id IS NULL");
        $p->exec("UPDATE rubriques_gains SET categorie='Rupture & Fin de Contrat', affectation='61715000', compte='61715000', plafond_dgi='Barème du Code du Travail', plafond_cnss='Totalement exonérée de CNSS et DGI', justificatifs='Lettre de licenciement, PV de l''inspecteur du travail / tribunal', source='Code du Travail / Arrêté n° 1314-25', source_maj='2025-10-01' WHERE code='366' AND societe_id IS NULL");
        $p->exec("UPDATE rubriques_gains SET categorie='Rupture & Fin de Contrat', affectation='61715000', compte='61715000', plafond_dgi='Fixée par tribunal ou conciliation', plafond_cnss='Exonérée selon la limite légale ou judiciaire', justificatifs='Jugement définitif ou PV de conciliation légalisé', source='Code du Travail / Arrêté n° 1314-25', source_maj='2025-10-01' WHERE code='367' AND societe_id IS NULL");
        $p->exec("UPDATE rubriques_gains SET categorie='Rupture & Fin de Contrat', affectation='61715000', compte='61715000', plafond_dgi='Plafonds selon barème légal', plafond_cnss='Exonérée sous conditions de l''accord DGI/CNSS', justificatifs='Convention de départ volontaire signée et légalisée', source='Code du Travail / Arrêté n° 1314-25', source_maj='2025-10-01' WHERE code='368' AND societe_id IS NULL");
        $p->exec("UPDATE rubriques_gains SET categorie='Rupture & Fin de Contrat', affectation='61715000', compte='61715000', plafond_dgi='Montant correspondant aux salaires', plafond_cnss='Assujettie sauf cas spécifiques d''exonération globale', justificatifs='Lettre de dispense de préavis', source='Code du Travail / Arrêté n° 1314-25', source_maj='2025-10-01' WHERE code='369' AND societe_id IS NULL");
        $p->exec("UPDATE rubriques_gains SET categorie='Rupture & Fin de Contrat', affectation='61715000', compte='61715000', plafond_dgi='Selon convention collective', plafond_cnss='Exonérée si assimilée à l''indemnité de départ', justificatifs='Notification de mise à la retraite', source='Code du Travail / Arrêté n° 1314-25', source_maj='2025-10-01' WHERE code='370' AND societe_id IS NULL");
        $p->exec("UPDATE rubriques_gains SET categorie='Rupture & Fin de Contrat', affectation='61715000', compte='61715000', plafond_dgi='Frais réels ou barème', plafond_cnss='Exonérée si intégrée aux dommages et intérêts', justificatifs='Protocole d''accord transactionnel', source='Code du Travail / Arrêté n° 1314-25', source_maj='2025-10-01' WHERE code='371' AND societe_id IS NULL");
        $p->exec("UPDATE rubriques_gains SET categorie='Rupture & Fin de Contrat', affectation='61715000', compte='61715000', plafond_dgi='Fixée par contrat', plafond_cnss='Exonérée si qualifiée de dommages et intérêts', justificatifs='Clause contractuelle et reçu pour solde de tout compte', source='Code du Travail / Arrêté n° 1314-25', source_maj='2025-10-01' WHERE code='372' AND societe_id IS NULL");
        $p->exec("UPDATE rubriques_gains SET categorie='Rupture & Fin de Contrat', affectation='61715000', compte='61715000', plafond_dgi='Selon préjudice commercial', plafond_cnss='Exonérée selon le Code du Travail', justificatifs='Calcul de la perte de clientèle validé par expert/tribunal', source='Code du Travail / Arrêté n° 1314-25', source_maj='2025-10-01' WHERE code='373' AND societe_id IS NULL");
        $p->exec("UPDATE rubriques_gains SET categorie='Rupture & Fin de Contrat', affectation='61715000', compte='61715000', plafond_dgi='Prise en charge de la formation', plafond_cnss='Exonérée si versée au centre de formation', justificatifs='Facture du centre de formation, plan de sauvegarde de l''emploi', source='Code du Travail / Arrêté n° 1314-25', source_maj='2025-10-01' WHERE code='374' AND societe_id IS NULL");
        $p->exec("UPDATE rubriques_gains SET categorie='Rupture & Fin de Contrat', affectation='61715000', compte='61715000', plafond_dgi='Selon autorisations réglementaires', plafond_cnss='Exonérée en période de crise majeure', justificatifs='Autorisation du gouverneur ou décision ministérielle', source='Code du Travail / Arrêté n° 1314-25', source_maj='2025-10-01' WHERE code='375' AND societe_id IS NULL");
        $p->exec("UPDATE rubriques_gains SET categorie='Rupture & Fin de Contrat', affectation='61715000', compte='61715000', plafond_dgi='Limite des dommages légaux', plafond_cnss='Exonérée à hauteur des plafonds légaux', justificatifs='Protocole de transaction enregistré auprès des autorités', source='Code du Travail / Arrêté n° 1314-25', source_maj='2025-10-01' WHERE code='376' AND societe_id IS NULL");
        $p->exec("UPDATE rubriques_gains SET categorie='Rupture & Fin de Contrat', affectation='61713000', compte='61713000', plafond_dgi='Forfait contractuel', plafond_cnss='Exonéré si lié à un transfert d''outils de fin de contrat', justificatifs='Rapport de fin de mission validé par l''entreprise', source='Code du Travail / Arrêté n° 1314-25', source_maj='2025-10-01' WHERE code='377' AND societe_id IS NULL");
        echo "   + métadonnées mises à jour pour $needUpdate rubriques gains\n";
    }
}

// === Rubriques retenues globales ===
$existing = $p->query("SELECT COUNT(*) FROM rubriques_retenues WHERE is_global = 1")->fetchColumn();
if (!$existing) {
    $p->exec("INSERT IGNORE INTO rubriques_retenues (societe_id, is_global, code, libelle, type_montant, valeur_defaut) VALUES
        (NULL, 1, 'AVANCE', 'Avance sur salaire', 'fixe', 0),
        (NULL, 1, 'PRET', 'Prêt personnel', 'fixe', 0),
        (NULL, 1, 'PRET_LOGEMENT', 'Prêt logement', 'fixe', 0),
        (NULL, 1, 'COTIS_SYNDICALE', 'Cotisation syndicale', 'fixe', 0),
        (NULL, 1, 'PENSION_ALIMENT', 'Pension alimentaire', 'fixe', 0),
        (NULL, 1, 'SAISIE_ARRET', 'Saisie-arrêt', 'fixe', 0)");
    echo "   + rubriques retenues globales insérées\n";
}

// === RIB : VARCHAR(40) → VARCHAR(255) pour stocker la valeur chiffrée ===
$needRibResize = $p->query("SHOW COLUMNS FROM societes LIKE 'rib'")->fetch()['Type'] === 'varchar(40)';
if ($needRibResize) {
    $p->exec("ALTER TABLE societes MODIFY COLUMN rib VARCHAR(255)");
    $p->exec("ALTER TABLE salaries MODIFY COLUMN rib VARCHAR(255)");
    echo "   + rib étendu à VARCHAR(255) (societes + salaries)\n";
}

// === Pénalités CNSS/AMO/TFP dans periodes ===
addCol($p, 'periodes', 'penalites_cnss DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER cloturee');
addCol($p, 'periodes', 'penalites_tfp DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER penalites_cnss');
addCol($p, 'periodes', 'penalites_amo DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER penalites_tfp');

// === Taux pénalités dans parametres_cnss_amo ===
addCol($p, 'parametres_cnss_amo', 'taux_penalites_cnss DECIMAL(5,2) NOT NULL DEFAULT 0.00 AFTER participation_amo');
addCol($p, 'parametres_cnss_amo', 'taux_penalites_tfp DECIMAL(5,2) NOT NULL DEFAULT 0.00 AFTER taux_penalites_cnss');
addCol($p, 'parametres_cnss_amo', 'taux_penalites_amo DECIMAL(5,2) NOT NULL DEFAULT 0.00 AFTER taux_penalites_tfp');

// === Taux total AMO cotisation ===
addCol($p, 'parametres_cnss_amo', 'taux_amo_total DECIMAL(5,2) NOT NULL DEFAULT 6.37 AFTER taux_amo_patronal');

// === Détail des pénalités (règles marocaines) ===
addCol($p, 'parametres_cnss_amo', 'penalite_cnss_premier_mois DECIMAL(5,2) NOT NULL DEFAULT 3.00 AFTER taux_penalites_amo');
addCol($p, 'parametres_cnss_amo', 'penalite_cnss_mois_suivants DECIMAL(5,2) NOT NULL DEFAULT 0.50 AFTER penalite_cnss_premier_mois');
addCol($p, 'parametres_cnss_amo', 'penalite_amo_taux DECIMAL(5,2) NOT NULL DEFAULT 1.00 AFTER penalite_cnss_mois_suivants');
addCol($p, 'parametres_cnss_amo', 'astreinte_cnss_par_salarie DECIMAL(10,2) NOT NULL DEFAULT 50.00 AFTER penalite_amo_taux');
addCol($p, 'parametres_cnss_amo', 'astreinte_amo_par_salarie DECIMAL(10,2) NOT NULL DEFAULT 100.00 AFTER astreinte_cnss_par_salarie');

addCol($p, 'rubriques_gains', 'compte VARCHAR(20) DEFAULT NULL AFTER affectation');
addCol($p, 'rubriques_gains', 'plafond_dgi_actif TINYINT(1) NOT NULL DEFAULT 0 AFTER justificatifs');
addCol($p, 'rubriques_gains', 'plafond_dgi_valeur DECIMAL(10,2) DEFAULT NULL AFTER plafond_dgi_actif');
addCol($p, 'rubriques_gains', 'plafond_dgi_type VARCHAR(50) DEFAULT NULL AFTER plafond_dgi_valeur');
addCol($p, 'rubriques_gains', 'plafond_cnss_actif TINYINT(1) NOT NULL DEFAULT 0 AFTER plafond_dgi_type');
addCol($p, 'rubriques_gains', 'plafond_cnss_valeur DECIMAL(10,2) DEFAULT NULL AFTER plafond_cnss_actif');
addCol($p, 'rubriques_gains', 'plafond_cnss_type VARCHAR(50) DEFAULT NULL AFTER plafond_cnss_valeur');
addCol($p, 'rubriques_gains', 'source VARCHAR(50) DEFAULT NULL AFTER plafond_cnss_type');
addCol($p, 'rubriques_gains', 'nature_edi VARCHAR(100) DEFAULT NULL AFTER source');
addCol($p, 'rubriques_gains', 'base_anciennete TINYINT(1) NOT NULL DEFAULT 0 AFTER nature_edi');
addCol($p, 'rubriques_gains', 'au_prorata TINYINT(1) NOT NULL DEFAULT 0 AFTER base_anciennete');
addCol($p, 'rubriques_gains', 'imposable_ir TINYINT(1) NOT NULL DEFAULT 1 AFTER au_prorata');
addCol($p, 'rubriques_gains', 'imposable_cnss TINYINT(1) NOT NULL DEFAULT 1 AFTER imposable_ir');

// Modifier type_montant pour inclure 'calcule'
try {
    $p->exec("ALTER TABLE rubriques_gains MODIFY COLUMN type_montant ENUM('fixe','proportionnel','calcule') NOT NULL DEFAULT 'fixe'");
    echo "  + type_montant: ajouté 'calcule'\n";
} catch (\PDOException $e) {
    // déjà modifié
}

// Copier affectation → compte pour les anciens enregistrements
$count = $p->exec("UPDATE rubriques_gains SET compte = affectation WHERE (compte IS NULL OR compte = '') AND (affectation IS NOT NULL AND affectation != '')");
if ($count > 0) echo "  + $count enregistrements: affectation → compte\n";

addCol($p, 'rubriques_gains', 'plafond_dgi_desc TEXT DEFAULT NULL AFTER plafond_cnss_type');
addCol($p, 'rubriques_gains', 'plafond_cnss_desc TEXT DEFAULT NULL AFTER plafond_dgi_desc');

// === Sources légales ===
$p->exec("CREATE TABLE IF NOT EXISTS sources_legales (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code            VARCHAR(20) NOT NULL UNIQUE,
    libelle         VARCHAR(200) NOT NULL,
    type            ENUM('loi','decret','arrete','note','circulaire','convention') NOT NULL,
    organisme       VARCHAR(50) NOT NULL,
    description     TEXT,
    reference_bo    VARCHAR(50),
    date_publication DATE,
    date_effet      DATE,
    url_officiel    VARCHAR(300),
    statut          ENUM('en_vigueur','modifie','abroge') DEFAULT 'en_vigueur',
    ordre           INT DEFAULT 0
) ENGINE=InnoDB");
echo "   + table sources_legales créée\n";

$existing = $p->query("SELECT COUNT(*) FROM sources_legales")->fetchColumn();
if (!$existing) {
    $p->exec("INSERT INTO sources_legales (code, libelle, type, organisme, description, reference_bo, date_publication, date_effet, statut, ordre) VALUES
        ('CT', 'Code du Travail (Loi n° 65-99)', 'loi', 'Inspection du Travail', 'Code du Travail marocain promulgué par la Loi n° 65-99. Régit les relations individuelles et collectives de travail.', 'BO n° 5210', '2003-09-11', '2004-06-08', 'en_vigueur', 1),
        ('DAHIR_CNSS', 'Dahir n° 1.72.184 — Régime de sécurité sociale', 'loi', 'CNSS', 'Dahir portant institution du régime de sécurité sociale au Maroc. Base légale de la CNSS.', 'BO n° 3120', '1972-07-27', '1972-10-01', 'modifie', 2),
        ('D266', 'Décret n° 2-25-266 — Application CNSS', 'decret', 'CNSS', 'Décret fixant les modalités d''application des dispositions relatives aux exonérations de cotisations CNSS.', 'BO n° 7443', '2025-04-24', '2025-10-01', 'en_vigueur', 3),
        ('A1314', 'Arrêté n° 1314-25 — Indemnités exonérées CNSS', 'arrete', 'CNSS', 'Arrêté du ministre de l''Économie et des Finances fixant la liste des indemnités exonérées de cotisations CNSS.', 'BO n° 7443', '2025-05-19', '2025-10-01', 'en_vigueur', 4),
        ('CGI', 'Code Général des Impôts', 'loi', 'DGI', 'Code Général des Impôts marocain. Définit les règles d''assujettissement à l''IR et à l''IS.', NULL, NULL, NULL, 'en_vigueur', 5),
        ('N16_2017', 'Note n° 16/2017 — Indemnités exonérées IR', 'note', 'DGI', 'Note circulaire de la DGI précisant la liste des indemnités exonérées de l''impôt sur le revenu.', NULL, '2017-01-01', '2017-01-01', 'en_vigueur', 6),
        ('LF', 'Loi de Finances', 'loi', 'DGI', 'Loi de Finances annuelle. Modifie chaque année les dispositions fiscales (barème IR, plafonds, etc.).', NULL, NULL, NULL, 'en_vigueur', 7),
        ('CCOLL', 'Conventions collectives sectorielles', 'convention', 'Inspection du Travail', 'Conventions collectives de travail applicables par secteur d''activité. Peuvent prévoir des indemnités spécifiques.', NULL, NULL, NULL, 'en_vigueur', 8)");
    echo "   + sources légales insérées\n";
}

// === Table pivot rubrique ↔ source ===
$p->exec("CREATE TABLE IF NOT EXISTS rubrique_sources_articles (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    rubrique_id     INT UNSIGNED NOT NULL,
    source_id       INT UNSIGNED NOT NULL,
    article         VARCHAR(50) NOT NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_rubrique_source (rubrique_id, source_id, article),
    FOREIGN KEY (rubrique_id) REFERENCES rubriques_gains(id) ON DELETE CASCADE,
    FOREIGN KEY (source_id) REFERENCES sources_legales(id) ON DELETE CASCADE
) ENGINE=InnoDB");
echo "   + table rubrique_sources_articles créée\n";

$existing = $p->query("SELECT COUNT(*) FROM rubrique_sources_articles")->fetchColumn();
if (!$existing) {
    $p->exec("INSERT INTO rubrique_sources_articles (rubrique_id, source_id, article)
        SELECT r.id, s.id, a.article
        FROM rubriques_gains r
        CROSS JOIN (SELECT 'CGI' AS c, 'Art. 57-1°' AS article, '330' AS code UNION ALL
                    SELECT 'CGI', 'Art. 57-1°', '331' UNION ALL
                    SELECT 'CGI', 'Art. 57-1°', '334' UNION ALL
                    SELECT 'CGI', 'Art. 57-1°', '337' UNION ALL
                    SELECT 'CGI', 'Art. 57-1°', '339' UNION ALL
                    SELECT 'CGI', 'Art. 57-1°', '340' UNION ALL
                    SELECT 'CGI', 'Art. 57-1°', '341' UNION ALL
                    SELECT 'CGI', 'Art. 57-1°', '342' UNION ALL
                    SELECT 'CGI', 'Art. 57-1°', '343' UNION ALL
                    SELECT 'CGI', 'Art. 57-1°', '344' UNION ALL
                    SELECT 'CGI', 'Art. 57-1°', '345' UNION ALL
                    SELECT 'CGI', 'Art. 57-1°', '346' UNION ALL
                    SELECT 'CGI', 'Art. 57-1°', '347' UNION ALL
                    SELECT 'CGI', 'Art. 57-1°', '348' UNION ALL
                    SELECT 'CGI', 'Art. 57-1°', '349' UNION ALL
                    SELECT 'CGI', 'Art. 57-1°', '350' UNION ALL
                    SELECT 'CGI', 'Art. 57-1°', '351' UNION ALL
                    SELECT 'CGI', 'Art. 57-1°', '352' UNION ALL
                    SELECT 'CGI', 'Art. 57-1°', '353' UNION ALL
                    SELECT 'CGI', 'Art. 57-1°', '354' UNION ALL
                    SELECT 'CGI', 'Art. 57-1°', '355' UNION ALL
                    SELECT 'CGI', 'Art. 57-1°', '356' UNION ALL
                    SELECT 'CGI', 'Art. 57-1°', '357' UNION ALL
                    SELECT 'CGI', 'Art. 57-1°', '358' UNION ALL
                    SELECT 'CGI', 'Art. 57-1°', '359' UNION ALL
                    SELECT 'CGI', 'Art. 57-1°', '360' UNION ALL
                    SELECT 'CGI', 'Art. 57-1°', '361' UNION ALL
                    SELECT 'CGI', 'Art. 57-1°', '362' UNION ALL
                    SELECT 'CGI', 'Art. 57-1°', '363' UNION ALL
                    SELECT 'CGI', 'Art. 57-1°', '364' UNION ALL
                    SELECT 'CGI', 'Art. 57-1°', '365' UNION ALL
                    SELECT 'CGI', 'Art. 57-7°', '366' UNION ALL
                    SELECT 'CGI', 'Art. 57-7°', '367' UNION ALL
                    SELECT 'CGI', 'Art. 57-7°', '368' UNION ALL
                    SELECT 'CGI', 'Art. 57 (soumis)', '501' UNION ALL
                    SELECT 'CGI', 'Art. 57 (soumis)', '502' UNION ALL
                    SELECT 'CGI', 'Art. 57 (soumis)', '503' UNION ALL
                    SELECT 'CGI', 'Art. 57 (soumis)', '504' UNION ALL
                    SELECT 'CGI', 'Art. 57 (soumis)', '505' UNION ALL
                    SELECT 'CT', 'Art. 53', '366' UNION ALL
                    SELECT 'CT', 'Art. 41', '367' UNION ALL
                    SELECT 'CT', 'Art. 43', '369' UNION ALL
                    SELECT 'CT', 'Art. 345-353', '501' UNION ALL
                    SELECT 'CT', 'Art. 345-353', '502' UNION ALL
                    SELECT 'CT', 'Art. 345-353', '503' UNION ALL
                    SELECT 'CT', 'Art. 345-353', '504' UNION ALL
                    SELECT 'CT', 'Art. 345', '505' UNION ALL
                    SELECT 'A1314', 'Titre I', '330' UNION ALL
                    SELECT 'A1314', 'Titre I', '334' UNION ALL
                    SELECT 'A1314', 'Titre I', '337' UNION ALL
                    SELECT 'A1314', 'Titre I', '339' UNION ALL
                    SELECT 'A1314', 'Titre I', '340' UNION ALL
                    SELECT 'A1314', 'Titre I', '341' UNION ALL
                    SELECT 'A1314', 'Titre I', '342' UNION ALL
                    SELECT 'A1314', 'Titre I', '351' UNION ALL
                    SELECT 'A1314', 'Titre I', '352' UNION ALL
                    SELECT 'A1314', 'Titre I', '353' UNION ALL
                    SELECT 'A1314', 'Titre II', '331' UNION ALL
                    SELECT 'A1314', 'Titre II', '343' UNION ALL
                    SELECT 'A1314', 'Titre II', '344' UNION ALL
                    SELECT 'A1314', 'Titre II', '345' UNION ALL
                    SELECT 'A1314', 'Titre II', '346' UNION ALL
                    SELECT 'A1314', 'Titre II', '347' UNION ALL
                    SELECT 'A1314', 'Titre II', '348' UNION ALL
                    SELECT 'A1314', 'Titre II', '349' UNION ALL
                    SELECT 'A1314', 'Titre II', '350' UNION ALL
                    SELECT 'A1314', 'Titre II', '360' UNION ALL
                    SELECT 'A1314', 'Titre V', '354' UNION ALL
                    SELECT 'A1314', 'Titre V', '355' UNION ALL
                    SELECT 'A1314', 'Titre V', '356' UNION ALL
                    SELECT 'A1314', 'Titre V', '357' UNION ALL
                    SELECT 'A1314', 'Titre V', '358' UNION ALL
                    SELECT 'A1314', 'Titre V', '359' UNION ALL
                    SELECT 'A1314', 'Titre V', '361' UNION ALL
                    SELECT 'A1314', 'Titre V', '362' UNION ALL
                    SELECT 'A1314', 'Titre V', '363' UNION ALL
                    SELECT 'A1314', 'Titre V', '364' UNION ALL
                    SELECT 'A1314', 'Titre V', '365' UNION ALL
                    SELECT 'A1314', 'Titre III', '366' UNION ALL
                    SELECT 'A1314', 'Titre III', '367' UNION ALL
                    SELECT 'A1314', 'Titre III', '368' UNION ALL
                    SELECT 'A1314', 'Titre III', '369' UNION ALL
                    SELECT 'A1314', 'Titre III', '370' UNION ALL
                    SELECT 'A1314', 'Titre III', '371' UNION ALL
                    SELECT 'A1314', 'Titre III', '372' UNION ALL
                    SELECT 'A1314', 'Titre III', '373' UNION ALL
                    SELECT 'A1314', 'Titre III', '374' UNION ALL
                    SELECT 'A1314', 'Titre III', '375' UNION ALL
                    SELECT 'A1314', 'Titre III', '376' UNION ALL
                    SELECT 'A1314', 'Titre VII', '377') AS a
        JOIN sources_legales s ON s.code = a.c
        WHERE r.code = a.code AND r.societe_id IS NULL");
    echo "   + articles par rubrique insérés\n";
}

// === Tables Barèmes et Réglages ===
$p->exec("CREATE TABLE IF NOT EXISTS bareme_anciennete (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    societe_id      INT UNSIGNED        NOT NULL,
    annees_min      TINYINT UNSIGNED    NOT NULL,
    annees_max      TINYINT UNSIGNED    NOT NULL,
    taux            DECIMAL(5,2)        NOT NULL,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (societe_id) REFERENCES societes(id) ON DELETE CASCADE
) ENGINE=InnoDB");
echo "   + table bareme_anciennete\n";

$p->exec("CREATE TABLE IF NOT EXISTS conge_annuel (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    societe_id      INT UNSIGNED        NOT NULL UNIQUE,
    jours_par_mois  DECIMAL(4,2)        NOT NULL DEFAULT 1.50,
    report_autorise TINYINT(1)          NOT NULL DEFAULT 1,
    report_max      TINYINT UNSIGNED    NOT NULL DEFAULT 15,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (societe_id) REFERENCES societes(id) ON DELETE CASCADE
) ENGINE=InnoDB");
echo "   + table conge_annuel\n";

// === Heures supplémentaires 25%/50%/100% dans paies ===
addCol($p, 'paies', 'heures_sup_25 DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER montant_heures_sup');
addCol($p, 'paies', 'heures_sup_50 DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER heures_sup_25');
addCol($p, 'paies', 'heures_sup_100 DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER heures_sup_50');
$affected = $p->exec("UPDATE paies SET heures_sup_25 = heures_supplementaires WHERE heures_sup_25 = 0 AND heures_supplementaires > 0");
if ($affected > 0) echo "   + anciennes heures_supplementaires copiées vers heures_sup_25\n";

// === Table des retenues personnalisées par paie ===
$p->exec("CREATE TABLE IF NOT EXISTS paie_retenues (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    paie_id        INT UNSIGNED NOT NULL,
    libelle        VARCHAR(200) NOT NULL,
    montant        DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    FOREIGN KEY (paie_id) REFERENCES paies(id) ON DELETE CASCADE
) ENGINE=InnoDB");
echo "   + table paie_retenues créée\n";

$p->exec("CREATE TABLE IF NOT EXISTS jours_feries (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    societe_id      INT UNSIGNED        NOT NULL,
    nom             VARCHAR(100)        NOT NULL,
    jour            TINYINT UNSIGNED    NOT NULL,
    mois            TINYINT UNSIGNED    NOT NULL,
    type            ENUM('fixe','variable') NOT NULL DEFAULT 'fixe',
    actif           TINYINT(1)          NOT NULL DEFAULT 1,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (societe_id) REFERENCES societes(id) ON DELETE CASCADE
) ENGINE=InnoDB");
echo "   + table jours_feries\n";

// Insérer les jours fériés par défaut pour chaque société existante
$existingJf = $p->query("SELECT COUNT(*) FROM jours_feries")->fetchColumn();
if (!$existingJf) {
    $societes = $p->query("SELECT id FROM societes")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($societes as $sid) {
        $p->exec("INSERT IGNORE INTO jours_feries (societe_id, nom, jour, mois, type, actif) VALUES
            ($sid, 'Jour de l''an', 1, 1, 'fixe', 1),
            ($sid, 'Fête du Trône', 30, 7, 'fixe', 1),
            ($sid, 'Fête de la révolution du Roi et du peuple', 20, 8, 'fixe', 1),
            ($sid, 'Anniversaire du Roi Mohammed VI', 21, 8, 'fixe', 1),
            ($sid, 'Fête de la Marche Verte', 6, 11, 'fixe', 1),
            ($sid, 'Fête de l''Indépendance', 18, 11, 'fixe', 1),
            ($sid, 'Fête du Travail', 1, 5, 'fixe', 1),
            ($sid, 'Aïd el-Fitr', 1, 1, 'variable', 1),
            ($sid, 'Aïd el-Adha', 1, 1, 'variable', 1),
            ($sid, '1er Moharram (Nouvel An islamique)', 1, 1, 'variable', 1),
            ($sid, 'Aïd al-Mawlid (Anniversaire du Prophète)', 1, 1, 'variable', 1)");
    }
    echo "   + jours fériés par défaut pour " . count($societes) . " société(s)\n";
}

// === Barème heures sup ===
$p->exec("CREATE TABLE IF NOT EXISTS bareme_heures_sup (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    societe_id      INT UNSIGNED        NOT NULL UNIQUE,
    taux_normal     DECIMAL(5,2)        NOT NULL DEFAULT 25.00,
    taux_majore     DECIMAL(5,2)        NOT NULL DEFAULT 50.00,
    taux_jour_ferie DECIMAL(5,2)        NOT NULL DEFAULT 100.00,
    seuil_heures    TINYINT UNSIGNED    NOT NULL DEFAULT 8,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (societe_id) REFERENCES societes(id) ON DELETE CASCADE
) ENGINE=InnoDB");
echo "   + table bareme_heures_sup\n";

// === Paie gains ===
$p->exec("CREATE TABLE IF NOT EXISTS paie_gains (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    paie_id        INT UNSIGNED NOT NULL,
    rubrique_id    INT UNSIGNED NOT NULL,
    montant        DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    FOREIGN KEY (paie_id) REFERENCES paies(id) ON DELETE CASCADE,
    FOREIGN KEY (rubrique_id) REFERENCES rubriques_gains(id) ON DELETE CASCADE,
    UNIQUE KEY unique_paie_rubrique (paie_id, rubrique_id)
) ENGINE=InnoDB");
echo "   + table paie_gains\n";

// === Barème SMIG / SMAG ===
$p->exec("CREATE TABLE IF NOT EXISTS bareme_smig_smag (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    societe_id      INT UNSIGNED        NOT NULL,
    annee           INT                 NOT NULL,
    type            ENUM('SMIG','SMAG') NOT NULL,
    horaire         DECIMAL(10,2)       NOT NULL DEFAULT 0.00,
    mensuel         DECIMAL(10,2)       NOT NULL DEFAULT 0.00,
    date_effet      DATE                DEFAULT NULL,
    updated_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (societe_id) REFERENCES societes(id) ON DELETE CASCADE,
    UNIQUE KEY unique_societe_annee_type (societe_id, annee, type)
) ENGINE=InnoDB");
echo "   + table bareme_smig_smag\n";

// === Colonne type dans paie_retenues ===
addCol($p, 'paie_retenues', "type ENUM('avance','pret','sanction','autre') NOT NULL DEFAULT 'autre' AFTER paie_id");

echo "\nMigrations terminées.\n";
