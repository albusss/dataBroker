<?php

declare(strict_types=1);

namespace App\Parser;

use App\DTO\ParserRequestDTO;
use App\DTO\ParserResponseDTO;
use App\Service\Util;
use Facebook\WebDriver\Exception\TimeoutException;
use Symfony\Component\DomCrawler\Crawler;
use Throwable;

use function array_filter;
use function implode;
use function strtolower;

class ParserClustrmaps extends AbstractParser
{
    private const WEBPAGE_URL = 'https://clustrmaps.com';

    /**
     * @throws Throwable
     */
    public function parse(ParserRequestDTO $request): array
    {
        // /persons/FirstName-LastName
        $searchUrl = '/persons/' . implode('-', array_filter([$request->firstName, $request->lastName]));

        $response = [];

        foreach ($this->getChromeClientIterator() as $client) {
            if (!$client->ping()) {
                continue;
            }

            $client->request('GET', self::WEBPAGE_URL . $searchUrl);

            try {
                $crawler = $client->waitFor('.container');
                $crawler->filter('.container > .row > div:first-child > div[itemprop=Person]')
                    ->each(static function (Crawler $node) use (&$response): void {
                        $fullName = $node->filter('.persons > span')->text();
                        $address  = $node->filter('.div[itemprop=address] > a')->text();
                        $link     = $node->filter('.persons')->getUri();
                        $age      = Util::onlyDigits($node->filter('.age')->text());

                        $response[] = new ParserResponseDTO($fullName, $address, $link, $age);
                    });
            } catch (TimeoutException) {
                $client->quit();

                continue;
            } catch (Throwable $e) {
                $client->quit();

                throw $e;
            }

            $client->quit();

            break;
        }

        return $response;
    }
}
