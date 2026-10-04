<?php

declare(strict_types=1);

namespace Infrastructure\Database;

use App\Domain\Entities\OneTimeCode;
use App\Domain\Exception\NotFoundException;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\OneTimeCodeId;
use App\Domain\ValueObjects\UserId;
use App\Infrastructure\Database\DbalOneTimeCodeRepository;
use Tests\Unit\Infrastructure\Database\DatabaseBaseTestCase;

final class DbalOneTimeCodeRepositoryTest extends DatabaseBaseTestCase {
    private DbalOneTimeCodeRepository $repository;

    protected function setUp() : void {
        parent::setUp();

        $this->loadSchema('users');
        $this->loadSchema('one_time_codes');

        $this->seed('users', [
            [
                'id' => '11111111-1111-1111-1111-111111111111',
                'email' => 'test@example.com',
                'first_name' => 'Test',
                'last_name' => 'User',
                'created_at' => '2026-01-01 00:00:00',
                'updated_at' => null,
                'deleted_at' => null,
            ],
        ]);

        $this->repository = new DbalOneTimeCodeRepository(
            $this->connection,
        );
    }

    public function testSaveCreatesOneTimeCode() : void {
        $code = new OneTimeCode(
            new OneTimeCodeId(
                '22222222-2222-2222-2222-222222222222',
            ),
            new UserId(
                '11111111-1111-1111-1111-111111111111',
            ),
            hash('sha256', '123456'),
            new DateTimeValue('2030-01-01 00:00:00'),
            null,
            new DateTimeValue('2026-01-01 00:00:00'),
        );

        $this->repository->save($code);

        $row = $this->connection
            ->executeQuery(
                'SELECT * FROM one_time_codes WHERE id = :id',
                [
                    'id' => '22222222-2222-2222-2222-222222222222',
                ],
            )
            ->fetchAssociative();

        self::assertNotFalse($row);
    }

    public function testGetByIdReturnsOneTimeCode() : void {
        $this->seed('one_time_codes', [
            [
                'id' => '22222222-2222-2222-2222-222222222222',
                'user_id' => '11111111-1111-1111-1111-111111111111',
                'code_hash' => hash('sha256', '123456'),
                'expires_at' => '2030-01-01 00:00:00',
                'consumed_at' => null,
                'created_at' => '2026-01-01 00:00:00',
            ],
        ]);

        $code = $this->repository->getById(
            new OneTimeCodeId(
                '22222222-2222-2222-2222-222222222222',
            ),
        );

        self::assertInstanceOf(
            OneTimeCode::class,
            $code,
        );

        self::assertSame(
            '22222222-2222-2222-2222-222222222222',
            $code->getId()->toString(),
        );
    }

    public function testGetByIdThrowsNotFoundException() : void {
        $this->expectException(
            NotFoundException::class,
        );

        $this->repository->getById(
            new OneTimeCodeId(
                '99999999-9999-9999-9999-999999999999',
            ),
        );
    }

    public function testGetActiveCodeByUserIdReturnsLatestUnconsumedCode() : void {
        $this->seed('one_time_codes', [
            [
                'id' => '22222222-2222-2222-2222-222222222222',
                'user_id' => '11111111-1111-1111-1111-111111111111',
                'code_hash' => hash('sha256', '111111'),
                'expires_at' => '2030-01-01 00:00:00',
                'consumed_at' => null,
                'created_at' => '2026-01-01 00:00:00',
            ],
            [
                'id' => '33333333-3333-3333-3333-333333333333',
                'user_id' => '11111111-1111-1111-1111-111111111111',
                'code_hash' => hash('sha256', '222222'),
                'expires_at' => '2030-01-02 00:00:00',
                'consumed_at' => null,
                'created_at' => '2026-01-02 00:00:00',
            ],
        ]);

        $code = $this->repository->getActiveCodeByUserId(
            new UserId(
                '11111111-1111-1111-1111-111111111111',
            ),
        );

        self::assertSame(
            '33333333-3333-3333-3333-333333333333',
            $code->getId()->toString(),
        );
    }

    public function testGetActiveCodeByUserIdThrowsNotFoundException() : void {
        $this->expectException(
            NotFoundException::class,
        );

        $this->repository->getActiveCodeByUserId(
            new UserId(
                '11111111-1111-1111-1111-111111111111',
            ),
        );
    }

    public function testInvalidateActiveCodesMarksCodesAsConsumed() : void {
        $this->seed('one_time_codes', [
            [
                'id' => '22222222-2222-2222-2222-222222222222',
                'user_id' => '11111111-1111-1111-1111-111111111111',
                'code_hash' => hash('sha256', '123456'),
                'expires_at' => '2030-01-01 00:00:00',
                'consumed_at' => null,
                'created_at' => '2026-01-01 00:00:00',
            ],
        ]);

        $this->repository->invalidateActiveCodes(
            new UserId(
                '11111111-1111-1111-1111-111111111111',
            ),
        );

        $row = $this->connection
            ->executeQuery(
                'SELECT consumed_at
                 FROM one_time_codes
                 WHERE id = :id',
                [
                    'id' => '22222222-2222-2222-2222-222222222222',
                ],
            )
            ->fetchAssociative();

        self::assertNotFalse($row);
        self::assertArrayHasKey('consumed_at', $row);
        self::assertNotNull($row['consumed_at']);
    }

    public function testSaveUpdatesExistingOneTimeCode() : void {
        $this->seed('one_time_codes', [
            [
                'id' => '22222222-2222-2222-2222-222222222222',
                'user_id' => '11111111-1111-1111-1111-111111111111',
                'code_hash' => hash('sha256', '111111'),
                'expires_at' => '2030-01-01 00:00:00',
                'consumed_at' => null,
                'created_at' => '2026-01-01 00:00:00',
            ],
        ]);

        $code = new OneTimeCode(
            new OneTimeCodeId(
                '22222222-2222-2222-2222-222222222222',
            ),
            new UserId(
                '11111111-1111-1111-1111-111111111111',
            ),
            hash('sha256', '222222'),
            new DateTimeValue('2031-01-01 00:00:00'),
            new DateTimeValue('2026-02-01 00:00:00'),
            new DateTimeValue('2026-01-01 00:00:00'),
        );

        $this->repository->save($code);

        $row = $this->connection
            ->executeQuery(
                '
            SELECT *
            FROM one_time_codes
            WHERE id = :id
            ',
                [
                    'id' => '22222222-2222-2222-2222-222222222222',
                ],
            )
            ->fetchAssociative();

        self::assertNotFalse($row);

        self::assertSame(
            hash('sha256', '222222'),
            $row['code_hash'],
        );

        self::assertSame(
            '2031-01-01 00:00:00',
            $row['expires_at'],
        );

        self::assertSame(
            '2026-02-01 00:00:00',
            $row['consumed_at'],
        );
    }
}
