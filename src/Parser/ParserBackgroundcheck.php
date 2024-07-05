<?php

declare(strict_types=1);

namespace App\Parser;

use App\DTO\ParserRequestDTO;
use App\DTO\ParserResponseDTO;
use Facebook\WebDriver\Exception\TimeoutException;
use Symfony\Component\DomCrawler\Crawler;
use Throwable;

use function array_filter;
use function http_build_query;
use function implode;

class ParserBackgroundcheck extends AbstractParser
{
    private const WEBPAGE_URL = 'https://backgroundcheck.run';

    /**
     * @throws Throwable
     */
    public function parse(ParserRequestDTO $request): array
    {
        // /ng/profile/search?fname=FirstName&lname=LastName&state=State(2 char)&city=City
        $searchUrl = '/ng/profile/search?' . http_build_query(array_filter([
            'fname' => $request->firstName,
            'lname' => $request->lastName,
            'state' => $request->city,
            'city'  => $request->state,
        ]));

        $response = [];

        foreach ($this->getChromeClientIterator() as $client) {
            if (!$client->ping()) {
                continue;
            }

            $client->request('GET', self::WEBPAGE_URL . $searchUrl);

            try {
                $crawler = $client->waitFor('.results_container');
                $crawler->filter('.b-pfl-list')->each(static function (Crawler $node) use (&$response): void {
                    $fullName = $node->filter('.name')->text();
                    $address  = implode(' | ', $node->filter('.r:last-child .info > span')->extract(['_text']));
                    $link     = $node->getUri();
                    $age      = $node->filter('.age')->innerText();

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
