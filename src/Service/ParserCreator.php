<?php

declare(strict_types=1);

namespace App\Service;

use App\Contract\Dictionary\ParserType;
use App\Contract\Parser\ParserInterface;
use App\Exception\NotFoundException;

use function class_exists;

class ParserCreator
{
    public function __construct(
        private readonly string $proxyServerDsn,
        private readonly string $chromeProfileDir,
        private readonly string $chromeUserDataDir,
    ) {
    }

    /**
     * @throws NotFoundException
     */
    public function create(ParserType $parser): ParserInterface
    {
        $parserClass = 'App\Parser\\' . $parser->name;

        if (!class_exists($parserClass)) {
            throw new NotFoundException('Parser class "' . $parser->name . '" not found');
        }

        return new $parserClass(
            $this->proxyServerDsn,
            $this->chromeProfileDir,
            $this->chromeUserDataDir,
        );
    }
}
