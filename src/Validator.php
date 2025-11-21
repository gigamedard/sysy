<?php

namespace Bookstore;

class Validator {
    private array $errors = [];

    public function validateOrder(array $data): bool {
        $this->errors = [];

        if (empty($data['name'])) {
            $this->errors['name'] = "Le nom est obligatoire.";
        }

        if (empty($data['phone'])) {
            $this->errors['phone'] = "Le téléphone est obligatoire.";
        } elseif (!preg_match('/^[0-9\+\s]+$/', $data['phone'])) {
            $this->errors['phone'] = "Le format du téléphone est invalide.";
        }

        if (empty($data['payment_method'])) {
            $this->errors['payment_method'] = "Veuillez choisir un moyen de paiement.";
        } elseif (!in_array($data['payment_method'], ['wave', 'om', 'cod'])) {
            $this->errors['payment_method'] = "Moyen de paiement invalide.";
        }

        return empty($this->errors);
    }

    public function getErrors(): array {
        return $this->errors;
    }
}
