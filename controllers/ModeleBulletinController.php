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

    private function getDefaultConfig(): array
    {
        return [
            'nom' => 'Modèle Standard Maroc',
            'couleur_primaire' => '#3b82f6',
            'sections' => [
                [
                    'titre' => 'Éléments du salaire',
                    'colonnes' => ['Code', 'Libellé', 'Base', 'Taux', 'Montant'],
                    'lignes' => [
                        ['code' => 'salaire_base', 'label' => 'Salaire de base', 'type' => 'gain', 'show_base' => true, 'show_taux' => true],
                        ['code' => 'prime_anciennete', 'label' => "Prime d'ancienneté", 'type' => 'gain', 'show_base' => true, 'show_taux' => true, 'conditionnel' => true],
                        ['code' => 'indemnite_transport', 'label' => 'Indemnité de transport', 'type' => 'gain', 'conditionnel' => true],
                        ['code' => 'indemnite_panier', 'label' => 'Indemnité de panier', 'type' => 'gain', 'conditionnel' => true],
                        ['code' => 'indemnite_representation', 'label' => 'Indemnité de représentation', 'type' => 'gain', 'conditionnel' => true],
                        ['code' => 'avantage_logement', 'label' => 'Avantage logement', 'type' => 'gain', 'conditionnel' => true],
                        ['code' => 'heures_sup', 'label' => 'Heures supplémentaires', 'type' => 'gain', 'show_base' => true, 'conditionnel' => true],
                    ],
                    'total' => ['code' => 'salaire_brut', 'label' => 'Salaire brut global (SBG)'],
                ],
                [
                    'titre' => 'Cotisations salariales',
                    'colonnes' => ['Code', 'Libellé', 'Base', 'Taux', 'Montant'],
                    'lignes' => [
                        ['code' => 'cnss_salariale', 'label' => 'Cotisation CNSS', 'type' => 'retenue', 'show_base' => true, 'show_taux' => true],
                        ['code' => 'amo_salariale', 'label' => 'Cotisation AMO', 'type' => 'retenue', 'show_base' => true, 'show_taux' => true],
                        ['code' => 'frais_professionnels', 'label' => 'Frais professionnels', 'type' => 'retenue', 'show_base' => true, 'show_taux' => true, 'conditionnel' => true],
                    ],
                    'total' => ['code' => 'sni', 'label' => 'Salaire net imposable (SNI)'],
                ],
                [
                    'titre' => 'Impôt sur le revenu',
                    'colonnes' => ['Code', 'Libellé', 'Base', 'Taux', 'Montant'],
                    'lignes' => [
                        ['code' => 'ir', 'label' => 'Impôt sur le revenu (IR)', 'type' => 'retenue', 'show_base' => true, 'show_taux' => true],
                        ['code' => 'deductions_familiales', 'label' => 'Déductions charges de famille', 'type' => 'gain', 'conditionnel' => true],
                    ],
                    'total' => null,
                ],
                [
                    'titre' => 'Cotisations patronales',
                    'colonnes' => ['Code', 'Libellé', 'Base', 'Taux', 'Montant'],
                    'lignes' => [
                        ['code' => 'cnss_patronale', 'label' => 'Cotisation CNSS patronale', 'type' => 'patronale', 'show_base' => true, 'show_taux' => true],
                        ['code' => 'amo_patronale', 'label' => 'Cotisation AMO patronale', 'type' => 'patronale', 'show_base' => true, 'show_taux' => true],
                        ['code' => 'allocation_familiale', 'label' => 'Allocation familiale', 'type' => 'patronale', 'show_base' => true, 'show_taux' => true],
                        ['code' => 'prestation_sociale', 'label' => 'Prestations sociales', 'type' => 'patronale', 'show_base' => true, 'show_taux' => true],
                        ['code' => 'taxe_formation', 'label' => 'Taxe de formation professionnelle', 'type' => 'patronale', 'show_base' => true, 'show_taux' => true],
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
