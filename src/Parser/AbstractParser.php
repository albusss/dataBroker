<?php

declare(strict_types=1);

namespace App\Parser;

use App\Contract\Parser\ParserInterface;
use App\DTO\ParserProxyDTO;
use Generator;
use Symfony\Component\Panther\Client;

use function get_class;
use function shuffle;
use function strrchr;
use function substr;

abstract class AbstractParser implements ParserInterface
{
    /**
     * @param ParserProxyDTO[] $proxies
     */
    public function __construct(
        private readonly array $proxies,
        private readonly string $chromeProfileDir,
        private readonly string $chromeUserDataDir,
    ) {
    }

    public function getName(): string
    {
        return substr(strrchr(get_class($this), '\\'), 1);
    }

    protected function getChromeClientIterator(): Generator
    {
        $proxies = $this->proxies;

        shuffle($proxies);

        foreach ($proxies as $proxy) {
            $proxyDSN = "$proxy->user:$proxy->password@$proxy->host:$proxy->port";

            yield Client::createChromeClient(
                // @see https://gist.github.com/ntamvl/4f93bbb7c9b4829c601104a2d2f91fe5
                arguments: [
                    '--disable-dev-shm-usage',
                    '--disable-gpu',
                    '--disable-setuid-sandbox',
                    '--headless',
                    '--no-sandbox',
                    '--profile-directory=' . $this->chromeProfileDir,
                    '--profile-directory=Default',
                    '--user-data-dir=' . $this->chromeUserDataDir,
                ],
                options: [
                    'capabilities' => [
                        'proxyType' => 'manual',
                        'httpProxy' => $proxyDSN,
                        'sslProxy'  => $proxyDSN,
                    ],
                ],
            );
        }
    }
}
