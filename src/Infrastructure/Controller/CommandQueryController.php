<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller;

use App\Application\QueryCommandUseCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Messenger\Exception\ExceptionInterface;

final readonly class CommandQueryController
{
    public function __construct(private QueryCommandUseCase $queryCommandUseCase)
    {
    }

    /**
     * @throws ExceptionInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $this->queryCommandUseCase->execute(uniqid());

        $response->getBody()->write('use case executed successfully');
        return $response;
    }

}