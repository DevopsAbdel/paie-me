<?php

namespace Core;

abstract class Controller
{
    protected function render(string $view, array $data = []): void
    {
        extract($data);
        require_once __DIR__ . '/../views/layout.php';
    }

    protected function redirect(string $url): void
    {
        header("Location: $url");
        exit;
    }

    protected function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function requireRole(string ...$roles): void
    {
        $userRole = Session::get('user_role', '');
        if (!in_array($userRole, $roles, true)) {
            $this->redirect('/paie-me/dashboard');
        }
    }

    protected function csrfField(): string
    {
        return Session::csrfField();
    }

    protected function checkCsrf(): void
    {
        if (!Session::validateCsrf()) {
            Session::setFlash('error', 'Requête invalide (CSRF).');
            $this->redirect($_SERVER['HTTP_REFERER'] ?? '/paie-me/dashboard');
        }
    }
}
