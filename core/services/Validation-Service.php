<?php

namespace ZEngine\Core\Services;

class ValidationService
{
    private array $data = [];
    private array $rules = [];
    private array $errors = [];
    private array $customMessages = [];

    public function validate(array $data, array $rules, array $messages = []): bool
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->customMessages = $messages;
        $this->errors = [];

        foreach ($rules as $field => $fieldRules) {
            $this->validateField($field, $fieldRules);
        }

        return empty($this->errors);
    }

    private function validateField(string $field, string|array $rules): void
    {
        if (is_string($rules)) {
            $rules = explode('|', $rules);
        }

        foreach ($rules as $rule) {
            $this->applyRule($field, $rule);
        }
    }

    private function applyRule(string $field, string $rule): void
    {
        [$ruleName, $parameter] = $this->parseRule($rule);
        $value = $this->data[$field] ?? null;

        $method = 'validate' . ucfirst($ruleName);

        if (method_exists($this, $method)) {
            $passes = $this->$method($value, $parameter, $field);

            if (!$passes) {
                $this->addError($field, $ruleName, $parameter);
            }
        }
    }

    private function parseRule(string $rule): array
    {
        if (str_contains($rule, ':')) {
            [$name, $parameter] = explode(':', $rule, 2);
            return [$name, $parameter];
        }

        return [$rule, null];
    }

    private function validateRequired($value): bool
    {
        return !empty($value);
    }

    private function validateEmail($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    private function validateMin($value, $min): bool
    {
        if (is_numeric($value)) {
            return $value >= $min;
        }

        return strlen($value) >= $min;
    }

    private function validateMax($value, $max): bool
    {
        if (is_numeric($value)) {
            return $value <= $max;
        }

        return strlen($value) <= $max;
    }

    private function validateNumeric($value): bool
    {
        return is_numeric($value);
    }

    private function validateInteger($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    private function validateString($value): bool
    {
        return is_string($value);
    }

    private function validateAlpha($value): bool
    {
        return ctype_alpha($value);
    }

    private function validateAlphaNum($value): bool
    {
        return ctype_alnum($value);
    }

    private function validateUrl($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    private function validateIp($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_IP) !== false;
    }

    private function validateIn($value, $list): bool
    {
        $options = explode(',', $list);
        return in_array($value, $options);
    }

    private function validateConfirmed($value, $parameter, $field): bool
    {
        $confirmField = $field . '_confirmation';
        return isset($this->data[$confirmField]) && $value === $this->data[$confirmField];
    }

    private function validateSame($value, $otherField): bool
    {
        return isset($this->data[$otherField]) && $value === $this->data[$otherField];
    }

    private function validateDifferent($value, $otherField): bool
    {
        return !isset($this->data[$otherField]) || $value !== $this->data[$otherField];
    }

    private function addError(string $field, string $rule, $parameter = null): void
    {
        $key = "{$field}.{$rule}";

        if (isset($this->customMessages[$key])) {
            $message = $this->customMessages[$key];
        } else {
            $message = $this->getDefaultMessage($field, $rule, $parameter);
        }

        $this->errors[$field][] = $message;
    }

    private function getDefaultMessage(string $field, string $rule, $parameter = null): string
    {
        $messages = [
            'required' => "The {$field} field is required.",
            'email' => "The {$field} must be a valid email address.",
            'min' => "The {$field} must be at least {$parameter}.",
            'max' => "The {$field} may not be greater than {$parameter}.",
            'numeric' => "The {$field} must be a number.",
            'integer' => "The {$field} must be an integer.",
            'string' => "The {$field} must be a string.",
            'alpha' => "The {$field} may only contain letters.",
            'alphaNum' => "The {$field} may only contain letters and numbers.",
            'url' => "The {$field} must be a valid URL.",
            'ip' => "The {$field} must be a valid IP address.",
            'in' => "The selected {$field} is invalid.",
            'confirmed' => "The {$field} confirmation does not match.",
            'same' => "The {$field} and {$parameter} must match.",
            'different' => "The {$field} and {$parameter} must be different.",
        ];

        return $messages[$rule] ?? "The {$field} is invalid.";
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function failed(): bool
    {
        return !empty($this->errors);
    }
}
