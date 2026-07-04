<?php

namespace Core;

class Validator
{
    private array $errors = [];
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function required(string $field, string $label = ''): self
    {
        $value = $this->data[$field] ?? '';
        if (trim((string) $value) === '') {
            $this->errors[] = ($label ?: $field) . ' est requis.';
        }
        return $this;
    }

    public function numeric(string $field, string $label = ''): self
    {
        $value = $this->data[$field] ?? '';
        if ($value !== '' && !is_numeric($value)) {
            $this->errors[] = ($label ?: $field) . ' doit être un nombre.';
        }
        return $this;
    }

    public function email(string $field, string $label = ''): self
    {
        $value = $this->data[$field] ?? '';
        if ($value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = ($label ?: $field) . ' doit être un email valide.';
        }
        return $this;
    }

    public function minLength(string $field, int $min, string $label = ''): self
    {
        $value = $this->data[$field] ?? '';
        if (mb_strlen($value) < $min) {
            $this->errors[] = ($label ?: $field) . " doit contenir au moins $min caractères.";
        }
        return $this;
    }

    public function maxLength(string $field, int $max, string $label = ''): self
    {
        $value = $this->data[$field] ?? '';
        if (mb_strlen($value) > $max) {
            $this->errors[] = ($label ?: $field) . " doit contenir au maximum $max caractères.";
        }
        return $this;
    }

    public function inArray(string $field, array $allowed, string $label = ''): self
    {
        $value = $this->data[$field] ?? '';
        if ($value !== '' && !in_array($value, $allowed, true)) {
            $this->errors[] = ($label ?: $field) . ' contient une valeur invalide.';
        }
        return $this;
    }

    public function date(string $field, string $label = ''): self
    {
        $value = $this->data[$field] ?? '';
        if ($value !== '' && !strtotime($value)) {
            $this->errors[] = ($label ?: $field) . ' doit être une date valide.';
        }
        return $this;
    }

    public function passes(): bool
    {
        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(): string
    {
        return $this->errors[0] ?? '';
    }
}
