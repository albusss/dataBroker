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

class ParserAbcheck extends AbstractParser
{
    private const WEBPAGE_URL = 'https://www.advancedbackgroundchecks.com';

    /**
     * @throws Throwable
     */
    public function parse(ParserRequestDTO $request): array
    {
        // /names/firstname-lastname_city-state(2 char)
        $searchUrl = '/names/' . strtolower(implode('_', array_filter([
            implode('-', array_filter([$request->firstName, $request->lastName])),
            implode('-', array_filter([$request->city, $request->state])),
        ])));

        $response = [];

        foreach ($this->getChromeClientIterator() as $client) {
            if (!$client->ping()) {
                continue;
            }

            $client->request('GET', self::WEBPAGE_URL . $searchUrl);

            try {
                $crawler = $client->waitFor('.cads-container');
                $crawler->filter('.card-block')->each(static function (Crawler $node) use (&$response): void {
                    if ($node->filter('span[id^="sponsoredbyspan"]')->text('')) {
                        return;
                    }

                    $fullName = $node->filter('.card-title')->innerText();
                    $address  = implode(' | ', $node->filter('.address-link-list > a')->extract(['_text']));
                    $link     = $node->filter('.link-to-details')->getUri();
                    $age      = Util::onlyDigits($node->filter('.card-title > span')->text());

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
