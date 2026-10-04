<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use App\Domain\Entities\OneTimeCode;
use App\Domain\Exception\NotFoundException;
use App\Domain\Repositories\OneTimeCodeRepository;
use App\Domain\ValueObjects\OneTimeCodeId;
use App\Domain\ValueObjects\UserId;
use Doctrine\DBAL\Exception;

class DbalOneTimeCodeRepository extends AbstractDbRepository implements OneTimeCodeRepository {
    private const TABLE = 'one_time_codes';

    /**
     * @throws Exception
     */
    public function save(OneTimeCode $code) : void {
        $row = $code->asDBRow();

        try {
            $this->getById($code->getId());

            $this->connection->update(self::TABLE, $row, ['id' => $code->getId()->toString(), ]);
        } catch (NotFoundException) {
            $this->connection->insert(self::TABLE, $row);
        }
    }

    /**
     * @throws Exception
     */
    public function getById(OneTimeCodeId $id) : OneTimeCode {
        $row = $this->connection
            ->executeQuery('SELECT * FROM ' . self::TABLE . ' WHERE id = :id', ['id' => $id->toString(), ])
            ->fetchAssociative();

        if ($row === false) {
            throw new NotFoundException(sprintf('OTP code with id %s not found', $id->toString()));
        }

        return OneTimeCode::fromDBRow($row);
    }

    /**
     * @throws Exception
     */
    public function getActiveCodeByUserId(UserId $userId) : OneTimeCode {
        $row = $this->connection
            ->executeQuery(
                'SELECT * FROM ' . self::TABLE . ' WHERE user_id = :userId AND consumed_at IS NULL ORDER BY created_at DESC LIMIT 1',
                ['userId' => $userId->toString(), ],
            )
            ->fetchAssociative();

        if ($row === false) {
            throw new NotFoundException(sprintf('No active OTP code found for user %s', $userId->toString()));
        }

        return OneTimeCode::fromDBRow($row);
    }

    /**
     * @throws Exception
     */
    public function invalidateActiveCodes(UserId $userId) : void {
        $this->connection->executeStatement(
            'UPDATE ' . self::TABLE . ' SET consumed_at = NOW() 
        WHERE user_id = :userId AND consumed_at IS NULL',
            ['userId' => $userId->toString(), ],
        );
    }
}
