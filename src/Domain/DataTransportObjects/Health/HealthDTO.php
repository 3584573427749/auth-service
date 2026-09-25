<?php


declare(strict_types=1);

namespace App\Domain\DataTransportObjects\Health;

readonly class HealthDTO implements \JsonSerializable {
    /**
     * @param array<string, string> $checks
     */
    public function __construct(
        private string $status,
        private string $service,
        private string $version,
        private array $checks,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize() : array {
        return [
            'status' => $this->status,
            'service' => $this->service,
            'version' => $this->version,
            'checks' => $this->checks,
        ];
    }
}
