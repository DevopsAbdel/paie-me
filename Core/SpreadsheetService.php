<?php

namespace Core;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Service générique d'import/export de données (style Odoo).
 *
 * - parseFile()        : lit un XLSX/XLS/CSV et retourne en-têtes + lignes.
 * - streamExport()     : télécharge un XLSX ou CSV (BOM UTF-8) avec en-têtes stylés.
 * - streamTemplate()   : télécharge un modèle d'import (en-têtes + ligne d'exemple
 *                        + feuille d'instructions).
 * - normalize*()       : normalisation des cellules (dates, nombres, énumérations, clés).
 */
final class SpreadsheetService
{
    /** Colonnes requises marquées d'un astérisque dans le modèle. */
    public const REQUIRED_MARK = ' *';

    /**
     * Lit un fichier tableur et retourne ses en-têtes (ligne 1) et ses lignes.
     *
     * @return array{headers: list<string>, rows: list<array<int, mixed>>}
     */
    public static function parseFile(string $path): array
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if ($ext === 'csv') {
            return self::parseCsv($path);
        }

        if (!class_exists(IOFactory::class)) {
            throw new \RuntimeException('PhpSpreadsheet n\'est pas installé (composer require phpoffice/phpspreadsheet).');
        }

        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $colCount = Coordinate::columnIndexFromString($highestColumn);
        $highestRow = min($highestRow, 5000); // garde-fou : pas de fichier illimité

        $headers = [];
        $rows = [];

        for ($r = 1; $r <= $highestRow; $r++) {
            $row = [];
            $empty = true;
            for ($c = 1; $c <= $colCount; $c++) {
                $cell = $sheet->getCell([$c, $r]);
                $value = self::cellValue($cell);
                $row[] = $value;
                if ($value !== null && $value !== '') {
                    $empty = false;
                }
            }
            if ($empty) {
                continue; // ligne vide ignorée
            }
            if ($r === 1) {
                $headers = array_map(fn ($v) => self::cleanHeader((string) $v), $row);
                continue;
            }
            $rows[] = $row;
        }

        return ['headers' => $headers, 'rows' => $rows];
    }

    /** Valeur brute d'une cellule, en convertissant les cellules de date en ISO. */
    private static function cellValue(Cell $cell): mixed
    {
        $value = $cell->getValue();
        if ($value instanceof \PhpOffice\PhpSpreadsheet\RichText\RichText) {
            $value = $value->getPlainText();
        }
        if ($value === null || $value === '') {
            return $value;
        }
        // Cellule de date (sérial Excel ou objet DateTime) → ISO Y-m-d.
        if (Date::isDateTime($cell)) {
            try {
                if (is_numeric($value)) {
                    return Date::excelToDateTimeObject((float) $value)->format('Y-m-d');
                }
                if ($value instanceof \DateTimeInterface) {
                    return $value->format('Y-m-d');
                }
            } catch (\Throwable $e) {
                return $value;
            }
        }
        return $value;
    }

    /** Lit un CSV : BOM retiré, encodage détecté, séparateur auto. */
    private static function parseCsv(string $path): array
    {
        $content = file_get_contents($path);
        if ($content === false) {
            throw new \RuntimeException('Fichier CSV illisible.');
        }
        if (str_starts_with($content, "\xEF\xBB\xBF")) {
            $content = substr($content, 3);
        }
        if (!mb_check_encoding($content, 'UTF-8')) {
            $content = mb_convert_encoding($content, 'UTF-8', 'Windows-1252');
        }

        // Détection du séparateur sur la première ligne.
        $firstLine = (string) strtok($content, "\n");
        $counts = [
            ';' => substr_count($firstLine, ';'),
            ',' => substr_count($firstLine, ','),
            "\t" => substr_count($firstLine, "\t"),
        ];
        arsort($counts);
        $delimiter = (string) array_key_first($counts);
        if ($counts[$delimiter] === 0) {
            $delimiter = ';';
        }

        $rows = [];
        $handle = fopen('php://temp', 'r+');
        fwrite($handle, $content);
        rewind($handle);
        while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
            $rows[] = $data;
        }
        fclose($handle);

        // Ligne 1 = en-têtes.
        $rawHeaders = array_shift($rows) ?? [];
        $headers = array_map(fn ($v) => self::cleanHeader((string) $v), $rawHeaders);

        return ['headers' => $headers, 'rows' => $rows];
    }

    private static function cleanHeader(string $header): string
    {
        return trim(str_replace(self::REQUIRED_MARK, '', $header));
    }

    /**
     * Télécharge un fichier XLSX (ou CSV si $filename se termine par .csv).
     *
     * @param array<int, array<string, mixed>> $rows    lignes indexées par 'field'
     * @param array<int, array<string, mixed>> $columns [['field','label','type', ...]]
     */
    public static function streamExport(array $rows, array $columns, string $filename): void
    {
        if (strtolower(pathinfo($filename, PATHINFO_EXTENSION)) === 'csv') {
            self::streamCsv($rows, $columns, $filename);
            return;
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Données');
        self::writeHeaders($sheet, array_map(fn ($c) => $c['label'], $columns), false);

        $r = 2;
        foreach ($rows as $row) {
            $c = 1;
            foreach ($columns as $col) {
                $value = $row[$col['field']] ?? '';
                self::setCellValue($sheet->getCell([$c, $r]), $value, $col['type'] ?? 'string');
                $c++;
            }
            $r++;
        }

        self::finishWorksheet($sheet, $columns);
        self::sendSpreadsheet($spreadsheet, $filename, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    /**
     * Télécharge un modèle d'import : feuille de données (en-têtes + ligne d'exemple)
     * puis feuille d'instructions.
     *
     * @param array<int, array<string, mixed>> $columns      [['field','label','type','required','example','note']]
     * @param array<int, string>               $instructions texte libre, une ligne par entrée
     * @param array<string, list<string>>      $validations  liste déroulante par champ (field => valeurs autorisées)
     */
    public static function streamTemplate(array $columns, string $filename, array $instructions = [], array $validations = []): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Données à importer');

        $labels = array_map(fn ($c) => $c['label'] . (!empty($c['required']) ? self::REQUIRED_MARK : ''), $columns);
        self::writeHeaders($sheet, $labels, true);

        // Ligne d'exemple.
        $c = 1;
        foreach ($columns as $col) {
            $example = $col['example'] ?? '';
            if (($col['type'] ?? '') === 'date' && $example !== '') {
                $example = self::normalizeDate($example) ?: $example;
            }
            self::setCellValue($sheet->getCell([$c, 2]), $example, $col['type'] ?? 'string');
            $c++;
        }
        if (!empty($columns)) {
            $sheet->getRowDimension(2)->setRowHeight(18);
        }

        self::finishWorksheet($sheet, $columns);

        // Listes déroulantes : feuille cachée « Listes » = sources des validations.
        if ($validations) {
            $lists = $spreadsheet->createSheet();
            $lists->setTitle('Listes');
            $lists->setSheetState(Worksheet::SHEETSTATE_HIDDEN);

            $c = 1;
            foreach ($columns as $col) {
                $field = $col['field'];
                if (!isset($validations[$field]) || !is_array($validations[$field])) {
                    $c++;
                    continue;
                }
                $values = array_values(array_filter(array_map('strval', $validations[$field])));
                if (!$values) {
                    $c++;
                    continue;
                }
                $r = 1;
                foreach ($values as $v) {
                    $lists->setCellValue([$c, $r], $v);
                    $r++;
                }
                $lastRow = $r - 1;

                $colLetter = Coordinate::stringFromColumnIndex($c);
                $range = $colLetter . '2:' . $colLetter . 2000;
                $validation = new DataValidation();
                $validation->setType(DataValidation::TYPE_LIST);
                $validation->setFormula1("'Listes'!\$" . $colLetter . "\$1:\$" . $colLetter . "\$" . $lastRow);
                $validation->setAllowBlank(true);
                $validation->setShowDropDown(true);
                $validation->setShowErrorMessage(true);
                $validation->setErrorStyle(DataValidation::STYLE_STOP);
                $validation->setErrorTitle('Valeur non conforme');
                $validation->setError('Cette valeur n\'est pas autorisée. Choisissez une valeur dans la liste déroulante.');
                $sheet->setDataValidation($range, $validation);
                $c++;
            }
        }

        // Feuille d'instructions.
        $inst = $spreadsheet->createSheet();
        $inst->setTitle('Instructions');
        $accent = '8B5CF6';
        $row = 1;

        // Titre.
        $inst->mergeCells('A' . $row . ':E' . $row);
        $inst->setCellValue('A' . $row, "MODÈLE D'IMPORT DES SALARIÉS — MODE D'EMPLOI");
        $inst->getStyle('A' . $row)->applyFromArray([
            'font' => ['bold' => true, 'size' => 15, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $accent]],
        ]);
        $inst->getRowDimension($row)->setRowHeight(28);
        $row += 2;

        // Étapes.
        $steps = [
            "1. TÉLÉCHARGER — ce modèle depuis la page Salariés (bouton « Modèle d'import »).",
            "2. REMPLIR — un salarié par ligne à partir de la ligne 3. Ne modifiez jamais les en-têtes. Les colonnes à liste déroulante n'acceptent que les valeurs proposées.",
            "3. IMPORTER — dans l'application : « Importer » → choisir ce fichier → « Vérifier le fichier », puis valider l'import. Aucune donnée n'est écrite tant que la vérification est sans erreur.",
        ];
        foreach ($steps as $s) {
            $inst->mergeCells('A' . $row . ':E' . $row);
            $inst->setCellValue('A' . $row, $s);
            $inst->getStyle('A' . $row)->getAlignment()->setWrapText(true);
            $row++;
        }
        $row += 1;

        // Règles importantes.
        $inst->mergeCells('A' . $row . ':E' . $row);
        $inst->setCellValue('A' . $row, 'RÈGLES IMPORTANTES');
        $inst->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;
        $rules = [
            'Les colonnes marquées « * » (astérisque) sont OBLIGATOIRES.',
            'La ligne d\'exemple (ligne 2) doit être supprimée avant l\'import.',
            'Les colonnes inconnues ou renommées sont ignorées à l\'import (jamais bloquant).',
            'Dates : JJ/MM/AAAA (ex: 15/03/2026) ou AAAA-MM-JJ. Montants : décimal simple, la virgule comme séparateur (ex: 3422,72).',
            'Sexe, Situation familiale, Type de contrat, Type de salaire, Fréquence, Mode de paiement, Service, Fonction et Société ont une liste déroulante.',
        ];
        foreach ($rules as $r) {
            $inst->mergeCells('A' . $row . ':E' . $row);
            $inst->setCellValue('A' . $row, '• ' . $r);
            $inst->getStyle('A' . $row)->getAlignment()->setWrapText(true);
            $row++;
        }
        $row += 1;

        // Détail des colonnes (table).
        $typeLabels = ['string' => 'Texte', 'number' => 'Montant', 'int' => 'Nombre entier', 'date' => 'Date', 'enum' => 'Liste', 'm2o' => 'Référentiel'];
        $header = $row;
        $inst->setCellValue('A' . $header, 'Colonne');
        $inst->setCellValue('B' . $header, 'Requis');
        $inst->setCellValue('C' . $header, 'Type');
        $inst->setCellValue('D' . $header, 'Exemple');
        $inst->setCellValue('E' . $header, 'Valeurs acceptées / Remarque');
        $inst->getStyle('A' . $header . ':E' . $header)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $accent]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $row++;

        $firstData = $row;
        foreach ($columns as $i => $col) {
            $field = $col['field'];
            $accepted = '';
            if (isset($validations[$field]) && $validations[$field]) {
                $accepted = implode(', ', $validations[$field]);
            } elseif (($col['type'] ?? '') === 'enum' && !empty($col['allowed'])) {
                $accepted = implode(', ', $col['allowed']);
            }
            if ($accepted === '' && !empty($col['note'])) {
                $accepted = (string) $col['note'];
            }
            if ($accepted === '' && array_key_exists('default', $col) && $col['default'] !== '') {
                $accepted = 'Valeur par défaut : ' . (string) $col['default'];
            }
            $inst->setCellValue('A' . $row, $col['label'] . (!empty($col['required']) ? self::REQUIRED_MARK : ''));
            $inst->setCellValue('B' . $row, !empty($col['required']) ? 'Obligatoire' : 'Optionnelle');
            $inst->setCellValue('C' . $row, $typeLabels[$col['type'] ?? 'string'] ?? 'Texte');
            $inst->setCellValue('D' . $row, (string) ($col['example'] ?? ''));
            $inst->setCellValue('E' . $row, $accepted);
            if (!empty($col['required'])) {
                $inst->getStyle('A' . $row)->getFont()->setBold(true);
                $inst->getStyle('B' . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('C62828'));
            } else {
                $inst->getStyle('B' . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('757575'));
            }
            $inst->getStyle('E' . $row)->getAlignment()->setWrapText(true);
            if (($i % 2) === 1) {
                $inst->getStyle('A' . $row . ':E' . $row)->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F3F4F8');
            }
            $row++;
        }
        $last = $row - 1;
        $inst->getStyle('A' . $firstData . ':E' . $last)->getBorders()->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)
            ->getColor()->setRGB('D5D9E2');

        // Pied de page.
        $row += 1;
        $inst->mergeCells('A' . $row . ':E' . $row);
        $inst->setCellValue('A' . $row, 'En cas de doute sur une valeur, vérifiez les listes déroulantes et le référentiel (Paramètres > Services) avant l\'import.');
        $inst->getStyle('A' . $row)->getFont()->setItalic(true);
        $row++;

        $inst->getColumnDimension('A')->setWidth(30);
        $inst->getColumnDimension('B')->setWidth(13);
        $inst->getColumnDimension('C')->setWidth(15);
        $inst->getColumnDimension('D')->setWidth(28);
        $inst->getColumnDimension('E')->setWidth(62);

        self::sendSpreadsheet($spreadsheet, $filename, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    /** Écrit les en-têtes avec style (fond violet accent, blanc, gras). */
    private static function writeHeaders(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, array $labels, bool $required): void
    {
        $c = 1;
        foreach ($labels as $label) {
            $cell = $sheet->getCell([$c, 1]);
            $cell->setValue($label);
            $c++;
        }
        $last = count($labels) ? Coordinate::stringFromColumnIndex(count($labels)) : 'A';
        $range = 'A1:' . $last . '1';
        $sheet->getStyle($range)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '8B5CF6']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(22);
    }

    /** Écrit une valeur en contrôlant son type (numérique vs texte, date). */
    private static function setCellValue(Cell $cell, mixed $value, string $type = 'string'): void
    {
        if ($value === null || $value === '') {
            $cell->setValue('');
            return;
        }
        if ($type === 'date') {
            $date = self::phpDate($value);
            if ($date) {
                $cell->setValue(Date::PHPToExcel($date));
                $cell->getStyle()->getNumberFormat()->setFormatCode('yyyy-mm-dd');
                return;
            }
            $cell->setValue((string) $value);
            return;
        }
        if ($type === 'number' || $type === 'int') {
            $cell->setValue((float) $value);
            if ($type === 'int') {
                $cell->getStyle()->getNumberFormat()->setFormatCode('0');
            } else {
                $cell->getStyle()->getNumberFormat()->setFormatCode('#,##0.00');
            }
            return;
        }
        $cell->setValue((string) $value);
    }

    private static function finishWorksheet(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, array $columns): void
    {
        // Largeurs par colonne.
        $defaults = [
            'string' => 18,
            'number' => 12,
            'int'    => 8,
            'date'   => 12,
            'enum'   => 18,
            'm2o'    => 18,
        ];
        $c = 1;
        foreach ($columns as $col) {
            $width = $col['width'] ?? ($defaults[$col['type'] ?? 'string'] ?? 18);
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($c))->setWidth($width);
            $c++;
        }
        // Autofiltre + ligne d'en-tête figée.
        if (count($columns) > 0) {
            $lastCol = Coordinate::stringFromColumnIndex(count($columns));
            $lastRow = max(2, $sheet->getHighestRow());
            $sheet->setAutoFilter('A1:' . $lastCol . $lastRow);
        }
        $sheet->freezePane('A2');
    }

    private static function sendSpreadsheet(Spreadsheet $spreadsheet, string $filename, string $mime): void
    {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        header('Content-Type: ' . $mime . '; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /** Export CSV (UTF-8 avec BOM) depuis les mêmes colonnes. */
    private static function streamCsv(array $rows, array $columns, string $filename): void
    {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . basename($filename) . '"');

        $output = fopen('php://output', 'w');
        fwrite($output, "\xEF\xBB\xBF");
        fputcsv($output, array_map(fn ($c) => $c['label'], $columns), ';');
        foreach ($rows as $row) {
            $line = [];
            foreach ($columns as $col) {
                $line[] = $row[$col['field']] ?? '';
            }
            fputcsv($output, $line, ';');
        }
        fclose($output);
        exit;
    }

    // ─────────────────────────── Normalisation ───────────────────────────

    /** Clé de comparaison : minuscules, sans accents, sans caractères non-alphanumériques. */
    public static function normalizeKey(string $value): string
    {
        $value = mb_strtolower(trim($value), 'UTF-8');
        $map = [
            'à' => 'a', 'â' => 'a', 'ä' => 'a', 'é' => 'e', 'è' => 'e', 'ê' => 'e',
            'ë' => 'e', 'î' => 'i', 'ï' => 'i', 'ô' => 'o', 'ö' => 'o', 'ù' => 'u',
            'û' => 'u', 'ü' => 'u', 'ç' => 'c', 'œ' => 'oe', 'æ' => 'ae', 'ÿ' => 'y',
        ];
        $value = strtr($value, $map);
        return preg_replace('/[^a-z0-9]/', '', $value) ?? '';
    }

    /** Normalise une date en Y-m-d. Accepte ISO, JJ/MM/AAAA, JJ-MM-AAAA, JJ.MM.AAAA. */
    public static function normalizeDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }
        if (is_numeric($value) && (float) $value >= 1 && (float) $value <= 2958465) {
            try {
                return Date::excelToDateTimeObject((float) $value)->format('Y-m-d');
            } catch (\Throwable $e) {
                return null;
            }
        }
        $value = trim((string) $value);
        $patterns = [
            '/^(\d{4})-(\d{1,2})-(\d{1,2})$/' => '$1-$2-$3',
        ];
        foreach ([
            'd/m/Y', 'm/d/Y', 'd-m-Y', 'm-d-Y', 'd.m.Y', 'm.d.Y', 'Y/m/d', 'Y-m-d',
        ] as $fmt) {
            $dt = \DateTime::createFromFormat($fmt, $value);
            $errors = \DateTime::getLastErrors();
            $nbErreurs = ($errors === false) ? 0 : (($errors['warning_count'] ?? 0) + ($errors['error_count'] ?? 0));
            if ($dt && $nbErreurs === 0) {
                return $dt->format('Y-m-d');
            }
        }
        return null;
    }

    /** Normalise un nombre : gère "4 500,50", "4500,50", "1,234.56", "4500.50". */
    public static function normalizeNumber(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (is_int($value) || is_float($value)) {
            return (float) $value;
        }
        $s = trim((string) $value);
        $s = str_replace(["\xc2\xa0", "\xe2\x80\xaf", ' '], '', $s);
        if ($s === '') {
            return null;
        }
        $lastComma = strrpos($s, ',');
        $lastDot = strrpos($s, '.');
        if ($lastComma !== false && $lastDot !== false) {
            // Le séparateur décimal est celui qui apparaît en dernier.
            if ($lastComma > $lastDot) {
                $s = str_replace('.', '', $s);
                $s = str_replace(',', '.', $s);
            } else {
                $s = str_replace(',', '', $s);
            }
        } elseif ($lastComma !== false && $lastDot === false) {
            $s = str_replace(',', '.', $s);
        }
        if (!is_numeric($s)) {
            return null;
        }
        return (float) $s;
    }

    /** Résout une énumération (insensible à la casse + mapping des formes accentuées). */
    public static function normalizeEnum(string $value, array $allowed, ?array $labelMap = null): ?string
    {
        $value = trim($value);
        $key = self::normalizeKey($value);
        if ($key === '') {
            return null;
        }
        foreach ($allowed as $allowedValue) {
            if (self::normalizeKey($allowedValue) === $key) {
                return $allowedValue;
            }
        }
        if ($labelMap) {
            foreach ($labelMap as $label => $allowedValue) {
                if (self::normalizeKey($label) === $key) {
                    return $allowedValue;
                }
            }
        }
        return null;
    }

    /**
     * Résout une valeur par nom parmi une liste d'options.
     *
     * @param array<int, array{id: int, nom: string}> $options
     */
    public static function matchByName(string $value, array $options): ?int
    {
        $value = self::normalizeKey($value);
        if ($value === '') {
            return null;
        }
        foreach ($options as $option) {
            if (self::normalizeKey((string) $option['nom']) === $value) {
                return (int) $option['id'];
            }
        }
        return null;
    }

    private static function phpDate(mixed $value): ?\DateTime
    {
        if ($value instanceof \DateTimeInterface) {
            return \DateTime::createFromInterface($value);
        }
        $iso = self::normalizeDate($value);
        if (!$iso) {
            return null;
        }
        $dt = \DateTime::createFromFormat('!Y-m-d', $iso);
        return $dt ?: null;
    }
}
