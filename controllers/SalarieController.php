<?php

namespace Controllers;

use Core\Controller;
use Core\Model;
use Core\Session;
use Core\Audit;
use Core\Crypto;
use Core\SpreadsheetService;
use PDO;

class SalarieController extends Controller
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
        $ctx = Session::get('societe_context');
        $sql = "
            SELECT s.*, so.raison_sociale, f.nom as fonction_nom
            FROM salaries s
            JOIN societes so ON s.societe_id = so.id
            LEFT JOIN fonctions f ON s.fonction_id = f.id
            WHERE so.user_id = $userId
        ";
        if ($ctx) {
            $sql .= " AND s.societe_id = " . (int)$ctx['id'];
        }
        $sql .= " ORDER BY LENGTH(s.matricule), s.matricule";
        $salaries = $this->db->query($sql)->fetchAll();

        $this->render('salaries/index.php', [
            'title'    => 'Salariés',
            'salaries' => $salaries,
            'ctx'      => $ctx,
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
            $data = $this->getPostData();
            if ($ctx) {
                $data['societe_id'] = (int)$ctx['id'];
            }
            $data['rib'] = Crypto::encrypt($data['rib']);
            $data['cin'] = Crypto::encrypt($data['cin']);
            $services = $this->db->query("SELECT * FROM services WHERE societe_id = " . (int)$data['societe_id'] . " ORDER BY nom")->fetchAll();

            if (empty($data['matricule'])) {
                $prefix = 'SAL';
                $last = $this->db->query("SELECT matricule FROM salaries WHERE societe_id = " . (int)$data['societe_id'] . " ORDER BY id DESC LIMIT 1")->fetch();
                if ($last && preg_match('/(\d+)$/', $last['matricule'], $m)) {
                    $prefix = preg_replace('/\d+$/', '', $last['matricule']);
                    $data['matricule'] = $prefix . str_pad(((int)$m[1] + 1), 4, '0', STR_PAD_LEFT);
                } else {
                    $data['matricule'] = $prefix . '0001';
                }
            }

            $stmt = $this->db->prepare("
                INSERT INTO salaries (societe_id, service_id, fonction_id, matricule, nom_famille, prenom, sexe, adresse, date_naissance, lieu_naissance, date_embauche, cin, cnss, situation_familiale, nb_enfants, enfants_a_charge, personnes_a_charge, poste, type_contrat, salaire_base, type_salaire, frequence_paiement, mode_paiement, rib, indemnite_transport, indemnite_panier, indemnite_representation, avantage_logement, avances_salaire, mutuelle)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $data['societe_id'], $data['service_id'], $data['fonction_id'], $data['matricule'], $data['nom_famille'], $data['prenom'],
                $data['sexe'], $data['adresse'], $data['date_naissance'], $data['lieu_naissance'], $data['date_embauche'], $data['cin'],
                $data['cnss'], $data['situation_familiale'], $data['nb_enfants'], $data['enfants_a_charge'], $data['personnes_a_charge'], $data['poste'],
                $data['type_contrat'], $data['salaire_base'], $data['type_salaire'],
                $data['frequence_paiement'], $data['mode_paiement'], $data['rib'],
                $data['indemnite_transport'], $data['indemnite_panier'],
                $data['indemnite_representation'], $data['avantage_logement'],
                $data['avances_salaire'], $data['mutuelle'],
            ]);

            Audit::log($this->db, 'create', 'salarie', (int) $this->db->lastInsertId(), 'Création salarié: ' . $data['nom_famille'] . ' ' . $data['prenom']);

            $newSalarieId = (int) $this->db->lastInsertId();
            $this->saveIndemnitesCustom($newSalarieId);
            $this->saveGainsCustom($newSalarieId);

            Session::setFlash('success', 'Salarié ajouté avec succès.');
            $redirectId = $fromSociete ?: ($ctx ? $ctx['id'] : null);
            $this->redirect($redirectId ? '/paie-me/societes/' . $redirectId . '/salaries' : '/paie-me/salaries');
        }

        $societeId = $fromSociete ?? ($ctx ? $ctx['id'] : null);
        $services = $societeId ? $this->db->query("SELECT * FROM services WHERE societe_id = $societeId ORDER BY nom")->fetchAll() : [];
        $fonctions = $societeId ? $this->db->query("SELECT * FROM fonctions WHERE societe_id = $societeId ORDER BY nom")->fetchAll() : [];
        $rubriquesIndemnites = $this->db->query("SELECT code, libelle, plafond_dgi, plafond_cnss FROM rubriques_gains WHERE code BETWEEN '330' AND '377' ORDER BY code")->fetchAll();
        $societeForGains = $fromSociete ?? ($ctx ? $ctx['id'] : 0);
        $rubriquesGains = $societeForGains ? $this->db->query("SELECT id, code, libelle, type_montant, valeur_defaut FROM rubriques_gains WHERE (societe_id IS NULL OR societe_id = $societeForGains) AND actif = 1 AND categorie = 'Gain standard' ORDER BY code")->fetchAll() : [];
        $this->render('salaries/form.php', [
            'title'       => 'Nouveau salarié',
            'salarie'     => null,
            'societes'    => $societes,
            'services'    => $services,
            'fonctions'   => $fonctions,
            'fromSociete' => $fromSociete,
            'societeContext' => $ctx,
            'indemnitesCustom' => [],
            'rubriquesIndemnites' => $rubriquesIndemnites,
            'gainsCustom' => [],
            'rubriquesGains' => $rubriquesGains,
        ]);
    }

    public function edit(int $id): void
    {
        $userId = Session::get('user_id');
        $ctx = Session::get('societe_context');
        $salarie = $this->db->query("
            SELECT s.* FROM salaries s
            JOIN societes so ON s.societe_id = so.id
            WHERE s.id = $id AND so.user_id = $userId
        ")->fetch();

        if (!$salarie) {
            Session::setFlash('error', 'Salarié introuvable.');
            $this->redirect('/paie-me/salaries');
        }

        if ($ctx) {
            $societes = [$ctx];
        } else {
            $societes = $this->db->query("SELECT id, raison_sociale FROM societes WHERE user_id = $userId ORDER BY raison_sociale")->fetchAll();
        }

        if ($this->isPost()) {
            $this->checkCsrf();
            $data = $this->getPostData();
            if ($ctx) {
                $data['societe_id'] = (int)$ctx['id'];
            }
            $data['rib'] = Crypto::encrypt($data['rib']);
            $data['cin'] = Crypto::encrypt($data['cin']);
            $stmt = $this->db->prepare("
                UPDATE salaries SET societe_id=?, service_id=?, fonction_id=?, matricule=?, nom_famille=?, prenom=?, sexe=?, adresse=?, date_naissance=?, lieu_naissance=?, date_embauche=?, cin=?, cnss=?, situation_familiale=?, nb_enfants=?, enfants_a_charge=?, personnes_a_charge=?, poste=?, type_contrat=?, salaire_base=?, type_salaire=?, frequence_paiement=?, mode_paiement=?, rib=?, indemnite_transport=?, indemnite_panier=?, indemnite_representation=?, avantage_logement=?, avances_salaire=?, mutuelle=?
                WHERE id = ?
            ");
            $stmt->execute([
                $data['societe_id'], $data['service_id'], $data['fonction_id'], $data['matricule'], $data['nom_famille'], $data['prenom'],
                $data['sexe'], $data['adresse'], $data['date_naissance'], $data['lieu_naissance'], $data['date_embauche'], $data['cin'],
                $data['cnss'], $data['situation_familiale'], $data['nb_enfants'], $data['enfants_a_charge'], $data['personnes_a_charge'], $data['poste'],
                $data['type_contrat'], $data['salaire_base'], $data['type_salaire'],
                $data['frequence_paiement'], $data['mode_paiement'], $data['rib'],
                $data['indemnite_transport'], $data['indemnite_panier'],
                $data['indemnite_representation'], $data['avantage_logement'],
                $data['avances_salaire'], $data['mutuelle'], $id,
            ]);

            Audit::log($this->db, 'update', 'salarie', $id, 'Modification salarié: ' . $salarie['nom_famille'] . ' ' . $salarie['prenom']);

            $this->saveIndemnitesCustom($id);
            $this->saveGainsCustom($id);

            Session::setFlash('success', 'Salarié mis à jour.');
            $societeId = $data['societe_id'] ?? $salarie['societe_id'];
            $this->redirect('/paie-me/societes/' . $societeId . '/salaries');
        }

        $fromSociete = isset($_GET['from_societe']) ? (int) $_GET['from_societe'] : null;
        $societeId = $salarie['societe_id'];
        $services = $this->db->query("SELECT * FROM services WHERE societe_id = $societeId ORDER BY nom")->fetchAll();
        $fonctions = $this->db->query("SELECT * FROM fonctions WHERE societe_id = $societeId ORDER BY nom")->fetchAll();

        $salarie['rib'] = Crypto::decrypt($salarie['rib']);
        $salarie['cin'] = Crypto::decrypt($salarie['cin']);

        $indemnitesCustom = $this->db->query("SELECT id, libelle, montant, plafond_dgi, plafond_cnss FROM salarie_indemnites WHERE salarie_id = $id AND actif = 1 ORDER BY id")->fetchAll();
        $rubriquesIndemnites = $this->db->query("SELECT code, libelle, plafond_dgi, plafond_cnss FROM rubriques_gains WHERE code BETWEEN '330' AND '377' ORDER BY code")->fetchAll();
        $gainsCustom = $this->db->query("SELECT sg.id, sg.rubrique_id, sg.montant, rg.code, rg.libelle FROM salarie_gains sg JOIN rubriques_gains rg ON sg.rubrique_id = rg.id WHERE sg.salarie_id = $id AND sg.actif = 1 ORDER BY rg.code")->fetchAll();
        $societeIdForGains = $salarie['societe_id'];
        $rubriquesGains = $this->db->query("SELECT id, code, libelle, type_montant, valeur_defaut FROM rubriques_gains WHERE (societe_id IS NULL OR societe_id = $societeIdForGains) AND actif = 1 AND categorie = 'Gain standard' ORDER BY code")->fetchAll();

        $this->render('salaries/form.php', [
            'title'           => 'Modifier salarié',
            'salarie'         => $salarie,
            'societes'        => $societes,
            'services'        => $services,
            'fonctions'       => $fonctions,
            'fromSociete'     => $fromSociete,
            'societeContext'  => $ctx,
            'indemnitesCustom'=> $indemnitesCustom,
            'rubriquesIndemnites' => $rubriquesIndemnites,
            'gainsCustom'     => $gainsCustom,
            'rubriquesGains'  => $rubriquesGains,
        ]);
    }

    public function delete(int $id): void
    {
        $this->checkCsrf();
        $this->requireRole('admin');
        $userId = Session::get('user_id');
        $salarie = $this->db->query("SELECT nom_famille, prenom FROM salaries WHERE id = $id")->fetch();
        Audit::log($this->db, 'delete', 'salarie', $id, 'Suppression salarié: ' . ($salarie['nom_famille'] ?? '') . ' ' . ($salarie['prenom'] ?? ''));
        $this->db->exec("
            DELETE s FROM salaries s
            JOIN societes so ON s.societe_id = so.id
            WHERE s.id = $id AND so.user_id = $userId
        ");
        Session::setFlash('success', 'Salarié supprimé.');
        $this->redirect('/paie-me/salaries');
    }

    public function stc(int $id): void
    {
        $userId = Session::get('user_id');
        $salarie = $this->db->query("
            SELECT s.*, so.raison_sociale, so.ice, so.if_fiscal, so.cnss as cnss_societe,
                   so.rc, so.ville, so.adresse, so.telephone, so.email, so.logo
            FROM salaries s
            JOIN societes so ON s.societe_id = so.id
            WHERE s.id = $id AND so.user_id = $userId
        ")->fetch();

        if (!$salarie) {
            Session::setFlash('error', 'Salarié introuvable.');
            $this->redirect('/paie-me/salaries');
        }

        $dernierePaie = $this->db->query("
            SELECT pa.*, p.mois, p.annee FROM paies pa
            JOIN periodes p ON pa.periode_id = p.id
            WHERE pa.salarie_id = $id
            ORDER BY p.annee DESC, p.mois DESC
            LIMIT 1
        ")->fetch();

        $salarie['rib'] = Crypto::decrypt($salarie['rib']);
        $salarie['cin'] = Crypto::decrypt($salarie['cin']);

        $this->render('salaries/stc.php', [
            'title'       => 'Solde de Tout Compte — ' . $salarie['nom_famille'] . ' ' . $salarie['prenom'],
            's'           => $salarie,
            'dernierePaie' => $dernierePaie,
        ]);
    }

    public function stcPdf(int $id): void
    {
        $userId = Session::get('user_id');
        $salarie = $this->db->query("
            SELECT s.*, so.raison_sociale, so.ice, so.if_fiscal, so.cnss as cnss_societe,
                   so.rc, so.ville, so.adresse, so.telephone, so.email, so.logo
            FROM salaries s
            JOIN societes so ON s.societe_id = so.id
            WHERE s.id = $id AND so.user_id = $userId
        ")->fetch();

        if (!$salarie) {
            Session::setFlash('error', 'Salarié introuvable.');
            $this->redirect('/paie-me/salaries');
        }

        $dernierePaie = $this->db->query("
            SELECT pa.*, p.mois, p.annee FROM paies pa
            JOIN periodes p ON pa.periode_id = p.id
            WHERE pa.salarie_id = $id
            ORDER BY p.annee DESC, p.mois DESC
            LIMIT 1
        ")->fetch();

        $salarie['rib'] = Crypto::decrypt($salarie['rib']);
        $salarie['cin'] = Crypto::decrypt($salarie['cin']);

        ob_start();
        require __DIR__ . '/../views/salaries/stc_pdf.php';
        $html = ob_get_clean();

        $dompdf = new \Dompdf\Dompdf(['defaultFont' => 'DejaVu Sans']);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $dompdf->stream('stc_' . $salarie['matricule'] . '.pdf', ['Attachment' => false]);
        exit;
    }

    private function getPostData(): array
    {
        $dateVide = static function (?string $v): ?string {
            $v = trim((string)$v);
            return $v === '' ? null : $v;
        };
        return [
            'societe_id'             => $_POST['societe_id'] ?? 0,
            'service_id'             => $_POST['service_id'] ?? null,
            'fonction_id'            => !empty($_POST['fonction_id']) ? (int)$_POST['fonction_id'] : null,
            'matricule'              => $_POST['matricule'] ?? '',
            'nom_famille'            => $_POST['nom_famille'] ?? '',
            'prenom'                 => $_POST['prenom'] ?? '',
            'sexe'                   => $_POST['sexe'] ?? null,
            'adresse'                => $_POST['adresse'] ?? '',
            'date_naissance'         => $dateVide($_POST['date_naissance'] ?? null),
            'lieu_naissance'         => $dateVide($_POST['lieu_naissance'] ?? null),
            'date_embauche'          => $dateVide($_POST['date_embauche'] ?? null),
            'cin'                    => $_POST['cin'] ?? '',
            'cnss'                   => $_POST['cnss'] ?? '',
            'situation_familiale'    => $_POST['situation_familiale'] ?? 'celibataire',
            'nb_enfants'             => $_POST['nb_enfants'] ?? 0,
            'enfants_a_charge'       => min($_POST['enfants_a_charge'] ?? 0, $_POST['nb_enfants'] ?? 0),
            'personnes_a_charge'     => min($_POST['personnes_a_charge'] ?? 0, ($_POST['nb_enfants'] ?? 0) + (($_POST['situation_familiale'] ?? '') === 'marie' ? 1 : 0)),
            'poste'                  => $_POST['poste'] ?? '',
            'type_contrat'           => $_POST['type_contrat'] ?? 'CDI',
            'salaire_base'           => $_POST['salaire_base'] ?? 0,
            'type_salaire'           => $_POST['type_salaire'] ?? 'mensuel',
            'frequence_paiement'     => $_POST['frequence_paiement'] ?? 'mensuel',
            'mode_paiement'          => $_POST['mode_paiement'] ?? 'virement',
            'rib'                    => $_POST['rib'] ?? '',
            'indemnite_transport'    => $_POST['indemnite_transport'] ?? 500.00,
            'indemnite_panier'       => $_POST['indemnite_panier'] ?? 780.00,
            'indemnite_representation' => $_POST['indemnite_representation'] ?? 0,
            'avantage_logement'      => $_POST['avantage_logement'] ?? 0,
            'avances_salaire'        => $_POST['avances_salaire'] ?? 0,
            'mutuelle'               => $_POST['mutuelle'] ?? 0,
        ];
    }

    private function saveIndemnitesCustom(int $salarieId): void
    {
        $this->db->exec("DELETE FROM salarie_indemnites WHERE salarie_id = $salarieId");

        if (empty($_POST['indemnite_custom_libelle'])) return;

        $stmt = $this->db->prepare("INSERT INTO salarie_indemnites (salarie_id, libelle, montant, plafond_dgi, plafond_cnss) VALUES (?, ?, ?, ?, ?)");
        foreach ($_POST['indemnite_custom_libelle'] as $idx => $libelle) {
            $libelle = trim($libelle);
            if ($libelle === '') continue;
            $montant = (float) ($_POST['indemnite_custom_montant'][$idx] ?? 0);
            if ($montant <= 0) continue;
            $plafondDgi = !empty($_POST['indemnite_custom_plafond_dgi'][$idx]) ? (float) $_POST['indemnite_custom_plafond_dgi'][$idx] : null;
            $plafondCnss = !empty($_POST['indemnite_custom_plafond_cnss'][$idx]) ? (float) $_POST['indemnite_custom_plafond_cnss'][$idx] : null;
            $stmt->execute([$salarieId, $libelle, $montant, $plafondDgi, $plafondCnss]);
        }
    }

    private function saveGainsCustom(int $salarieId): void
    {
        $this->db->exec("DELETE FROM salarie_gains WHERE salarie_id = $salarieId");

        if (empty($_POST['gain_custom_rubrique_id'])) return;

        $stmt = $this->db->prepare("INSERT INTO salarie_gains (salarie_id, rubrique_id, montant) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE montant = VALUES(montant)");
        foreach ($_POST['gain_custom_rubrique_id'] as $idx => $rubriqueId) {
            $rubriqueId = (int) $rubriqueId;
            if ($rubriqueId <= 0) continue;
            $montant = (float) ($_POST['gain_custom_montant'][$idx] ?? 0);
            if ($montant <= 0) continue;
            $stmt->execute([$salarieId, $rubriqueId, $montant]);
        }
    }

    protected function render(string $view, array $data = []): void
    {
        extract($data);
        ob_start();
        require __DIR__ . '/../views/' . $view;
        $content = ob_get_clean();
        require __DIR__ . '/../views/layout.php';
    }

    // ─────────────────────────────── Import / Export (méthode Odoo) ───────────────────────────────

    /**
     * Colonnes importables/exportables (style Odoo).
     * L'export et le modèle d'import se déduisent de cette liste → round-trip garanti.
     */
    private const IMPORT_COLUMNS = [
        ['field' => 'matricule', 'label' => 'Matricule', 'type' => 'string', 'required' => false],
        ['field' => 'nom_famille', 'label' => 'Nom', 'type' => 'string', 'required' => true],
        ['field' => 'prenom', 'label' => 'Prénom', 'type' => 'string', 'required' => true],
        ['field' => 'sexe', 'label' => 'Sexe', 'type' => 'enum', 'allowed' => ['M', 'F'], 'required' => false],
        ['field' => 'situation_familiale', 'label' => 'Situation familiale', 'type' => 'enum', 'allowed' => ['celibataire', 'marie', 'divorce', 'veuf'], 'labelMap' => ['célibataire' => 'celibataire', 'marié' => 'marie', 'mariée' => 'marie', 'divorcé' => 'divorce', 'divorcée' => 'divorce'], 'required' => false, 'default' => 'celibataire'],
        ['field' => 'nb_enfants', 'label' => 'Nombre d\'enfants', 'type' => 'int', 'required' => false, 'default' => 0],
        ['field' => 'date_naissance', 'label' => 'Date de naissance', 'type' => 'date', 'required' => false],
        ['field' => 'lieu_naissance', 'label' => 'Lieu de naissance', 'type' => 'string', 'required' => false],
        ['field' => 'cin', 'label' => 'CIN', 'type' => 'string', 'required' => false],
        ['field' => 'cnss', 'label' => 'CNSS', 'type' => 'string', 'required' => false],
        ['field' => 'adresse', 'label' => 'Adresse', 'type' => 'string', 'required' => false],
        ['field' => 'service', 'label' => 'Service', 'type' => 'm2o', 'required' => false],
        ['field' => 'fonction', 'label' => 'Fonction', 'type' => 'm2o', 'required' => false],
        ['field' => 'poste', 'label' => 'Poste', 'type' => 'string', 'required' => false],
        ['field' => 'date_embauche', 'label' => 'Date d\'embauche', 'type' => 'date', 'required' => false],
        ['field' => 'type_contrat', 'label' => 'Type de contrat', 'type' => 'enum', 'allowed' => ['CDI', 'CDD', 'stage', 'interim', 'anapec', 'tahfiz'], 'labelMap' => ['intérim' => 'interim'], 'required' => false, 'default' => 'CDI'],
        ['field' => 'type_salaire', 'label' => 'Type de salaire', 'type' => 'enum', 'allowed' => ['mensuel', 'horaire', 'journalier'], 'required' => false, 'default' => 'mensuel'],
        ['field' => 'frequence_paiement', 'label' => 'Fréquence de paiement', 'type' => 'enum', 'allowed' => ['mensuel', 'quinzaine', 'hebdomadaire'], 'required' => false, 'default' => 'mensuel'],
        ['field' => 'mode_paiement', 'label' => 'Mode de paiement', 'type' => 'enum', 'allowed' => ['virement', 'cheque', 'especes'], 'labelMap' => ['chèque' => 'cheque', 'espèces' => 'especes'], 'required' => false, 'default' => 'virement'],
        ['field' => 'salaire_base', 'label' => 'Salaire de base', 'type' => 'number', 'required' => true],
        ['field' => 'rib', 'label' => 'RIB', 'type' => 'string', 'required' => false],
        ['field' => 'indemnite_transport', 'label' => 'Indemnité de transport', 'type' => 'number', 'required' => false, 'default' => 500],
        ['field' => 'indemnite_panier', 'label' => 'Indemnité de panier', 'type' => 'number', 'required' => false, 'default' => 780],
        ['field' => 'indemnite_representation', 'label' => 'Indemnité de représentation', 'type' => 'number', 'required' => false, 'default' => 0],
        ['field' => 'avantage_logement', 'label' => 'Avantage logement', 'type' => 'number', 'required' => false, 'default' => 0],
        ['field' => 'societe', 'label' => 'Société', 'type' => 'm2o', 'required' => false],
    ];

    /** Télécharge l'export Excel des salariés (mêmes en-têtes que le modèle d'import). */
    public function export(): void
    {
        $userId = Session::get('user_id');
        $ctx = Session::get('societe_context');
        $sql = "
            SELECT s.*, so.raison_sociale, sv.nom AS service_nom, f.nom AS fonction_nom
            FROM salaries s
            JOIN societes so ON s.societe_id = so.id
            LEFT JOIN services sv ON s.service_id = sv.id
            LEFT JOIN fonctions f ON s.fonction_id = f.id
            WHERE so.user_id = $userId
        ";
        if ($ctx) {
            $sql .= " AND s.societe_id = " . (int) $ctx['id'];
        }
        $sql .= " ORDER BY so.raison_sociale, s.nom_famille, s.prenom";
        $salaries = $this->db->query($sql)->fetchAll();

        $columns = [];
        foreach (self::IMPORT_COLUMNS as $col) {
            $columns[] = ['field' => $col['field'], 'label' => $col['label'], 'type' => $col['type']];
        }

        $rows = [];
        foreach ($salaries as $s) {
            $rows[] = [
                'matricule'               => $s['matricule'],
                'nom_famille'             => $s['nom_famille'],
                'prenom'                  => $s['prenom'],
                'sexe'                    => $s['sexe'],
                'situation_familiale'     => $s['situation_familiale'],
                'nb_enfants'              => (int) $s['nb_enfants'],
                'date_naissance'          => $s['date_naissance'],
                'lieu_naissance'          => $s['lieu_naissance'],
                'cin'                     => Crypto::tryDecrypt($s['cin'] ?? ''),
                'cnss'                    => $s['cnss'],
                'adresse'                 => $s['adresse'],
                'service'                 => $s['service_nom'] ?? '',
                'fonction'                => $s['fonction_nom'] ?? '',
                'poste'                   => $s['poste'],
                'date_embauche'           => $s['date_embauche'],
                'type_contrat'            => $s['type_contrat'],
                'type_salaire'            => $s['type_salaire'],
                'frequence_paiement'      => $s['frequence_paiement'],
                'mode_paiement'           => $s['mode_paiement'],
                'salaire_base'            => (float) $s['salaire_base'],
                'rib'                     => Crypto::tryDecrypt($s['rib'] ?? ''),
                'indemnite_transport'     => (float) $s['indemnite_transport'],
                'indemnite_panier'        => (float) $s['indemnite_panier'],
                'indemnite_representation'=> (float) $s['indemnite_representation'],
                'avantage_logement'       => (float) $s['avantage_logement'],
                'societe'                 => $s['raison_sociale'],
            ];
        }

        SpreadsheetService::streamExport($rows, $columns, 'salaries_' . date('Y-m-d') . '.xlsx');
    }

    /** Télécharge le modèle d'import (en-têtes + ligne d'exemple + feuille d'instructions). */
    public function importModele(): void
    {
        $ctx = Session::get('societe_context');
        $columns = [];
        foreach (self::IMPORT_COLUMNS as $col) {
            $col['required'] = !empty($col['required']);
            $columns[] = $col;
        }
        foreach ($columns as &$col) {
            if ($col['field'] === 'societe') {
                $col['required'] = empty($ctx);
                $col['note'] = $ctx
                    ? 'Ignorée : l\'import se fait dans la société active.'
                    : 'Nom exact de la société. Requise car aucune société active.';
            }
        }
        unset($col);

        $examples = [
            'matricule' => 'SAL0010',
            'nom_famille' => 'ALAMI',
            'prenom' => 'Yassine',
            'sexe' => 'M',
            'situation_familiale' => 'marie',
            'nb_enfants' => 2,
            'date_naissance' => '15/03/1990',
            'lieu_naissance' => 'Casablanca',
            'cin' => 'BK123456',
            'cnss' => '12345678',
            'adresse' => '12 rue des Orangers, Casablanca',
            'service' => 'Comptabilité',
            'fonction' => 'Comptable',
            'poste' => 'Comptable',
            'date_embauche' => '01/01/2024',
            'type_contrat' => 'CDI',
            'type_salaire' => 'mensuel',
            'frequence_paiement' => 'mensuel',
            'mode_paiement' => 'virement',
            'salaire_base' => 4500,
            'rib' => '023 780 0001234567890123 45',
            'indemnite_transport' => 500,
            'indemnite_panier' => 780,
            'indemnite_representation' => 0,
            'avantage_logement' => 0,
            'societe' => $ctx ? $ctx['raison_sociale'] : 'TechMaroc Solutions',
        ];
        foreach ($columns as &$col) {
            $col['example'] = $examples[$col['field']] ?? '';
        }
        unset($col);

        // Listes déroulantes du modèle (empêchent les valeurs non conformes à la base).
        $validations = $this->buildTemplateValidations($ctx);

        SpreadsheetService::streamTemplate($columns, 'modele_import_salaries.xlsx', [], $validations);
    }

    /** Valeurs autorisées des listes déroulantes du modèle d'import (enums + référentiels). */
    private function buildTemplateValidations(?array $ctx): array
    {
        $validations = [];

        // Enums : uniquement les valeurs canoniques (pas de variantes accentuées,
        // sinon doublons visibles dans la liste déroulante).
        foreach (self::IMPORT_COLUMNS as $col) {
            if (($col['type'] ?? '') !== 'enum') {
                continue;
            }
            $validations[$col['field']] = $col['allowed'] ?? [];
        }

        // Société : active seule si un contexte est ouvert (colonne ignorée), sinon toutes.
        if ($ctx) {
            $validations['societe'] = [$ctx['raison_sociale']];
        } else {
            $validations['societe'] = $this->db->query(
                "SELECT DISTINCT raison_sociale FROM societes WHERE user_id = " . (int) Session::get('user_id') . " ORDER BY raison_sociale"
            )->fetchAll(\PDO::FETCH_COLUMN);
        }

        // Service / Fonction : référentiel de la société active si contexte, sinon toutes.
        $scope = $ctx ? ' WHERE societe_id = ' . (int) $ctx['id'] : ' WHERE societe_id IN (SELECT id FROM societes WHERE user_id = ' . (int) Session::get('user_id') . ')';
        $validations['service']  = $this->db->query("SELECT DISTINCT nom FROM services$scope ORDER BY nom")->fetchAll(\PDO::FETCH_COLUMN);
        $validations['fonction'] = $this->db->query("SELECT DISTINCT nom FROM fonctions$scope ORDER BY nom")->fetchAll(\PDO::FETCH_COLUMN);

        return $validations;
    }

    /** Étape 1 (Test Odoo) : parse + valide sans rien écrire, affiche le rapport. */
    public function importPreview(): void
    {
        $this->checkCsrf();
        if (!isset($_FILES['fichier']) || ($_FILES['fichier']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            Session::setFlash('error', 'Veuillez sélectionner un fichier.');
            $this->importRedirect();
        }
        $file = $_FILES['fichier'];
        if ((int) $file['size'] > 10 * 1024 * 1024) {
            Session::setFlash('error', 'Fichier trop volumineux (maximum 10 Mo).');
            $this->importRedirect();
        }
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['xlsx', 'xls', 'csv'], true)) {
            Session::setFlash('error', 'Format non supporté. Utilisez un fichier XLSX, XLS ou CSV.');
            $this->importRedirect();
        }
        if (!is_uploaded_file($file['tmp_name'])) {
            Session::setFlash('error', 'Fichier invalide.');
            $this->importRedirect();
        }

        $dir = __DIR__ . '/../uploads/imports';
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        $dest = $dir . '/imp_' . bin2hex(random_bytes(8)) . '.' . $ext;
        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            Session::setFlash('error', 'Impossible d\'enregistrer le fichier.');
            $this->importRedirect();
        }

        try {
            $parsed = SpreadsheetService::parseFile($dest);
        } catch (\Throwable $e) {
            @unlink($dest);
            Session::setFlash('error', 'Fichier illisible : ' . $e->getMessage());
            $this->importRedirect();
        }

        if (!$parsed['headers']) {
            @unlink($dest);
            Session::setFlash('error', 'Le fichier ne contient aucune colonne (en-tête manquant).');
            $this->importRedirect();
        }

        $report = $this->validateImportData($parsed['headers'], $parsed['rows']);
        if ($report['errors'] > 0) {
            @unlink($dest); // erreurs → pas d'import possible
        } else {
            Session::set('import_pending', $dest);
        }

        $this->render('salaries/import_result.php', [
            'title'   => 'Import de salariés — Vérification',
            'report'  => $report,
            'ctx'     => Session::get('societe_context'),
            'file'    => basename($file['name']),
        ]);
    }

    /** Étape 2 (Import Odoo) : re-valide puis insère tout en une transaction. */
    public function importCommit(): void
    {
        $this->checkCsrf();
        $pending = Session::get('import_pending');
        if (!$pending) {
            Session::setFlash('error', 'Aucun import en attente.');
            $this->importRedirect();
        }
        $base = realpath(__DIR__ . '/../uploads/imports');
        $file = realpath($pending);
        if ($base === false || $file === false || !str_starts_with($file, $base . DIRECTORY_SEPARATOR)) {
            Session::remove('import_pending');
            Session::setFlash('error', 'Import expiré, veuillez recommencer.');
            $this->importRedirect();
        }

        try {
            $parsed = SpreadsheetService::parseFile($file);
        } catch (\Throwable $e) {
            Session::setFlash('error', 'Fichier illisible.');
            $this->importRedirect();
        }
        $report = $this->validateImportData($parsed['headers'], $parsed['rows']);

        if ($report['errors'] > 0 || $report['total'] === 0) {
            Session::remove('import_pending');
            @unlink($file);
            Session::setFlash('error', 'Import annulé : le fichier contient des erreurs. Corrigez-le puis réimportez.');
            $this->importRedirect();
        }

        $db = $this->db;
        $db->beginTransaction();
        try {
            $stmt = $db->prepare("
                INSERT INTO salaries (societe_id, service_id, fonction_id, matricule, nom_famille, prenom, sexe, adresse, date_naissance, lieu_naissance, date_embauche, cin, cnss, situation_familiale, nb_enfants, enfants_a_charge, personnes_a_charge, poste, type_contrat, salaire_base, type_salaire, frequence_paiement, mode_paiement, rib, indemnite_transport, indemnite_panier, indemnite_representation, avantage_logement, avances_salaire, mutuelle)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $count = 0;
            foreach ($report['rows'] as $row) {
                if (!empty($row['_errors'])) {
                    continue;
                }
                $nbEnfants = (int) ($row['nb_enfants'] ?? 0);
                $stmt->execute([
                    (int) $row['societe_id'],
                    $row['service_id'] ?? null,
                    $row['fonction_id'] ?? null,
                    (string) $row['matricule'],
                    (string) $row['nom_famille'],
                    (string) $row['prenom'],
                    $row['sexe'] ?: null,
                    (string) $row['adresse'],
                    $row['date_naissance'] ?: null,
                    (string) $row['lieu_naissance'],
                    $row['date_embauche'] ?: null,
                    $row['cin'] ? Crypto::encrypt((string) $row['cin']) : null,
                    (string) $row['cnss'],
                    (string) $row['situation_familiale'],
                    $nbEnfants,
                    $nbEnfants,
                    $nbEnfants + (($row['situation_familiale'] ?? '') === 'marie' ? 1 : 0),
                    (string) $row['poste'],
                    (string) $row['type_contrat'],
                    (float) ($row['salaire_base'] ?? 0),
                    (string) $row['type_salaire'],
                    (string) $row['frequence_paiement'],
                    (string) $row['mode_paiement'],
                    $row['rib'] ? Crypto::encrypt((string) $row['rib']) : null,
                    (float) ($row['indemnite_transport'] ?? 500),
                    (float) ($row['indemnite_panier'] ?? 780),
                    (float) ($row['indemnite_representation'] ?? 0),
                    (float) ($row['avantage_logement'] ?? 0),
                    0,
                    0,
                ]);
                $count++;
            }
            $db->commit();
            Audit::log($db, 'import', 'salarie', null, "Import de $count salariés depuis le fichier.");
        } catch (\Throwable $e) {
            $db->rollBack();
            Session::remove('import_pending');
            @unlink($file);
            Session::setFlash('error', 'Erreur pendant l\'import : ' . $e->getMessage());
            $this->importRedirect();
        }

        Session::remove('import_pending');
        @unlink($file);
        Session::setFlash('success', $count . ' salarié(s) importé(s) avec succès.');
        $this->importRedirect();
    }

    /**
     * Valide les données d'un fichier (méthode Odoo) : aucune écriture, rapport
     * d'erreurs ligne × colonne + lignes normalisées prêtes à insérer.
     */
    private function validateImportData(array $headers, array $rows): array
    {
        $fieldMap = $this->buildFieldMap($headers);
        $lookups = $this->buildSocieteLookups();
        $ctx = Session::get('societe_context');

        $report = [
            'total'   => count($rows),
            'valid'   => 0,
            'errors'  => 0,
            'unknown' => $fieldMap['unknown'],
            'missing' => $fieldMap['missing'],
            'rows'    => [],
            'societesById' => $lookups['societesById'],
        ];

        $societeIdx = $fieldMap['map']['societe'] ?? null;

        foreach ($rows as $i => $row) {
            $line = $i + 2; // 1 = ligne d'en-têtes
            $data = [];
            foreach (self::IMPORT_COLUMNS as $col) {
                $data[$col['field']] = null;
            }
            $data['_line'] = $line;
            $data['_errors'] = [];

            // ── Société (m2o résolu en premier) ──
            $societeId = null;
            $societeRaw = $societeIdx !== null ? trim((string) ($row[$societeIdx] ?? '')) : '';
            if ($ctx) {
                $societeId = (int) $ctx['id'];
                if ($societeRaw !== '') {
                    $found = SpreadsheetService::matchByName($societeRaw, $lookups['societeOptions']);
                    if ($found !== $societeId) {
                        $data['_errors'][] = ['label' => 'Société', 'value' => $societeRaw, 'message' => 'La société « ' . $societeRaw . ' » ne correspond pas à la société active.'];
                    }
                }
            } elseif ($societeRaw === '') {
                $data['_errors'][] = ['label' => 'Société', 'value' => '', 'message' => 'Champ requis manquant : indiquez la société, ou ouvrez d\'abord une société dans le menu.'];
            } else {
                $found = SpreadsheetService::matchByName($societeRaw, $lookups['societeOptions']);
                if ($found === null) {
                    $data['_errors'][] = ['label' => 'Société', 'value' => $societeRaw, 'message' => 'Aucun enregistrement trouvé pour « ' . $societeRaw . ' » dans le champ Société.'];
                } else {
                    $societeId = $found;
                }
            }
            $data['societe_id'] = $societeId;

            // ── Autres champs ──
            $invalidFields = [];
            foreach (self::IMPORT_COLUMNS as $col) {
                $field = $col['field'];
                if ($field === 'societe') {
                    continue;
                }
                $idx = $fieldMap['map'][$field] ?? null;
                $raw = $idx !== null ? trim((string) ($row[$idx] ?? '')) : '';
                switch ($col['type']) {
                    case 'string':
                        $data[$field] = $raw;
                        if ($data[$field] === '' && array_key_exists('default', $col)) {
                            $data[$field] = $col['default'];
                        }
                        break;

                    case 'number':
                    case 'int':
                        if ($raw === '') {
                            $data[$field] = $col['default'] ?? null;
                        } else {
                            $num = SpreadsheetService::normalizeNumber($raw);
                            if ($num === null) {
                                $invalidFields[] = $field;
                                $data['_errors'][] = ['label' => $col['label'], 'value' => $raw, 'message' => 'Valeur invalide « ' . $raw . ' » — un nombre est attendu.'];
                            } else {
                                $data[$field] = $col['type'] === 'int' ? (int) $num : $num;
                            }
                        }
                        break;

                    case 'date':
                        if ($raw === '') {
                            $data[$field] = null;
                        } else {
                            $d = SpreadsheetService::normalizeDate($raw);
                            if ($d === null) {
                                $invalidFields[] = $field;
                                $data['_errors'][] = ['label' => $col['label'], 'value' => $raw, 'message' => 'Valeur invalide « ' . $raw . ' » — date attendue (JJ/MM/AAAA ou AAAA-MM-JJ).'];
                            } else {
                                $data[$field] = $d;
                            }
                        }
                        break;

                    case 'enum':
                        if ($raw === '') {
                            $data[$field] = $col['default'] ?? null;
                        } else {
                            $e = SpreadsheetService::normalizeEnum($raw, $col['allowed'], $col['labelMap'] ?? null);
                            if ($e === null) {
                                $invalidFields[] = $field;
                                $data['_errors'][] = ['label' => $col['label'], 'value' => $raw, 'message' => 'Valeur invalide « ' . $raw . ' » — autorisées : ' . implode(', ', $col['allowed']) . '.'];
                            } else {
                                $data[$field] = $e;
                            }
                        }
                        break;

                    case 'm2o':
                        if ($field === 'service' || $field === 'fonction') {
                            $data[$field . '_nom'] = $raw;
                            if ($raw === '') {
                                $data[$field . '_id'] = null;
                            } elseif ($societeId) {
                                $lookup = $field === 'service' ? ($lookups['servicesByName'][$societeId] ?? []) : ($lookups['fonctionsByName'][$societeId] ?? []);
                                $id = $lookup[SpreadsheetService::normalizeKey($raw)] ?? null;
                                if ($id === null) {
                                    $invalidFields[] = $field;
                                    $data['_errors'][] = ['label' => $col['label'], 'value' => $raw, 'message' => 'Aucun enregistrement trouvé pour « ' . $raw . ' » dans le champ ' . $col['label'] . '.'];
                                } else {
                                    $data[$field . '_id'] = $id;
                                }
                            }
                        }
                        break;
                }
            }

            // ── Champs requis ──
            foreach (self::IMPORT_COLUMNS as $col) {
                if (empty($col['required']) || $col['field'] === 'societe') {
                    continue;
                }
                if (in_array($col['field'], $invalidFields, true)) {
                    continue;
                }
                $val = $data[$col['field']] ?? null;
                if ($val === null || $val === '') {
                    $data['_errors'][] = ['label' => $col['label'], 'value' => '', 'message' => 'Champ requis manquant ou vide.'];
                }
            }

            $report['rows'][$i] = $data;
        }

        // ── Doublons de matricule dans le fichier (même société) ──
        $seen = [];
        foreach ($report['rows'] as $i => &$data) {
            $societeId = $data['societe_id'] ?? null;
            $m = trim((string) ($data['matricule'] ?? ''));
            if (!$societeId || $m === '') {
                continue;
            }
            $k = SpreadsheetService::normalizeKey($m);
            if (isset($seen[$societeId]) && in_array($k, $seen[$societeId], true)) {
                $data['_errors'][] = ['label' => 'Matricule', 'value' => $m, 'message' => 'Valeur en double pour le matricule « ' . $m . ' » dans ce fichier.'];
            } else {
                $seen[$societeId][] = $k;
            }
        }
        unset($data);

        // ── Matricules déjà présents en base ──
        $explicit = [];
        foreach ($report['rows'] as $data) {
            $m = trim((string) ($data['matricule'] ?? ''));
            if ($m !== '' && !empty($data['societe_id'])) {
                $explicit[] = $m;
            }
        }
        if ($explicit) {
            $in = implode(',', array_fill(0, count($explicit), '?'));
            $stmt = $this->db->prepare("SELECT matricule, societe_id FROM salaries WHERE matricule IN ($in)");
            $stmt->execute($explicit);
            $existing = [];
            foreach ($stmt->fetchAll() as $e) {
                $existing[SpreadsheetService::normalizeKey($e['matricule']) . '|' . $e['societe_id']] = $e['matricule'];
            }
            foreach ($report['rows'] as $i => &$data) {
                $m = trim((string) ($data['matricule'] ?? ''));
                if ($m === '' || empty($data['societe_id'])) {
                    continue;
                }
                if (isset($existing[SpreadsheetService::normalizeKey($m) . '|' . $data['societe_id']])) {
                    $data['_errors'][] = ['label' => 'Matricule', 'value' => $m, 'message' => 'Le matricule « ' . $m . ' » existe déjà en base.'];
                }
            }
            unset($data);
        }

        // ── Auto-génération des matricules manquants ──
        $this->finalizeMatricules($report['rows']);

        // ── Compteurs ──
        foreach ($report['rows'] as $data) {
            if (empty($data['_errors'])) {
                $report['valid']++;
            } else {
                $report['errors']++;
            }
        }

        return $report;
    }

    /** Rapproche chaque en-tête de fichier à un champ (insensible casse/accents). */
    private function buildFieldMap(array $headers): array
    {
        $map = [];
        $unknown = [];
        foreach ($headers as $i => $header) {
            $norm = SpreadsheetService::normalizeKey((string) $header);
            if ($norm === '') {
                continue;
            }
            $matched = false;
            foreach (self::IMPORT_COLUMNS as $col) {
                if (SpreadsheetService::normalizeKey($col['label']) === $norm || SpreadsheetService::normalizeKey($col['field']) === $norm) {
                    $map[$col['field']] = $i;
                    $matched = true;
                    break;
                }
            }
            if (!$matched) {
                $unknown[] = (string) $header;
            }
        }
        $missing = [];
        foreach (self::IMPORT_COLUMNS as $col) {
            if (!empty($col['required']) && !isset($map[$col['field']])) {
                $missing[] = $col['label'];
            }
        }
        return ['map' => $map, 'unknown' => $unknown, 'missing' => $missing];
    }

    /** Lookups société + services + fonctions indexés par nom normalisé. */
    private function buildSocieteLookups(): array
    {
        $userId = Session::get('user_id');
        $societes = $this->db->query("SELECT id, raison_sociale FROM societes WHERE user_id = $userId ORDER BY raison_sociale")->fetchAll();

        $servicesByName = [];
        $fonctionsByName = [];
        if ($societes) {
            $in = implode(',', array_map(fn ($s) => (int) $s['id'], $societes));
            foreach ($this->db->query("SELECT id, societe_id, nom FROM services WHERE societe_id IN ($in)")->fetchAll() as $sv) {
                $servicesByName[(int) $sv['societe_id']][SpreadsheetService::normalizeKey($sv['nom'])] = (int) $sv['id'];
            }
            foreach ($this->db->query("SELECT id, societe_id, nom FROM fonctions WHERE societe_id IN ($in)")->fetchAll() as $f) {
                $fonctionsByName[(int) $f['societe_id']][SpreadsheetService::normalizeKey($f['nom'])] = (int) $f['id'];
            }
        }

        return [
            'societeOptions' => array_map(fn ($s) => ['id' => (int) $s['id'], 'nom' => $s['raison_sociale']], $societes),
            'societesById'   => array_column($societes, 'raison_sociale', 'id'),
            'servicesByName' => $servicesByName,
            'fonctionsByName'=> $fonctionsByName,
        ];
    }

    /** Génère les matricules manquants (séquence par société, sans collision). */
    private function finalizeMatricules(array &$rows): void
    {
        $state = [];
        foreach ($rows as $i => &$row) {
            $societeId = $row['societe_id'] ?? null;
            if (!$societeId) {
                continue;
            }
            if (!isset($state[$societeId])) {
                $used = [];
                $maxN = 0;
                $prefix = 'SAL';
                foreach ($this->db->query("SELECT matricule FROM salaries WHERE societe_id = $societeId")->fetchAll() as $m) {
                    $used[SpreadsheetService::normalizeKey($m['matricule'])] = 1;
                    if (preg_match('/(\d+)$/', $m['matricule'], $mm)) {
                        $maxN = max($maxN, (int) $mm[1]);
                    }
                }
                $last = $this->db->query("SELECT matricule FROM salaries WHERE societe_id = $societeId ORDER BY id DESC LIMIT 1")->fetch();
                if ($last && preg_match('/(\d+)$/', $last['matricule'], $mm)) {
                    $prefix = preg_replace('/\d+$/', '', $last['matricule']);
                }
                $state[$societeId] = ['used' => $used, 'prefix' => $prefix, 'n' => $maxN];
            }
            $m = trim((string) ($row['matricule'] ?? ''));
            if ($m !== '') {
                $state[$societeId]['used'][SpreadsheetService::normalizeKey($m)] = 1;
                continue;
            }
            do {
                $state[$societeId]['n']++;
                $candidate = $state[$societeId]['prefix'] . str_pad($state[$societeId]['n'], 4, '0', STR_PAD_LEFT);
            } while (isset($state[$societeId]['used'][SpreadsheetService::normalizeKey($candidate)]));
            $state[$societeId]['used'][SpreadsheetService::normalizeKey($candidate)] = 1;
            $row['matricule'] = $candidate;
        }
        unset($row);
    }

    private function importRedirect(): void
    {
        $ctx = Session::get('societe_context');
        if ($ctx) {
            $this->redirect('/paie-me/societes/' . $ctx['id'] . '/salaries');
        }
        $this->redirect('/paie-me/salaries');
    }
}
