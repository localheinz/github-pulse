#!/usr/bin/env php
<?php

use Github\Client;
use League\Flysystem;
use Localheinz\GitHub\Pulse;
use Symfony\Component\Cache;
use Symfony\Component\Console;
use Symfony\Component\PropertyInfo;
use Symfony\Component\Serializer;

$autoloaders = [
    __DIR__ . '/../../../vendor/autoload.php',
    __DIR__ . '/vendor/autoload.php',
];

foreach ($autoloaders as $autoloader) {
    if (\file_exists($autoloader)) {
        require $autoloader;
        break;
    }
}

$client = new Client();

$client->addCache(new Cache\Adapter\FilesystemAdapter(
    '',
    0,
    __DIR__ . '/data/cache'
));

$application = new Console\Application('github-pulse', '0.1.0');

$fileSystem = new Flysystem\Filesystem(new Flysystem\Adapter\Local(__DIR__ . '/data/github'));

$denormalizer = new Serializer\Normalizer\PropertyNormalizer(
    null,
    new Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter(),
    new PropertyInfo\Extractor\PhpDocExtractor()
);

$serializer = new Serializer\Serializer([
    $denormalizer,
]);

$denormalizer->setSerializer($serializer);

$organizationRepository = new Pulse\Repository\OrganizationRepository(
    $client,
    $denormalizer,
    $fileSystem
);

$repositoryRepository = new Pulse\Repository\RepositoryRepository(
    $client,
    $denormalizer,
    $fileSystem
);

$pullRequestRepository = new Pulse\Repository\PullRequestRepository(
    $client,
    $denormalizer,
    $fileSystem
);

$application->addCommands([
    new Pulse\Console\GenerateCommand(
        $client,
        $organizationRepository,
        $repositoryRepository,
        $pullRequestRepository
    ),
]);

$application->run();
