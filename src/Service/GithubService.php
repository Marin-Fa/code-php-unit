<?php

namespace App\Service;

use App\Enum\HealthStatus;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GithubService
{
    public function __construct(private HttpClientInterface $httpClient, private LoggerInterface $logger)
    {

    }
    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getHealthReport(string $dinosaurName): HealthStatus
    {
        $health = HealthStatus::HEALTHY;

        // we need to call the GitHub API
        //$client = HttpClient::create();

        // dependency injection we don't need the static client anymore
        $response = $this->httpClient->request(
            method: 'GET',
            url: 'https://api.github.com/repos/SymfonyCasts/dino-park/issues'
        );

        $this->logger->info('Request Dino Issues', [
            'dino' => $dinosaurName,
            'responseStatus' => $response->getStatusCode(),
        ]);

        // We need to look at the issues
        foreach ($response->toArray() as $issue) {
            if (str_contains($issue['title'], $dinosaurName)) {
                $health = $this->getDinoStatusFromLabels($issue['labels']);
            }
        }

        return $health;
    }

    private function getDinoStatusFromLabels(array $labels): HealthStatus
    {
        $status = null;
        dump($labels);
        foreach ($labels as $label) {
            $label = $label['name'];
            // We only care about "Status" labels
            if (!str_starts_with($label, 'Status:')) {
                continue;
            }
            // Remove the "Status:" and whitespace from the label
            $status = trim(substr($label, strlen('Status:')));

            $health = HealthStatus::tryFrom($status);

            // Determine if we know about the label - throw an exception if we don't
            if ($health === null) {
                throw new \RuntimeException(sprintf('%s is an unknown status label!', $label));
            }
        }

       return $health ?? HealthStatus::HEALTHY;
    }
}