<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\ParserRequestDTO;
use Predis\Client as PredisClient;
use Symfony\Component\Serializer\SerializerInterface;

class ParserQueueClient
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly PredisClient $client,
    ) {
    }

    public function pushRequest(ParserRequestDTO $requestDTO): void
    {
        // @see https://redis.io/docs/latest/commands/rpush/
        $this->client->rpush('request', [$this->serializer->serialize($requestDTO, 'json')]);
    }
}
