<?php

declare(strict_types=1);

namespace App\Application\Handlers\UserRole;

use App\Application\Clients\GroupService\GroupServiceClient;
use App\Application\Commands\UserRole\UserRoleCommand;
use App\Domain\Entities\UserRole;
use App\Domain\Repositories\RoleRepository;
use App\Domain\Repositories\UserRepository;
use App\Domain\Repositories\UserRoleRepository;
use App\Domain\ValueObjects\RoleId;
use App\Domain\ValueObjects\UserId;
use Doctrine\DBAL\Connection;

class SaveUserRoleHandler extends UserRoleHandler {
    public function __construct(
        Connection $db,
        UserRoleRepository $repository,
        UserRepository $userRepository,
        RoleRepository $roleRepository,
        private GroupServiceClient $groupServiceClient,
    ) {
        parent::__construct($db, $repository, $userRepository, $roleRepository);
    }

    public function handle(UserRoleCommand $command) : void {
        $this->db->beginTransaction();
        try {
            $userId = new UserId($command->userId);
            $roleId = new RoleId($command->roleId);

            // Check if user and role exists
            $user = $this->userRepository->getById($userId);
            $role = $this->roleRepository->getById($roleId);
            $userRole = new UserRole(
                $userId,
                $roleId,
            );

            $this->repository->save($userRole);
            if ($role->isLeader()) {
                $this->groupServiceClient->syncLeader(
                    $user->getId(),
                    $user->getFirstName(),
                    $user->getLastName(),
                    true,
                );
            }
            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
