<?php

namespace Core;

use PDO;

class Audit
{
    public static function log(PDO $db, string $action, string $entityType, ?int $entityId = null, ?string $description = null): void
    {
        $userId = Session::get('user_id', 0);
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        try {
            $stmt = $db->prepare("
                INSERT INTO audit_log (user_id, action, entity_type, entity_id, description, ip_address)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$userId, $action, $entityType, $entityId, $description, $ip]);
        } catch (\PDOException $e) {
            // La journalisation est best-effort : un échec ne doit jamais bloquer
            // l'application (ex: utilisateur supprimé en admin alors que sa
            // session est encore active → violation de la FK audit_log.user_id).
        }
    }
}
