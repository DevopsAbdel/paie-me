<?php

namespace Controllers;

use Core\Controller;
use Core\Model;
use Core\Session;
use Dompdf\Dompdf;
use PDO;

class CongeController extends Controller
{
    private PDO $db;

    public function __construct()
    {
        if (!Session::has('user_id')) {
            $this->redirect('/paie-me/login');
        }
        $this->db = Model::db();
    }

    public function index(int $id): void
    {
        $userId = Session::get('user_id');
        $societe = $this->db->query("SELECT * FROM societes WHERE id = $id AND user_id = $userId")->fetch();
        if (!$societe) {
            Session::setFlash('error', 'Société introuvable.');
            $this->redirect('/paie-me/societes');
        }

        Session::set('societe_context', [
            'id' => $societe['id'],
            'raison_sociale' => $societe['raison_sociale'],
            'ice' => $societe['ice'],
            'cnss' => $societe['cnss'],
        ]);

        $this->db->exec("DELETE FROM conges WHERE societe_id = $id AND date_fin < '1900-01-01'");

        $conges = $this->db->query("
            SELECT c.*, CONCAT(s.prenom, ' ', s.nom_famille) AS nom_complet, s.matricule
            FROM conges c
            JOIN salaries s ON s.id = c.salarie_id
            WHERE c.societe_id = $id
            ORDER BY c.date_debut DESC
        ")->fetchAll();

        $this->render('conges/conges.php', [
            'title' => 'Gestion des congés — ' . $societe['raison_sociale'],
            'societe' => $societe,
            'baseUrl' => '/paie-me/societes/' . $id . '/conges',
            'conges' => $conges,
        ]);
    }

    public function nouveau(int $id): void
    {
        $userId = Session::get('user_id');
        $societe = $this->db->query("SELECT * FROM societes WHERE id = $id AND user_id = $userId")->fetch();
        if (!$societe) { $this->redirect('/paie-me/societes'); }

        if ($this->isPost()) {
            $this->checkCsrf();
            $salarieId = (int) ($_POST['salarie_id'] ?? 0);
            $dateDebut = $_POST['date_debut'] ?? '';
            $dateFin   = $_POST['date_fin'] ?? '';
            $nbJours   = (float) ($_POST['nb_jours'] ?? 0);
            $type      = $_POST['type_conge'] ?? 'paye';
            $obs       = trim($_POST['observation'] ?? '');
            $statut    = $_POST['statut'] ?? 'en_attente';

            if ($salarieId && $dateDebut && $dateFin && $nbJours > 0) {
                if ($type === 'paye') {
                    $congeAnnuel = $this->db->query("SELECT * FROM conge_annuel WHERE societe_id = $id")->fetch();
                    $delai = (int) ($congeAnnuel['delai_anciennete'] ?? 6);

                    $salarie = $this->db->query("SELECT date_embauche FROM salaries WHERE id = $salarieId")->fetch();
                    if ($salarie && $salarie['date_embauche']) {
                        $embauche = new \DateTime($salarie['date_embauche']);
                        $debut = new \DateTime($dateDebut);
                        $moisAnciennete = ($embauche->diff($debut)->m) + ($embauche->diff($debut)->y * 12);
                        if ($moisAnciennete < $delai) {
                            Session::setFlash('error', "Le salarié n'a pas assez d'ancienneté. Congé payé autorisé après {$delai} mois de travail (actuellement {$moisAnciennete} mois).");
                            $this->redirect('/paie-me/societes/' . $id . '/conges/nouveau');
                        }
                    }

                    $reportMaxAnnees = (int) ($congeAnnuel['report_max_annees'] ?? 2);
                    if ($reportMaxAnnees > 0) {
                        $annee = (int) date('Y', strtotime($dateDebut));
                        $prevReport = $this->db->query("SELECT report FROM conges_soldes WHERE salarie_id = $salarieId AND annee = " . ($annee - 1))->fetch();
                        if ($prevReport && (float) $prevReport['report'] > 0) {
                            $prevPrevReport = $this->db->query("SELECT report FROM conges_soldes WHERE salarie_id = $salarieId AND annee = " . ($annee - 2))->fetch();
                            if ($prevPrevReport && (float) $prevPrevReport['report'] > 0) {
                                Session::setFlash('error', "Le report de congé est limité à {$reportMaxAnnees} années consécutives. Le report ne peut plus être reporté.");
                                $this->redirect('/paie-me/societes/' . $id . '/conges/nouveau');
                            }
                        }
                    }
                }
                $stmt = $this->db->prepare("
                    INSERT INTO conges (societe_id, salarie_id, date_debut, date_fin, nb_jours, type_conge, observation, statut)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$id, $salarieId, $dateDebut, $dateFin, $nbJours, $type, $obs, $statut]);

                if ($type === 'paye') {
                    $annee = (int) date('Y', strtotime($dateDebut));
                    $upd = $this->db->prepare("
                        UPDATE conges_soldes SET conges_pris = conges_pris + ?
                        WHERE salarie_id = ? AND annee = ?
                    ");
                    $upd->execute([$nbJours, $salarieId, $annee]);
                    if ($upd->rowCount() === 0) {
                        $ins = $this->db->prepare("
                            INSERT INTO conges_soldes (societe_id, salarie_id, annee, solde_initial, conges_pris, report)
                            VALUES (?, ?, ?, 0, ?, 0)
                            ON DUPLICATE KEY UPDATE conges_pris = conges_pris + VALUES(conges_pris)
                        ");
                        $ins->execute([$id, $salarieId, $annee, $nbJours]);
                    }
                }

                Session::setFlash('success', 'Demande de congé créée.');
            } else {
                Session::setFlash('error', 'Veuillez remplir tous les champs obligatoires.');
            }
            $this->redirect('/paie-me/societes/' . $id . '/conges');
        }

        $salaries = $this->db->query("
            SELECT id, matricule, nom_famille, prenom
            FROM salaries WHERE societe_id = $id
            ORDER BY nom_famille, prenom
        ")->fetchAll();

        $this->render('conges/nouveau.php', [
            'title' => 'Nouvelle demande de congé — ' . $societe['raison_sociale'],
            'societe' => $societe,
            'baseUrl' => '/paie-me/societes/' . $id . '/conges',
            'salaries' => $salaries,
        ]);
    }

    public function modifier(int $id, int $id2): void
    {
        $userId = Session::get('user_id');
        $societe = $this->db->query("SELECT * FROM societes WHERE id = $id AND user_id = $userId")->fetch();
        if (!$societe) { $this->redirect('/paie-me/societes'); }

        $congeId = $id2;
        $conge = $this->db->query("
            SELECT c.*, CONCAT(s.prenom, ' ', s.nom_famille) AS nom_complet, s.matricule
            FROM conges c
            JOIN salaries s ON s.id = c.salarie_id
            WHERE c.id = $congeId AND c.societe_id = $id
        ")->fetch();
        if (!$conge) { $this->redirect('/paie-me/societes/' . $id . '/conges'); }

        if ($this->isPost()) {
            $this->checkCsrf();
            $anciensJours = (float) $conge['nb_jours'];
            $dateDebut = $_POST['date_debut'] ?? $conge['date_debut'];
            $dateFin   = $_POST['date_fin'] ?? $conge['date_fin'];
            $nbJours   = (float) ($_POST['nb_jours'] ?? $anciensJours);
            $type      = $_POST['type_conge'] ?? $conge['type_conge'];
            $obs       = trim($_POST['observation'] ?? '');
            $statut    = $_POST['statut'] ?? $conge['statut'];

            if ($type === 'paye') {
                $congeAnnuel = $this->db->query("SELECT * FROM conge_annuel WHERE societe_id = $id")->fetch();
                $delai = (int) ($congeAnnuel['delai_anciennete'] ?? 6);

                $salarie = $this->db->query("SELECT date_embauche FROM salaries WHERE id = {$conge['salarie_id']}")->fetch();
                if ($salarie && $salarie['date_embauche']) {
                    $embauche = new \DateTime($salarie['date_embauche']);
                    $debut = new \DateTime($dateDebut);
                    $moisAnciennete = ($embauche->diff($debut)->m) + ($embauche->diff($debut)->y * 12);
                    if ($moisAnciennete < $delai) {
                        Session::setFlash('error', "Le salarié n'a pas assez d'ancienneté. Congé payé autorisé après {$delai} mois de travail (actuellement {$moisAnciennete} mois).");
                        $this->redirect('/paie-me/societes/' . $id . '/conges/modifier/' . $congeId);
                    }
                }

                $reportMaxAnnees = (int) ($congeAnnuel['report_max_annees'] ?? 2);
                if ($reportMaxAnnees > 0) {
                    $annee = (int) date('Y', strtotime($dateDebut));
                    $prevReport = $this->db->query("SELECT report FROM conges_soldes WHERE salarie_id = {$conge['salarie_id']} AND annee = " . ($annee - 1))->fetch();
                    if ($prevReport && (float) $prevReport['report'] > 0) {
                        $prevPrevReport = $this->db->query("SELECT report FROM conges_soldes WHERE salarie_id = {$conge['salarie_id']} AND annee = " . ($annee - 2))->fetch();
                        if ($prevPrevReport && (float) $prevPrevReport['report'] > 0) {
                            Session::setFlash('error', "Le report de congé est limité à {$reportMaxAnnees} années consécutives. Le report ne peut plus être reporté.");
                            $this->redirect('/paie-me/societes/' . $id . '/conges/modifier/' . $congeId);
                        }
                    }
                }
            }

            $diff = $nbJours - $anciensJours;
            $stmt = $this->db->prepare("
                UPDATE conges SET date_debut=?, date_fin=?, nb_jours=?, type_conge=?, observation=?, statut=?
                WHERE id=? AND societe_id=?
            ");
            $stmt->execute([$dateDebut, $dateFin, $nbJours, $type, $obs, $statut, $congeId, $id]);

            if ($type === 'paye' && $diff != 0) {
                $annee = (int) date('Y', strtotime($dateDebut));
                $upd = $this->db->prepare("
                    UPDATE conges_soldes SET conges_pris = conges_pris + ?
                    WHERE salarie_id = ? AND annee = ?
                ");
                $upd->execute([$diff, $conge['salarie_id'], $annee]);
            }

            Session::setFlash('success', 'Congé modifié.');
            $this->redirect('/paie-me/societes/' . $id . '/conges');
        }

        $this->render('conges/nouveau.php', [
            'title' => 'Modifier le congé — ' . $societe['raison_sociale'],
            'societe' => $societe,
            'baseUrl' => '/paie-me/societes/' . $id . '/conges',
            'salaries' => [],
            'conge' => $conge,
        ]);
    }

    public function supprimer(int $id, int $id2): void
    {
        $userId = Session::get('user_id');
        $societe = $this->db->query("SELECT * FROM societes WHERE id = $id AND user_id = $userId")->fetch();
        if (!$societe) { $this->redirect('/paie-me/societes'); }

        $congeId = $id2;
        $conge = $this->db->query("SELECT * FROM conges WHERE id = $congeId AND societe_id = $id")->fetch();
        if ($conge) {
            if ($conge['type_conge'] === 'paye') {
                $annee = (int) date('Y', strtotime($conge['date_debut']));
                $upd = $this->db->prepare("
                    UPDATE conges_soldes SET conges_pris = GREATEST(0, conges_pris - ?)
                    WHERE salarie_id = ? AND annee = ?
                ");
                $upd->execute([$conge['nb_jours'], $conge['salarie_id'], $annee]);
            }
            $this->db->exec("DELETE FROM conges WHERE id = $congeId");
            Session::setFlash('success', 'Congé supprimé.');
        }

        $this->redirect('/paie-me/societes/' . $id . '/conges');
    }

    public function soldeInitial(int $id): void
    {
        $userId = Session::get('user_id');
        $societe = $this->db->query("SELECT * FROM societes WHERE id = $id AND user_id = $userId")->fetch();
        if (!$societe) { $this->redirect('/paie-me/societes'); }

        if ($this->isPost()) {
            $this->checkCsrf();
            $annee = (int) ($_POST['annee'] ?? date('Y'));
            if (!empty($_POST['solde']) && is_array($_POST['solde'])) {
                foreach ($_POST['solde'] as $salarieId => $soldeInit) {
                    $salarieId = (int) $salarieId;
                    $soldeInit = (float) $soldeInit;
                    $stmt = $this->db->prepare("
                        INSERT INTO conges_soldes (societe_id, salarie_id, annee, solde_initial, conges_pris, report)
                        VALUES (?, ?, ?, ?, 0, 0)
                        ON DUPLICATE KEY UPDATE solde_initial = VALUES(solde_initial)
                    ");
                    $stmt->execute([$id, $salarieId, $annee, $soldeInit]);
                }
            }
            Session::setFlash('success', 'Soldes initiaux enregistrés.');
            $this->redirect('/paie-me/societes/' . $id . '/conges/solde-initial');
        }

        $annee = (int) ($_GET['annee'] ?? date('Y'));
        $salaries = $this->db->query("
            SELECT s.id, s.matricule, s.nom_famille, s.prenom,
                   COALESCE(cs.solde_initial, 0) AS solde_initial,
                   COALESCE(cs.conges_pris, 0) AS conges_pris,
                   COALESCE(cs.report, 0) AS report
            FROM salaries s
            LEFT JOIN conges_soldes cs ON cs.salarie_id = s.id AND cs.annee = $annee
            WHERE s.societe_id = $id
            ORDER BY s.nom_famille, s.prenom
        ")->fetchAll();

        $this->render('conges/solde_initial.php', [
            'title' => 'Solde congé initial — ' . $societe['raison_sociale'],
            'societe' => $societe,
            'baseUrl' => '/paie-me/societes/' . $id . '/conges',
            'salaries' => $salaries,
            'annee' => $annee,
        ]);
    }

    public function attestation(int $id): void
    {
        $userId = Session::get('user_id');
        $societe = $this->db->query("SELECT * FROM societes WHERE id = $id AND user_id = $userId")->fetch();
        if (!$societe) { $this->redirect('/paie-me/societes'); }

        $congeId = (int) ($_GET['conge_id'] ?? 0);
        $conge = null;
        if ($congeId) {
            $conge = $this->db->query("
                SELECT c.*, CONCAT(s.prenom, ' ', s.nom_famille) AS nom_complet, s.matricule, s.poste
                FROM conges c
                JOIN salaries s ON s.id = c.salarie_id
                WHERE c.id = $congeId AND c.societe_id = $id
            ")->fetch();
        }

        $conges = $this->db->query("
            SELECT c.id, c.date_debut, c.date_fin, c.nb_jours, c.type_conge,
                   CONCAT(s.prenom, ' ', s.nom_famille) AS nom_complet, s.matricule
            FROM conges c
            JOIN salaries s ON s.id = c.salarie_id
            WHERE c.societe_id = $id
            ORDER BY c.date_debut DESC
        ")->fetchAll();

        $this->render('conges/attestation.php', [
            'title' => 'Attestation de congé — ' . $societe['raison_sociale'],
            'societe' => $societe,
            'baseUrl' => '/paie-me/societes/' . $id . '/conges',
            'conges' => $conges,
            'conge' => $conge,
        ]);
    }

    public function pdf(int $id, int $id2): void
    {
        $userId = Session::get('user_id');
        $societe = $this->db->query("SELECT * FROM societes WHERE id = $id AND user_id = $userId")->fetch();
        if (!$societe) { $this->redirect('/paie-me/societes'); }

        $congeId = $id2;
        $conge = $this->db->query("
            SELECT c.*, CONCAT(s.prenom, ' ', s.nom_famille) AS nom_complet, s.matricule, s.poste
            FROM conges c
            JOIN salaries s ON s.id = c.salarie_id
            WHERE c.id = $congeId AND c.societe_id = $id
        ")->fetch();
        if (!$conge) {
            Session::setFlash('error', 'Congé introuvable.');
            $this->redirect('/paie-me/societes/' . $id . '/conges/attestation');
        }

        ob_start();
        require __DIR__ . '/../views/conges/attestation_pdf.php';
        $html = ob_get_clean();

        $dompdf = new Dompdf(['defaultFont' => 'DejaVu Sans']);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $nomFichier = 'attestation_conge_' . $conge['matricule'] . '_' . date('Ymd', strtotime($conge['date_debut'])) . '.pdf';
        $dompdf->stream($nomFichier, ['Attachment' => true]);
        exit;
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
