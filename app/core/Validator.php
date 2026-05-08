<?php
/**
 * Validator - Input validation
 */
class Validator
{
    private array $errors = [];
    private array $data   = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public static function make(array $data, array $rules): self
    {
        $v = new self($data);
        $v->validate($rules);
        return $v;
    }

    private function validate(array $rules): void
    {
        foreach ($rules as $field => $ruleString) {
            $value = $this->data[$field] ?? null;
            $ruleList = explode('|', $ruleString);

            foreach ($ruleList as $rule) {
                if (str_contains($rule, ':')) {
                    [$ruleName, $param] = explode(':', $rule, 2);
                } else {
                    $ruleName = $rule;
                    $param    = null;
                }

                $this->applyRule($field, $value, $ruleName, $param);
            }
        }
    }

    private function applyRule(string $field, mixed $value, string $rule, ?string $param): void
    {
        $label = ucfirst(str_replace('_', ' ', $field));

        switch ($rule) {
            case 'required':
                if ($value === null || $value === '') {
                    $this->errors[$field] = "{$label} is required.";
                }
                break;
            case 'email':
                if ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->errors[$field] = "{$label} must be a valid email address.";
                }
                break;
            case 'min':
                if ($value !== null && strlen((string)$value) < (int)$param) {
                    $this->errors[$field] = "{$label} must be at least {$param} characters.";
                }
                break;
            case 'max':
                if ($value !== null && strlen((string)$value) > (int)$param) {
                    $this->errors[$field] = "{$label} must not exceed {$param} characters.";
                }
                break;
            case 'numeric':
                if ($value !== null && $value !== '' && !is_numeric($value)) {
                    $this->errors[$field] = "{$label} must be a number.";
                }
                break;
            case 'integer':
                if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_INT)) {
                    $this->errors[$field] = "{$label} must be an integer.";
                }
                break;
            case 'alpha':
                if ($value && !ctype_alpha(str_replace(' ', '', $value))) {
                    $this->errors[$field] = "{$label} must contain only letters.";
                }
                break;
            case 'alphanumeric':
                if ($value && !ctype_alnum(str_replace([' ', '_', '-'], '', $value))) {
                    $this->errors[$field] = "{$label} must contain only letters and numbers.";
                }
                break;
            case 'date':
                if ($value && !strtotime($value)) {
                    $this->errors[$field] = "{$label} must be a valid date.";
                }
                break;
            case 'in':
                $allowed = explode(',', $param);
                if ($value && !in_array($value, $allowed)) {
                    $this->errors[$field] = "{$label} must be one of: {$param}.";
                }
                break;
            case 'confirmed':
                $confirmField = $field . '_confirmation';
                if ($value !== ($this->data[$confirmField] ?? null)) {
                    $this->errors[$field] = "{$label} confirmation does not match.";
                }
                break;
            case 'strong_password':
                if ($value) {
                    if (!preg_match('/[A-Z]/', $value)) {
                        $this->errors[$field] = "{$label} must contain at least one uppercase letter.";
                    } elseif (!preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?`~]/', $value)) {
                        $this->errors[$field] = "{$label} must contain at least one special character (!@#\$%^&* etc.).";
                    }
                }
                break;
            case 'url':
                if ($value && !filter_var($value, FILTER_VALIDATE_URL)) {
                    $this->errors[$field] = "{$label} must be a valid URL.";
                }
                break;
        }
    }

    public function fails(): bool
    {
        return !empty($this->errors);
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
        return reset($this->errors) ?: '';
    }
}
