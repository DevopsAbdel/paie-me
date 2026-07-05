<?php

namespace Controllers;

use Core\Controller;
use Core\Model;
use Core\Session;
use Core\Validator;
use Core\Audit;
use Core\Crypto;
use PDO;

class SocieteController extends Controller
{
    private PDO $db;

    public function __construct()
    {
        if (!Session::has('user_id')) {
            $this->redirect('/paie-me/login');
        }
        $this->db = Model::db();
    }

    public function index(): void
    {
        $userId = Session::get('user_id');
        $societes = $this->db->query("SELECT * FROM societes WHERE user_id = $userId ORDER BY raison_sociale")->fetchAll();

        $this->render('societes/index.php', [
            'title'    => 'Sociétés',
            'societes' => $societes,
        ]);
    }

    public function create(): void
    {
        if ($this->isPost()) {
            $this->checkCsrf();
            $v = new Validator($_POST);
            $v->required('raison_sociale', 'Raison sociale')
              ->required('ice', 'ICE')
              ->required('if_fiscal', 'IF')
              ->maxLength('ice', 20, 'ICE')
              ->maxLength('if_fiscal', 20, 'IF')
              ->email('email', 'Email');

            if (!$v->passes()) {
                Session::setFlash('error', $v->firstError());
                $this->redirect('/paie-me/societes/create');
            }

            $userId = Session::get('user_id');
            $data = $this->getPostData();
            $data['rib'] = Crypto::encrypt($data['rib']);

            $stmt = $this->db->prepare("
                INSERT INTO societes (user_id, raison_sociale, forme_juridique, ice, if_fiscal, rc, tp, cnss, adresse, ville, telephone, email, site_web, banque, agence, rib, damancom_login, damancom_password, simpl_login, simpl_password, cimr_login, cimr_password)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $userId, $data['raison_sociale'], $data['forme_juridique'], $data['ice'], $data['if_fiscal'],
                $data['rc'], $data['tp'], $data['cnss'], $data['adresse'], $data['ville'], $data['telephone'],
                $data['email'], $data['site_web'], $data['banque'], $data['agence'], $data['rib'],
                $data['damancom_login'], $data['damancom_password'],
                $data['simpl_login'], $data['simpl_password'],
                $data['cimr_login'], $data['cimr_password'],
            ]);

            $societeId = $this->db->lastInsertId();
            Audit::log($this->db, 'create', 'societe', (int) $societeId, 'Création société: ' . $data['raison_sociale']);

            Session::setFlash('success', 'Société créée avec succès.');
            $this->redirect('/paie-me/societes');
        }

        $this->render('societes/form.php', [
            'title'   => 'Nouvelle société',
            'societe' => null,
        ]);
    }

    public function show(int $id): void
    {
        $userId = Session::get('user_id');
        $societe = $this->db->query("SELECT * FROM societes WHERE id = $id AND user_id = $userId")->fetch();

        if (!$societe) {
            Session::setFlash('error', 'Société introuvable.');
            $this->redirect('/paie-me/societes');
        }

        Session::set('societe_context', [
            'id'             => $societe['id'],
            'raison_sociale' => $societe['raison_sociale'],
            'ice'            => $societe['ice'],
            'cnss'           => $societe['cnss'],
        ]);

        $salaries = $this->db->query("SELECT s.*, f.nom as fonction_nom FROM salaries s LEFT JOIN fonctions f ON s.fonction_id = f.id WHERE s.societe_id = $id AND s.actif = 1 ORDER BY s.nom_famille, s.prenom")->fetchAll();
        $periodes = $this->db->query("SELECT p.*, (SELECT COUNT(*) FROM paies WHERE periode_id = p.id) as nb_paies FROM periodes p WHERE p.societe_id = $id ORDER BY p.annee DESC, p.mois DESC")->fetchAll();
        $bulletins = $this->db->query("
            SELECT b.*, pa.salaire_brut, pa.net_a_payer, pa.ir, s.nom_famille, s.prenom, p.mois, p.annee
            FROM bulletins b
            JOIN paies pa ON b.paie_id = pa.id
            JOIN salaries s ON pa.salarie_id = s.id
            JOIN periodes p ON pa.periode_id = p.id
            WHERE pa.societe_id = $id
            ORDER BY p.annee DESC, p.mois DESC, s.nom_famille
        ")->fetchAll();

        $societe['rib'] = Crypto::decrypt($societe['rib']);

        $this->render('societes/show.php', [
            'title'     => $societe['raison_sociale'],
            'societe'   => $societe,
            'salaries'  => $salaries,
            'periodes'  => $periodes,
            'bulletins' => $bulletins,
            'societeId' => $id,
        ]);
    }

    public function edit(int $id): void
    {
        $userId = Session::get('user_id');
        $societe = $this->db->query("SELECT * FROM societes WHERE id = $id AND user_id = $userId")->fetch();

        if (!$societe) {
            Session::setFlash('error', 'Société introuvable.');
            $this->redirect('/paie-me/societes');
        }

        if ($this->isPost()) {
            $this->checkCsrf();
            $v = new Validator($_POST);
            $v->required('raison_sociale', 'Raison sociale')
              ->required('ice', 'ICE')
              ->required('if_fiscal', 'IF');
            if (!$v->passes()) {
                Session::setFlash('error', $v->firstError());
                $this->redirect('/paie-me/societes/' . $id . '/edit');
            }
            $data = $this->getPostData();
            $data['rib'] = Crypto::encrypt($data['rib']);

            $stmt = $this->db->prepare("
                UPDATE societes SET raison_sociale=?, forme_juridique=?, ice=?, if_fiscal=?, rc=?, tp=?, cnss=?, adresse=?, ville=?, telephone=?, email=?, site_web=?, banque=?, agence=?, rib=?, damancom_login=?, damancom_password=?, simpl_login=?, simpl_password=?, cimr_login=?, cimr_password=?
                WHERE id = ?
            ");
            $stmt->execute([
                $data['raison_sociale'], $data['forme_juridique'], $data['ice'], $data['if_fiscal'],
                $data['rc'], $data['tp'], $data['cnss'], $data['adresse'], $data['ville'], $data['telephone'],
                $data['email'], $data['site_web'], $data['banque'], $data['agence'], $data['rib'],
                $data['damancom_login'], $data['damancom_password'],
                $data['simpl_login'], $data['simpl_password'],
                $data['cimr_login'], $data['cimr_password'], $id,
            ]);

            Audit::log($this->db, 'update', 'societe', $id, 'Modification société: ' . $societe['raison_sociale']);

            Session::setFlash('success', 'Société mise à jour.');
            $this->redirect('/paie-me/societes');
        }

        $societe['rib'] = Crypto::decrypt($societe['rib']);

        $this->render('societes/form.php', [
            'title'   => 'Modifier société',
            'societe' => $societe,
        ]);
    }

    public function clearContext(): void
    {
        Session::remove('societe_context');
        $this->redirect('/paie-me/societes');
    }

    public function switchContext(int $id): void
    {
        $this->redirect('/paie-me/societes/' . $id);
    }

    public function delete(int $id): void
    {
        $this->checkCsrf();
        $this->requireRole('admin');
        $userId = Session::get('user_id');
        $societe = $this->db->query("SELECT raison_sociale FROM societes WHERE id = $id")->fetch();
        Audit::log($this->db, 'delete', 'societe', $id, 'Suppression société: ' . ($societe['raison_sociale'] ?? ''));
        $this->db->exec("DELETE FROM societes WHERE id = $id AND user_id = $userId");
        Session::setFlash('success', 'Société supprimée.');
        $this->redirect('/paie-me/societes');
    }

    public function parametres(int $id, string $sous_tab = 'banque'): void
    {
        $userId = Session::get('user_id');
        $societe = $this->db->query("SELECT * FROM societes WHERE id = $id AND user_id = $userId")->fetch();

        if (!$societe) {
            Session::setFlash('error', 'Société introuvable.');
            $this->redirect('/paie-me/societes');
        }

        Session::set('societe_context', [
            'id'             => $societe['id'],
            'raison_sociale' => $societe['raison_sociale'],
            'ice'            => $societe['ice'],
            'cnss'           => $societe['cnss'],
        ]);

        // Delete actions via GET
        $deleteActions = [
            'delete_service'    => ['table' => 'services',             'tab' => 'services'],
            'delete_fonction'   => ['table' => 'fonctions',           'tab' => 'services'],
            'delete_gain'       => ['table' => 'rubriques_gains',      'tab' => 'gains'],
            'delete_retenue'    => ['table' => 'rubriques_retenues',   'tab' => 'retenues'],
            'delete_organisme'  => ['table' => 'organismes',           'tab' => 'organismes'],
            'delete_attestation' => ['table' => 'modeles_attestation', 'tab' => 'attestations'],
        ];
        foreach ($deleteActions as $param => $cfg) {
            if (isset($_GET[$param])) {
                $this->db->exec("DELETE FROM {$cfg['table']} WHERE id = " . (int)$_GET[$param] . " AND societe_id = $id");
                Session::setFlash('success', 'Supprimé avec succès.');
                $this->redirect('/paie-me/societes/' . $id . '/parametres/' . $cfg['tab']);
            }
        }

        if ($this->isPost()) {
            $this->checkCsrf();
            $sousTab = $_POST['sous_tab'] ?? 'banque';

            if ($sousTab === 'bareme') {
                foreach ($_POST['min'] ?? [] as $idBareme => $min) {
                    $max = $_POST['max'][$idBareme] ?? 0;
                    $taux = $_POST['taux'][$idBareme] ?? 0;
                    $deduction = $_POST['deduction'][$idBareme] ?? 0;
                    $type = $_POST['type'][$idBareme] ?? 'mensuel';
                    $stmt = $this->db->prepare("UPDATE bareme_ir SET min=?, max=?, taux=?, deduction=?, type=? WHERE id=?");
                    $stmt->execute([$min, $max, $taux, $deduction, $type, $idBareme]);
                }
                Session::setFlash('success', 'Barème IR mis à jour.');
            } elseif ($sousTab === 'cnss_amo') {
                $stmt = $this->db->prepare("
                    INSERT INTO parametres_cnss_amo (societe_id, plafond_cnss, taux_cnss_salarial, taux_cnss_patronal, taux_amo_salarial, taux_amo_patronal, taux_amo_total, taux_allocations_familiales, taux_prestations_sociales, taxe_formation, participation_amo, taux_penalites_cnss, taux_penalites_tfp, taux_penalites_amo, penalite_cnss_premier_mois, penalite_cnss_mois_suivants, penalite_amo_taux, astreinte_cnss_par_salarie, astreinte_amo_par_salarie)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE plafond_cnss=VALUES(plafond_cnss), taux_cnss_salarial=VALUES(taux_cnss_salarial), taux_cnss_patronal=VALUES(taux_cnss_patronal), taux_amo_salarial=VALUES(taux_amo_salarial), taux_amo_patronal=VALUES(taux_amo_patronal), taux_amo_total=VALUES(taux_amo_total), taux_allocations_familiales=VALUES(taux_allocations_familiales), taux_prestations_sociales=VALUES(taux_prestations_sociales), taxe_formation=VALUES(taxe_formation), participation_amo=VALUES(participation_amo), taux_penalites_cnss=VALUES(taux_penalites_cnss), taux_penalites_tfp=VALUES(taux_penalites_tfp), taux_penalites_amo=VALUES(taux_penalites_amo), penalite_cnss_premier_mois=VALUES(penalite_cnss_premier_mois), penalite_cnss_mois_suivants=VALUES(penalite_cnss_mois_suivants), penalite_amo_taux=VALUES(penalite_amo_taux), astreinte_cnss_par_salarie=VALUES(astreinte_cnss_par_salarie), astreinte_amo_par_salarie=VALUES(astreinte_amo_par_salarie)
                ");
                $stmt->execute([
                    $id,
                    $_POST['plafond_cnss'] ?? 6000,
                    $_POST['taux_cnss_salarial'] ?? 4.48,
                    $_POST['taux_cnss_patronal'] ?? 8.98,
                    $_POST['taux_amo_salarial'] ?? 2.26,
                    $_POST['taux_amo_patronal'] ?? 4.11,
                    $_POST['taux_amo_total'] ?? 6.37,
                    $_POST['taux_allocations_familiales'] ?? 6.40,
                    $_POST['taux_prestations_sociales'] ?? 13.46,
                    $_POST['taxe_formation'] ?? 1.60,
                    $_POST['participation_amo'] ?? 1.85,
                    $_POST['taux_penalites_cnss'] ?? 0,
                    $_POST['taux_penalites_tfp'] ?? 0,
                    $_POST['taux_penalites_amo'] ?? 0,
                    $_POST['penalite_cnss_premier_mois'] ?? 3.00,
                    $_POST['penalite_cnss_mois_suivants'] ?? 0.50,
                    $_POST['penalite_amo_taux'] ?? 1.00,
                    $_POST['astreinte_cnss_par_salarie'] ?? 50.00,
                    $_POST['astreinte_amo_par_salarie'] ?? 100.00,
                ]);
                Session::setFlash('success', 'Taux CNSS/AMO mis à jour.');
                } elseif ($sousTab === 'penalites') {
                    $periodeId = (int) ($_POST['periode_id'] ?? 0);
                    if ($periodeId) {
                        $stmt = $this->db->prepare("UPDATE periodes SET penalites_cnss=?, penalites_tfp=?, penalites_amo=? WHERE id=? AND societe_id=?");
                        $stmt->execute([
                            $_POST['penalites_cnss'] ?? 0,
                            $_POST['penalites_tfp'] ?? 0,
                            $_POST['penalites_amo'] ?? 0,
                            $periodeId, $id,
                        ]);
                        Session::setFlash('success', 'Pénalités mises à jour.');
                    }
                    $this->redirect('/paie-me/societes/' . $id . '?tab=cnss');
                    return;
            } elseif ($sousTab === 'calcul_penalites') {
                $periodeId = (int) ($_POST['periode_id'] ?? 0);
                $moisRetard = (int) ($_POST['mois_retard'] ?? 0);
                if ($periodeId && $moisRetard > 0) {
                    $params = $this->db->query("SELECT * FROM parametres_cnss_amo WHERE societe_id = $id")->fetch();
                    if (!$params) $params = ['penalite_cnss_premier_mois'=>3.00,'penalite_cnss_mois_suivants'=>0.50,'penalite_amo_taux'=>1.00,'astreinte_cnss_par_salarie'=>50.00,'astreinte_amo_par_salarie'=>100.00,'taux_penalites_tfp'=>0];
                    $nbSalaries = (int) $this->db->query("SELECT COUNT(*) FROM paies WHERE periode_id = $periodeId")->fetchColumn();
                    $totalCNSS = (float) $this->db->query("SELECT COALESCE(SUM(cnss),0) FROM paies WHERE periode_id = $periodeId")->fetchColumn();
                    $totalAMO = (float) $this->db->query("SELECT COALESCE(SUM(amo),0) FROM paies WHERE periode_id = $periodeId")->fetchColumn();
                    $totalTFP = (float) $this->db->query("SELECT COALESCE(SUM(tfp),0) FROM paies WHERE periode_id = $periodeId")->fetchColumn();

                    $penaliteCNSS = $totalCNSS * ($params['penalite_cnss_premier_mois'] / 100);
                    if ($moisRetard > 1) {
                        $penaliteCNSS += $totalCNSS * ($params['penalite_cnss_mois_suivants'] / 100) * ($moisRetard - 1);
                    }
                    $penaliteCNSS += $params['astreinte_cnss_par_salarie'] * $moisRetard * $nbSalaries;
                    $penaliteAMO = $totalAMO * ($params['penalite_amo_taux'] / 100) * $moisRetard;
                    $penaliteAMO += $params['astreinte_amo_par_salarie'] * $moisRetard * $nbSalaries;
                    $penaliteTFP = $totalTFP * ($params['taux_penalites_tfp'] / 100) * $moisRetard;

                    $stmt = $this->db->prepare("UPDATE periodes SET penalites_cnss=?, penalites_tfp=?, penalites_amo=? WHERE id=? AND societe_id=?");
                    $stmt->execute([round($penaliteCNSS, 2), round($penaliteTFP, 2), round($penaliteAMO, 2), $periodeId, $id]);
                    Session::setFlash('success', 'Pénalités calculées automatiquement.');
                }
                $this->redirect('/paie-me/societes/' . $id . '?tab=cnss');
                return;
            } elseif ($sousTab === 'services') {
                if (!empty($_POST['service_nom'])) {
                    $stmt = $this->db->prepare("INSERT INTO services (societe_id, nom, description) VALUES (?, ?, ?)");
                    $stmt->execute([$id, $_POST['service_nom'], $_POST['service_description'] ?? '']);
                    Session::setFlash('success', 'Service ajouté.');
                }
                if (!empty($_POST['fonction_nom'])) {
                    $stmt = $this->db->prepare("INSERT INTO fonctions (societe_id, service_id, nom, description) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$id, $_POST['fonction_service_id'] ? (int)$_POST['fonction_service_id'] : null, $_POST['fonction_nom'], $_POST['fonction_description'] ?? '']);
                    Session::setFlash('success', 'Fonction ajoutée.');
                }
            } elseif ($sousTab === 'gains') {
                if (!empty($_POST['code'])) {
                    $gainId = !empty($_POST['gain_id']) ? (int)$_POST['gain_id'] : null;
                    $p = function($k, $d = null) { return $_POST[$k] ?? $d; };
                    $fields = [
                        'code' => $p('code'),
                        'libelle' => $p('libelle'),
                        'type_montant' => $p('type_montant', 'fixe'),
                        'valeur_defaut' => $p('valeur_defaut', 0),
                        'categorie' => $p('categorie'),
                        'compte' => $p('compte'),
                        'justificatifs' => $p('justificatifs'),
                        'source' => $p('source'),
                        'nature_edi' => $p('nature_edi'),
                        'actif' => (int)$p('actif', 0),
                        'is_global' => (int)$p('is_global', 0),
                        'base_anciennete' => (int)$p('base_anciennete', 0),
                        'au_prorata' => (int)$p('au_prorata', 0),
                        'imposable_ir' => (int)$p('imposable_ir', 0),
                        'imposable_cnss' => (int)$p('imposable_cnss', 0),
                        'plafond_dgi_actif' => (int)$p('plafond_dgi_actif', 0),
                        'plafond_dgi_valeur' => $p('plafond_dgi_valeur'),
                        'plafond_dgi_type' => $p('plafond_dgi_type'),
                        'plafond_cnss_actif' => (int)$p('plafond_cnss_actif', 0),
                        'plafond_cnss_valeur' => $p('plafond_cnss_valeur'),
                        'plafond_cnss_type' => $p('plafond_cnss_type'),
                        'plafond_dgi_desc' => $p('plafond_dgi_desc'),
                        'plafond_cnss_desc' => $p('plafond_cnss_desc'),
                    ];
                    if ($gainId) {
                        $sql = "UPDATE rubriques_gains SET " . implode(', ', array_map(fn($k) => "$k=?", array_keys($fields))) . " WHERE id=? AND (societe_id=? OR societe_id IS NULL)";
                        $stmt = $this->db->prepare($sql);
                        $stmt->execute([...array_values($fields), $gainId, $id]);
                        $msg = 'Rubrique modifiée.';
                    } else {
                        $fields['societe_id'] = $id;
                        $cols = array_keys($fields);
                        $vals = array_values($fields);
                        $sql = "INSERT INTO rubriques_gains (" . implode(',', $cols) . ") VALUES (" . implode(',', array_fill(0, count($cols), '?')) . ")";
                        $stmt = $this->db->prepare($sql);
                        $stmt->execute($vals);
                        $msg = 'Rubrique ajoutée.';
                    }
                    if (($p('format') ?? '') === 'json') {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => true, 'message' => $msg]);
                        exit;
                    }
                    Session::setFlash('success', $msg);
                }
            } elseif ($sousTab === 'retenues') {
                if (!empty($_POST['code'])) {
                    $stmt = $this->db->prepare("INSERT INTO rubriques_retenues (societe_id, code, libelle, type_montant, valeur_defaut) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$id, $_POST['code'], $_POST['libelle'], $_POST['type_montant'] ?? 'fixe', $_POST['valeur_defaut'] ?? 0]);
                    Session::setFlash('success', 'Retenue ajoutée.');
                }
            } elseif ($sousTab === 'organismes') {
                if (!empty($_POST['nom'])) {
                    $stmt = $this->db->prepare("INSERT INTO organismes (societe_id, nom, type, login, mot_de_passe) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$id, $_POST['nom'], $_POST['type'] ?? 'autre', $_POST['login'] ?? '', $_POST['mot_de_passe'] ?? '']);
                    Session::setFlash('success', 'Organisme ajouté.');
                }
            } elseif ($sousTab === 'attestations') {
                if (!empty($_POST['titre'])) {
                    $stmt = $this->db->prepare("INSERT INTO modeles_attestation (societe_id, titre, contenu) VALUES (?, ?, ?)");
                    $stmt->execute([$id, $_POST['titre'], $_POST['contenu'] ?? '']);
                    Session::setFlash('success', 'Modèle d\'attestation ajouté.');
                }
            } else {
                $stmt = $this->db->prepare("
                    UPDATE societes SET banque=?, agence=?, rib=?, damancom_login=?, damancom_password=?, simpl_login=?, simpl_password=?, cimr_login=?, cimr_password=?
                    WHERE id = ?
                ");
                $stmt->execute([
                    $_POST['banque'] ?? '',
                    $_POST['agence'] ?? '',
                    Crypto::encrypt($_POST['rib'] ?? ''),
                    $_POST['damancom_login'] ?? '',
                    $_POST['damancom_password'] ?? '',
                    $_POST['simpl_login'] ?? '',
                    $_POST['simpl_password'] ?? '',
                    $_POST['cimr_login'] ?? '',
                    $_POST['cimr_password'] ?? '',
                    $id,
                ]);
                Session::setFlash('success', 'Paramètres mis à jour.');
            }

            $this->redirect('/paie-me/societes/' . $id . '/parametres/' . $sousTab);
        }

        $baremeMensuel = $this->db->query("SELECT * FROM bareme_ir WHERE type='mensuel' ORDER BY `min`")->fetchAll();
        $baremeAnnuel  = $this->db->query("SELECT * FROM bareme_ir WHERE type='annuel' ORDER BY `min`")->fetchAll();
        $cnssParams = $this->db->query("SELECT * FROM parametres_cnss_amo WHERE societe_id = $id")->fetch();
        if (!$cnssParams) $cnssParams = ['plafond_cnss'=>6000,'taux_cnss_salarial'=>4.48,'taux_cnss_patronal'=>8.98,'taux_amo_salarial'=>2.26,'taux_amo_patronal'=>4.11,'taux_amo_total'=>6.37,'taux_allocations_familiales'=>6.40,'taux_prestations_sociales'=>13.46,'taxe_formation'=>1.60,'participation_amo'=>1.85,'taux_penalites_cnss'=>0,'taux_penalites_tfp'=>0,'taux_penalites_amo'=>0,'penalite_cnss_premier_mois'=>3.00,'penalite_cnss_mois_suivants'=>0.50,'penalite_amo_taux'=>1.00,'astreinte_cnss_par_salarie'=>50.00,'astreinte_amo_par_salarie'=>100.00];
        $salaries = $this->db->query("SELECT id, nom_famille, prenom, salaire_base FROM salaries WHERE societe_id = $id AND actif = 1 ORDER BY nom_famille, prenom")->fetchAll();
        $services = $this->db->query("SELECT * FROM services WHERE societe_id = $id ORDER BY nom")->fetchAll();
        $fonctions = $this->db->query("SELECT f.*, s.nom as service_nom FROM fonctions f LEFT JOIN services s ON f.service_id = s.id WHERE f.societe_id = $id ORDER BY s.nom, f.nom")->fetchAll();
        $gains = $this->db->query("SELECT * FROM rubriques_gains WHERE (societe_id IS NULL OR societe_id = $id) ORDER BY is_global DESC, code")->fetchAll();
        $retenues = $this->db->query("SELECT * FROM rubriques_retenues WHERE (societe_id IS NULL OR societe_id = $id) ORDER BY is_global DESC, code")->fetchAll();
        $organismes = $this->db->query("SELECT * FROM organismes WHERE societe_id = $id ORDER BY nom")->fetchAll();
        $attestations = $this->db->query("SELECT * FROM modeles_attestation WHERE societe_id = $id ORDER BY titre")->fetchAll();

        $societe['rib'] = Crypto::decrypt($societe['rib']);

        $titles = [
            'general'      => 'Informations générales',
            'banque'       => 'Coordonnées bancaires',
            'teleservices' => 'Accès téléservices',
            'bareme'       => 'Barème IR 2025',
            'codification' => 'Codification & numérotation',
            'cnss_amo'     => 'Taux CNSS & AMO',
            'bcp'          => 'BCP — Bordereau de Cotisations et Paiement',
            'services'     => 'Services',
            'gains'        => 'Rubriques de gains',
            'retenues'     => 'Rubriques de retenues',
            'organismes'   => 'Organismes',
            'attestations' => 'Modèles d\'attestation',
            'journal'      => 'Journal de comptabilisation',
        ];
        $subView = 'banque';
        if (in_array($sous_tab, array_keys($titles))) {
            $subView = $sous_tab;
        }
        $baseUrl = '/paie-me/societes/' . $id . '/parametres';

        $this->render('societes/parametres/' . $subView . '.php', [
            'title'         => $titles[$subView] . ' — ' . $societe['raison_sociale'],
            'societe'       => $societe,
            'baseUrl'       => $baseUrl,
            'bareme'        => $baremeMensuel,
            'baremeAnnuel'  => $baremeAnnuel,
            'cnssParams'    => $cnssParams,
            'salaries'      => $salaries,
            'services'      => $services,
            'fonctions'     => $fonctions,
            'gains'         => $gains,
            'retenues'      => $retenues,
            'organismes'    => $organismes,
            'attestations'  => $attestations,
        ]);
    }

    private function getPostData(): array
    {
        return [
            'raison_sociale'    => $_POST['raison_sociale'] ?? '',
            'forme_juridique'   => $_POST['forme_juridique'] ?? 'SARL',
            'ice'               => $_POST['ice'] ?? '',
            'if_fiscal'         => $_POST['if_fiscal'] ?? '',
            'rc'                => $_POST['rc'] ?? '',
            'tp'                => $_POST['tp'] ?? '',
            'cnss'              => $_POST['cnss'] ?? '',
            'adresse'           => $_POST['adresse'] ?? '',
            'ville'             => $_POST['ville'] ?? '',
            'telephone'         => $_POST['telephone'] ?? '',
            'email'             => $_POST['email'] ?? '',
            'site_web'          => $_POST['site_web'] ?? '',
            'banque'            => $_POST['banque'] ?? '',
            'agence'            => $_POST['agence'] ?? '',
            'rib'               => $_POST['rib'] ?? '',
            'damancom_login'    => $_POST['damancom_login'] ?? '',
            'damancom_password' => $_POST['damancom_password'] ?? '',
            'simpl_login'       => $_POST['simpl_login'] ?? '',
            'simpl_password'    => $_POST['simpl_password'] ?? '',
            'cimr_login'        => $_POST['cimr_login'] ?? '',
            'cimr_password'     => $_POST['cimr_password'] ?? '',
        ];
    }

    protected function render(string $view, array $data = []): void
    {
        extract($data);
        ob_start();
        require __DIR__ . '/../views/' . $view;
        $content = ob_get_clean();
        require __DIR__ . '/../views/layout.php';
    }
}
