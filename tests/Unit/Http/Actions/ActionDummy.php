<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Actions;

use App\Http\Actions\Action;
use Psr\Http\Message\ResponseInterface;

class ActionDummy extends Action {
    /**
     * @return string[]
     */
    public function publicGetFormData() : array {
        return $this->getFormData();
    }

    // 👇 Exponera protected metoder publikt för test

    public function publicResolveArg(string $name) : string {
        return $this->resolveArg($name);
    }

    public function publicRespondWithData(mixed $data = null, int $statusCode = 200) : ResponseInterface {
        return $this->respondWithData($data, $statusCode);
    }

    protected function action() : ResponseInterface {
        // Standard: bara returnera response
        return $this->response;
    }
}
