<?php

declare(strict_types=1);

namespace App\Parser;

use App\Contract\Parser\ParserInterface;
use App\DTO\ParserProxyDTO;

use function get_class;
use function strrchr;
use function substr;

abstract class AbstractParser implements ParserInterface
{
    /**
     * @param ParserProxyDTO[] $proxies
     */
    public function __construct(
        protected readonly array $proxies,
    ) {
    }

    public function getName(): string
    {
        return substr(strrchr(get_class($this), '\\'), 1);
    }
}
