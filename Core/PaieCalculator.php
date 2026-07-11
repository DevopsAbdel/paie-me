<?php

namespace Core;

use PDO;

class PaieCalculator
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function calcAnciennete(string $dateEmbauche, string $dateFin): float
    {
        $embauche = new \DateTime($dateEmbauche);
        $fin = new \DateTime($dateFin);
        $annees = (int) $embauche->diff($fin)->format('%y');

        if ($annees >= 25) return 0.25;
        if ($annees >= 20) return 0.20;
        if ($annees >= 12) return 0.15;
        if ($annees >= 5)  return 0.10;
        if ($annees >= 2)  return 0.05;
        return 0.00;
    }

    public function calculateIr(float $sni): float
    {
        $brackets = $this->db->query("SELECT * FROM bareme_ir WHERE type = 'mensuel' ORDER BY min")->fetchAll();
        foreach ($brackets as $b) {
            if ($sni >= (float) $b['min'] && $sni <= (float) $b['max']) {
                $ir = $sni * (float) $b['taux'] / 100 - (float) $b['deduction'];
                return round(max($ir, 0), 2);
            }
        }
        return 0;
    }

    public function calculerPaie(array $s, array $cnssParams, string $dateFin, float $heuresSup25 = 0, float $heuresSup50 = 0, float $heuresSup100 = 0, array $gains = [], array $retenues = [], string $dateDebut = '', array $baremeHS = [], ?int $joursTravailleOverride = null, float $joursConge = 0, float $joursFeries = 0, array $indemnitesCustom = []): array
    {
        $salaireBase = (float) $s['salaire_base'];

        if ($joursTravailleOverride !== null) {
            $joursTravailles = max(min($joursTravailleOverride, 26), 0);
        } else {
            $joursTravailles = 26;
            if ($dateDebut && !empty($s['date_embauche'])) {
                $embauche = new \DateTime($s['date_embauche']);
                $debut = new \DateTime($dateDebut);
                $fin = new \DateTime($dateFin);
                $dateSortie = !empty($s['date_sortie']) ? new \DateTime($s['date_sortie']) : null;

                $debutPeriode = $embauche > $debut ? $embauche : $debut;
                $finPeriode = ($dateSortie && $dateSortie < $fin) ? $dateSortie : $fin;
                $diff = (int) $debutPeriode->diff($finPeriode)->format('%a') + 1;
                $joursTravailles = max(min($diff, 26), 0);
            }
        }
        $totalPaidDays = min($joursTravailles + $joursConge + $joursFeries, 26);
        $prorata = $totalPaidDays / 26;

        $tauxHoraire = $salaireBase > 0 ? $salaireBase / 191 : 0;
        $taux25 = (float) ($baremeHS['taux_normal'] ?? 25);
        $taux50 = (float) ($baremeHS['taux_majore'] ?? 50);
        $taux100 = (float) ($baremeHS['taux_jour_ferie'] ?? 100);

        $montantHS25 = round($heuresSup25 * $tauxHoraire * $taux25 / 100, 2);
        $montantHS50 = round($heuresSup50 * $tauxHoraire * $taux50 / 100, 2);
        $montantHS100 = round($heuresSup100 * $tauxHoraire * $taux100 / 100, 2);
        $montantHeuresSup = $montantHS25 + $montantHS50 + $montantHS100;
        $heuresSup = $heuresSup25 + $heuresSup50 + $heuresSup100;

        $salaireBaseProrata = round($salaireBase * $prorata, 2);

        $prorataTravaille = $joursTravailles / 26;

        $primeAnciennete = 0;
        if (!empty($s['date_embauche'])) {
            $baseAnciennete = round($salaireBase * $prorataTravaille, 2)
                + round($salaireBase / 26 * $joursConge, 2)
                + round($salaireBase / 26 * $joursFeries, 2)
                + $montantHeuresSup;
            $primeAnciennete = round($baseAnciennete * $this->calcAnciennete($s['date_embauche'], $dateFin), 2);
        }

        $transport     = round((float) ($s['indemnite_transport'] ?? 0) * $prorata, 2);
        $panier        = round((float) ($s['indemnite_panier'] ?? 0) * $prorata, 2);
        $representation = round((float) ($s['indemnite_representation'] ?? 0) * $prorata, 2);
        $logement      = round((float) ($s['avantage_logement'] ?? 0) * $prorata, 2);

        $totalGains = 0;
        $gainsImposables = 0;
        foreach ($gains as $g) {
            $baseGain = $g['type_montant'] === 'proportionnel'
                ? $salaireBase * (float) $g['valeur_defaut'] / 100
                : (float) $g['valeur_defaut'];
            $montant = round($baseGain * $prorata, 2);
            $totalGains += $montant;
            if ($g['imposable']) $gainsImposables += $montant;
        }

        $totalRetenuesCustom = 0;
        foreach ($retenues as $r) {
            $baseRet = $r['type_montant'] === 'proportionnel'
                ? $salaireBase * (float) $r['valeur_defaut'] / 100
                : (float) $r['valeur_defaut'];
            $totalRetenuesCustom += round($baseRet * $prorata, 2);
        }

        $indemniteCustomTotal = 0;
        $indemniteCustomExonere = 0;
        foreach ($indemnitesCustom as $ic) {
            $montantProrata = round((float) $ic['montant'] * $prorata, 2);
            $indemniteCustomTotal += $montantProrata;
            if (!empty($ic['plafond_cnss'])) {
                $plafondCnssProrata = round((float) $ic['plafond_cnss'] * $prorata, 2);
                $indemniteCustomExonere += max($montantProrata - $plafondCnssProrata, 0);
            }
        }

        $sb = $salaireBaseProrata + $primeAnciennete + $montantHeuresSup + $transport + $panier + $representation + $logement + $totalGains + $indemniteCustomTotal;

        $plafondTransport = round(500 * $prorata, 2);
        $plafondPanier = round(780 * $prorata, 2);
        $transportExonere = min($transport, $plafondTransport);
        $panierExonere = min($panier, $plafondPanier);
        $sbi = $sb - $transportExonere - $panierExonere - $indemniteCustomExonere;

        $plafonne = min($sb, (float) ($cnssParams['plafond_cnss'] ?? 6000));
        $cnss = round($plafonne * (float) ($cnssParams['taux_cnss_salarial'] ?? 4.48) / 100, 2);
        $amo  = round($sb * (float) ($cnssParams['taux_amo_salarial'] ?? 2.26) / 100, 2);

        if ($sbi * 12 <= 78000) {
            $fraisPro = round($sbi * 0.35, 2);
        } else {
            $fraisPro = round(min($sbi * 0.25, 2916.70), 2);
        }

        $sni = round($sbi - ($cnss + $amo) - $fraisPro, 2);

        $ir = $this->calculateIr(max($sni, 0));

        $nbCharges = (int) ($s['personnes_a_charge'] ?? 0);
        if ($nbCharges === 0) {
            $enfants = (int) ($s['enfants_a_charge'] ?? $s['nb_enfants'] ?? 0);
            $nbCharges = $enfants + (($s['situation_familiale'] ?? 'celibataire') === 'marie' ? 1 : 0);
        }
        $deductionsFamiliales = round(min($nbCharges, 6) * 50, 2);

        $irNet = round(max($ir - $deductionsFamiliales, 0), 2);

        $avances = (float) ($s['avances_salaire'] ?? 0);
        $mutuelle = (float) ($s['mutuelle'] ?? 0);
        $autresRetenues = $avances + $mutuelle + $totalRetenuesCustom;

        $netAvant = round($sb - ($cnss + $amo) - $ir, 2);
        $net = round(max($netAvant + $deductionsFamiliales - $autresRetenues, 0), 2);

        $cnssPatronale = round($plafonne * (float) ($cnssParams['taux_cnss_patronal'] ?? 8.98) / 100, 2);
        $amoPatronale  = round($sb * (float) ($cnssParams['taux_amo_patronal'] ?? 4.11) / 100, 2);

        return compact(
            'joursTravailles', 'primeAnciennete', 'heuresSup25', 'heuresSup50', 'heuresSup100',
            'heuresSup', 'montantHeuresSup', 'montantHS25', 'montantHS50', 'montantHS100',
            'transport', 'panier', 'representation', 'logement', 'totalGains', 'indemniteCustomTotal', 'sb', 'sbi', 'plafonne',
            'cnss', 'amo', 'fraisPro', 'sni', 'deductionsFamiliales', 'avances',
            'mutuelle', 'autresRetenues', 'netAvant', 'net', 'cnssPatronale', 'amoPatronale'
        ) + ['ir' => $ir, 'irNet' => $irNet];
    }
}

