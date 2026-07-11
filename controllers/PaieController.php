<?php

namespace Controllers;

use Core\Controller;
use Core\Model;
use Core\PaieCalculator;
use Core\Session;
use Core\Validator;
use Core\Audit;
use PDO;

class PaieController extends Controller
{
    private PDO $db;
    private PaieCalculator $calculator;

    public function __construct()
    {
        if (!Session::has('user_id')) {
            $this->redirect('/paie-me/login');
        }
        $this->db = Model::db();
        $this->calculator = new PaieCalculator($this->db);
    }

    public function index(): void
    {
        $userId = Session::get('user_id');
        $ctx = Session::get('societe_context');
        $sql = "
            SELECT p.*, so.raison_sociale,
                (SELECT COUNT(*) FROM paies WHERE periode_id = p.id) as nb_paies
            FROM periodes p
            JOIN societes so ON p.societe_id = so.id
            WHERE so.user_id = $userId
        ";
        if ($ctx) {
            $sql .= " AND p.societe_id = " . (int)$ctx['id'];
        }
        $sql .= " ORDER BY p.annee DESC, p.mois DESC";
        $periodes = $this->db->query($sql)->fetchAll();

        $this->render('paies/index.php', [
            'title'    => 'Paies',
            'periodes' => $periodes,
        ]);
    }

    public function create(): void
    {
        $userId = Session::get('user_id');
        $ctx = Session::get('societe_context');

        if ($ctx) {
            $fromSociete = (int)$ctx['id'];
            $societes = [$ctx];
        } else {
            $societes = $this->db->query("SELECT id, raison_sociale FROM societes WHERE user_id = $userId ORDER BY raison_sociale")->fetchAll();
            $fromSociete = isset($_GET['from_societe']) ? (int) $_GET['from_societe'] : null;
        }

        if ($this->isPost()) {
            $this->checkCsrf();
            $v = new Validator($_POST);
            $v->required('societe_id', 'Société')
              ->required('mois', 'Mois')
              ->required('annee', 'Année')
              ->numeric('mois', 'Mois')
              ->numeric('annee', 'Année')
              ->date('date_debut', 'Date début')
              ->date('date_fin', 'Date fin');

            if (!$v->passes()) {
                Session::setFlash('error', $v->firstError());
                $this->redirect('/paie-me/paies/create');
            }

            $societeId  = $ctx ? (int)$ctx['id'] : (int) $_POST['societe_id'];
            $mois       = (int) $_POST['mois'];
            $annee      = (int) $_POST['annee'];
            $dateDebut  = $_POST['date_debut'];
            $dateFin    = $_POST['date_fin'];

            $existing = $this->db->prepare("SELECT id FROM periodes WHERE societe_id = ? AND mois = ? AND annee = ?");
            $existing->execute([$societeId, $mois, $annee]);
            if ($existing->fetch()) {
                Session::setFlash('error', 'Cette période existe déjà pour cette société.');
                $this->redirect('/paie-me/paies/create');
            }

            $stmt = $this->db->prepare("
                INSERT INTO periodes (societe_id, mois, annee, date_debut, date_fin)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$societeId, $mois, $annee, $dateDebut, $dateFin]);
            $periodeId = $this->db->lastInsertId();

            Audit::log($this->db, 'create', 'periode', (int) $periodeId, 'Création période: ' . $mois . '/' . $annee);

            Session::setFlash('success', 'Période créée avec succès.');
            $this->redirect('/paie-me/paies/' . $periodeId . '/lignes');
        }

        $this->render('paies/form.php', [
            'title'       => 'Nouvelle période de paie',
            'societes'    => $societes,
            'fromSociete' => $fromSociete,
        ]);
    }

    public function calculate(int $id): void
    {
        $userId = Session::get('user_id');
        $periode = $this->db->query("
            SELECT p.* FROM periodes p
            JOIN societes so ON p.societe_id = so.id
            WHERE p.id = $id AND so.user_id = $userId
        ")->fetch();

        if (!$periode) {
            Session::setFlash('error', 'Période introuvable.');
            $this->redirect('/paie-me/paies');
        }

        if (!empty($periode['cloturee'])) {
            Session::setFlash('error', 'Cette période est clôturée. Vous ne pouvez plus la modifier.');
            $this->redirect('/paie-me/paies');
        }

        $existingPaies = $this->db->query("SELECT id, salarie_id, jours_travailles, jours_conge, jours_feries, heures_supplementaires, heures_sup_25, heures_sup_50, heures_sup_100, indemnite_transport, indemnite_panier, indemnite_representation, avantage_logement FROM paies WHERE periode_id = $id")->fetchAll();
        $heuresSup25Map = [];
        $heuresSup50Map = [];
        $heuresSup100Map = [];
        $gainsOverridesMap = [];
        $indemnitesMap = [];
        $retenuesOverridesMap = [];
        foreach ($existingPaies as $ep) {
            $sId = (int) $ep['salarie_id'];
            $heuresSup25Map[$sId] = (float) ($ep['heures_sup_25'] ?? 0);
            $heuresSup50Map[$sId] = (float) ($ep['heures_sup_50'] ?? 0);
            $heuresSup100Map[$sId] = (float) ($ep['heures_sup_100'] ?? 0);
            $indemnitesMap[$sId] = [
                'jours_travailles' => (int) ($ep['jours_travailles'] ?? 30),
                'jours_conge' => (float) ($ep['jours_conge'] ?? 0),
                'jours_feries' => (float) ($ep['jours_feries'] ?? 0),
                'indemnite_transport' => (float) $ep['indemnite_transport'],
                'indemnite_panier' => (float) $ep['indemnite_panier'],
                'indemnite_representation' => (float) $ep['indemnite_representation'],
                'avantage_logement' => (float) $ep['avantage_logement'],
            ];
            $pId = (int) $ep['id'];
            $pgs = $this->db->query("SELECT rubrique_id, montant FROM paie_gains WHERE paie_id = $pId")->fetchAll();
            foreach ($pgs as $pg) {
                $gainsOverridesMap[$sId][(int) $pg['rubrique_id']] = (float) $pg['montant'];
            }
            $prs = $this->db->query("SELECT type, libelle, montant FROM paie_retenues WHERE paie_id = $pId")->fetchAll();
            if (!empty($prs)) {
                $retenuesOverridesMap[$sId] = $prs;
            }
        }

        if (count($existingPaies) > 0) {
            $this->db->exec("DELETE FROM paies WHERE periode_id = $id");
        }

        $insertGain = null;
        $insertRet = null;

        $societeId = $periode['societe_id'];
        $dateDebut = $periode['date_debut'];
        $dateFin = $periode['date_fin'];
        $cnssParams = $this->db->query("SELECT * FROM parametres_cnss_amo WHERE societe_id = $societeId")->fetch() ?: [];
        $baremeHS = $this->db->query("SELECT * FROM bareme_heures_sup WHERE societe_id = $societeId")->fetch() ?: [];
        $gains = $this->gainsAuto($societeId);
        $retenues = $this->mergeRubriques('rubriques_retenues', $societeId);

        $salaries = $this->db->query("SELECT id, salaire_base, date_embauche, date_sortie, situation_familiale, indemnite_transport, indemnite_panier, indemnite_representation, avantage_logement, nb_enfants, avances_salaire, mutuelle FROM salaries WHERE societe_id = $societeId AND actif = 1")->fetchAll();

        $indemnitesCustomCache = [];
        $tmpRows = $this->db->query("SELECT salarie_id, libelle, montant, plafond_dgi, plafond_cnss FROM salarie_indemnites WHERE actif = 1 ORDER BY id")->fetchAll();
        foreach ($tmpRows as $tmpR) {
            $indemnitesCustomCache[(int) $tmpR['salarie_id']][] = $tmpR;
        }
        unset($tmpRows);

        $salarieGainsCache = [];
        $tmpSG = $this->db->query("SELECT sg.salarie_id, rg.id as rubrique_id, rg.code, rg.libelle, rg.type_montant, rg.valeur_defaut, rg.imposable FROM salarie_gains sg JOIN rubriques_gains rg ON sg.rubrique_id = rg.id WHERE sg.actif = 1 AND rg.actif = 1 ORDER BY rg.code")->fetchAll();
        foreach ($tmpSG as $tmpR) {
            $salarieGainsCache[(int) $tmpR['salarie_id']][] = $tmpR;
        }
        unset($tmpSG);

        foreach ($salaries as $s) {
            $hs25 = $heuresSup25Map[(int) $s['id']] ?? 0;
            $hs50 = $heuresSup50Map[(int) $s['id']] ?? 0;
            $hs100 = $heuresSup100Map[(int) $s['id']] ?? 0;
            $indemnOverrides = $indemnitesMap[(int) $s['id']] ?? [];
            if ($indemnOverrides) {
                $s['indemnite_transport'] = $indemnOverrides['indemnite_transport'];
                $s['indemnite_panier'] = $indemnOverrides['indemnite_panier'];
                $s['indemnite_representation'] = $indemnOverrides['indemnite_representation'];
                $s['avantage_logement'] = $indemnOverrides['avantage_logement'];
            }
            $jc = (float) ($indemnOverrides['jours_conge'] ?? 0);
            $jf = (float) ($indemnOverrides['jours_feries'] ?? 0);
            $ic = $indemnitesCustomCache[(int) $s['id']] ?? [];
            $gainsSalarie = $salarieGainsCache[(int) $s['id']] ?? [];
            $mergedGains = $this->mergeGainsWithSalarie($gains, $gainsSalarie);
            $c = $this->calculator->calculerPaie($s, $cnssParams, $dateFin, $hs25, $hs50, $hs100, $mergedGains, $retenues, $dateDebut, $baremeHS, null, $jc, $jf, $ic);

            $stmtPaie = $this->db->prepare("
                INSERT INTO paies (periode_id, salarie_id, societe_id, jours_travailles, salaire_brut, sbi, prime_anciennete, salaire_plafonne_cnss, indemnite_transport, indemnite_panier, indemnite_representation, avantage_logement, total_gains, heures_supplementaires, montant_heures_sup, heures_sup_25, heures_sup_50, heures_sup_100, cnss_salariale, amo_salariale, mutuelle, sni, ir, deductions_familiales, autres_retenues, net_avant_retenues, net_a_payer, cnss_patronale, amo_patronale, frais_professionnels)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmtPaie->execute([
                $id, $s['id'], $societeId, $c['joursTravailles'],
                $c['sb'], $c['sbi'], $c['primeAnciennete'], $c['plafonne'],
                $c['transport'], $c['panier'], $c['representation'], $c['logement'],
                $c['totalGains'],
                $c['heuresSup'], $c['montantHeuresSup'],
                $c['heuresSup25'], $c['heuresSup50'], $c['heuresSup100'],
                $c['cnss'], $c['amo'], $c['mutuelle'], $c['sni'], $c['ir'], $c['deductionsFamiliales'],
                $c['autresRetenues'], $c['netAvant'], $c['net'],
                $c['cnssPatronale'], $c['amoPatronale'], $c['fraisPro'],
            ]);

            $newPaieId = $this->db->lastInsertId();
            if (!empty($gainsOverridesMap[(int) $s['id']])) {
                if (!$insertGain) {
                    $insertGain = $this->db->prepare("INSERT INTO paie_gains (paie_id, rubrique_id, montant) VALUES (?, ?, ?)");
                }
                foreach ($gainsOverridesMap[(int) $s['id']] as $rId => $montant) {
                    $insertGain->execute([$newPaieId, $rId, $montant]);
                }
            }
            if (!empty($retenuesOverridesMap[(int) $s['id']])) {
                if (!$insertRet) {
                    $insertRet = $this->db->prepare("INSERT INTO paie_retenues (paie_id, type, libelle, montant) VALUES (?, ?, ?, ?)");
                }
                foreach ($retenuesOverridesMap[(int) $s['id']] as $r) {
                    $type = $r['type'] ?? 'autre';
                    $insertRet->execute([$newPaieId, $type, $r['libelle'], $r['montant']]);
                }
            }
        }

        $nbBulletins = BulletinController::genererPourPeriode($id, $this->db);

        Audit::log($this->db, 'calculate', 'periode', $id, 'Recalcul paies période');

        Session::setFlash('success', 'Paies recalculées pour ' . count($salaries) . ' salariés. ' . $nbBulletins . ' bulletins générés.');
        $this->redirect('/paie-me/paies');
    }

    public function cloturer(int $id): void
    {
        $userId = Session::get('user_id');
        $periode = $this->db->query("
            SELECT p.* FROM periodes p
            JOIN societes so ON p.societe_id = so.id
            WHERE p.id = $id AND so.user_id = $userId
        ")->fetch();

        if (!$periode) {
            Session::setFlash('error', 'Période introuvable.');
            $this->redirect('/paie-me/paies');
        }

        if (!empty($periode['cloturee'])) {
            Session::setFlash('error', 'Cette période est déjà clôturée.');
            $this->redirect('/paie-me/paies');
        }

        $paiesCount = $this->db->query("SELECT COUNT(*) FROM paies WHERE periode_id = $id")->fetchColumn();
        if ((int) $paiesCount === 0) {
            Session::setFlash('error', 'Impossible de clôturer une période sans paies.');
            $this->redirect('/paie-me/paies');
        }

        $this->db->exec("UPDATE periodes SET cloturee = 1 WHERE id = $id");
        Audit::log($this->db, 'cloture', 'periode', $id, 'Clôture période');
        Session::setFlash('success', 'Période clôturée avec succès.');
        $this->redirect('/paie-me/paies');
    }

    public function rouvrir(int $id): void
    {
        $userId = Session::get('user_id');
        $periode = $this->db->query("
            SELECT p.* FROM periodes p
            JOIN societes so ON p.societe_id = so.id
            WHERE p.id = $id AND so.user_id = $userId
        ")->fetch();

        if (!$periode) {
            Session::setFlash('error', 'Période introuvable.');
            $this->redirect('/paie-me/paies');
        }

        if (empty($periode['cloturee'])) {
            Session::setFlash('error', 'Cette période n\'est pas clôturée.');
            $this->redirect('/paie-me/paies');
        }

        $this->db->exec("UPDATE periodes SET cloturee = 0 WHERE id = $id");
        Audit::log($this->db, 'rouvrir', 'periode', $id, 'Réouverture période');
        Session::setFlash('success', 'Période réouverte avec succès.');
        $this->redirect('/paie-me/paies');
    }

    public function supprimerPeriode(int $id): void
    {
        $userId = Session::get('user_id');
        $periode = $this->db->query("
            SELECT p.* FROM periodes p
            JOIN societes so ON p.societe_id = so.id
            WHERE p.id = $id AND so.user_id = $userId
        ")->fetch();

        if (!$periode) {
            Session::setFlash('error', 'Période introuvable.');
            $this->redirect('/paie-me/paies');
        }

        $mois = str_pad($periode['mois'], 2, '0', STR_PAD_LEFT);
        $this->db->exec("DELETE FROM periodes WHERE id = $id");
        Audit::log($this->db, 'supprimer', 'periode', $id, 'Suppression période: ' . $mois . '/' . $periode['annee']);
        Session::setFlash('success', 'Période supprimée avec succès.');
        $this->redirect('/paie-me/paies');
    }

    public function lignes(int $id): void
    {
        $userId = Session::get('user_id');
        $periode = $this->db->query("
            SELECT p.*, so.raison_sociale FROM periodes p
            JOIN societes so ON p.societe_id = so.id
            WHERE p.id = $id AND so.user_id = $userId
        ")->fetch();

        if (!$periode) {
            Session::setFlash('error', 'Période introuvable.');
            $this->redirect('/paie-me/paies');
        }

        $paies = $this->db->query("
            SELECT pa.*, s.nom_famille, s.prenom, s.matricule, s.cnss
            FROM paies pa
            JOIN salaries s ON pa.salarie_id = s.id
            WHERE pa.periode_id = $id
            ORDER BY s.nom_famille, s.prenom
        ")->fetchAll();

        $disponibles = $this->db->prepare("
            SELECT s.id, s.matricule, s.cnss, s.nom_famille, s.prenom, s.salaire_base
            FROM salaries s
            WHERE s.societe_id = (SELECT societe_id FROM periodes WHERE id = ?)
            AND s.actif = 1
            AND s.id NOT IN (SELECT salarie_id FROM paies WHERE periode_id = ?)
            ORDER BY s.nom_famille, s.prenom
        ");
        $disponibles->execute([$id, $id]);
        $disponibles = $disponibles->fetchAll();

        $this->render('paies/lignes.php', [
            'title'       => 'Paies — ' . $periode['raison_sociale'] . ' ' . str_pad($periode['mois'], 2, '0', STR_PAD_LEFT) . '/' . $periode['annee'],
            'periode'     => $periode,
            'paies'       => $paies,
            'disponibles' => $disponibles,
        ]);
    }

    public function editPaie(int $id): void
    {
        $userId = Session::get('user_id');
        $paie = $this->db->query("
            SELECT pa.*, s.nom_famille, s.prenom, s.salaire_base, so.raison_sociale,
                   p.mois, p.annee, p.id as periode_id, p.societe_id
            FROM paies pa
            JOIN salaries s ON pa.salarie_id = s.id
            JOIN societes so ON pa.societe_id = so.id
            JOIN periodes p ON pa.periode_id = p.id
            WHERE pa.id = $id AND so.user_id = $userId
        ")->fetch();

        if (!$paie) {
            Session::setFlash('error', 'Paie introuvable.');
            $this->redirect('/paie-me/paies');
        }

        $periode = $this->db->query("SELECT cloturee FROM periodes WHERE id = {$paie['periode_id']}")->fetch();
        if ($periode && !empty($periode['cloturee'])) {
            Session::setFlash('error', 'Cette période est clôturée. Vous ne pouvez plus la modifier.');
            $this->redirect('/paie-me/paies');
        }

        $societeId = (int) $paie['societe_id'];
        $paieRetenues = $this->db->query("SELECT id, type, libelle, montant FROM paie_retenues WHERE paie_id = $id ORDER BY id")->fetchAll();
        $paieGains = $this->db->query("
            SELECT pg.rubrique_id, pg.montant, rg.code, rg.libelle, rg.type_montant, rg.valeur_defaut, rg.imposable
            FROM paie_gains pg
            JOIN rubriques_gains rg ON pg.rubrique_id = rg.id
            WHERE pg.paie_id = $id
            ORDER BY rg.code
        ")->fetchAll();
        $baremeHS = $this->db->query("SELECT * FROM bareme_heures_sup WHERE societe_id = $societeId")->fetch() ?: [];

        $codesIndemnites = ['330', '331', '340', '346'];
        $codesGains = array_unique(array_column($paieGains, 'code'));
        $codes = array_unique(array_merge($codesIndemnites, $codesGains));
        $plafonds = [];
        if (!empty($codes)) {
            $ph = implode(',', array_fill(0, count($codes), '?'));
            $s = $this->db->prepare("SELECT code, plafond_dgi_actif, plafond_dgi_valeur, plafond_dgi_type, plafond_cnss_actif, plafond_cnss_valeur, plafond_cnss_type, plafond_dgi_desc, plafond_cnss_desc FROM rubriques_gains WHERE code IN ($ph)");
            $s->execute(array_values($codes));
            foreach ($s->fetchAll() as $row) {
                $plafonds[$row['code']] = $row;
            }
        }

        if ($this->isPost()) {
            $this->checkCsrf();
            $hs25 = (float) ($_POST['heures_sup_25'] ?? 0);
            $hs50 = (float) ($_POST['heures_sup_50'] ?? 0);
            $hs100 = (float) ($_POST['heures_sup_100'] ?? 0);
            $stmt = $this->db->prepare("
                UPDATE paies SET
                    jours_travailles = ?,
                    jours_conge = ?,
                    jours_feries = ?,
                    heures_supplementaires = ?,
                    heures_sup_25 = ?,
                    heures_sup_50 = ?,
                    heures_sup_100 = ?,
                    indemnite_transport = ?,
                    indemnite_panier = ?,
                    indemnite_representation = ?,
                    avantage_logement = ?
                WHERE id = ?
            ");
            $stmt->execute([
                (int) ($_POST['jours_travailles'] ?? 30),
                (float) ($_POST['jours_conge'] ?? 0),
                (float) ($_POST['jours_feries'] ?? 0),
                $hs25 + $hs50 + $hs100,
                $hs25, $hs50, $hs100,
                (float) ($_POST['indemnite_transport'] ?? 0),
                (float) ($_POST['indemnite_panier'] ?? 0),
                (float) ($_POST['indemnite_representation'] ?? 0),
                (float) ($_POST['avantage_logement'] ?? 0),
                $id,
            ]);

            $this->db->exec("DELETE FROM paie_gains WHERE paie_id = $id");
            $insertGain = $this->db->prepare("INSERT INTO paie_gains (paie_id, rubrique_id, montant) VALUES (?, ?, ?)");

            if (!empty($_POST['gain_existing_rubrique_id'])) {
                foreach ($_POST['gain_existing_rubrique_id'] as $idx => $rubriqueId) {
                    $montant = (float) ($_POST['gain_existing_montant'][$idx] ?? 0);
                    if ($montant > 0) {
                        $insertGain->execute([$id, (int)$rubriqueId, $montant]);
                    }
                }
            }

            if (!empty($_POST['gain_new_rubrique_id'])) {
                foreach ($_POST['gain_new_rubrique_id'] as $idx => $rubriqueId) {
                    $montant = (float) ($_POST['gain_new_montant'][$idx] ?? 0);
                    if ($montant > 0) {
                        $insertGain->execute([$id, (int)$rubriqueId, $montant]);
                    }
                }
            }

            $this->db->exec("DELETE FROM paie_retenues WHERE paie_id = $id");
            $insertRet = $this->db->prepare("INSERT INTO paie_retenues (paie_id, type, libelle, montant) VALUES (?, ?, ?, ?)");

            if (!empty($_POST['retenue_libelle_existing'])) {
                foreach ($_POST['retenue_libelle_existing'] as $oldIdx => $libelle) {
                    $libelle = trim($libelle);
                    if ($libelle === '') continue;
                    $montant = (float) ($_POST['retenue_montant_existing'][$oldIdx] ?? 0);
                    $type = $_POST['retenue_type_existing'][$oldIdx] ?? 'autre';
                    $typesValides = ['avance','pret','sanction','autre'];
                    if (!in_array($type, $typesValides)) $type = 'autre';
                    if ($montant > 0) {
                        $insertRet->execute([$id, $type, $libelle, $montant]);
                    }
                }
            }

            if (!empty($_POST['retenue_new_rubrique_id'])) {
                foreach ($_POST['retenue_new_rubrique_id'] as $idx => $rubriqueId) {
                    $montant = (float) ($_POST['retenue_new_montant'][$idx] ?? 0);
                    if ($montant > 0) {
                        $rub = $this->db->query("SELECT code, libelle FROM rubriques_retenues WHERE id = " . (int)$rubriqueId)->fetch();
                        if ($rub) {
                            $type = $this->mapRetenueType($rub['code']);
                            $insertRet->execute([$id, $type, $rub['libelle'], $montant]);
                        }
                    }
                }
            }

            Audit::log($this->db, 'update', 'paie', $id, 'Modification paie: ' . $paie['nom_famille'] . ' ' . $paie['prenom']);

            if (!empty($_POST['recalculer'])) {
                $paieActualisee = $this->db->query("SELECT * FROM paies WHERE id = $id")->fetch();
                $this->recalculerPaie($id, $paieActualisee);
                Audit::log($this->db, 'recalculer', 'paie', $id, 'Recalcul paie: ' . $paie['nom_famille'] . ' ' . $paie['prenom']);
                Session::setFlash('success', 'Paie recalculée avec succès.');
                $this->redirect('/paie-me/paies/paie/' . $id . '/edit');
            }

            $fermer = !empty($_POST['fermer_apres']);
            Session::setFlash('success', 'Paie mise à jour.');
            if ($fermer) {
                $this->redirect('/paie-me/paies/' . $paie['periode_id'] . '/lignes');
            } else {
                $this->redirect('/paie-me/paies/paie/' . $id . '/edit');
            }
        }

        $rubriquesRetenues = $this->mergeRubriques('rubriques_retenues', $societeId);
        $rubriquesGains = $this->mergeRubriques('rubriques_gains', $societeId);

        $this->render('paies/edit.php', [
            'title'             => 'Modifier la paie — ' . $paie['nom_famille'] . ' ' . $paie['prenom'],
            'paie'              => $paie,
            'paieRetenues'      => $paieRetenues,
            'paieGains'         => $paieGains,
            'baremeHS'          => $baremeHS,
            'plafonds'          => $plafonds,
            'rubriquesRetenues' => $rubriquesRetenues,
            'rubriquesGains'    => $rubriquesGains,
        ]);
    }

    public function supprimerPaie(int $id): void
    {
        $userId = Session::get('user_id');
        $paie = $this->db->query("
            SELECT pa.*, so.user_id, p.cloturee, s.nom_famille, s.prenom
            FROM paies pa
            JOIN salaries s ON pa.salarie_id = s.id
            JOIN societes so ON pa.societe_id = so.id
            JOIN periodes p ON pa.periode_id = p.id
            WHERE pa.id = $id AND so.user_id = $userId
        ")->fetch();

        if (!$paie) {
            Session::setFlash('error', 'Paie introuvable.');
            $this->redirect('/paie-me/paies');
        }

        if (!empty($paie['cloturee'])) {
            Session::setFlash('error', 'Cette période est clôturée.');
            $this->redirect('/paie-me/paies/' . $paie['periode_id'] . '/lignes');
        }

        $periodeId = (int) $paie['periode_id'];
        $nom = $paie['nom_famille'] . ' ' . $paie['prenom'];

        $this->db->exec("DELETE FROM bulletins WHERE paie_id = $id");
        $this->db->exec("DELETE FROM paie_gains WHERE paie_id = $id");
        $this->db->exec("DELETE FROM paie_retenues WHERE paie_id = $id");
        $this->db->exec("DELETE FROM paies WHERE id = $id");

        Audit::log($this->db, 'supprimer', 'paie', $id, 'Suppression paie: ' . $nom);
        Session::setFlash('success', 'Paie de ' . $nom . ' supprimée.');
        $this->redirect('/paie-me/paies/' . $periodeId . '/lignes');
    }

    public function ajouterSalaries(int $id): void
    {
        $userId = Session::get('user_id');
        $periode = $this->db->query("
            SELECT p.*, so.raison_sociale FROM periodes p
            JOIN societes so ON p.societe_id = so.id
            WHERE p.id = $id AND so.user_id = $userId
        ")->fetch();

        if (!$periode) {
            Session::setFlash('error', 'Période introuvable.');
            $this->redirect('/paie-me/paies');
        }

        if (!empty($periode['cloturee'])) {
            Session::setFlash('error', 'Cette période est clôturée.');
            $this->redirect('/paie-me/paies/' . $id . '/lignes');
        }

        $societeId = $periode['societe_id'];
        $dateDebut = $periode['date_debut'];
        $dateFin = $periode['date_fin'];
        $cnssParams = $this->db->query("SELECT * FROM parametres_cnss_amo WHERE societe_id = $societeId")->fetch() ?: [];
        $baremeHS = $this->db->query("SELECT * FROM bareme_heures_sup WHERE societe_id = $societeId")->fetch() ?: [];
        $gains = $this->gainsAuto($societeId);
        $retenues = $this->mergeRubriques('rubriques_retenues', $societeId);

        if (!empty($_POST['all'])) {
            $salaries = $this->db->query("
                SELECT id, salaire_base, date_embauche, date_sortie, situation_familiale,
                       indemnite_transport, indemnite_panier, indemnite_representation,
                       avantage_logement, nb_enfants, avances_salaire, mutuelle
                FROM salaries
                WHERE societe_id = $societeId AND actif = 1
                AND id NOT IN (SELECT salarie_id FROM paies WHERE periode_id = $id)
            ")->fetchAll();
        } else {
            $ids = $_POST['salarie_ids'] ?? [];
            if (empty($ids) || !is_array($ids)) {
                Session::setFlash('error', 'Aucun salarié sélectionné.');
                $this->redirect('/paie-me/paies/' . $id . '/lignes');
            }
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $this->db->prepare("
                SELECT id, salaire_base, date_embauche, date_sortie, situation_familiale,
                       indemnite_transport, indemnite_panier, indemnite_representation,
                       avantage_logement, nb_enfants, avances_salaire, mutuelle
                FROM salaries
                WHERE societe_id = $societeId AND actif = 1
                AND id IN ($placeholders)
                AND id NOT IN (SELECT salarie_id FROM paies WHERE periode_id = $id)
            ");
            $stmt->execute($ids);
            $salaries = $stmt->fetchAll();
        }

        $compteur = 0;
        foreach ($salaries as $s) {
            $ic = $this->db->query("SELECT libelle, montant, plafond_dgi, plafond_cnss FROM salarie_indemnites WHERE salarie_id = {$s['id']} AND actif = 1 ORDER BY id")->fetchAll();
            $gs = $this->db->query("SELECT sg.salarie_id, rg.id as rubrique_id, rg.code, rg.libelle, rg.type_montant, rg.valeur_defaut, rg.imposable FROM salarie_gains sg JOIN rubriques_gains rg ON sg.rubrique_id = rg.id WHERE sg.salarie_id = {$s['id']} AND sg.actif = 1 AND rg.actif = 1 ORDER BY rg.code")->fetchAll();
            $mergedGains = $this->mergeGainsWithSalarie($gains, $gs);
            $c = $this->calculator->calculerPaie($s, $cnssParams, $dateFin, 0, 0, 0, $mergedGains, $retenues, $dateDebut, $baremeHS, null, 0, 0, $ic);

            $stmtPaie = $this->db->prepare("
                INSERT INTO paies (periode_id, salarie_id, societe_id, jours_travailles, salaire_brut, sbi, prime_anciennete, salaire_plafonne_cnss, indemnite_transport, indemnite_panier, indemnite_representation, avantage_logement, total_gains, heures_supplementaires, montant_heures_sup, heures_sup_25, heures_sup_50, heures_sup_100, cnss_salariale, amo_salariale, mutuelle, sni, ir, deductions_familiales, autres_retenues, net_avant_retenues, net_a_payer, cnss_patronale, amo_patronale, frais_professionnels)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmtPaie->execute([
                $id, $s['id'], $societeId,
                $c['joursTravailles'],
                $c['sb'], $c['sbi'], $c['primeAnciennete'], $c['plafonne'],
                $c['transport'], $c['panier'], $c['representation'], $c['logement'],
                $c['totalGains'],
                $c['heuresSup'], $c['montantHeuresSup'],
                $c['heuresSup25'], $c['heuresSup50'], $c['heuresSup100'],
                $c['cnss'], $c['amo'], $c['mutuelle'], $c['sni'], $c['ir'], $c['deductionsFamiliales'],
                $c['autresRetenues'], $c['netAvant'], $c['net'],
                $c['cnssPatronale'], $c['amoPatronale'], $c['fraisPro'],
            ]);
            $compteur++;
        }

        $nbBulletins = BulletinController::genererPourPeriode($id, $this->db);

        Audit::log($this->db, 'ajouter-salaries', 'periode', $id, 'Ajout de ' . $compteur . ' salariés à la période');

        Session::setFlash('success', $compteur . ' salarié(s) ajouté(s) à la période. ' . $nbBulletins . ' bulletin(s) généré(s).');
        $this->redirect('/paie-me/paies/' . $id . '/lignes');
    }

    public function journal(int $id): void
    {
        $userId = Session::get('user_id');
        $periode = $this->db->query("
            SELECT p.*, so.raison_sociale FROM periodes p
            JOIN societes so ON p.societe_id = so.id
            WHERE p.id = $id AND so.user_id = $userId
        ")->fetch();

        if (!$periode) {
            Session::setFlash('error', 'Période introuvable.');
            $this->redirect('/paie-me/paies');
        }

        $paies = $this->db->query("
            SELECT pa.*, s.nom_famille, s.prenom, s.matricule
            FROM paies pa
            JOIN salaries s ON pa.salarie_id = s.id
            WHERE pa.periode_id = $id
            ORDER BY s.nom_famille, s.prenom
        ")->fetchAll();

        $totaux = [
            'salaire_brut' => 0, 'sbi' => 0, 'prime_anciennete' => 0,
            'total_gains' => 0, 'montant_heures_sup' => 0,
            'indemnite_transport' => 0, 'indemnite_panier' => 0,
            'indemnite_representation' => 0, 'avantage_logement' => 0,
            'cnss_salariale' => 0, 'amo_salariale' => 0,
            'frais_professionnels' => 0, 'mutuelle' => 0,
            'ir' => 0, 'autres_retenues' => 0,
            'net_a_payer' => 0,
            'cnss_patronale' => 0, 'amo_patronale' => 0,
        ];

        foreach ($paies as $pa) {
            foreach ($totaux as $k => &$v) {
                $v += (float) ($pa[$k] ?? 0);
            }
        }
        unset($v);

        $this->render('paies/journal.php', [
            'title'  => 'Journal de paie — ' . $periode['raison_sociale'] . ' ' . str_pad($periode['mois'], 2, '0', STR_PAD_LEFT) . '/' . $periode['annee'],
            'periode' => $periode,
            'paies'  => $paies,
            'totaux' => $totaux,
        ]);
    }

    protected function render(string $view, array $data = []): void
    {
        extract($data);
        ob_start();
        require __DIR__ . '/../views/' . $view;
        $content = ob_get_clean();
        require __DIR__ . '/../views/layout.php';
    }

    private function recalculerPaie(int $paieId, array $paie): void
    {
        $periode = $this->db->query("SELECT * FROM periodes WHERE id = {$paie['periode_id']}")->fetch();
        if (!$periode) return;

        $salarie = $this->db->query("SELECT * FROM salaries WHERE id = {$paie['salarie_id']}")->fetch();
        if (!$salarie) return;

        $societeId = $paie['societe_id'];
        $cnssParams = $this->db->query("SELECT * FROM parametres_cnss_amo WHERE societe_id = $societeId")->fetch() ?: [];
        $baremeHS = $this->db->query("SELECT * FROM bareme_heures_sup WHERE societe_id = $societeId")->fetch() ?: [];
        $gains = $this->gainsAuto($societeId);
        $retenues = $this->mergeRubriques('rubriques_retenues', $societeId);

        $hs25 = (float) ($paie['heures_sup_25'] ?? 0);
        $hs50 = (float) ($paie['heures_sup_50'] ?? 0);
        $hs100 = (float) ($paie['heures_sup_100'] ?? 0);

        $salarie['indemnite_transport'] = $paie['indemnite_transport'];
        $salarie['indemnite_panier'] = $paie['indemnite_panier'];
        $salarie['indemnite_representation'] = $paie['indemnite_representation'];
        $salarie['avantage_logement'] = $paie['avantage_logement'];

        $joursOverride = min((int) ($paie['jours_travailles'] ?? 26), 26);
        $jc = (float) ($paie['jours_conge'] ?? 0);
        $jf = (float) ($paie['jours_feries'] ?? 0);
        $ic = $this->db->query("SELECT libelle, montant, plafond_dgi, plafond_cnss FROM salarie_indemnites WHERE salarie_id = {$salarie['id']} AND actif = 1 ORDER BY id")->fetchAll();
        $gs = $this->db->query("SELECT sg.salarie_id, rg.id as rubrique_id, rg.code, rg.libelle, rg.type_montant, rg.valeur_defaut, rg.imposable FROM salarie_gains sg JOIN rubriques_gains rg ON sg.rubrique_id = rg.id WHERE sg.salarie_id = {$salarie['id']} AND sg.actif = 1 AND rg.actif = 1 ORDER BY rg.code")->fetchAll();
        $mergedGains = $this->mergeGainsWithSalarie($gains, $gs);
        $c = $this->calculator->calculerPaie($salarie, $cnssParams, $periode['date_fin'], $hs25, $hs50, $hs100, $mergedGains, $retenues, $periode['date_debut'], $baremeHS, $joursOverride, $jc, $jf, $ic);

        $stmt = $this->db->prepare("
            UPDATE paies SET
                salaire_brut = ?, sbi = ?, prime_anciennete = ?,
                salaire_plafonne_cnss = ?,
                total_gains = ?, montant_heures_sup = ?,
                cnss_salariale = ?, amo_salariale = ?, mutuelle = ?,
                sni = ?, ir = ?, deductions_familiales = ?,
                autres_retenues = ?, net_avant_retenues = ?, net_a_payer = ?,
                cnss_patronale = ?, amo_patronale = ?, frais_professionnels = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $c['sb'], $c['sbi'], $c['primeAnciennete'],
            $c['plafonne'],
            $c['totalGains'], $c['montantHeuresSup'],
            $c['cnss'], $c['amo'], $c['mutuelle'],
            $c['sni'], $c['ir'], $c['deductionsFamiliales'],
            $c['autresRetenues'], $c['netAvant'], $c['net'],
            $c['cnssPatronale'], $c['amoPatronale'], $c['fraisPro'],
            $paieId,
        ]);

        BulletinController::genererPourPeriode($paie['periode_id'], $this->db);
    }

    private function gainsAuto(int $societeId): array
    {
        return array_values(array_filter(
            $this->mergeRubriques('rubriques_gains', $societeId),
            fn($g) => ($g['categorie'] ?? '') === 'Gain standard'
        ));
    }

    private function mergeGainsWithSalarie(array $autoGains, array $salarieGains): array
    {
        if (empty($salarieGains)) return $autoGains;

        $merged = [];
        foreach ($autoGains as $g) {
            $merged[$g['code']] = $g;
        }
        foreach ($salarieGains as $sg) {
            $merged[$sg['code']] = $sg;
        }
        return array_values($merged);
    }

    private function mergeRubriques(string $table, int $societeId): array
    {
        $all = $this->db->query("SELECT * FROM $table WHERE (societe_id IS NULL OR societe_id = $societeId) AND actif = 1 ORDER BY is_global, code")->fetchAll();
        $merged = [];
        foreach ($all as $r) {
            $merged[$r['code']] = $r;
        }
        return array_values($merged);
    }

    private function mapRetenueType(string $code): string
    {
        $map = [
            '801' => 'avance',
            '802' => 'pret',
            '803' => 'pret',
        ];
        return $map[$code] ?? 'autre';
    }
}
