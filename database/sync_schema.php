<?php
/**
 * Synchronisation du schéma — copie les tables, colonnes et index MANQUANTS
 * de paie_me vers paie_me_demo, SANS toucher aux données existantes.
 * Usage CLI : php database/sync_schema.php
 * Appelable depuis l'application via sync_schema_database().
 */

function sync_pdo(string $dsn): PDO
{
    return new PDO($dsn, "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
}

/**
 * Convertit un défaut SQL en littéral (CURRENT_TIMESTAMP / numérique brut / chaîne quotée).
 */
function sqlDefaultLiteral(string $default, string $type): string
{
    $lower = strtolower(trim($default));
    if ($lower === 'current_timestamp' || $lower === 'current_timestamp()' || $lower === 'now()') {
        return 'CURRENT_TIMESTAMP';
    }
    if (preg_match('/^(tinyint|smallint|mediumint|int|bigint|decimal|float|double|bit|year)\b/i', $type)) {
        return $default;
    }
    return "'" . str_replace("'", "''", $default) . "'";
}

/**
 * Reconstruit la définition d'une colonne (SHOW FULL COLUMNS) pour un ALTER ADD COLUMN.
 */
function buildColumnDef(array $c): string
{
    $def = "`{$c['Field']}` {$c['Type']}";
    if (!empty($c['Collation']) && $c['Collation'] !== 'NULL') {
        $charset = preg_replace('/_[a-z0-9_]+$/i', '', $c['Collation']);
        $def .= " CHARACTER SET $charset COLLATE {$c['Collation']}";
    }
    if (str_contains($c['Extra'], 'auto_increment')) {
        $def .= " AUTO_INCREMENT";
    }
    $def .= ($c['Null'] === 'YES') ? " NULL" : " NOT NULL";
    if ($c['Default'] !== null) {
        $def .= " DEFAULT " . sqlDefaultLiteral((string) $c['Default'], $c['Type']);
    }
    if (preg_match('/on update (current_timestamp)/i', $c['Extra'], $m)) {
        $def .= " ON UPDATE CURRENT_TIMESTAMP";
    }
    if (!empty($c['Comment'])) {
        $def .= " COMMENT " . sqlDefaultLiteral($c['Comment'], 'text');
    }
    return $def;
}

/**
 * Index d'une table groupés par nom (SKIP la PK), colonnes triées par Seq_in_index.
 */
function tableIndexes(PDO $pdo, string $table): array
{
    $indexes = [];
    foreach ($pdo->query("SHOW INDEX FROM `$table`")->fetchAll() as $r) {
        if ($r['Key_name'] === 'PRIMARY') continue;
        $indexes[$r['Key_name']]['non_unique'] = (int) $r['Non_unique'];
        $indexes[$r['Key_name']]['cols'][(int) $r['Seq_in_index']] = $r['Column_name'];
    }
    foreach ($indexes as &$ix) {
        ksort($ix['cols']);
        $ix['cols'] = array_values($ix['cols']);
    }
    return $indexes;
}

/**
 * Copie le schéma manquant de paie_me vers paie_me_demo.
 * Retourne un rapport : tables_created, columns_added, indexes_added, errors.
 */
function sync_schema_database(): array
{
    $config = require __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../Core/Model.php';
    $source = $config['dbname'];
    $target = \Core\Model::demoDbName();

    $server = sync_pdo("mysql:host={$config['host']};port={$config['port']};charset={$config['charset']}");
    $server->exec("CREATE DATABASE IF NOT EXISTS `$target` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

    $src = sync_pdo("mysql:host={$config['host']};port={$config['port']};dbname=$source;charset={$config['charset']}");
    $tgt = sync_pdo("mysql:host={$config['host']};port={$config['port']};dbname=$target;charset={$config['charset']}");

    $report = ['tables_created' => [], 'columns_added' => [], 'indexes_added' => [], 'errors' => []];
    $q = fn(string $db) => $tgt->quote($db);

    $srcTables = $src->query("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = " . $q($source) . " AND TABLE_TYPE = 'BASE TABLE'")->fetchAll(PDO::FETCH_COLUMN);
    $tgtTables = $tgt->query("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = " . $q($target) . " AND TABLE_TYPE = 'BASE TABLE'")->fetchAll(PDO::FETCH_COLUMN);

    // 1. Tables manquantes → SHOW CREATE TABLE complet (colonnes + clés + index + FK)
    foreach ($srcTables as $table) {
        if (in_array($table, $tgtTables, true)) continue;
        $ddl = $src->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_ASSOC)['Create Table'];
        try {
            $tgt->exec($ddl);
            $report['tables_created'][] = $table;
        } catch (\PDOException $e) {
            $report['errors'][] = "table $table : " . $e->getMessage();
        }
    }

    // 2. Colonnes manquantes sur les tables existantes
    foreach ($srcTables as $table) {
        if (!in_array($table, $tgtTables, true)) continue;
        $srcCols = $src->query("SHOW FULL COLUMNS FROM `$table`")->fetchAll();
        $tgtCols = [];
        foreach ($tgt->query("SHOW FULL COLUMNS FROM `$table`")->fetchAll() as $c) {
            $tgtCols[$c['Field']] = $c['Field'];
        }
        foreach ($srcCols as $c) {
            if (isset($tgtCols[$c['Field']])) continue;
            $def = buildColumnDef($c);
            try {
                $tgt->exec("ALTER TABLE `$table` ADD COLUMN $def");
                $report['columns_added'][] = "$table.{$c['Field']}";
            } catch (\PDOException $e) {
                $report['errors'][] = "colonne $table.{$c['Field']} : " . $e->getMessage();
            }
        }

        // 3. Index manquants (hors PK, hors colonnes ajoutées à l'instant)
        try {
            $srcIdx = tableIndexes($src, $table);
            $tgtIdx = tableIndexes($tgt, $table);
            foreach ($srcIdx as $name => $ix) {
                if (isset($tgtIdx[$name])) continue;
                $cols = '`' . implode('`,`', $ix['cols']) . '`';
                $sql = $ix['non_unique'] == 0
                    ? "ALTER TABLE `$table` ADD UNIQUE INDEX `$name` ($cols)"
                    : "ALTER TABLE `$table` ADD INDEX `$name` ($cols)";
                $tgt->exec($sql);
                $report['indexes_added'][] = "$table.$name";
            }
        } catch (\PDOException $e) {
            $report['errors'][] = "index $table : " . $e->getMessage();
        }
    }

    return $report;
}

if (PHP_SAPI === 'cli') {
    $report = sync_schema_database();
    foreach ($report['tables_created'] as $t) echo "  + table $t créée\n";
    foreach ($report['columns_added'] as $c) echo "  + colonne $c ajoutée\n";
    foreach ($report['indexes_added'] as $i) echo "  + index $i ajouté\n";
    foreach ($report['errors'] as $e) echo "  [ERR] $e\n";
    if (!$report['tables_created'] && !$report['columns_added'] && !$report['indexes_added'] && !$report['errors']) {
        echo "  + schéma à jour (aucune différence)\n";
    }
    exit(empty($report['errors']) ? 0 : 1);
}
