<?php

namespace ZEngine\Core\Services;

class FormBuilderService
{
    private array $fields = [];
    private array $errors = [];
    private string $method = 'POST';
    private string $action = '';
    private array $attributes = [];

    public function open(string $action = '', string $method = 'POST', array $attributes = []): string
    {
        $this->action = $action;
        $this->method = strtoupper($method);
        $this->attributes = $attributes;

        $attrs = $this->buildAttributes(array_merge(['action' => $action, 'method' => $method === 'GET' ? 'GET' : 'POST'], $attributes));

        $html = "<form {$attrs}>";

        if (!in_array($method, ['GET', 'POST'])) {
            $html .= $this->hidden('_method', $method);
        }

        return $html;
    }

    public function close(): string
    {
        return '</form>';
    }

    public function text(string $name, string $value = '', array $attributes = []): string
    {
        return $this->input('text', $name, $value, $attributes);
    }

    public function email(string $name, string $value = '', array $attributes = []): string
    {
        return $this->input('email', $name, $value, $attributes);
    }

    public function password(string $name, array $attributes = []): string
    {
        return $this->input('password', $name, '', $attributes);
    }

    public function hidden(string $name, string $value = '', array $attributes = []): string
    {
        return $this->input('hidden', $name, $value, $attributes);
    }

    public function number(string $name, string $value = '', array $attributes = []): string
    {
        return $this->input('number', $name, $value, $attributes);
    }

    public function file(string $name, array $attributes = []): string
    {
        return $this->input('file', $name, '', $attributes);
    }

    public function input(string $type, string $name, string $value = '', array $attributes = []): string
    {
        $attrs = $this->buildAttributes(array_merge([
            'type' => $type,
            'name' => $name,
            'value' => $value,
            'id' => $attributes['id'] ?? $name,
        ], $attributes));

        return "<input {$attrs}>";
    }

    public function textarea(string $name, string $value = '', array $attributes = []): string
    {
        $attrs = $this->buildAttributes(array_merge([
            'name' => $name,
            'id' => $attributes['id'] ?? $name,
        ], $attributes));

        return "<textarea {$attrs}>{$value}</textarea>";
    }

    public function select(string $name, array $options, string $selected = '', array $attributes = []): string
    {
        $attrs = $this->buildAttributes(array_merge([
            'name' => $name,
            'id' => $attributes['id'] ?? $name,
        ], $attributes));

        $html = "<select {$attrs}>";

        foreach ($options as $value => $label) {
            $isSelected = $value == $selected ? ' selected' : '';
            $html .= "<option value=\"{$value}\"{$isSelected}>{$label}</option>";
        }

        $html .= '</select>';

        return $html;
    }

    public function checkbox(string $name, string $value = '1', bool $checked = false, array $attributes = []): string
    {
        $attrs = $this->buildAttributes(array_merge([
            'type' => 'checkbox',
            'name' => $name,
            'value' => $value,
            'id' => $attributes['id'] ?? $name,
        ], $attributes));

        if ($checked) {
            $attrs .= ' checked';
        }

        return "<input {$attrs}>";
    }

    public function radio(string $name, string $value, bool $checked = false, array $attributes = []): string
    {
        $attrs = $this->buildAttributes(array_merge([
            'type' => 'radio',
            'name' => $name,
            'value' => $value,
            'id' => $attributes['id'] ?? "{$name}_{$value}",
        ], $attributes));

        if ($checked) {
            $attrs .= ' checked';
        }

        return "<input {$attrs}>";
    }

    public function submit(string $text = 'Submit', array $attributes = []): string
    {
        $attrs = $this->buildAttributes(array_merge([
            'type' => 'submit',
            'value' => $text,
        ], $attributes));

        return "<button {$attrs}>{$text}</button>";
    }

    public function button(string $text, string $type = 'button', array $attributes = []): string
    {
        $attrs = $this->buildAttributes(array_merge([
            'type' => $type,
        ], $attributes));

        return "<button {$attrs}>{$text}</button>";
    }

    public function label(string $for, string $text, array $attributes = []): string
    {
        $attrs = $this->buildAttributes(array_merge([
            'for' => $for,
        ], $attributes));

        return "<label {$attrs}>{$text}</label>";
    }

    public function token(): string
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        $token = bin2hex(random_bytes(32));
        $_SESSION['_token'] = $token;

        return $this->hidden('_token', $token);
    }

    public function setErrors(array $errors): void
    {
        $this->errors = $errors;
    }

    public function error(string $field): ?string
    {
        return $this->errors[$field] ?? null;
    }

    public function hasError(string $field): bool
    {
        return isset($this->errors[$field]);
    }

    private function buildAttributes(array $attributes): string
    {
        $html = [];

        foreach ($attributes as $key => $value) {
            if (is_numeric($key)) {
                $html[] = $value;
            } elseif ($value !== null && $value !== false) {
                $html[] = $key . '="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '"';
            }
        }

        return implode(' ', $html);
    }
}
