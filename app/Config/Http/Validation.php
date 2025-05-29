<?php

namespace App\Config\Http;

class Validation
{
    protected array $errors = [];
    protected array $validatedData = [];
    protected array $availableRules = [
        "required" => [
            "message" => "Este campo es obligatorio",
            "condition" => null
        ],
        "valid_email" => [
            "message" => "Debe ser un correo electrónico válido",
            "condition" => null
        ],
        "numeric" => [
            "message" => "Debe ser un dato numerico",
            "condition" => null
        ],
        "min" => [
            "message" => "Debe tener al menos :param caracteres",
            "condition" => null
        ],
        "max" => [
            "message" => "Debe tener máximo :param caracteres",
            "condition" => null
        ]
    ];

    public function __construct()
    {
        $this->initAvailableRules();
    }

    public function validate(Request $request, array $rules): array
    {
        try {
            $this->errors = [];
            $this->validatedData = [];

            $data = $request->body;

            foreach ($rules as $field => $ruleString) {
                $value = $data[$field] ?? null;
                $rulesList = explode('|', $ruleString);

                foreach ($rulesList as $rule) {
                    $param = null;

                    if (str_contains($rule, ':')) [$rule, $param] = explode(':', $rule, 2);

                    $this->validateRule($rule, $field, ["value" => $value, "param" => $param]);
                }

                if (!isset($this->errors[$field])) $this->validatedData[$field] = $value;
            }
        } catch (\TypeError $e) {
            Response::response(500, "Error en el controlador: " . $e->getMessage());
        }

        if (!empty($this->errors)) Response::response(400, null, $this->errors);

        return $data;
    }

    private function validateRule(string $rule, string $field, array $values): void
    {
        if (!array_key_exists($rule, $this->availableRules)) throw new \TypeError("Tipo de regla no soportada en el controlador: {$rule}");;

        $condition = $this->availableRules[$rule]['condition'];

        if ($condition($values)) {
            // [Mcerquera 20250528] Reemplazar :param en el mensaje, si existe
            $message = str_replace(':param', $values["param"], $this->availableRules[$rule]['message']);

            $this->errors[$field][] = $message;
        }
    }

    private function initAvailableRules()
    {
        $this->availableRules["required"]["condition"] = fn($values) => is_null($values["value"]) || $values["value"] == '';
        $this->availableRules["valid_email"]["condition"] = fn($values) => !filter_var($values["value"], FILTER_VALIDATE_EMAIL);
        $this->availableRules["numeric"]["condition"] = fn($values) => !is_numeric($values["value"]);
        $this->availableRules["min"]["condition"] = fn($values) => strlen($values["value"]) < (int)$values["param"];
        $this->availableRules["max"]["condition"] = fn($values) => strlen($values["value"]) > (int)$values["param"];
    }
}
