<?php

declare(strict_types=1);

use App\Application\Services\MailService;
use App\Infrastructure\Services\AppLogMailService;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

return function (ContainerBuilder $builder) {

    $builder->addDefinitions([
        // Mailservice
        MailService::class => static function (
            ContainerInterface $c,
        ) : MailService {
            return new AppLogMailService(
                $c->get(LoggerInterface::class),
            );
        },
    ]);
};
