<?php

namespace Controllers;

use Core\Controller;
use Core\Model;
use Core\Session;
use Dompdf\Dompdf;
use PDO;

class BulletinController extends Controller
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
            SELECT b.*, pa.salaire_brut, pa.net_a_payer, pa.cnss_salariale, pa.amo_salariale, pa.ir,
                   s.nom_famille, s.prenom, so.raison_sociale, p.mois, p.annee
            FROM bulletins b
            JOIN paies pa ON b.paie_id = pa.id
            JOIN salaries s ON pa.salarie_id = s.id
            JOIN periodes p ON pa.periode_id = p.id
            JOIN societes so ON pa.societe_id = so.id
            WHERE so.user_id = $userId
        ";
        if ($ctx) {
            $sql .= " AND pa.societe_id = " . (int)$ctx['id'];
        }
        $sql .= " ORDER BY p.annee DESC, p.mois DESC, s.nom_famille";
        $bulletins = $this->db->query($sql)->fetchAll();

        $this->render('bulletins/index.php', [
            'title'     => 'Bulletins de paie',
            'bulletins' => $bulletins,
            'ctx'       => $ctx,
        ]);
    }

    public function show(int $id): void
    {
        $bulletin = $this->getBulletin($id);
        if (!$bulletin) {
            Session::setFlash('error', 'Bulletin introuvable.');
            $this->redirect('/paie-me/bulletins');
        }

        $template = $this->getTemplate($bulletin['societe_id']);

        $this->render('bulletins/show.php', [
            'title'    => 'Bulletin de paie',
            'b'        => $bulletin,
            'template' => $template,
        ]);
    }

    public function pdf(int $id): void
    {
        $bulletin = $this->getBulletin($id);
        if (!$bulletin) {
            Session::setFlash('error', 'Bulletin introuvable.');
            $this->redirect('/paie-me/bulletins');
        }

        $template = $this->getTemplate($bulletin['societe_id']);
        $b = $bulletin;

        ob_start();
        require __DIR__ . '/../views/bulletins/pdf.php';
        $html = ob_get_clean();

        $dompdf = new Dompdf(['defaultFont' => 'DejaVu Sans']);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $mois = str_pad($bulletin['mois'], 2, '0', STR_PAD_LEFT);
        $translit = ['À'=>'A','Á'=>'A','Â'=>'A','Ã'=>'A','Ä'=>'A','Å'=>'A','Æ'=>'AE','Ç'=>'C','È'=>'E','É'=>'E','Ê'=>'E','Ë'=>'E','Ì'=>'I','Í'=>'I','Î'=>'I','Ï'=>'I','Ð'=>'D','Ñ'=>'N','Ò'=>'O','Ó'=>'O','Ô'=>'O','Õ'=>'O','Ö'=>'O','Ø'=>'OE','Ù'=>'U','Ú'=>'U','Û'=>'U','Ü'=>'U','Ý'=>'Y','Þ'=>'TH','ß'=>'ss','à'=>'a','á'=>'a','â'=>'a','ã'=>'a','ä'=>'a','å'=>'a','æ'=>'ae','ç'=>'c','è'=>'e','é'=>'e','ê'=>'e','ë'=>'e','ì'=>'i','í'=>'i','î'=>'i','ï'=>'i','ð'=>'d','ñ'=>'n','ò'=>'o','ó'=>'o','ô'=>'o','õ'=>'o','ö'=>'o','ø'=>'oe','ù'=>'u','ú'=>'u','û'=>'u','ü'=>'u','ý'=>'y','þ'=>'th','ÿ'=>'y'];
        $prenom = str_replace(' ', '', strtr(mb_strtolower($bulletin['prenom']), $translit));
        $nom = str_replace(' ', '', strtr(mb_strtolower($bulletin['nom_famille']), $translit));
        $societe = str_replace(' ', '', strtr(mb_strtolower($bulletin['raison_sociale']), $translit));
        $nomFichier = $bulletin['annee'] . '-' . $mois . '_' . $nom . '_' . $prenom . '_' . $societe . '.pdf';

        $dompdf->stream($nomFichier, ['Attachment' => true]);
        exit;
    }

    public static function genererPourPeriode(int $periodeId, PDO $db): int
    {
        $paies = $db->query("SELECT id, societe_id FROM paies WHERE periode_id = $periodeId")->fetchAll();
        $count = 0;
        foreach ($paies as $pa) {
            $existing = $db->query("SELECT id FROM bulletins WHERE paie_id = {$pa['id']}")->fetch();
            if ($existing) continue;

            $societe = $db->query("SELECT raison_sociale FROM societes WHERE id = {$pa['societe_id']}")->fetch();
            $prefix = strtoupper(mb_substr($societe['raison_sociale'], 0, 3));
            $numero = $prefix . '-' . str_pad((string) $pa['id'], 5, '0', STR_PAD_LEFT);

            $stmt = $db->prepare("INSERT INTO bulletins (paie_id, numero, date_emission) VALUES (?, ?, CURDATE())");
            $stmt->execute([$pa['id'], $numero]);
            $count++;
        }
        return $count;
    }

    private function getBulletin(int $id): ?array
    {
        $userId = Session::get('user_id');
        $stmt = $this->db->prepare("
            SELECT b.*, pa.*, s.nom_famille, s.prenom, s.matricule, s.cin, s.cnss as cnss_num, s.poste, s.fonction_id,
                   f.nom as fonction_nom,
                   s.date_embauche, s.situation_familiale, s.nb_enfants, s.indemnite_transport,
                   s.indemnite_panier, s.indemnite_representation, s.avantage_logement, s.salaire_base,
                   so.raison_sociale, so.ice, so.if_fiscal, so.cnss as cnss_societe, so.rc, so.ville,
                   so.adresse, so.telephone, so.email, so.logo, so.banque, so.rib,
                   so.modele_bulletin_id,
                   p.mois, p.annee
            FROM bulletins b
            JOIN paies pa ON b.paie_id = pa.id
            JOIN salaries s ON pa.salarie_id = s.id
            LEFT JOIN fonctions f ON s.fonction_id = f.id
            JOIN periodes p ON pa.periode_id = p.id
            JOIN societes so ON pa.societe_id = so.id
            WHERE b.id = ? AND so.user_id = ?
            LIMIT 1
        ");
        $stmt->execute([$id, $userId]);
        return $stmt->fetch() ?: null;
    }

    private function getTemplate(int $societeId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM modeles_bulletins WHERE societe_id = ? ORDER BY defaut DESC LIMIT 1");
        $stmt->execute([$societeId]);
        $template = $stmt->fetch();
        if (!$template) {
            return $this->getDefaultTemplate();
        }
        $template['config'] = json_decode($template['config'], true) ?: [];
        return $template;
    }

    private function getDefaultTemplate(): array
    {
        return [
            'nom' => 'Modèle Standard',
            'config' => [
                'couleur_primaire' => '#3b82f6',
                'net_label' => 'Net à payer',
                'net_color' => '#3b82f6',
                'show_footer' => true,
                'sections' => [
                    [
                        'titre' => 'Salaire et indemnités',
                        'colonnes' => ['Code', 'Libellé', 'Montant'],
                        'lignes' => [
                            ['code' => '100', 'label' => 'Salaire de base'],
                            ['code' => '204', 'label' => "Prime d'ancienneté"],
                            ['code' => '330', 'label' => 'Indemnité de transport'],
                            ['code' => '346', 'label' => 'Indemnité de panier'],
                            ['code' => '331', 'label' => 'Indemnité de représentation'],
                            ['code' => '340', 'label' => 'Avantage logement'],
                            ['code' => '201', 'label' => 'Heures sup. 25%'],
                            ['code' => '202', 'label' => 'Heures sup. 50%'],
                            ['code' => '203', 'label' => 'Heures sup. 100%'],
                        ],
                        'total' => ['code' => 'SB', 'label' => 'Salaire brut'],
                    ],
                    [
                        'titre' => 'Cotisations salariales',
                        'colonnes' => ['Code', 'Libellé', 'Montant'],
                        'lignes' => [
                            ['code' => '400', 'label' => 'CNSS (part salariale)'],
                            ['code' => '410', 'label' => 'AMO (part salariale)'],
                            ['code' => '420', 'label' => 'Mutuelle'],
                            ['code' => '501', 'label' => 'Frais professionnels'],
                        ],
                        'total' => ['code' => '502', 'label' => 'Salaire net imposable (SNI)'],
                    ],
                    [
                        'titre' => 'Impôt sur le revenu',
                        'colonnes' => ['Code', 'Libellé', 'Montant'],
                        'lignes' => [
                            ['code' => '600', 'label' => 'Impôt sur le revenu (IR)'],
                            ['code' => '601', 'label' => 'Déductions charges de famille'],
                        ],
                        'total' => null,
                    ],
                ],
            ],
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
