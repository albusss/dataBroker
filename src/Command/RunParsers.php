<?php

namespace App\Command;

use App\Entity\SearchRequest;
use App\Entity\SearchResult;
use App\Enum\Parsers;
use App\Enum\SearchRequestStatus;
use App\Repository\SearchRequestRepository;
use App\Repository\SearchResultRepository;
use Generator;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Process;
use Throwable;

#[AsCommand(
    name: 'app:run-parsers',
    description: '',
)]
class RunParsers extends Command implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private const TIMEOUT_3_MIN = 180;

    private Generator $parsers;

    public function __construct(
        private bool $isVirtualParsing,
        private ContainerBagInterface $params,
        private SearchRequestRepository $searchRequestRepository,
        private SearchResultRepository $searchResultRepository,
        private ?OutputInterface $output = null,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->output = $output;
        /** @var SearchRequest[] $res */
        $res = $this->searchRequestRepository->findBy(['status' => SearchRequestStatus::IN_PROGRESS]);
        if (count($res) > 0) {
            $this->log('There is active job running');
            return self::SUCCESS;
        }

        /** @var SearchRequest $res */
        $res = $this->searchRequestRepository->findOneBy(['status' => SearchRequestStatus::NEW]);

        if (!$res) {
            return self::SUCCESS;
        }

        try {
            $res->setStatus(SearchRequestStatus::IN_PROGRESS);
            $this->searchRequestRepository->save($res);
            $this->parsers = $this->getParsersIterator();
            $this->log('Start process job. requestId: ' . $res->getId());

            $this->process($res);
        } catch (Throwable $e) {
            $this->logError(sprintf("Something terrible: %s. requestId: %s", $e->getMessage(), $res->getId()));

            $res->setStatus(SearchRequestStatus::ERROR);
            $this->searchRequestRepository->save($res);
            return self::FAILURE;
        }

        $this->log('Request finished ' . $res->getId());

        return self::SUCCESS;
    }

    private function process(SearchRequest $request): void
    {
        foreach ($this->parsers as $parser) {
            if ($this->isVirtualParsing) {
                $this->runVirtualProcess($request, $parser);
            } else {
                $this->runProcess($request, $parser);
                shell_exec('pkill chrome');
            }
        }

        $request->setStatus(SearchRequestStatus::DONE);
        $this->searchRequestRepository->save($request);
    }

    private function runProcess(SearchRequest $res, $parser): void
    {
        for ($tryCount = 1; $tryCount < 4; $tryCount++) {
            try {
                $process = $this->addNewProcess($res, $parser);
                $out = json_decode($process->getOutput(), true, 512, JSON_THROW_ON_ERROR);
                $this->log('Successful response from scrapper. Output : ' . $process->getOutput());
                $message = $out['message'] ?? null;
                $error = $out['error'] ?? null;

                if ($error !== null) {
                    $this->logError(sprintf(
                            "Error inside parser: %s. Error from: %s. Trying again. Try count: %d",
                            $out['error'],
                            $parser['name'],
                            $tryCount)
                    );

                    unset($process);
                    continue;
                }

                if (is_array($message)) {
                    foreach ($message as $item) {
                        $result = SearchResult::fromParser($parser['name'], $item, $res);
                        $this->searchResultRepository->save($result);
                    }

                    unset($process);
                    $this->log('Successful response from '. $parser['name']);

                    return;
                }
            } catch (ProcessFailedException $e) {
                $this->logError('Process can not be executed: ' . $e->getMessage());

                if ($tryCount >= 3) {
                    $result = SearchResult::forError($parser['name'], $res);
                    $this->searchResultRepository->save($result);
                    unset($process);
                    return;
                }

                unset($process);
            } catch (ProcessTimedOutException) {
                $this->logError(sprintf('Process reached timeout (%d). RequestId %d', self::TIMEOUT_3_MIN, $res->getId()));

                if ($tryCount >= 3) {
                    $result = SearchResult::forError($parser['name'], $res);
                    $this->searchResultRepository->save($result);
                    unset($process);
                    return;
                }
                unset($process);
            }
        }

        $this->log('Try count exceed. Parser: '. $parser['name']);
        $result = SearchResult::forError($parser['name'], $res);
        $this->searchResultRepository->save($result);
        unset($process);
    }

    private function addNewProcess(SearchRequest $request, $parser): Process
    {
        $projectPath = $this->params->get('kernel.project_dir');
        $path = sprintf('%s/%s', $projectPath, $parser['path']);
        $process = new Process([
            'node',
            $path,
            $request->getFirstname(),
            $request->getLastname(),
            $request->getCity(),
            $request->getState() ?? ''
        ]);
        $process->setTimeout(self::TIMEOUT_3_MIN);

        $this->log('Start searching: ' . $parser['name']);
        $this->log('Command: ' . $process->getCommandLine());

        $process->mustRun();

        return $process;
    }

    private function log(string $message): void
    {
        $this->output->writeln($message, OutputInterface::OUTPUT_PLAIN);
        $this->logger->info($message);
    }

    private function logError(string $message): void
    {
        $this->output->writeln($message, OutputInterface::OUTPUT_PLAIN);
        $this->logger->error($message);
    }

    private function getParsersIterator(): Generator
    {
        if ($this->isVirtualParsing) {
            $this->logger->info('Get virtual parsers');
            foreach (Parsers::VIRTUAL_PARSERS as $parser) {
                yield $parser;
            }
        } else {
            $this->logger->info('Get prod parsers');
            foreach (Parsers::PARSERS as $parser) {
                yield $parser;
            }
        }
    }

    private function runVirtualProcess(SearchRequest $request, string $parserName): void
    {
        if (!(random_int(0, 100) <= 70)) {// %
            return;
        }

        $searchPath = sprintf(
            [
                '/names/%s-%s_%s-%s',
                '/people/%s+%s/%s/%s/',
                '/profile/search?fname=%s&lname=%s&city=%s&state=%s',
                '/search/person?fn=%s&ln=%s&city=%s&state=%s',
                '/results/?firstName=%s&lastName=%s&city=%s&state=%s',
            ][random_int(0, 4)],
            $request->getFirstname(),
            $request->getLastname(),
            $request->getCity(),
            $request->getState(),
        );

        $maxResults = random_int(2, 7);

        for ($i = 0; $i < $maxResults; $i++) {
            $data = [
                'name' => ucfirst($request->getFirstname()) . ' ' . ucfirst($request->getLastname()),
                'age' => random_int(21, 70),
                'location' => ucfirst($request->getCity()) . ', '
                    . (strlen($request->getState()) === 2
                        ? strtoupper($request->getState()) : ucfirst($request->getState())),
                'link' => 'https://' . $parserName . $searchPath,
            ];

            $this->searchResultRepository->save(SearchResult::fromParser($parserName, $data, $request));
        }

        $this->log('Successful response from '. $parserName);
    }
}
