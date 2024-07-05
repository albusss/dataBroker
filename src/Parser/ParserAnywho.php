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

class ParserAnywho extends AbstractParser
{
    private const WEBPAGE_URL = 'https://www.anywho.com';

    /**
     * @throws Throwable
     */
    public function parse(ParserRequestDTO $request): array
    {
        // /people/firstname+lastname/city+state(2 char)/
        $searchUrl = '/people/' . strtolower(implode('/', array_filter([
            implode('+', array_filter([$request->firstName, $request->lastName])),
            implode('+', array_filter([$request->city, $request->state])),
        ]))) . '/';

        $response = [];

        foreach ($this->getChromeClientIterator() as $client) {
            if (!$client->ping()) {
                continue;
            }

            $client->request('GET', self::WEBPAGE_URL . $searchUrl);

            try {
                $crawler = $client->waitFor('.people-container');
                $crawler->filter('.person')->each(static function (Crawler $node) use (&$response): void {
                    $fullName = $node->filter('.person-info > strong')->text();
                    $address  = $node->filter('.person-info > p:first-child')->text();
                    $link     = $node->filter('.view-profile')->getUri();
                    $age      = Util::onlyDigits($node->filter('.person-info')->innerText());

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
