-- Mise à jour des rubriques de gains globales depuis le fichier MD
-- Liste exhaustive des 44 indemnités et primes (Décret n° 2-24-130)

-- ============================================================
-- I. Transport & Déplacement
-- ============================================================

UPDATE rubriques_gains SET
    categorie = 'Transport & Déplacement',
    libelle = 'Indemnité de déplacement justifiée',
    compte = '61713',
    plafond_dgi = 'Nourriture (10x SMIG hor.), Hébergement (30x SMIG hor.)',
    plafond_dgi_desc = 'Nourriture (10x SMIG hor.), Hébergement (30x SMIG hor.)',
    plafond_cnss = 'Totalement exonéré si justifié',
    plafond_cnss_desc = 'Totalement exonéré si justifié',
    justificatifs = 'Pièces justificatives (factures, tickets, ordre de mission)'
WHERE code = '339' AND is_global = 1;

UPDATE rubriques_gains SET
    categorie = 'Transport & Déplacement',
    libelle = 'Indemnité de déplacement forfaitaire ponctuelle',
    compte = '61713',
    plafond_dgi = 'Nourriture (10x SMIG hor.), Hébergement (30x SMIG hor.)',
    plafond_dgi_desc = 'Nourriture (10x SMIG hor.), Hébergement (30x SMIG hor.)',
    plafond_cnss = 'Repas: 171 DH/j, Hébergement: 513 DH/nuit',
    plafond_cnss_desc = 'Repas: 171 DH/j, Hébergement: 513 DH/nuit',
    justificatifs = 'Ordre de mission stipulant la nature ponctuelle'
WHERE code = '340' AND is_global = 1;

UPDATE rubriques_gains SET
    categorie = 'Transport & Déplacement',
    libelle = 'Indemnité de déplacement forfaitaire régulière',
    compte = '61713',
    plafond_dgi = '<= 5000 DH et <= Salaire de base',
    plafond_dgi_desc = '<= 5000 DH et <= Salaire de base',
    plafond_cnss = 'Exonération dans la limite de 100% du S.B. (max 5000 DH/mois)',
    plafond_cnss_desc = 'Exonération dans la limite de 100% du S.B. (max 5000 DH/mois)',
    justificatifs = 'Déplacements professionnels hors périmètre urbain (> 50 km)'
WHERE code = '341' AND is_global = 1;

UPDATE rubriques_gains SET
    categorie = 'Transport & Déplacement',
    libelle = 'Indemnité kilométrique',
    compte = '61713',
    plafond_dgi = '3 DH / KM',
    plafond_dgi_desc = '3 DH / KM',
    plafond_cnss = '3 DH / KM',
    plafond_cnss_desc = '3 DH / KM',
    justificatifs = 'Carnet de bord, carte grise au nom du salarié, trajet < 50 KM'
WHERE code = '334' AND is_global = 1;

UPDATE rubriques_gains SET
    categorie = 'Transport & Déplacement',
    libelle = 'Indemnité de transport urbain',
    compte = '61713',
    plafond_dgi = '500.00 DH / mois',
    plafond_dgi_desc = '500.00 DH / mois',
    plafond_cnss = '500.00 DH / mois',
    plafond_cnss_desc = '500.00 DH / mois',
    justificatifs = 'Lieu de travail situé au milieu urbain de la ville'
WHERE code = '330' AND is_global = 1;

UPDATE rubriques_gains SET
    categorie = 'Transport & Déplacement',
    libelle = 'Indemnité de transport hors urbain',
    compte = '61713',
    plafond_dgi = '750.00 DH / mois',
    plafond_dgi_desc = '750.00 DH / mois',
    plafond_cnss = '750.00 DH / mois',
    plafond_cnss_desc = '750.00 DH / mois',
    justificatifs = 'Lieu de travail situé en dehors du milieu urbain'
WHERE code = '342' AND is_global = 1;

UPDATE rubriques_gains SET
    categorie = 'Transport & Déplacement',
    libelle = 'Voiture de fonction ou de service',
    compte = '61713',
    plafond_dgi = 'Charges supportées par l''entreprise',
    plafond_dgi_desc = 'Charges supportées par l''entreprise',
    plafond_cnss = 'Totalement exonéré',
    plafond_cnss_desc = 'Totalement exonéré',
    justificatifs = 'Usage strictement professionnel ou convention d''affectation'
WHERE code = '351' AND is_global = 1;

UPDATE rubriques_gains SET
    categorie = 'Transport & Déplacement',
    libelle = 'Indemnité de tournée',
    compte = '61713',
    plafond_dgi = '1 500.00 DH / mois',
    plafond_dgi_desc = '1 500.00 DH / mois',
    plafond_cnss = '1 500.00 DH / mois',
    plafond_cnss_desc = '1 500.00 DH / mois',
    justificatifs = 'Périmètre de déplacement limité à 50 KM, planning de tournée'
WHERE code = '337' AND is_global = 1;

UPDATE rubriques_gains SET
    categorie = 'Transport & Déplacement',
    libelle = 'Indemnité de voyage à l''étranger',
    compte = '61713',
    plafond_dgi = 'Frais réels justifiés',
    plafond_dgi_desc = 'Frais réels justifiés',
    plafond_cnss = 'Frais réels sur justificatifs ou barème officiel',
    plafond_cnss_desc = 'Frais réels sur justificatifs ou barème officiel',
    justificatifs = 'Ordre de mission international, billets, factures hôtel'
WHERE code = '352' AND is_global = 1;

UPDATE rubriques_gains SET
    categorie = 'Transport & Déplacement',
    libelle = 'Indemnité de déménagement / mutation',
    compte = '61713',
    plafond_dgi = 'Frais réels sur factures',
    plafond_dgi_desc = 'Frais réels sur factures',
    plafond_cnss = 'Exonéré si requis par l''employeur',
    plafond_cnss_desc = 'Exonéré si requis par l''employeur',
    justificatifs = 'Décision de mutation, factures du déménageur'
WHERE code = '353' AND is_global = 1;

-- ============================================================
-- II. Spécifiques à certains emplois
-- ============================================================

UPDATE rubriques_gains SET
    categorie = 'Spécifiques à certains emplois',
    libelle = 'Indemnité de caisse (responsabilité pécuniaire)',
    compte = '61713',
    plafond_dgi = '190 DH / mois',
    plafond_dgi_desc = '190 DH / mois',
    plafond_cnss = '239.40 DH / 26 jours de travail',
    plafond_cnss_desc = '239.40 DH / 26 jours de travail',
    justificatifs = 'Poste de caissier ou manipulation effective de fonds'
WHERE code = '360' AND is_global = 1;

UPDATE rubriques_gains SET
    categorie = 'Spécifiques à certains emplois',
    libelle = 'Indemnité de représentation',
    compte = '61713',
    plafond_dgi = '10% du salaire de base',
    plafond_dgi_desc = '10% du salaire de base',
    plafond_cnss = '10% du salaire de base',
    plafond_cnss_desc = '10% du salaire de base',
    justificatifs = 'Poste de direction, d''encadrement supérieur ou équivalent'
WHERE code = '331' AND is_global = 1;

UPDATE rubriques_gains SET
    categorie = 'Spécifiques à certains emplois',
    libelle = 'Prime d''outillage',
    compte = '61713',
    plafond_dgi = '100 DH / mois',
    plafond_dgi_desc = '100 DH / mois',
    plafond_cnss = '119.70 DH / 26 jours de travail',
    plafond_cnss_desc = '119.70 DH / 26 jours de travail',
    justificatifs = 'Le salarié doit être propriétaire de ses propres équipements'
WHERE code = '343' AND is_global = 1;

UPDATE rubriques_gains SET
    categorie = 'Spécifiques à certains emplois',
    libelle = 'Prime de salissure',
    compte = '61713',
    plafond_dgi = '210 DH / mois',
    plafond_dgi_desc = '210 DH / mois',
    plafond_cnss = '239.40 DH / 26 jours de travail',
    plafond_cnss_desc = '239.40 DH / 26 jours de travail',
    justificatifs = 'Travaux salissants / insalubres (bleu de travail requis)'
WHERE code = '344' AND is_global = 1;

UPDATE rubriques_gains SET
    categorie = 'Spécifiques à certains emplois',
    libelle = 'Prime d''usure de vêtements / Tenue',
    compte = '61713',
    plafond_dgi = 'Frais réels ou barème interne',
    plafond_dgi_desc = 'Frais réels ou barème interne',
    plafond_cnss = 'Exonéré si port obligatoire pour le service',
    plafond_cnss_desc = 'Exonéré si port obligatoire pour le service',
    justificatifs = 'Obligation contractuelle ou règlement intérieur'
WHERE code = '345' AND is_global = 1;

UPDATE rubriques_gains SET
    categorie = 'Spécifiques à certains emplois',
    libelle = 'Indemnité de panier / Panier de nuit',
    compte = '61713',
    plafond_dgi = '2x SMIG horaire par jour',
    plafond_dgi_desc = '2x SMIG horaire par jour',
    plafond_cnss = 'Exonération selon plafond légal en vigueur',
    plafond_cnss_desc = 'Exonération selon plafond légal en vigueur',
    justificatifs = 'Horaires de nuit ou travail continu sans coupure'
WHERE code = '346' AND is_global = 1;

UPDATE rubriques_gains SET
    categorie = 'Spécifiques à certains emplois',
    libelle = 'Indemnité de pénibilité',
    compte = '61713',
    plafond_dgi = 'Selon convention collective',
    plafond_dgi_desc = 'Selon convention collective',
    plafond_cnss = 'Exonéré sous réserve d''un cadre réglementé',
    plafond_cnss_desc = 'Exonéré sous réserve d''un cadre réglementé',
    justificatifs = 'Attestation de conditions de travail pénibles'
WHERE code = '347' AND is_global = 1;

UPDATE rubriques_gains SET
    categorie = 'Spécifiques à certains emplois',
    libelle = 'Indemnité de risque / Danger',
    compte = '61713',
    plafond_dgi = 'Selon barème sectoriel',
    plafond_dgi_desc = 'Selon barème sectoriel',
    plafond_cnss = 'Exonéré si le risque est inhérent à la fonction',
    plafond_cnss_desc = 'Exonéré si le risque est inhérent à la fonction',
    justificatifs = 'Fiche de poste, rapport d''évaluation des risques'
WHERE code = '348' AND is_global = 1;

UPDATE rubriques_gains SET
    categorie = 'Spécifiques à certains emplois',
    libelle = 'Indemnité d''astreinte',
    compte = '61713',
    plafond_dgi = 'Selon convention collective',
    plafond_dgi_desc = 'Selon convention collective',
    plafond_cnss = 'Exonéré si liée à des interventions urgentes hors horaires',
    plafond_cnss_desc = 'Exonéré si liée à des interventions urgentes hors horaires',
    justificatifs = 'Planning d''astreinte et rapports d''intervention'
WHERE code = '349' AND is_global = 1;

UPDATE rubriques_gains SET
    categorie = 'Spécifiques à certains emplois',
    libelle = 'Indemnité de garde',
    compte = '61713',
    plafond_dgi = 'Barème interne conventionné',
    plafond_dgi_desc = 'Barème interne conventionné',
    plafond_cnss = 'Exonéré dans le cadre médical ou de sécurité',
    plafond_cnss_desc = 'Exonéré dans le cadre médical ou de sécurité',
    justificatifs = 'Registre des gardes effectuées'
WHERE code = '350' AND is_global = 1;

-- ============================================================
-- III. Caractère Social & Familial
-- ============================================================

UPDATE rubriques_gains SET
    categorie = 'Caractère Social & Familial',
    libelle = 'Allocations familiales additionnelles',
    compte = '61712',
    plafond_dgi = 'Plafond légal CNSS',
    plafond_dgi_desc = 'Plafond légal CNSS',
    plafond_cnss = 'Totalement exonéré',
    plafond_cnss_desc = 'Totalement exonéré',
    justificatifs = 'Livret de famille, attestation de non-paiement par ailleurs'
WHERE code = '354' AND is_global = 1;

UPDATE rubriques_gains SET
    categorie = 'Caractère Social & Familial',
    libelle = 'Allocation de naissance',
    compte = '61712',
    plafond_dgi = 'Plafond interne raisonnable',
    plafond_dgi_desc = 'Plafond interne raisonnable',
    plafond_cnss = 'Exonéré si ponctuel',
    plafond_cnss_desc = 'Exonéré si ponctuel',
    justificatifs = 'Extrait d''acte de naissance du nouveau-né'
WHERE code = '355' AND is_global = 1;

UPDATE rubriques_gains SET
    categorie = 'Caractère Social & Familial',
    libelle = 'Allocation de mariage',
    compte = '61712',
    plafond_dgi = 'Barème social de l''entreprise',
    plafond_dgi_desc = 'Barème social de l''entreprise',
    plafond_cnss = 'Exonéré si ponctuel',
    plafond_cnss_desc = 'Exonéré si ponctuel',
    justificatifs = 'Acte de mariage adoulé ou officiel'
WHERE code = '356' AND is_global = 1;

UPDATE rubriques_gains SET
    categorie = 'Caractère Social & Familial',
    libelle = 'Allocation de décès / Obsèques',
    compte = '61712',
    plafond_dgi = 'Frais réels ou forfait social',
    plafond_dgi_desc = 'Frais réels ou forfait social',
    plafond_cnss = 'Totalement exonéré',
    plafond_cnss_desc = 'Totalement exonéré',
    justificatifs = 'Certificat de décès du conjoint ou d''un ascendant/descendant direct'
WHERE code = '357' AND is_global = 1;

UPDATE rubriques_gains SET
    categorie = 'Caractère Social & Familial',
    libelle = 'Prime de scolarité / Rentrée scolaire',
    compte = '61712',
    plafond_dgi = 'Plafond par enfant/an',
    plafond_dgi_desc = 'Plafond par enfant/an',
    plafond_cnss = 'Exonéré si attribué aux enfants à charge',
    plafond_cnss_desc = 'Exonéré si attribué aux enfants à charge',
    justificatifs = 'Certificat de scolarité annuel'
WHERE code = '358' AND is_global = 1;

UPDATE rubriques_gains SET
    categorie = 'Caractère Social & Familial',
    libelle = 'Bons d''achat / Cadeaux de fin d''année',
    compte = '61712',
    plafond_dgi = 'Plafond annuel (ex: 10% SMIG)',
    plafond_dgi_desc = 'Plafond annuel (ex: 10% SMIG)',
    plafond_cnss = 'Exonéré dans la limite du plafond social',
    plafond_cnss_desc = 'Exonéré dans la limite du plafond social',
    justificatifs = 'Distribution générale à l''occasion de fêtes (Aïd, Achoura, etc.)'
WHERE code = '359' AND is_global = 1;

UPDATE rubriques_gains SET
    categorie = 'Caractère Social & Familial',
    libelle = 'Subvention de cantine / Titres repas',
    compte = '61712',
    plafond_dgi = 'Plafond par ticket / jour',
    plafond_dgi_desc = 'Plafond par ticket / jour',
    plafond_cnss = 'Exonéré selon la quote-part patronale réglementaire',
    plafond_cnss_desc = 'Exonéré selon la quote-part patronale réglementaire',
    justificatifs = 'Factures du prestataire de restauration ou émetteur de titres'
WHERE code = '361' AND is_global = 1;

UPDATE rubriques_gains SET
    categorie = 'Caractère Social & Familial',
    libelle = 'Prise en charge des frais médicaux non remboursés',
    compte = '61712',
    plafond_dgi = 'Sur dossier médical',
    plafond_dgi_desc = 'Sur dossier médical',
    plafond_cnss = 'Exonéré si géré par le fonds social / mutuelle',
    plafond_cnss_desc = 'Exonéré si géré par le fonds social / mutuelle',
    justificatifs = 'Décompte AMO/Mutuelle et ordonnances restées à charge'
WHERE code = '362' AND is_global = 1;

UPDATE rubriques_gains SET
    categorie = 'Caractère Social & Familial',
    libelle = 'Aide aux vacances / Estivage',
    compte = '61712',
    plafond_dgi = 'Plafond annuel fixe',
    plafond_dgi_desc = 'Plafond annuel fixe',
    plafond_cnss = 'Exonéré si géré via les œuvres sociales (COS)',
    plafond_cnss_desc = 'Exonéré si géré via les œuvres sociales (COS)',
    justificatifs = 'Factures d''organismes de vacances ou convention COS'
WHERE code = '363' AND is_global = 1;

UPDATE rubriques_gains SET
    categorie = 'Caractère Social & Familial',
    libelle = 'Secours exceptionnel / Social',
    compte = '61712',
    plafond_dgi = 'Forfait ponctuel motivé',
    plafond_dgi_desc = 'Forfait ponctuel motivé',
    plafond_cnss = 'Exonéré si situation de précarité avérée',
    plafond_cnss_desc = 'Exonéré si situation de précarité avérée',
    justificatifs = 'Dossier d''assistante sociale ou justificatifs de force majeure'
WHERE code = '364' AND is_global = 1;

UPDATE rubriques_gains SET
    categorie = 'Caractère Social & Familial',
    libelle = 'Bourses d''études pour les enfants',
    compte = '61712',
    plafond_dgi = 'Selon mérite et critères sociaux',
    plafond_dgi_desc = 'Selon mérite et critères sociaux',
    plafond_cnss = 'Exonéré si versé directement à l''établissement',
    plafond_cnss_desc = 'Exonéré si versé directement à l''établissement',
    justificatifs = 'Facture de l''école/université, attestation de réussite'
WHERE code = '365' AND is_global = 1;

-- ============================================================
-- IV. Rupture & Fin de Contrat
-- ============================================================

UPDATE rubriques_gains SET
    categorie = 'Rupture & Fin de Contrat',
    libelle = 'Indemnité légale de licenciement',
    compte = '61715',
    plafond_dgi = 'Barème du Code du Travail',
    plafond_dgi_desc = 'Barème du Code du Travail',
    plafond_cnss = 'Totalement exonérée de CNSS et DGI',
    plafond_cnss_desc = 'Totalement exonérée de CNSS et DGI',
    justificatifs = 'Lettre de licenciement, PV de l''inspecteur du travail / tribunal'
WHERE code = '366' AND is_global = 1;

UPDATE rubriques_gains SET
    categorie = 'Rupture & Fin de Contrat',
    libelle = 'Indemnité de licenciement abusive (dommages-intérêts)',
    compte = '61715',
    plafond_dgi = 'Fixée par tribunal ou conciliation',
    plafond_dgi_desc = 'Fixée par tribunal ou conciliation',
    plafond_cnss = 'Exonérée selon la limite légale ou judiciaire',
    plafond_cnss_desc = 'Exonérée selon la limite légale ou judiciaire',
    justificatifs = 'Jugement définitif ou PV de conciliation légalisé'
WHERE code = '367' AND is_global = 1;

UPDATE rubriques_gains SET
    categorie = 'Rupture & Fin de Contrat',
    libelle = 'Indemnité de départ volontaire / Retraite',
    compte = '61715',
    plafond_dgi = 'Plafonds selon barème légal',
    plafond_dgi_desc = 'Plafonds selon barème légal',
    plafond_cnss = 'Exonérée sous conditions de l''accord DGI/CNSS',
    plafond_cnss_desc = 'Exonérée sous conditions de l''accord DGI/CNSS',
    justificatifs = 'Convention de départ volontaire signée et légalisée'
WHERE code = '368' AND is_global = 1;

UPDATE rubriques_gains SET
    categorie = 'Rupture & Fin de Contrat',
    libelle = 'Indemnité de préavis (si dispensé par l''employeur)',
    compte = '61715',
    plafond_dgi = 'Montant correspondant aux salaires',
    plafond_dgi_desc = 'Montant correspondant aux salaires',
    plafond_cnss = 'Assujettie sauf cas spécifiques d''exonération globale',
    plafond_cnss_desc = 'Assujettie sauf cas spécifiques d''exonération globale',
    justificatifs = 'Lettre de dispense de préavis'
WHERE code = '369' AND is_global = 1;

UPDATE rubriques_gains SET
    categorie = 'Rupture & Fin de Contrat',
    libelle = 'Prime de fin de carrière',
    compte = '61715',
    plafond_dgi = 'Selon convention collective',
    plafond_dgi_desc = 'Selon convention collective',
    plafond_cnss = 'Exonérée si assimilée à l''indemnité de départ',
    plafond_cnss_desc = 'Exonérée si assimilée à l''indemnité de départ',
    justificatifs = 'Notification de mise à la retraite'
WHERE code = '370' AND is_global = 1;

UPDATE rubriques_gains SET
    categorie = 'Rupture & Fin de Contrat',
    libelle = 'Indemnité compensatrice de logement (rupture)',
    compte = '61715',
    plafond_dgi = 'Frais réels ou barème',
    plafond_dgi_desc = 'Frais réels ou barème',
    plafond_cnss = 'Exonérée si intégrée aux dommages et intérêts',
    plafond_cnss_desc = 'Exonérée si intégrée aux dommages et intérêts',
    justificatifs = 'Protocole d''accord transactionnel'
WHERE code = '371' AND is_global = 1;

UPDATE rubriques_gains SET
    categorie = 'Rupture & Fin de Contrat',
    libelle = 'Indemnité de non-concurrence (cas spécifiques)',
    compte = '61715',
    plafond_dgi = 'Fixée par contrat',
    plafond_dgi_desc = 'Fixée par contrat',
    plafond_cnss = 'Exonérée si qualifiée de dommages et intérêts',
    plafond_cnss_desc = 'Exonérée si qualifiée de dommages et intérêts',
    justificatifs = 'Clause contractuelle et reçu pour solde de tout compte'
WHERE code = '372' AND is_global = 1;

UPDATE rubriques_gains SET
    categorie = 'Rupture & Fin de Contrat',
    libelle = 'Indemnité de clientèle (VRP)',
    compte = '61715',
    plafond_dgi = 'Selon préjudice commercial',
    plafond_dgi_desc = 'Selon préjudice commercial',
    plafond_cnss = 'Exonérée selon le Code du Travail',
    plafond_cnss_desc = 'Exonérée selon le Code du Travail',
    justificatifs = 'Calcul de la perte de clientèle validé par expert/tribunal'
WHERE code = '373' AND is_global = 1;

UPDATE rubriques_gains SET
    categorie = 'Rupture & Fin de Contrat',
    libelle = 'Indemnité de reconversion professionnelle',
    compte = '61715',
    plafond_dgi = 'Prise en charge de la formation',
    plafond_dgi_desc = 'Prise en charge de la formation',
    plafond_cnss = 'Exonérée si versée au centre de formation',
    plafond_cnss_desc = 'Exonérée si versée au centre de formation',
    justificatifs = 'Facture du centre de formation, plan de sauvegarde de l''emploi'
WHERE code = '374' AND is_global = 1;

UPDATE rubriques_gains SET
    categorie = 'Rupture & Fin de Contrat',
    libelle = 'Indemnité de chômage technique / Partiel',
    compte = '61715',
    plafond_dgi = 'Selon autorisations réglementaires',
    plafond_dgi_desc = 'Selon autorisations réglementaires',
    plafond_cnss = 'Exonérée en période de crise majeure',
    plafond_cnss_desc = 'Exonérée en période de crise majeure',
    justificatifs = 'Autorisation du gouverneur ou décision ministérielle'
WHERE code = '375' AND is_global = 1;

UPDATE rubriques_gains SET
    categorie = 'Rupture & Fin de Contrat',
    libelle = 'Indemnité transactionnelle globale',
    compte = '61715',
    plafond_dgi = 'Limite des dommages légaux',
    plafond_dgi_desc = 'Limite des dommages légaux',
    plafond_cnss = 'Exonérée à hauteur des plafonds légaux',
    plafond_cnss_desc = 'Exonérée à hauteur des plafonds légaux',
    justificatifs = 'Protocole de transaction enregistré auprès des autorités'
WHERE code = '376' AND is_global = 1;

UPDATE rubriques_gains SET
    categorie = 'Rupture & Fin de Contrat',
    libelle = 'Prime de tutorat / Fin de projet',
    compte = '61713',
    plafond_dgi = 'Forfait contractuel',
    plafond_dgi_desc = 'Forfait contractuel',
    plafond_cnss = 'Exonéré si lié à un transfert d''outils de fin de contrat',
    plafond_cnss_desc = 'Exonéré si lié à un transfert d''outils de fin de contrat',
    justificatifs = 'Rapport de fin de mission validé par l''entreprise'
WHERE code = '377' AND is_global = 1;
