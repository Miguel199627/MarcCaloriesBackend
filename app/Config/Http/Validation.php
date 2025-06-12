<?php

namespace App\Config\Http;

class Validation
{
    protected static array $errors = [];
    protected static array $validatedData = [];
    protected static array $availableRules = [
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

    public static function validate(Request $request, array $rules): array
    {
        try {
            self::$errors = [];
            self::$validatedData = [];

            $data = $request->body;

            foreach ($rules as $field => $ruleString) {
                $value = $data[$field] ?? null;
                $rulesList = explode('|', $ruleString);

                foreach ($rulesList as $rule) {
                    $param = null;

                    if (str_contains($rule, ':')) [$rule, $param] = explode(':', $rule, 2);

                    self::validateRule($rule, $field, ["value" => $value, "param" => $param]);
                }

                if (!isset(self::$errors[$field])) self::$validatedData[$field] = $value;
            }
        } catch (\TypeError $e) {
            Response::response(500, "Error en el controlador: " . $e->getMessage());
        }

        if (!empty(self::$errors)) Response::response(400, null, self::$errors);

        return $data;
    }

    private static function validateRule(string $rule, string $field, array $values): void
    {
        if (!array_key_exists($rule, self::$availableRules)) throw new \TypeError("Tipo de regla no soportada en el controlador: {$rule}");

        $condition = self::$availableRules[$rule]['condition'];

        if ($condition($values)) {
            // [Mcerquera 20250528] Reemplazar :param en el mensaje, si existe
            $message = str_replace(':param', $values["param"], self::$availableRules[$rule]['message']);

            self::$errors[$field][] = $message;
        }
    }

    private function initAvailableRules()
    {
        self::$availableRules["required"]["condition"] = fn($values) => is_null($values["value"]) || $values["value"] == '';
        self::$availableRules["valid_email"]["condition"] = fn($values) => !is_null($values["value"]) && !filter_var($values["value"], FILTER_VALIDATE_EMAIL);
        self::$availableRules["numeric"]["condition"] = fn($values) => !is_null($values["value"]) && !is_numeric($values["value"]);
        self::$availableRules["min"]["condition"] = fn($values) => !is_null($values["value"]) && strlen($values["value"]) < (int)$values["param"];
        self::$availableRules["max"]["condition"] = fn($values) => !is_null($values["value"]) && strlen($values["value"]) > (int)$values["param"];
    }
}
