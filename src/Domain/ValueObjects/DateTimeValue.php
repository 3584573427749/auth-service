<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

use InvalidArgumentException;
use JsonSerializable;
use DateTimeImmutable;
use DateTimeInterface;
use Exception;

final class DateTimeValue implements JsonSerializable {
    private DateTimeImmutable $value;

    /**
     * Accepts:
     * - ISO-8601 string (any timezone)
     * - DateTimeInterface (any timezone)
     *
     * Internally everything is stored as UTC.
     */
    public function __construct(string|DateTimeInterface $value) {
        if ($value instanceof DateTimeInterface) {
<<<<<<< HEAD
            $this->value = (new DateTimeImmutable($value->format('Y-m-d H:i:s')));
=======
            $this->value = (new DateTimeImmutable($value->format(DateTimeInterface::ATOM)))
                ->setTimezone(new \DateTimeZone('UTC'));
>>>>>>> 3514ea23110550671937378b74d086646a5c510c

            return;
        }

        try {
            $dt = new DateTimeImmutable($value);
        } catch (Exception) {
            throw new InvalidArgumentException("Invalid datetime value: {$value}");
        }

        $this->value = $dt->setTimezone(new \DateTimeZone('UTC'));
    }

    public static function fromString(string $value):self {
        return new self($value);
    }

    public function toDateTimeImmutable():DateTimeImmutable {
        return $this->value;
    }

    public function toString():string {
<<<<<<< HEAD
        return $this->value->format('Y-m-d H:i');
=======
        return $this->value->format(DateTimeInterface::ATOM); // always UTC Z
>>>>>>> 3514ea23110550671937378b74d086646a5c510c
    }

    public function __toString():string {
        return $this->toString();
    }

    public function jsonSerialize():string {
        return $this->toString();
    }

    public function equals(self $other):bool {
        return $this->value->getTimestamp() === $other->value->getTimestamp();
    }
}