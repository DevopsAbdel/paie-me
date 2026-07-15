<?php

namespace Controllers;

use Core\Controller;
use Core\Model;
use Core\Session;
use PDO;

class ModeleBulletinController extends Controller
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

        $societe = $this->db->prepare("SELECT * FROM societes WHERE id = ? AND user_id = ?");
        $societe->execute([$id, $userId]);
        $societe = $societe->fetch();
        if (!$societe) {
            Session::setFlash('error', 'Société introuvable.');
            $this->redirect('/paie-me/societes');
        }

        $modeles = $this->db->prepare("SELECT * FROM modeles_bulletins WHERE societe_id = ? ORDER BY defaut DESC, nom");
        $modeles->execute([$id]);
        $modeles = $modeles->fetchAll();

        foreach ($modeles as &$m) {
            $m['config'] = json_decode($m['config'], true) ?: [];
        }

        $this->render('modeles_bulletins/index.php', [
            'title'    => 'Modèles Bulletins de Paie',
            'societe'  => $societe,
            'modeles'  => $modeles,
            'baseUrl'  => '/paie-me/societes/' . $id . '/modeles-bulletins',
        ]);
    }

    public function store(int $id): void
    {
        $userId = Session::get('user_id');

        $check = $this->db->prepare("SELECT id FROM societes WHERE id = ? AND user_id = ?");
        $check->execute([$id, $userId]);
        if (!$check->fetch()) {
            Session::setFlash('error', 'Accès refusé.');
            $this->redirect('/paie-me/societes');
        }

        $nom = trim($_POST['nom'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (empty($nom)) {
            Session::setFlash('error', 'Le nom du modèle est requis.');
            $this->redirect('/paie-me/societes/' . $id . '/modeles-bulletins');
        }

        $config = $this->getDefaultConfig();

        $stmt = $this->db->prepare("INSERT INTO modeles_bulletins (societe_id, nom, description, config, defaut) VALUES (?, ?, ?, ?, 0)");
        $stmt->execute([$id, $nom, $description, json_encode($config)]);

        Session::setFlash('success', 'Modèle créé avec succès.');
        $this->redirect('/paie-me/societes/' . $id . '/modeles-bulletins');
    }

    public function update(int $id, int $id2): void
    {
        $userId = Session::get('user_id');

        $check = $this->db->prepare("SELECT id FROM societes WHERE id = ? AND user_id = ?");
        $check->execute([$id, $userId]);
        if (!$check->fetch()) {
            Session::setFlash('error', 'Accès refusé.');
            $this->redirect('/paie-me/societes');
        }

        $nom = trim($_POST['nom'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (empty($nom)) {
            Session::setFlash('error', 'Le nom du modèle est requis.');
            $this->redirect('/paie-me/societes/' . $id . '/modeles-bulletins');
        }

        $stmt = $this->db->prepare("UPDATE modeles_bulletins SET nom = ?, description = ? WHERE id = ? AND societe_id = ?");
        $stmt->execute([$nom, $description, $id2, $id]);

        Session::setFlash('success', 'Modèle mis à jour.');
        $this->redirect('/paie-me/societes/' . $id . '/modeles-bulletins');
    }

    public function delete(int $id, int $id2): void
    {
        $userId = Session::get('user_id');

        $check = $this->db->prepare("SELECT id FROM societes WHERE id = ? AND user_id = ?");
        $check->execute([$id, $userId]);
        if (!$check->fetch()) {
            Session::setFlash('error', 'Accès refusé.');
            $this->redirect('/paie-me/societes');
        }

        $count = $this->db->prepare("SELECT COUNT(*) FROM modeles_bulletins WHERE societe_id = ?");
        $count->execute([$id]);
        if ($count->fetchColumn() <= 1) {
            Session::setFlash('error', 'Impossible de supprimer le dernier modèle.');
            $this->redirect('/paie-me/societes/' . $id . '/modeles-bulletins');
        }

        $this->db->prepare("DELETE FROM modeles_bulletins WHERE id = ? AND societe_id = ?")->execute([$id2, $id]);

        $this->db->prepare("UPDATE societes SET modele_bulletin_id = NULL WHERE modele_bulletin_id = ? AND id = ?")->execute([$id2, $id]);

        Session::setFlash('success', 'Modèle supprimé.');
        $this->redirect('/paie-me/societes/' . $id . '/modeles-bulletins');
    }

    public function assign(int $id, int $id2): void
    {
        $userId = Session::get('user_id');

        $check = $this->db->prepare("SELECT id FROM societes WHERE id = ? AND user_id = ?");
        $check->execute([$id, $userId]);
        if (!$check->fetch()) {
            Session::setFlash('error', 'Accès refusé.');
            $this->redirect('/paie-me/societes');
        }

        $stmt = $this->db->prepare("UPDATE societes SET modele_bulletin_id = ? WHERE id = ?");
        $stmt->execute([$id2, $id]);

        Session::setFlash('success', 'Modèle affecté à la société.');
        $this->redirect('/paie-me/societes/' . $id . '/modeles-bulletins');
    }

    public function editor(int $id, int $id2): void
    {
        $userId = Session::get('user_id');

        $societe = $this->db->prepare("SELECT * FROM societes WHERE id = ? AND user_id = ?");
        $societe->execute([$id, $userId]);
        $societe = $societe->fetch();
        if (!$societe) {
            Session::setFlash('error', 'Société introuvable.');
            $this->redirect('/paie-me/societes');
        }

        $modele = $this->db->prepare("SELECT * FROM modeles_bulletins WHERE id = ? AND societe_id = ?");
        $modele->execute([$id2, $id]);
        $modele = $modele->fetch();
        if (!$modele) {
            Session::setFlash('error', 'Modèle introuvable.');
            $this->redirect('/paie-me/societes/' . $id . '/modeles-bulletins');
        }

        $config = json_decode($modele['config'], true) ?: $this->getDefaultConfig();

        $this->render('modeles_bulletins/editor.php', [
            'title'    => 'Éditeur — ' . $modele['nom'],
            'societe'  => $societe,
            'modele'   => $modele,
            'config'   => $config,
            'baseUrl'  => '/paie-me/societes/' . $id . '/modeles-bulletins',
        ]);
    }

    public function updateConfig(int $id, int $id2): void
    {
        $userId = Session::get('user_id');

        $check = $this->db->prepare("SELECT id FROM societes WHERE id = ? AND user_id = ?");
        $check->execute([$id, $userId]);
        if (!$check->fetch()) {
            Session::setFlash('error', 'Accès refusé.');
            $this->redirect('/paie-me/societes');
        }

        $raw = file_get_contents('php://input');
        $config = json_decode($raw, true);

        if (!is_array($config) || empty($config['sections'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Configuration invalide.']);
            return;
        }

        $nom = trim($config['nom'] ?? '');
        $description = trim($config['description'] ?? '');
        unset($config['nom'], $config['description']);

        if (!empty($nom)) {
            $this->db->prepare("UPDATE modeles_bulletins SET nom = ?, description = ?, config = ? WHERE id = ? AND societe_id = ?")
                ->execute([$nom, $description, json_encode($config), $id2, $id]);
        } else {
            $this->db->prepare("UPDATE modeles_bulletins SET config = ? WHERE id = ? AND societe_id = ?")
                ->execute([json_encode($config), $id2, $id]);
        }

        http_response_code(200);
        echo json_encode(['success' => true]);
    }

    public function preview(int $id, int $id2): void
    {
        $userId = Session::get('user_id');

        $societe = $this->db->prepare("SELECT * FROM societes WHERE id = ? AND user_id = ?");
        $societe->execute([$id, $userId]);
        $societe = $societe->fetch();
        if (!$societe) {
            Session::setFlash('error', 'Société introuvable.');
            $this->redirect('/paie-me/societes');
        }

        $modele = $this->db->prepare("SELECT * FROM modeles_bulletins WHERE id = ? AND societe_id = ?");
        $modele->execute([$id2, $id]);
        $modele = $modele->fetch();
        if (!$modele) {
            Session::setFlash('error', 'Modèle introuvable.');
            $this->redirect('/paie-me/societes/' . $id . '/modeles-bulletins');
        }

        $config = json_decode($modele['config'], true) ?: $this->getDefaultConfig();

        $b = [
            'raison_sociale' => $societe['raison_sociale'],
            'ice' => $societe['ice'] ?? 'ICE000000000',
            'if_fiscal' => $societe['if_fiscal'] ?? '00000000',
            'cnss_societe' => $societe['cnss_societe'] ?? '0000000',
            'rc' => $societe['rc'] ?? '',
            'adresse' => $societe['adresse'] ?? '',
            'telephone' => $societe['telephone'] ?? '',
            'email' => $societe['email'] ?? '',
            'ville' => $societe['ville'] ?? '',
            'numero' => 'PREV-00001',
            'mois' => date('m'),
            'annee' => date('Y'),
            'date_emission' => date('Y-m-d'),
            'nom_famille' => 'Dupont',
            'prenom' => 'Mohamed',
            'matricule' => 'MAT-001',
            'date_embauche' => '2018-03-15',
            'cin' => 'BK 123456',
            'cnss_num' => '198765432',
            'situation_familiale' => 'Marié',
            'nb_enfants' => 2,
            'fonction_nom' => 'Ingénieur',
            'poste' => 'Ingénieur',
            'jours_travailles' => 26,
            'salaire_base' => 8000,
            'prime_anciennete' => 800,
            'indemnite_transport' => 300,
            'indemnite_panier' => 200,
            'indemnite_representation' => 0,
            'avantage_logement' => 0,
            'montant_hs_25' => 0,
            'montant_hs_50' => 0,
            'montant_hs_100' => 0,
            'salaire_brut' => 9300,
            'cnss_salariale' => 358.40,
            'amo_salariale' => 210.18,
            'mutuelle' => 0,
            'frais_professionnels' => 232.50,
            'sni' => 8498.92,
            'ir' => 1122.65,
            'deductions_familiales' => 360,
            'net_a_payer' => 7376.27,
            'cnss_patronale' => 716.80,
            'amo_patronale' => 382.23,
            'sbi' => 8000,
        ];

        $cumuls = [
            'cumul_brut' => 9300 * 7, 'cumul_cnss' => 358.40 * 7, 'cumul_amo' => 210.18 * 7,
            'cumul_mutuelle' => 0, 'cumul_fp' => 232.50 * 7, 'cumul_ir' => 1122.65 * 7,
            'cumul_sni' => 8498.92 * 7, 'cumul_net' => 7376.27 * 7, 'cumul_jours' => 26 * 7,
            'cumul_transport' => 300 * 7, 'cumul_panier' => 200 * 7, 'cumul_representation' => 0,
            'jours_conge_consommes' => 5, 'jours_conge_restants' => 21,
        ];

        $cnssParams = [
            'plafond_cnss' => 6000,
            'taux_cnss_salarial' => 4.48,
            'taux_cnss_patronal' => 8.98,
            'taux_amo_salarial' => 2.26,
            'taux_amo_patronal' => 4.11,
        ];

        $template = ['config' => $config];

        extract(['b' => $b, 'cumuls' => $cumuls, 'cnssParams' => $cnssParams, 'template' => $template]);
        require __DIR__ . '/../views/modeles_bulletins/preview.php';
    }

    public function duplicate(int $id, int $id2): void
    {
        $userId = Session::get('user_id');

        $check = $this->db->prepare("SELECT id FROM societes WHERE id = ? AND user_id = ?");
        $check->execute([$id, $userId]);
        if (!$check->fetch()) {
            Session::setFlash('error', 'Accès refusé.');
            $this->redirect('/paie-me/societes');
        }

        $modele = $this->db->prepare("SELECT * FROM modeles_bulletins WHERE id = ? AND societe_id = ?");
        $modele->execute([$id2, $id]);
        $modele = $modele->fetch();
        if (!$modele) {
            Session::setFlash('error', 'Modèle introuvable.');
            $this->redirect('/paie-me/societes/' . $id . '/modeles-bulletins');
        }

        $stmt = $this->db->prepare("INSERT INTO modeles_bulletins (societe_id, nom, description, config, defaut) VALUES (?, ?, ?, ?, 0)");
        $stmt->execute([$id, $modele['nom'] . ' (copie)', $modele['description'], $modele['config']]);

        Session::setFlash('success', 'Modèle dupliqué.');
        $this->redirect('/paie-me/societes/' . $id . '/modeles-bulletins');
    }

    private function getDefaultConfig(): array
    {
        return [
            'nom' => 'Modèle Standard Maroc',
            'couleur_primaire' => '#3b82f6',
            'sections' => [
                [
                    'titre' => 'Salaire et indemnités',
                    'colonnes' => ['Code', 'Libellé', 'Base', 'Taux', 'Montant'],
                    'lignes' => [
                        ['code' => '100', 'label' => 'Salaire de base'],
                        ['code' => '204', 'label' => "Prime d'ancienneté", 'conditionnel' => true],
                        ['code' => '330', 'label' => 'Indemnité de transport', 'conditionnel' => true],
                        ['code' => '346', 'label' => 'Indemnité de panier', 'conditionnel' => true],
                        ['code' => '331', 'label' => 'Indemnité de représentation', 'conditionnel' => true],
                        ['code' => '340', 'label' => 'Avantage logement', 'conditionnel' => true],
                        ['code' => '201', 'label' => 'Heures sup. 25%', 'conditionnel' => true],
                        ['code' => '202', 'label' => 'Heures sup. 50%', 'conditionnel' => true],
                        ['code' => '203', 'label' => 'Heures sup. 100%', 'conditionnel' => true],
                    ],
                    'total' => ['code' => 'SB', 'label' => 'Salaire brut'],
                ],
                [
                    'titre' => 'Cotisations salariales',
                    'colonnes' => ['Code', 'Libellé', 'Base', 'Taux', 'Montant'],
                    'lignes' => [
                        ['code' => '400', 'label' => 'CNSS (part salariale)'],
                        ['code' => '410', 'label' => 'AMO (part salariale)'],
                        ['code' => '420', 'label' => 'Mutuelle', 'conditionnel' => true],
                        ['code' => '501', 'label' => 'Frais professionnels'],
                    ],
                    'total' => ['code' => '502', 'label' => 'Salaire net imposable (SNI)'],
                ],
                [
                    'titre' => 'Impôt sur le revenu',
                    'colonnes' => ['Code', 'Libellé', 'Base', 'Taux', 'Montant'],
                    'lignes' => [
                        ['code' => '600', 'label' => 'Impôt sur le revenu (IR)'],
                        ['code' => '601', 'label' => 'Déductions charges de famille', 'conditionnel' => true],
                    ],
                    'total' => null,
                ],
                [
                    'titre' => 'Cotisations patronales',
                    'colonnes' => ['Code', 'Libellé', 'Base', 'Taux', 'Montant'],
                    'lignes' => [
                        ['code' => '400P', 'label' => 'CNSS (part patronale)'],
                        ['code' => '410P', 'label' => 'AMO (part patronale)'],
                    ],
                    'total' => null,
                ],
            ],
            'net_label' => 'Net à payer',
            'net_color' => '#3b82f6',
            'show_footer' => true,
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
