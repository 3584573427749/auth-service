<?php
declare(strict_types=1);

namespace App\Domain\Exception;
class NotFoundException extends \RuntimeException {
    public function __construct(string $message, private array $details = []) {
        parent::__construct($message);
    }

    public function getDetails(): array {
        return $this->details;
    }
}
