<?php

declare(strict_types=1);

namespace App\Parser;

use App\DTO\ParserRequestDTO;
use App\DTO\ParserResponseDTO;
use Facebook\WebDriver\Exception\TimeoutException;
use Symfony\Component\DomCrawler\Crawler;
use Throwable;

use function array_filter;
use function implode;
use function strtolower;

class ParserAddresses extends AbstractParser
{
    private const WEBPAGE_URL = 'https://www.addresses.com';

    /**
     * @throws Throwable
     */
    public function parse(ParserRequestDTO $request): array
    {
        $searchUrl = '/people/' . strtolower(implode('_', array_filter([
            implode('+', array_filter([$request->firstName, $request->lastName])),
        ]))) . '/';

        $response = [];

        foreach ($this->getChromeClientIterator() as $client) {
            if (!$client->ping()) {
                continue;
            }

            $client->request('GET', self::WEBPAGE_URL . $searchUrl);

            try {
                $crawler = $client->waitFor('.people-container');
                $crawler->filter('.person-info')->each(static function (Crawler $node) use (&$response): void {
                    $fullName = $node->filter('.person-name')->text();
                    $address  = $node->filter('.p:last-child')->text();
                    $link     = $node->filter('.view-profile')->getUri();

                    $response[] = new ParserResponseDTO($fullName, $address, $link, null);
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
