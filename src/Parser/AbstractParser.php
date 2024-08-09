<?php

declare(strict_types=1);

namespace App\Parser;

use App\Contract\Parser\ParserInterface;
use Symfony\Component\Panther\Client;

use function get_class;
use function strrchr;
use function substr;

abstract class AbstractParser implements ParserInterface
{
    public function __construct(
        private readonly string $proxyServerDsn,
        private readonly string $chromeProfileDir,
        private readonly string $chromeUserDataDir,
    ) {
    }

    public function getName(): string
    {
        return substr(strrchr(get_class($this), '\\'), 1);
    }

    protected function getChromeClient(): Client
    {
        return Client::createChromeClient(
            // @see https://gist.github.com/ntamvl/4f93bbb7c9b4829c601104a2d2f91fe5
            arguments: [
                '--disable-blink-features=AutomationControlled',
                '--disable-dev-shm-usage',
                '--disable-extensions',
                '--disable-gpu',
                '--disable-popup-blocking',
                '--disable-setuid-sandbox',
                '--headless',
                '--no-sandbox',
                '--profile-directory=' . $this->chromeProfileDir,
                '--profile-directory=Default',
                '--proxy-server=' . $this->proxyServerDsn,
                '--start-maximized',
                '--user-data-dir=' . $this->chromeUserDataDir,
                '--window-size=1200,1100',
            ],
        );
    }
}
