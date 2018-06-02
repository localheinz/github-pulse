<?php

declare(strict_types=1);

/**
 * Copyright (c) 2017 Andreas Möller.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @link https://github.com/localheinz/github-pulse
 */

namespace Localheinz\GitHub\Pulse\Console;

use Github\Client;
use Localheinz\GitHub\Pulse\Exception;
use Localheinz\GitHub\Pulse\Repository;
use Localheinz\GitHub\Pulse\Resource;
use Symfony\Component\Console;
use Symfony\Component\Stopwatch;

final class GenerateCommand extends Console\Command\Command
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var Repository\OrganizationRepositoryInterface
     */
    private $organizationRepository;

    /**
     * @var Repository\RepositoryRepositoryInterface
     */
    private $repositoryRepository;

    /**
     * @var Repository\PullRequestRepositoryInterface
     */
    private $pullRequestRepository;

    /**
     * @var Stopwatch\Stopwatch
     */
    private $stopwatch;

    /**
     * @var \DateTimeZone
     */
    private $dateTimeZone;

    public function __construct(
        Client $client,
        Repository\OrganizationRepositoryInterface $organizationRepository,
        Repository\RepositoryRepositoryInterface $repositoryRepository,
        Repository\PullRequestRepositoryInterface $pullRequestRepository,
        Stopwatch\Stopwatch $stopwatch = null,
        \DateTimeZone $dateTimeZone = null
    ) {
        parent::__construct();

        $this->client = $client;
        $this->organizationRepository = $organizationRepository;
        $this->repositoryRepository = $repositoryRepository;
        $this->pullRequestRepository = $pullRequestRepository;
        $this->stopwatch = $stopwatch ?: new Stopwatch\Stopwatch();
        $this->dateTimeZone = $dateTimeZone ?: new \DateTimeZone('UTC');
    }

    protected function configure()
    {
        $startTime = new \DateTimeImmutable('1970-01-01 00:00:00', $this->dateTimeZone);
        $endTime = new \DateTimeImmutable('now', $this->dateTimeZone);
        $format = 'Y-m-d H:i:s';

        $this
            ->setName('generate')
            ->setDescription('Generates a pulse for am organization')
            ->addArgument(
                'organization',
                Console\Input\InputArgument::REQUIRED,
                'The organization, e.g., "foo"'
            )
            ->addOption(
                'start-time',
                's',
                Console\Input\InputOption::VALUE_REQUIRED,
                'The start time, e.g. "2015-01-01 00:00:00"',
                $startTime->format($format)
            )
            ->addOption(
                'end-time',
                'e',
                Console\Input\InputOption::VALUE_REQUIRED,
                'The end time, e.g. "2017-01-01 00:00:00"',
                $endTime->format($format)
            )
            ->addOption(
                'auth-token',
                'a',
                Console\Input\InputOption::VALUE_OPTIONAL,
                'The GitHub token'
            );
    }

    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        $this->stopwatch->start('activity');

        $io = new Console\Style\SymfonyStyle(
            $input,
            $output
        );

        $io->title('Localheinz GitHub Pulse');

        $io->writeln('Going to determine the organization wide pulse:');

        $io->newLine();

        $startTime = $input->getOption('start-time');
        $endTime = $input->getOption('end-time');

        $io->listing([
            \sprintf(
                'in organization "%s"',
                $input->getArgument('organization')
            ),
            \sprintf(
                'between "%s" and "%s"',
                $startTime,
                $endTime
            ),
        ]);

        $authToken = $input->getOption('auth-token');

        if (null !== $authToken) {
            $this->client->authenticate(
                $authToken,
                Client::AUTH_HTTP_TOKEN
            );
        }

        try {
            $organization = $this->organizationRepository->find($input->getArgument('organization'));
        } catch (Exception\ResourceNotFoundException $exception) {
            $io->error(\sprintf(
                'Organization "%s" could not be found',
                $input->getArgument('organization')
            ));

            return 1;
        }

        $io->section(\sprintf(
            'Repositories in "%s"',
            $organization->login()
        ));

        $repositories = $this->repositoryRepository->findAll($organization);

        if (0 === \count($repositories)) {
            $io->error(\sprintf(
                'Could not find any repositories in organization "%s"',
                $organization->login()
            ));

            return 1;
        }

        $io->listing(\array_map(function (Resource\RepositoryInterface $repository) {
            return $repository->name();
        }, $repositories));

        $io->success(\sprintf(
            'Found %s repositories',
            \count($repositories)
        ));

        $io->section(\sprintf(
            'Processing repositories in "%s"',
            $organization->login()
        ));

        $io->comment('This may take a while and fail when the GitHub API rate limit is exhausted. Run it again, then.');

        $allPullRequests = [];

        \array_walk($repositories, function (Resource\RepositoryInterface $repository) use ($io, $organization, $startTime, $endTime, &$allPullRequests) {
            $io->write(\sprintf(
                ' * %s ',
                $repository->name()
            ));

            $pullRequests = $this->pullRequestRepository->findAll(
                $organization,
                $repository
            );

            \array_walk($pullRequests, function (Resource\PullRequestInterface $pullRequest) use ($io, $organization, $repository, $startTime, $endTime, &$allPullRequests) {
                $pullRequest = $this->pullRequestRepository->find(
                    $organization,
                    $repository,
                    $pullRequest->number()
                );

                if (null === $pullRequest->mergedAt()
                    || $pullRequest->createdAt() < $startTime
                    || $pullRequest->createdAt() > $endTime
                ) {
                    return;
                }

                $user = $pullRequest->user()->login();

                if (false === \array_key_exists($user, $allPullRequests)) {
                    $allPullRequests[$user] = [
                        'pullRequests' => 0,
                        'commits' => 0,
                        'firstPullRequest' => $pullRequest->createdAt(),
                        'lastPullRequest' => $pullRequest->createdAt(),
                    ];
                }

                ++$allPullRequests[$user]['pullRequests'];
                $allPullRequests[$user]['commits'] += $pullRequest->commits();

                if ($pullRequest->createdAt() < $allPullRequests[$user]['firstPullRequest']) {
                    $allPullRequests[$user]['firstPullRequest'] = $pullRequest->createdAt();
                }

                if ($pullRequest->createdAt() > $allPullRequests[$user]['lastPullRequest']) {
                    $allPullRequests[$user]['lastPullRequest'] = $pullRequest->createdAt();
                }
            });

            $io->writeln(\sprintf(
                '(%s pull requests)',
                \count($pullRequests)
            ));
        });

        $io->newLine();

        $io->section(\sprintf(
            'Pulse based on commits of merged pull requests in organization "%s" between "%s" and "%s"',
            $organization->login(),
            $startTime,
            $endTime
        ));

        \uksort($allPullRequests, function ($a, $b) use ($allPullRequests) {
            return $allPullRequests[$b]['commits'] <=> $allPullRequests[$a]['commits'];
        });

        $io->table(
            [
                '#',
                'User',
                'Commits of merged pull requests',
                'Merged pull requests',
                'First PR',
                'Last PR',
            ],
            \array_map(function ($user, $data) {
                static $row;

                if (null === $row) {
                    $row = 0;
                }

                ++$row;

                return [
                    $row,
                    $user,
                    $data['commits'],
                    $data['pullRequests'],
                    $data['firstPullRequest'],
                    $data['lastPullRequest'],
                ];
            }, \array_keys($allPullRequests), \array_values($allPullRequests))
        );

        $io->success(\sprintf(
            'Found %s merged pull request(s) in organization "%s" between "%s" and "%s"',
            \array_sum(\array_column(
                $allPullRequests,
                'pullRequests'
            )),
            $organization->login(),
            $startTime,
            $endTime
        ));

        $event = $this->stopwatch->stop('activity');

        $io->writeln($this->formatStopwatchEvent($event));

        return 0;
    }

    private function formatStopwatchEvent(Stopwatch\StopwatchEvent $event): string
    {
        return \sprintf(
            'Time: %s, Memory: %s.',
            Console\Helper\Helper::formatTime($event->getDuration() / 1000),
            Console\Helper\Helper::formatMemory($event->getMemory())
        );
    }
}
