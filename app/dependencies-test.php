<?php

declare(strict_types=1);

use App\Application\Clients\GroupService\GroupServiceClient;
use DI\ContainerBuilder;
use Tests\Fakes\FakeGroupServiceClient;

return function (ContainerBuilder $builder) {

    $builder->addDefinitions([
        GroupServiceClient::class => DI\factory(
            fn () => new FakeGroupServiceClient(),
        ),
    ]);
};
