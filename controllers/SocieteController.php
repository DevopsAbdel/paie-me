<?php

namespace Controllers;

use Core\Controller;
use Core\Model;
use Core\Session;
use Core\Validator;
use Core\Audit;
use Core\Crypto;
use Core\Helper;
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

            $logoPath = $this->handleLogoUpload();
            if ($logoPath !== null) {
                $this->db->prepare("UPDATE societes SET logo = ? WHERE id = ?")->execute([$logoPath, $societeId]);
            }

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
            'logo'           => $societe['logo'] ?? null,
        ]);

        $societe['rib'] = Crypto::decrypt($societe['rib']);

        $stats = $this->getStats((int) $id);

        $title = 'Infos société';
        $actions = '';

        $this->render('societes/show.php', [
            'title'        => $title,
            'browserTitle' => 'Infos — ' . $societe['raison_sociale'],
            'actions'      => $actions,
            'societe'      => $societe,
            'stats'        => $stats,
        ]);
    }

    private function getStats(int $societeId): array
    {
        $nbSalaries = (int) $this->db->query("SELECT COUNT(*) FROM salaries WHERE societe_id = $societeId AND actif = 1")->fetchColumn();
        $nbPaies = (int) $this->db->query("SELECT COUNT(*) FROM paies WHERE societe_id = $societeId")->fetchColumn();
        $masseSalariale = (float) $this->db->query("SELECT COALESCE(SUM(net_a_payer), 0) FROM paies WHERE societe_id = $societeId")->fetchColumn();
        $nbPeriodes = (int) $this->db->query("SELECT COUNT(*) FROM periodes WHERE societe_id = $societeId")->fetchColumn();

        $dernierePeriode = $this->db->query(
            "SELECT p.mois, p.annee, p.cloturee
             FROM periodes p
             WHERE p.societe_id = $societeId
             ORDER BY p.annee DESC, p.mois DESC
             LIMIT 1"
        )->fetch();

        return [
            'nb_salaries'    => $nbSalaries,
            'nb_paies'       => $nbPaies,
            'nb_periodes'    => $nbPeriodes,
            'masse_salariale' => $masseSalariale,
            'derniere_periode' => $dernierePeriode,
        ];
    }

    public function salaries(int $id): void
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
            'logo'           => $societe['logo'] ?? null,
        ]);

        $salaries = $this->db->query("SELECT s.*, f.nom as fonction_nom FROM salaries s LEFT JOIN fonctions f ON s.fonction_id = f.id WHERE s.societe_id = $id AND s.actif = 1 ORDER BY LENGTH(s.matricule), s.matricule")->fetchAll();
        foreach ($salaries as &$s) {
            $s['cin'] = Crypto::tryDecrypt($s['cin'] ?? '');
        }
        unset($s);

        $title = 'Salariés';
        $actions = '';

        $this->render('societes/salaries_list.php', [
            'title'        => $title,
            'browserTitle' => 'Salariés — ' . $societe['raison_sociale'],
            'actions'      => $actions,
            'societe'      => $societe,
            'salaries'     => $salaries,
        ]);
    }

    public function paies(int $id): void
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
            'logo'           => $societe['logo'] ?? null,
        ]);

        $periodes = $this->db->query("SELECT p.*, (SELECT COUNT(*) FROM paies WHERE periode_id = p.id) as nb_paies FROM periodes p WHERE p.societe_id = $id ORDER BY p.annee DESC, p.mois DESC")->fetchAll();

        $title = 'Paies';
        $actions = '';

        $this->render('societes/paies_list.php', [
            'title'        => $title,
            'browserTitle' => 'Paies — ' . $societe['raison_sociale'],
            'actions'      => $actions,
            'societe'      => $societe,
            'periodes'     => $periodes,
        ]);
    }

    public function bulletins(int $id): void
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
            'logo'           => $societe['logo'] ?? null,
        ]);

        $bulletins = $this->db->query("
            SELECT b.*, pa.salaire_brut, pa.net_a_payer, pa.ir, s.nom_famille, s.prenom, p.mois, p.annee
            FROM bulletins b
            JOIN paies pa ON b.paie_id = pa.id
            JOIN salaries s ON pa.salarie_id = s.id
            JOIN periodes p ON pa.periode_id = p.id
            WHERE pa.societe_id = $id
            ORDER BY p.annee DESC, p.mois DESC, s.nom_famille
        ")->fetchAll();

        $title = 'Bulletins';
        $actions = '';

        $this->render('societes/bulletins_list.php', [
            'title'        => $title,
            'browserTitle' => 'Bulletins — ' . $societe['raison_sociale'],
            'actions'      => $actions,
            'societe'      => $societe,
            'bulletins'    => $bulletins,
        ]);
    }

    public function cnss(int $id): void
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
            'logo'           => $societe['logo'] ?? null,
        ]);

        $periodes = $this->db->query("SELECT p.*, (SELECT COUNT(*) FROM paies WHERE periode_id = p.id) as nb_paies FROM periodes p WHERE p.societe_id = $id ORDER BY p.annee DESC, p.mois DESC")->fetchAll();
        $societe['rib'] = Crypto::decrypt($societe['rib']);

        $title = 'CNSS / Damancom';
        $actions = '';

        $this->render('societes/cnss.php', [
            'title'        => $title,
            'browserTitle' => 'CNSS / Damancom — ' . $societe['raison_sociale'],
            'actions'      => $actions,
            'societe'      => $societe,
            'periodes'     => $periodes,
        ]);
    }

    public function ir(int $id): void
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
            'logo'           => $societe['logo'] ?? null,
        ]);

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

        $title = 'IR / SIMPL';
        $actions = '';

        $this->render('societes/ir.php', [
            'title'        => $title,
            'browserTitle' => 'IR / SIMPL — ' . $societe['raison_sociale'],
            'actions'      => $actions,
            'societe'      => $societe,
            'periodes'     => $periodes,
            'bulletins'    => $bulletins,
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

            $logoPath = $this->handleLogoUpload();
            if ($logoPath !== null) {
                if (!empty($societe['logo'])) {
                    @unlink(Helper::logoFilePath($societe['logo']));
                }
                $this->db->prepare("UPDATE societes SET logo = ? WHERE id = ?")->execute([$logoPath, $id]);
            } elseif (!empty($_POST['logo_remove']) && !empty($societe['logo'])) {
                @unlink(Helper::logoFilePath($societe['logo']));
                $this->db->prepare("UPDATE societes SET logo = NULL WHERE id = ?")->execute([$id]);
            }

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
            'logo'           => $societe['logo'] ?? null,
        ]);

        // Delete actions via GET
        $deleteActions = [
            'delete_service'    => ['table' => 'services',             'tab' => 'services'],
            'delete_fonction'   => ['table' => 'fonctions',           'tab' => 'services'],
            'delete_gain'       => ['table' => 'rubriques_gains',      'tab' => 'gains'],
            'delete_retenue'    => ['table' => 'rubriques_retenues',   'tab' => 'retenues'],
            'delete_organisme'  => ['table' => 'organismes',           'tab' => 'organismes_sociaux'],
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
                    INSERT INTO parametres_cnss_amo (societe_id, plafond_cnss, taux_cnss_salarial, taux_cnss_patronal, taux_cnss_patronal_non_plafonne, taux_amo_salarial, taux_amo_patronal, taux_amo_total, taux_allocations_familiales, taux_prestations_sociales, taxe_formation, participation_amo, taux_penalites_cnss, taux_penalites_tfp, taux_penalites_amo, penalite_cnss_premier_mois, penalite_cnss_mois_suivants, penalite_amo_taux, astreinte_cnss_par_salarie, astreinte_amo_par_salarie)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE plafond_cnss=VALUES(plafond_cnss), taux_cnss_salarial=VALUES(taux_cnss_salarial), taux_cnss_patronal=VALUES(taux_cnss_patronal), taux_cnss_patronal_non_plafonne=VALUES(taux_cnss_patronal_non_plafonne), taux_amo_salarial=VALUES(taux_amo_salarial), taux_amo_patronal=VALUES(taux_amo_patronal), taux_amo_total=VALUES(taux_amo_total), taux_allocations_familiales=VALUES(taux_allocations_familiales), taux_prestations_sociales=VALUES(taux_prestations_sociales), taxe_formation=VALUES(taxe_formation), participation_amo=VALUES(participation_amo), taux_penalites_cnss=VALUES(taux_penalites_cnss), taux_penalites_tfp=VALUES(taux_penalites_tfp), taux_penalites_amo=VALUES(taux_penalites_amo), penalite_cnss_premier_mois=VALUES(penalite_cnss_premier_mois), penalite_cnss_mois_suivants=VALUES(penalite_cnss_mois_suivants), penalite_amo_taux=VALUES(penalite_amo_taux), astreinte_cnss_par_salarie=VALUES(astreinte_cnss_par_salarie), astreinte_amo_par_salarie=VALUES(astreinte_amo_par_salarie)
                ");
                $stmt->execute([
                    $id,
                    $_POST['plafond_cnss'] ?? 6000,
                    $_POST['taux_cnss_salarial'] ?? 4.48,
                    $_POST['taux_cnss_patronal'] ?? 8.98,
                    $_POST['taux_cnss_patronal_non_plafonne'] ?? 0,
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
                    $this->redirect('/paie-me/societes/' . $id . '/cnss');
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
                $this->redirect('/paie-me/societes/' . $id . '/cnss');
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
                        'source_maj' => $p('source_maj'),
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
            } elseif ($sousTab === 'organismes' || $sousTab === 'organismes_sociaux') {
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
            'general'        => 'Informations générales',
            'banque'         => 'Coordonnées bancaires',
            'teleservices'   => 'Accès téléservices',
            'codification'   => 'Codification & numérotation',
            'bcp'            => 'BCP — Bordereau de Cotisations et Paiement',
            'services'       => 'Services',
            'gains'          => 'Rubriques de gains',
            'retenues'       => 'Rubriques de retenues',
            'attestations'   => 'Modèles d\'attestation',
            'cnss_amo'       => 'CNSS et AMO',
            'organismes_sociaux' => 'Organismes Sociaux',
            'journal'        => 'Journal de comptabilisation',
        ];
        $subView = 'banque';
        if (in_array($sous_tab, array_keys($titles))) {
            $subView = $sous_tab;
        }
        $baseUrl = '/paie-me/societes/' . $id . '/parametres';

        $this->render('societes/parametres/' . $subView . '.php', [
            'title'         => 'Paramètres',
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

    public function baremes(int $id, string $sous_tab = 'anciennete'): void
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
            'logo'           => $societe['logo'] ?? null,
        ]);

        if (isset($_GET['delete_bareme'])) {
            $this->db->exec("DELETE FROM bareme_smig_smag WHERE id = " . (int)$_GET['delete_bareme'] . " AND societe_id = $id");
            Session::setFlash('success', 'Barème supprimé.');
            $this->redirect('/paie-me/societes/' . $id . '/baremes/smig_smag');
        }

        if ($this->isPost()) {
            $this->checkCsrf();
            $sousTab = $_POST['sous_tab'] ?? 'anciennete';

            if ($sousTab === 'bareme' || $sousTab === 'impot_revenu') {
                foreach ($_POST['min'] ?? [] as $idBareme => $min) {
                    $max = $_POST['max'][$idBareme] ?? 0;
                    $taux = $_POST['taux'][$idBareme] ?? 0;
                    $deduction = $_POST['deduction'][$idBareme] ?? 0;
                    $type = $_POST['type'][$idBareme] ?? 'mensuel';
                    $stmt = $this->db->prepare("UPDATE bareme_ir SET min=?, max=?, taux=?, deduction=?, type=? WHERE id=?");
                    $stmt->execute([$min, $max, $taux, $deduction, $type, $idBareme]);
                }
                Session::setFlash('success', 'Barème IR mis à jour.');
                $this->redirect('/paie-me/societes/' . $id . '/baremes/' . $sousTab);
                return;
            }

            if ($sousTab === 'anciennete') {
                $this->db->exec("DELETE FROM bareme_anciennete WHERE societe_id = $id");
                foreach ($_POST['annees_min'] ?? [] as $k => $v) {
                    if ($_POST['taux'][$k] > 0) {
                        $stmt = $this->db->prepare("INSERT INTO bareme_anciennete (societe_id, annees_min, annees_max, taux) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$id, $v, $_POST['annees_max'][$k], $_POST['taux'][$k]]);
                    }
                }
                Session::setFlash('success', 'Barème d\'ancienneté mis à jour.');
            }

            if ($sousTab === 'conge_annuel') {
                $stmt = $this->db->prepare("
                    INSERT INTO conge_annuel (societe_id, jours_par_mois, report_autorise, report_max, delai_anciennete, report_max_annees)
                    VALUES (?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE report_autorise=VALUES(report_autorise), report_max=VALUES(report_max), delai_anciennete=VALUES(delai_anciennete), report_max_annees=VALUES(report_max_annees)
                ");
                $reportMax = min(15, max(0, (int)($_POST['report_max'] ?? 15)));
                $reportMaxAnnees = min(2, max(0, (int)($_POST['report_max_annees'] ?? 2)));
                $delaiAnciennete = max(6, (int)($_POST['delai_anciennete'] ?? 6));
                $stmt->execute([$id, $_POST['jours_par_mois'] ?? 1.50, (int)($_POST['report_autorise'] ?? 0), $reportMax, $delaiAnciennete, $reportMaxAnnees]);

                $this->db->exec("DELETE FROM droit_conge WHERE societe_id = $id");
                if (!empty($_POST['dc_annees_min'])) {
                    $ins = $this->db->prepare("INSERT INTO droit_conge (societe_id, annees_min, annees_max, jours_par_mois, jours_supplementaires) VALUES (?, ?, ?, ?, ?)");
                    foreach ($_POST['dc_annees_min'] as $k => $min) {
                        $max = (int)($_POST['dc_annees_max'][$k] ?? 0);
                        $jpm = (float)($_POST['dc_jours_par_mois'][$k] ?? 1.50);
                        $jsup = (float)($_POST['dc_jours_sup'][$k] ?? 0);
                        if ($max > 0 || $jpm > 0) {
                            $ins->execute([$id, $min, $max, $jpm, $jsup]);
                        }
                    }
                }

                Session::setFlash('success', 'Configuration congé annuel mise à jour.');
            }

            if ($sousTab === 'jours_feries') {
                if (!empty($_POST['edit_jf_id'])) {
                    $stmt = $this->db->prepare("UPDATE jours_feries SET nom=?, jour=?, mois=?, type=? WHERE id=? AND societe_id=?");
                    $stmt->execute([$_POST['nom'], $_POST['jour'], $_POST['mois'], $_POST['type'] ?? 'fixe', (int)$_POST['edit_jf_id'], $id]);
                    Session::setFlash('success', 'Jour férié modifié.');
                } elseif (!empty($_POST['nom'])) {
                    $stmt = $this->db->prepare("INSERT INTO jours_feries (societe_id, nom, jour, mois, type, actif) VALUES (?, ?, ?, ?, ?, 1)");
                    $stmt->execute([$id, $_POST['nom'], $_POST['jour'], $_POST['mois'], $_POST['type'] ?? 'fixe']);
                    Session::setFlash('success', 'Jour férié ajouté.');
                }
                if (isset($_GET['delete_jf'])) {
                    $this->db->exec("DELETE FROM jours_feries WHERE id = " . (int)$_GET['delete_jf'] . " AND societe_id = $id");
                    Session::setFlash('success', 'Jour férié supprimé.');
                    $this->redirect('/paie-me/societes/' . $id . '/baremes/jours_feries');
                }
            }

            if ($sousTab === 'heures_sup') {
                $stmt = $this->db->prepare("
                    INSERT INTO bareme_heures_sup (societe_id, taux_normal, taux_majore, taux_jour_ferie, seuil_heures)
                    VALUES (?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE taux_normal=VALUES(taux_normal), taux_majore=VALUES(taux_majore), taux_jour_ferie=VALUES(taux_jour_ferie), seuil_heures=VALUES(seuil_heures)
                ");
                $stmt->execute([$id, $_POST['taux_normal'] ?? 25, $_POST['taux_majore'] ?? 50, $_POST['taux_jour_ferie'] ?? 100, $_POST['seuil_heures'] ?? 8]);
                Session::setFlash('success', 'Barème heures sup mis à jour.');
            }

            if ($sousTab === 'smig_smag') {
                if (!empty($_POST['bareme_id'])) {
                    $upd = $this->db->prepare("UPDATE bareme_smig_smag SET horaire=?, mensuel=?, date_effet=? WHERE id=? AND societe_id=?");
                    foreach ($_POST['bareme_id'] as $idx => $bid) {
                        $horaire = (float) ($_POST['horaire'][$idx] ?? 0);
                        $mensuel = (float) ($_POST['mensuel'][$idx] ?? 0);
                        $dateEffet = !empty($_POST['date_effet'][$idx]) ? $_POST['date_effet'][$idx] : null;
                        $upd->execute([$horaire, $mensuel, $dateEffet, $bid, $id]);
                    }
                }
                $anneeNew = (int) ($_POST['nouvelle_annee'] ?? 0);
                $typeNew = $_POST['nouveau_type'] ?? '';
                if ($anneeNew > 0 && in_array($typeNew, ['SMIG', 'SMAG'])) {
                    $ins = $this->db->prepare("INSERT INTO bareme_smig_smag (societe_id, annee, type, horaire, mensuel) VALUES (?, ?, ?, ?, ?)");
                    $ins->execute([$id, $anneeNew, $typeNew, $_POST['nouveau_horaire'] ?? 0, $_POST['nouveau_mensuel'] ?? 0]);
                }
                Session::setFlash('success', 'Barème SMIG/SMAG mis à jour.');
            }

            if ($sousTab === 'reference') {
                $this->handleReferencePost($id);
                return;
            }

            $this->redirect('/paie-me/societes/' . $id . '/baremes/' . $sousTab);
        }

        $baremeMensuel = $this->db->query("SELECT * FROM bareme_ir WHERE type='mensuel' ORDER BY `min`")->fetchAll();
        $baremeAnnuel  = $this->db->query("SELECT * FROM bareme_ir WHERE type='annuel' ORDER BY `min`")->fetchAll();
        $anciennete    = $this->db->query("SELECT * FROM bareme_anciennete WHERE societe_id = $id ORDER BY annees_min")->fetchAll();
        $conge         = $this->db->query("SELECT * FROM conge_annuel WHERE societe_id = $id")->fetch();
        $droitConge    = $this->db->query("SELECT * FROM droit_conge WHERE societe_id = $id ORDER BY annees_min")->fetchAll();
        $joursFeries   = $this->db->query("SELECT * FROM jours_feries WHERE societe_id = $id ORDER BY mois, jour")->fetchAll();
        $heuresSup     = $this->db->query("SELECT * FROM bareme_heures_sup WHERE societe_id = $id")->fetch();
        $baremeSmigSmag = $this->db->query("SELECT * FROM bareme_smig_smag WHERE societe_id = $id ORDER BY annee DESC, type")->fetchAll();

        $refSmigSmag   = $this->db->query("SELECT * FROM bareme_smig_smag WHERE societe_id IS NULL ORDER BY annee DESC, type")->fetchAll();
        $refAnciennete = $this->db->query("SELECT * FROM bareme_anciennete WHERE societe_id IS NULL ORDER BY annees_min")->fetchAll();
        $refHeuresSup  = $this->db->query("SELECT * FROM bareme_heures_sup WHERE societe_id IS NULL")->fetch();
        $isAdmin       = Session::get('user_role') === 'admin';

        $titles = [
            'anciennete'    => 'Barème d\'ancienneté',
            'conge_annuel'  => 'Congé annuel',
            'jours_feries'  => 'Jours fériés',
            'impot_revenu'  => 'Impôt sur le revenu',
            'heures_sup'    => 'Heures supplémentaires',
            'smig_smag'     => 'Barème SMIG & SMAG',
            'reference'     => 'Barème de référence',
        ];
        $subView = in_array($sous_tab, array_keys($titles)) ? $sous_tab : 'anciennete';
        $baseUrl = '/paie-me/societes/' . $id . '/baremes';

        $this->render('societes/baremes/' . $subView . '.php', [
            'title'        => 'Barèmes',
            'societe'      => $societe,
            'baseUrl'      => $baseUrl,
            'bareme'       => $baremeMensuel,
            'baremeAnnuel' => $baremeAnnuel,
            'anciennete'   => $anciennete,
            'conge'        => $conge,
            'droitConge'   => $droitConge,
            'joursFeries'   => $joursFeries,
            'heuresSup'     => $heuresSup,
            'baremeSmigSmag' => $baremeSmigSmag,
            'refSmigSmag'   => $refSmigSmag,
            'refAnciennete' => $refAnciennete,
            'refHeuresSup'  => $refHeuresSup,
            'isAdmin'       => $isAdmin,
        ]);
    }

    /**
     * Gestion POST de la sous-page « Barème de référence » (réservé admin).
     *  - ref_action = apply  → propage le barème de référence à toutes les sociétés
     *  - ref_action = save   → enregistre la section du barème de référence (ref_type)
     */
    private function handleReferencePost(int $societeId): void
    {
        if (Session::get('user_role') !== 'admin') {
            Session::setFlash('error', 'Seul un administrateur peut gérer le barème de référence.');
            $this->redirect('/paie-me/societes/' . $societeId . '/baremes/reference');
            return;
        }

        $refAction = $_POST['ref_action'] ?? 'save';

        if ($refAction === 'apply') {
            $societeIds = $this->db->query("SELECT id FROM societes ORDER BY id")->fetchAll(\PDO::FETCH_COLUMN);
            if (empty($societeIds)) {
                Session::setFlash('info', 'Aucune société à mettre à jour.');
                $this->redirect('/paie-me/societes/' . $societeId . '/baremes/reference');
                return;
            }

            $nbSoc = count($societeIds);

            $refSmig = $this->db->query("SELECT * FROM bareme_smig_smag WHERE societe_id IS NULL")->fetchAll();
            if ($refSmig) {
                $ups = $this->db->prepare("INSERT INTO bareme_smig_smag (societe_id, annee, type, horaire, mensuel, date_effet)
                    VALUES (?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE horaire=VALUES(horaire), mensuel=VALUES(mensuel), date_effet=VALUES(date_effet)");
                foreach ($societeIds as $sid) {
                    foreach ($refSmig as $r) {
                        $ups->execute([$sid, $r['annee'], $r['type'], $r['horaire'], $r['mensuel'], $r['date_effet']]);
                    }
                }
            }

            $refAnc = $this->db->query("SELECT * FROM bareme_anciennete WHERE societe_id IS NULL ORDER BY annees_min")->fetchAll();
            if ($refAnc) {
                $del = $this->db->prepare("DELETE FROM bareme_anciennete WHERE societe_id = ?");
                $ins = $this->db->prepare("INSERT INTO bareme_anciennete (societe_id, annees_min, annees_max, taux) VALUES (?, ?, ?, ?)");
                foreach ($societeIds as $sid) {
                    $del->execute([$sid]);
                    foreach ($refAnc as $a) {
                        $ins->execute([$sid, $a['annees_min'], $a['annees_max'], $a['taux']]);
                    }
                }
            }

            $refHs = $this->db->query("SELECT * FROM bareme_heures_sup WHERE societe_id IS NULL")->fetch();
            if ($refHs) {
                $ups = $this->db->prepare("INSERT INTO bareme_heures_sup (societe_id, taux_normal, taux_majore, taux_jour_ferie, seuil_heures)
                    VALUES (?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE taux_normal=VALUES(taux_normal), taux_majore=VALUES(taux_majore), taux_jour_ferie=VALUES(taux_jour_ferie), seuil_heures=VALUES(seuil_heures)");
                foreach ($societeIds as $sid) {
                    $ups->execute([$sid, $refHs['taux_normal'], $refHs['taux_majore'], $refHs['taux_jour_ferie'], $refHs['seuil_heures']]);
                }
            }

            Session::setFlash('success', 'Barème de référence appliqué à ' . $nbSoc . ' société(s).');
            $this->redirect('/paie-me/societes/' . $societeId . '/baremes/reference');
            return;
        }

        $refType = $_POST['ref_type'] ?? '';

        if ($refType === 'smig') {
            $this->db->exec("DELETE FROM bareme_smig_smag WHERE societe_id IS NULL");
            if (!empty($_POST['ref_smig_annee'])) {
                $ins = $this->db->prepare("INSERT INTO bareme_smig_smag (societe_id, annee, type, horaire, mensuel, date_effet) VALUES (NULL, ?, ?, ?, ?, ?)");
                foreach ($_POST['ref_smig_annee'] as $k => $annee) {
                    $annee = (int) $annee;
                    $type = $_POST['ref_smig_type'][$k] ?? 'SMIG';
                    if ($annee <= 0 || !in_array($type, ['SMIG', 'SMAG'])) continue;
                    $dateEffet = !empty($_POST['ref_smig_date_effet'][$k]) ? $_POST['ref_smig_date_effet'][$k] : null;
                    $ins->execute([$annee, $type, (float) ($_POST['ref_smig_horaire'][$k] ?? 0), (float) ($_POST['ref_smig_mensuel'][$k] ?? 0), $dateEffet]);
                }
            }
            Session::setFlash('success', 'Barème de référence SMIG/SMAG enregistré.');
        }

        if ($refType === 'anciennete') {
            $this->db->exec("DELETE FROM bareme_anciennete WHERE societe_id IS NULL");
            if (!empty($_POST['ref_anc_min'])) {
                $ins = $this->db->prepare("INSERT INTO bareme_anciennete (societe_id, annees_min, annees_max, taux) VALUES (NULL, ?, ?, ?)");
                foreach ($_POST['ref_anc_min'] as $k => $min) {
                    $taux = (float) ($_POST['ref_anc_taux'][$k] ?? 0);
                    if ($taux < 0) continue;
                    $ins->execute([(int) $min, (int) ($_POST['ref_anc_max'][$k] ?? 0), $taux]);
                }
            }
            Session::setFlash('success', 'Barème de référence d\'ancienneté enregistré.');
        }

        if ($refType === 'heures_sup') {
            $this->db->exec("DELETE FROM bareme_heures_sup WHERE societe_id IS NULL");
            $stmt = $this->db->prepare("INSERT INTO bareme_heures_sup (societe_id, taux_normal, taux_majore, taux_jour_ferie, seuil_heures) VALUES (NULL, ?, ?, ?, ?)");
            $stmt->execute([$_POST['ref_hs_taux_normal'] ?? 25, $_POST['ref_hs_taux_majore'] ?? 50, $_POST['ref_hs_taux_jour_ferie'] ?? 100, $_POST['ref_hs_seuil_heures'] ?? 8]);
            Session::setFlash('success', 'Barème de référence heures sup enregistré.');
        }

        $this->redirect('/paie-me/societes/' . $societeId . '/baremes/reference');
    }

    private function handleLogoUpload(): ?string
    {
        if (empty($_FILES['logo']['name'])) {
            return null;
        }

        $file = $_FILES['logo'];
        if ((int) $file['error'] !== UPLOAD_ERR_OK) {
            Session::setFlash('error', 'Erreur lors de l\'upload du logo.');
            return null;
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg'], true)) {
            Session::setFlash('error', 'Format de logo invalide (PNG, JPG, GIF, WEBP, SVG autorisés).');
            return null;
        }

        if (!is_uploaded_file($file['tmp_name'])) {
            Session::setFlash('error', 'Fichier uploadé invalide.');
            return null;
        }

        $dir = __DIR__ . '/../uploads/logos';
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $nom = 'logo_' . bin2hex(random_bytes(8)) . '.' . $ext;
        $dest = $dir . '/' . $nom;
        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            Session::setFlash('error', 'Impossible de sauvegarder le logo.');
            return null;
        }

        return 'uploads/logos/' . $nom;
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
