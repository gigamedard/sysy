<?php

namespace Bookstore;

class Validator {
    private array $errors = [];

    public function validateOrder(array $data): bool {
        $this->errors = [];

        if (empty($data['name']) || strlen(trim($data['name'])) < 3) {
            $this->errors[] = "Le nom doit contenir au moins 3 caractères.";
        }

        if (empty($data['phone']) || !preg_match('/^[0-9\s\-]+$/', $data['phone'])) {
            $this->errors[] = "Le numéro de téléphone est invalide.";
        }

        if (empty($data['delivery_address']) || strlen(trim($data['delivery_address'])) < 10) {
            $this->errors[] = "Veuillez fournir une adresse de livraison complète (minimum 10 caractères).";
        }

        if (empty($data['payment_method']) || !in_array($data['payment_method'], ['wave', 'om', 'cod'])) {
            $this->errors[] = "Veuillez sélectionner un moyen de paiement valide.";
        }

        return empty($this->errors);
    }

    public function getErrors(): array {
        return $this->errors;
    }
}
