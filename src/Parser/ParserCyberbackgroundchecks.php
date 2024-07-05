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

class ParserCyberbackgroundchecks extends AbstractParser
{
    private const WEBPAGE_URL = 'https://www.cyberbackgroundchecks.com';

    /**
     * @throws Throwable
     */
    public function parse(ParserRequestDTO $request): array
    {
        // /people/firstname-lastname/state(2 char)/city
        $searchUrl = '/people/' . strtolower(implode('/', array_filter([
            implode('-', array_filter([$request->firstName, $request->lastName])),
            implode('/', array_filter([$request->city, $request->state])),
        ])));

        $response = [];

        foreach ($this->getChromeClientIterator() as $client) {
            if (!$client->ping()) {
                continue;
            }

            $client->request('GET', self::WEBPAGE_URL . $searchUrl);

            try {
                $crawler = $client->waitFor('.search-results__content');
                $crawler->filter('.card.card-hover')->each(static function (Crawler $node) use (&$response): void {
                    // skip wam-ad-partnerId-*-slotId-*
                    if ($node->attr('id')) {
                        return;
                    }

                    $fullName = $node->filter('.card-header .name-given')->text();
                    $address  = $node->filter('.card-body .address')->text();
                    $link     = $node->filter('.card-body a[href*=detail]')->getUri();
                    $age      = Util::onlyDigits($node->filter('.card-header .age')->text('')) ?: null;

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
