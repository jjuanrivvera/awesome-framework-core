<?php

namespace Awesome;

use PDO;

class Validator
{
    /**
     * Validation errors
     * @var array<mixed>
     */
    protected array $errors = [];

    /**
     * Validation rules
     * @var array<mixed>
     */
    protected array $rules = [];

    /**
     * Data to validate
     * @var array<mixed>
     */
    protected array $data = [];

    /**
     * @var array<mixed>
     */
    protected array $messages = [];

    /**
     * @var array<mixed>
     */
    protected array $defaultMessages = [
        'required' => 'The :attribute field is required.',
        'min' => 'The :attribute field must be at least :min characters.',
        'max' => 'The :attribute field must be less than :max characters.',
        'email' => 'The :attribute field must be a valid email address.',
        'unique' => 'The :attribute field must be unique.',
        'confirmed' => 'The :attribute field must be confirmed.',
        'integer' => 'The :attribute field must be an integer.',
        'numeric' => 'The :attribute field must be numeric.',
        'min_value' => 'The :attribute field must be greater than :min_value.',
        'max_value' => 'The :attribute field must be less than :max_value.',
        'min_length' => 'The :attribute field must be at least :min_length characters.',
        'max_length' => 'The :attribute field must be less than :max_length characters.',
        'alpha' => 'The :attribute field must be alpha.',
        'alpha_numeric' => 'The :attribute field must be alpha numeric.',
        'alpha_dash' => 'The :attribute field must be alpha dash.',
        'in' => 'The :attribute field must be in :values.',
        'not_in' => 'The :attribute field must not be in :values.',
        'url' => 'The :attribute field must be a valid URL.',
        'ip' => 'The :attribute field must be a valid IP address.',
        'date' => 'The :attribute field must be a valid date.',
        'date_format' => 'The :attribute field must be a valid date format.',
        'same' => 'The :attribute field must be the same as :same.',
        'different' => 'The :attribute field must be different than :same.',
        'regex' => 'The :attribute field must match the :regex pattern.',
        'boolean' => 'The :attribute field must be a boolean.',
        'json' => 'The :attribute field must be a valid JSON string.',
        'array' => 'The :attribute field must be an array.',
        'timezone' => 'The :attribute field must be a valid timezone.'
    ];
    /**
     * @var Database
     */
    private mixed $db;

    /**
     * Validator constructor
     * @param array<mixed> $data
     * @param array<mixed> $rules
     * @param array<mixed> $messages
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \Exception
     */
    public function __construct(array $data, array $rules, array $messages = [])
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->messages = $messages;
        $this->db = container('Awesome\Database');
        $this->db->connect();
    }

    /**
     * Validate data
     * @return bool
     */
    public function validate(): bool
    {
        foreach ($this->rules as $field => $rules) {
            $rules = explode('|', $rules);

            foreach ($rules as $rule) {
                $rule = explode(':', $rule);

                $method = 'validate' . str_replace(
                    ' ',
                    '',
                    ucwords(str_replace('_', ' ', $rule[0]))
                );

                if (isset($rule[1])) {
                    $this->$method($field, $rule[1]);
                } else {
                    $this->$method($field);
                }
            }
        }

        return empty($this->errors);
    }

    /**
     * Get errors
     * @return array<mixed>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Validate required
     * @param string $field
     * @return void
     */
    protected function validateRequired(string $field): void
    {
        if (!isset($this->data[$field]) || empty($this->data[$field])) {
            $this->errors[$field] = $this->getMessage($field, 'required');
        }
    }

    /**
     * Validate email
     * @param string $field
     * @return void
     */
    protected function validateEmail(string $field): void
    {
        if (!filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = $this->getMessage($field, 'email');
        }
    }

    /**
     * Validate min
     * @param string $field
     * @param int $min
     * @return void
     */
    protected function validateMin(string $field, int $min): void
    {
        if (strlen($this->data[$field]) < $min) {
            $this->errors[$field] = $this->getMessage($field, 'min', $min);
        }
    }

    /**
     * Validate max
     * @param string $field
     * @param int $max
     * @return void
     */
    protected function validateMax(string $field, int $max): void
    {
        if (strlen($this->data[$field]) > $max) {
            $this->errors[$field] = $this->getMessage($field, 'max', $max);
        }
    }

    /**
     * Validate integer
     * @param string $field
     * @return void
     */
    protected function validateInteger(string $field): void
    {
        if (!filter_var($this->data[$field], FILTER_VALIDATE_INT)) {
            $this->errors[$field] = $this->getMessage($field, 'integer');
        }
    }

    /**
     * Validate boolean
     * @param string $field
     * @return void
     */
    protected function validateBoolean(string $field): void
    {
        if (!filter_var($this->data[$field], FILTER_VALIDATE_BOOLEAN)) {
            $this->errors[$field] = $this->getMessage($field, 'boolean');
        }
    }

    /**
     * Validate url
     * @param string $field
     * @return void
     */
    protected function validateUrl(string $field): void
    {
        if (!filter_var($this->data[$field], FILTER_VALIDATE_URL)) {
            $this->errors[$field] = $this->getMessage($field, 'url');
        }
    }

    /**
     * Validate ip
     * @param string $field
     * @return void
     */
    protected function validateIp(string $field): void
    {
        if (!filter_var($this->data[$field], FILTER_VALIDATE_IP)) {
            $this->errors[$field] = $this->getMessage($field, 'ip');
        }
    }

    /**
     * Validate alpha
     * @param string $field
     * @return void
     */
    protected function validateAlpha(string $field): void
    {
        if (!ctype_alpha($this->data[$field])) {
            $this->errors[$field] = $this->getMessage($field, 'alpha');
        }
    }

    /**
     * Validate alpha numeric
     * @param string $field
     * @return void
     */
    protected function validateAlphaNumeric(string $field): void
    {
        if (!ctype_alnum($this->data[$field])) {
            $this->errors[$field] = $this->getMessage($field, 'alpha_numeric');
        }
    }

    /**
     * Validate alpha dash
     * @param string $field
     * @return void
     */
    protected function validateAlphaDash(string $field): void
    {
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $this->data[$field])) {
            $this->errors[$field] = $this->getMessage($field, 'alpha_dash');
        }
    }

    /**
     * Validate numeric
     * @param string $field
     * @return void
     */
    protected function validateNumeric(string $field): void
    {
        if (!is_numeric($this->data[$field])) {
            $this->errors[$field] = $this->getMessage($field, 'numeric');
        }
    }

    /**
     * Validate regex
     * @param string $field
     * @param string $regex
     * @return void
     */
    protected function validateRegex(string $field, string $regex): void
    {
        if (!preg_match($regex, $this->data[$field])) {
            $this->errors[$field] = $this->getMessage($field, 'regex');
        }
    }

    /**
     * Validate date
     * @param string $field
     * @return void
     */
    protected function validateDate(string $field): void
    {
        if (!strtotime($this->data[$field])) {
            $this->errors[$field] = $this->getMessage($field, 'date');
        }
    }

    /**
     * Validate date format
     * @param string $field
     * @param string $format
     * @return void
     */
    protected function validateDateFormat(string $field, string $format): void
    {
        $date = \DateTime::createFromFormat($format, $this->data[$field]);

        if (!$date || $date->format($format) !== $this->data[$field]) {
            $this->errors[$field] = $this->getMessage($field, 'date_format', $format);
        }
    }

    /**
     * Validate in
     * @param string $field
     * @param array<mixed> $values
     * @return void
     */
    protected function validateIn(string $field, array $values): void
    {
        if (!in_array($this->data[$field], $values)) {
            $this->errors[$field] = $this->getMessage($field, 'in', implode(', ', $values));
        }
    }

    /**
     * Validate not in
     * @param string $field
     * @param array<mixed> $values
     * @return void
     */
    protected function validateNotIn(string $field, array $values): void
    {
        if (in_array($this->data[$field], $values)) {
            $this->errors[$field] = $this->getMessage($field, 'not_in', implode(', ', $values));
        }
    }

    /**
     * Validate json
     * @param string $field
     * @return void
     */
    protected function validateJson(string $field): void
    {
        if (!is_array(json_decode($this->data[$field], true))) {
            $this->errors[$field] = $this->getMessage($field, 'json');
        }
    }

    /**
     * Validate same
     * @param string $field
     * @param string $field2
     * @return void
     */
    protected function validateSame(string $field, string $field2): void
    {
        if ($this->data[$field] !== $this->data[$field2]) {
            $this->errors[$field] = $this->getMessage($field, 'same', $field2);
        }
    }

    /**
     * Validate different
     * @param string $field
     * @param string $field2
     * @return void
     */
    protected function validateDifferent(string $field, string $field2): void
    {
        if ($this->data[$field] === $this->data[$field2]) {
            $this->errors[$field] = $this->getMessage($field, 'different', $field2);
        }
    }

    /**
     * Validate array
     * @param string $field
     * @return void
     */
    protected function validateArray(string $field): void
    {
        if (!is_array($this->data[$field])) {
            $this->errors[$field] = $this->getMessage($field, 'array');
        }
    }

    /**
     * Validate timezone
     * @param string $field
     * @return void
     */
    protected function validateTimezone(string $field): void
    {
        if (!in_array($this->data[$field], timezone_identifiers_list())) {
            $this->errors[$field] = $this->getMessage($field, 'timezone');
        }
    }

    /**
     * Validate unique
     * @param string $field
     * @param string $table
     * @param string $column
     * @param string $id
     * @return void
     */
    protected function validateUnique(string $field, string $table, string $column = null, string $id = null): void
    {
        $column = $column ?? $field;

        $query = "SELECT * FROM {$table} WHERE {$column} = :value";
        $statement = $this->db->connection->prepare($query);
        $statement->bindParam(':value', $this->data[$field]);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result && $result['id'] != $id) {
            $this->errors[$field] = $this->getMessage($field, 'unique', $table);
        }
    }

    /**
     * Validate min value
     * @param string $field
     * @param int $min
     * @return void
     */
    protected function validateMinValue(string $field, int $min): void
    {
        if ($this->data[$field] < $min) {
            $this->errors[$field] = $this->getMessage($field, 'min_value', $min);
        }
    }

    /**
     * Validate max value
     * @param string $field
     * @param int $max
     * @return void
     */
    protected function validateMaxValue(string $field, int $max): void
    {
        if ($this->data[$field] > $max) {
            $this->errors[$field] = $this->getMessage($field, 'max_value', $max);
        }
    }

    /**
     * Validate min length
     * @param string $field
     * @param int $min
     * @return void
     */
    protected function validateMinLength(string $field, int $min): void
    {
        if (strlen($this->data[$field]) < $min) {
            $this->errors[$field] = $this->getMessage($field, 'min_length', $min);
        }
    }

    /**
     * Validate max length
     * @param string $field
     * @param int $max
     * @return void
     */
    protected function validateMaxLength(string $field, int $max): void
    {
        if (strlen($this->data[$field]) > $max) {
            $this->errors[$field] = $this->getMessage($field, 'max_length', $max);
        }
    }

    /**
     * Get error message
     * @param string $field
     * @param string $rule
     * @param int|string $param
     * @return string
     */
    protected function getMessage(string $field, string $rule, $param = ''): string
    {
        $message = $this->messages[$field][$rule] ?? $this->messages[$rule] ?? $this->defaultMessages[$rule];

        return str_replace([':attribute', ":$rule"], [$field, $param], $message);
    }
}
