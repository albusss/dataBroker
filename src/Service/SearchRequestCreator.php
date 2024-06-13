<?php

namespace App\Service;

use App\Contract\Dictionary\ParserType;
use App\Contract\Dictionary\SearchRequestStatusType;
use App\Entity\SearchRequest;
use App\Entity\User;
use App\Exception\AlreadyExistsException;
use App\Message\SearchRequestMessage;
use App\Repository\SearchRequestRepository;
use DateTimeImmutable;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class SearchRequestCreator
{
    public function __construct(
        private readonly SearchRequestRepository $searchRequestRepository,
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    /**
     * @throws AlreadyExistsException
     */
    public function create(
        UserInterface $user,
        string $firstName,
        string $lastName,
        string $city,
        string $state,
    ): void {
        $searchPeriod = new DateTimeImmutable('-2 day'); // from 2 days until now

        $request = $this->searchRequestRepository
            ->findRequest($firstName, $lastName, $city, $state, $searchPeriod);

        if ($request) {
            /** @var User $user */
            if ($user->getSearchRequests()->contains($request)) {
                throw new AlreadyExistsException('Search request already exists');
            }

            $user->addSearchRequest($request);
            $request->addUser($user);

            $this->searchRequestRepository->save($request);

            return;
        }

        $request = (new SearchRequest())
            ->setFirstName($firstName)
            ->setLastName($lastName)
            ->setCity($city)
            ->setState($state)
            ->setStatus(SearchRequestStatusType::New)
            ->setCreatedAt(new DateTimeImmutable())
            ->addUser($user);

        /** @var User $user */
        $user->addSearchRequest($request);

        $this->searchRequestRepository->save($request);

        foreach (ParserType::cases() as $case) {
            $this->messageBus->dispatch(new SearchRequestMessage($case, $request->getId()));
        }
    }
}
