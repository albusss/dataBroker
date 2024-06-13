<?php

namespace App\Controller;

use App\Entity\SearchRequest;
use App\Entity\SearchResult;
use App\Entity\User;
use App\Exception\AlreadyExistsException;
use App\Repository\SearchRequestRepository;
use App\Repository\SearchResultRepository;
use App\Service\SearchRequestCreator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use function krsort;
use function strtolower;
use function trim;

class MainController extends AbstractController
{
    #[Route(path: '/', name: 'home', methods: ['GET'])]
    public function main(): Response
    {
        return $this->render('main/index.html.twig');
    }

    #[Route(path: '/search', name: 'search', methods: ['GET'])]
    public function search(Request $request, SearchRequestCreator $creator): Response
    {
        $firstname = strtolower(trim($request->get('_firstname', '')));
        $lastname  = strtolower(trim($request->get('_lastname', '')));
        $city      = strtolower(trim($request->get('_city', '')));
        $state     = strtolower(trim($request->get('_state', '')));

        try {
            $creator->create($this->getUser(), $firstname, $lastname, $city, $state);
        } catch (AlreadyExistsException $e) {
            $this->addFlash('info', $e->getMessage());

            return $this->render('main/dashboard.html.twig', [
                'searches' => $this->getLatsSearches(),
            ]);
        }

        return $this->render('main/search.html.twig', [
            'firstname' => $firstname,
            'lastname'  => $lastname,
            'city'      => $city,
            'state'     => $state,
        ]);
    }

    #[Route(path: '/dashboard', name: 'dashboard', methods: ['GET'])]
    public function dashboard(
        Request $request,
        SearchRequestRepository $requestRepository,
        SearchResultRepository $resultRepository,
    ): Response {
        $searchId     = $request->get('search_id', '');
        $lastSearches = $this->getLatsSearches();

        $result = $requestRepository->find($searchId);

        /** @var User $user */
        $user = $this->getUser();

        if (!$user->getSearchRequests()->contains($result)) {
            return $this->render('main/dashboard.html.twig', [
                'searches' => $lastSearches,
            ]);
        }

        return $this->render('main/dashboard.html.twig', [
            'searches' => $lastSearches,
            'results'  => $this->prepareResults(
                $resultRepository->findBy(['searchRequest' => $result]),
            ),
        ]);
    }

    private function getLatsSearches(): array
    {
        /** @var User $user */
        $user = $this->getUser();

        $i    = 0;
        $temp = [];

        /** @var SearchRequest $searchRequest */
        foreach ($user->getSearchRequests() as $searchRequest) {
            if (++$i >= 10) {
                break;
            }

            $searchId = $searchRequest->getId();

            $temp[$searchId] = [
                'id'     => $searchId,
                'fname'  => $searchRequest->getFirstName(),
                'lname'  => $searchRequest->getLastName(),
                'city'   => $searchRequest->getCity(),
                'date'   => $searchRequest->getCreatedAt()->format('m-d-Y'),
                'status' => $searchRequest->getStatus(),
            ];
        }

        krsort($temp);

        return $temp;
    }

    private function prepareResults(array $results): array
    {
        $temp = [];

        /** @var SearchResult $result */
        foreach ($results as $result) {
            $temp[$result->getParserName()][] = [
                'name'    => $result->getFullName(),
                'address' => $result->getAddress(),
                'age'     => $result->getAge(),
                'link'    => $result->getLink(),
            ];
        }

        return $temp;
    }
}
