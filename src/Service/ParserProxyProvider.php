<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\ParserProxyDTO;
use RuntimeException;

use function explode;

class ParserProxyProvider
{
    /**
     * @var ParserProxyDTO[]
     */
    private readonly array $proxies;

    /**
     * @param string[] $proxiesDSN
     */
    public function __construct(array $proxiesDSN)
    {
        $proxies = [];

        foreach ($proxiesDSN as $dsn) {
            [$host, $port, $user, $password] = explode(':', $dsn) + [null, null, null, null];

            if (empty($host) || empty($port) || empty($user) || empty($password)) {
                throw new RuntimeException('Invalid proxy DSN "' . $dsn . '"');
            }

            $proxies[] = new ParserProxyDTO($host, $port, $user, $password);
        }

        if (empty($proxies)) {
            throw new RuntimeException('At least one proxy DSN must be provided');
        }

        $this->proxies = $proxies;
    }

    /**
     * @return ParserProxyDTO[]
     */
    public function provide(): array
    {
        return $this->proxies;
    }
}
