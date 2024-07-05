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

class ParserAdvancedPeopleSearch extends AbstractParser
{
    private const WEBPAGE_URL = 'https://www.advanced-people-search.com';

    /**
     * @throws Throwable
     */
    public function parse(ParserRequestDTO $request): array
    {
        // /people/FirstName+LastName/City/State(2 char)/
        $searchUrl = '/people/' . implode('/', array_filter([
            implode('+', array_filter([$request->firstName, $request->lastName])),
            implode('/', array_filter([$request->city, $request->state])),
        ])) . '/';

        $response = [];

        foreach ($this->getChromeClientIterator() as $client) {
            if (!$client->ping()) {
                continue;
            }

            $client->request('GET', self::WEBPAGE_URL . $searchUrl);

            try {
                $crawler = $client->waitFor('.results-list');
                $crawler->filter('.result-content')->each(static function (Crawler $node) use (&$response): void {
                    $fullName = $node->filter('.result-name span')->text();
                    $address  = $node->filter('.result-current-address')->text();
                    $age      = Util::onlyDigits($node->filter('.result-name')->innerText()) ?: null;

                    $response[] = new ParserResponseDTO($fullName, $address, null, $age);
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
