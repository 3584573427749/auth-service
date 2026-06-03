<?php
declare(strict_types=1);

namespace App\Application;

use Dotenv\Dotenv;

class Settings {
    private static ?Settings $instance = null;
    private array $data = [];

    /**
     * Lista över nycklar som ska typkonverteras.
     * Möjliga typer: int, bool, float
     * Strängar konverteras inte.
     */
    protected array $casts = [
        'DB_PORT' => 'int',
        'APP_DEBUG' => 'bool',
    ];

    private function __construct() {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        // Läs alla miljövariabler till intern array
        $this->data = $_ENV;
    }

    public static function getInstance(): Settings {
        if (self::$instance === null) {
            self::$instance = new Settings();
        }

        return self::$instance;
    }

    /**
     * Hämta env‑värde med fallback.
     */
    public function get(string $key, mixed $default = null): mixed {
        if (!array_key_exists($key, $this->data)) {
            return $default;
        }

        $value = $this->data[$key];

        // Typkonvertering om definierat
        if (isset($this->casts[$key])) {
            return $this->cast($value, $this->casts[$key]);
        }

        return $value;
    }

    private function cast(string $value, string $type): mixed {
        return match ($type) {
            'int' => (int)$value,
            'bool' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'float' => (float)$value,
            default => $value,
        };
    }
}