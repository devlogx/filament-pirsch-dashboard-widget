<?php

namespace Devlogx\FilamentPirsch\Concerns;

use GuzzleHttp\Client;

class PirschClient
{
    const DEFAULT_BASE_URL = 'https://api.pirsch.io';
    const AUTHENTICATION_ENDPOINT = '/api/v1/token';
    const DOMAIN_ENDPOINT = '/api/v1/domain';
    const SESSION_DURATION_ENDPOINT = '/api/v1/statistics/duration/session';
    const TIME_ON_PAGE_ENDPOINT = '/api/v1/statistics/duration/page';
    const ACTIVE_VISITORS_ENDPOINT = '/api/v1/statistics/active';
    const VISITORS_ENDPOINT = '/api/v1/statistics/visitor';
    private string $clientID;
    private string $clientSecret;
    private Client $client;

    private \stdClass $domain;

    public function __construct($timeout = 5.0, $baseURL = PirschClient::DEFAULT_BASE_URL)
    {
        $this->clientID = config('filament-pirsch-dashboard-widget.client_id');
        $this->clientSecret = config('filament-pirsch-dashboard-widget.client_secret');
        $this->client = new Client([
            'base_uri' => $baseURL,
            'timeout' => floatval($timeout)
        ]);
        $this->domain = $this->domain();
    }

    public function getDomain(): \stdClass
    {
        return $this->domain;
    }

    private function getAccessToken() {
        if (empty($this->clientID)) {
            return $this->clientSecret;
        } else if (isset($_SESSION['pirsch_access_token'])) {
            return $_SESSION['pirsch_access_token'];
        }

        return '';
    }

    private function refreshToken() {
        try {
            if (empty($this->clientID)) {
                throw new \Exception('Single access tokens cannot be refreshed');
            }

            $response = $this->client->post(self::AUTHENTICATION_ENDPOINT, [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ],
                'json' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->clientID,
                    'client_secret' => $this->clientSecret
                ]
            ]);

            if ($response->getStatusCode() != 200) {
                throw new \Exception('Error refreshing token '.$response->getStatusCode().': '.$response->getBody());
            }

            $resp = json_decode($response->getBody());
            $_SESSION['pirsch_access_token'] = $resp->access_token;
        } catch(\GuzzleHttp\Exception\RequestException $e) {
            if ($e->getResponse()->getStatusCode() != 200) {
                throw new \Exception('Error refreshing token '.$e->getResponse()->getStatusCode().': '.$e->getResponse()->getBody());
            }
        }
    }

    private function getRequestHeader(): array
    {
        return [
            'Authorization' => 'Bearer '.$this->getAccessToken(),
            'Content-Type' => 'application/json'
        ];
    }

    public function domain($retry = true) {
        try {
            if ($this->getAccessToken() === '' && $retry) {
                $this->refreshToken();
            }

            $response = $this->client->get(self::DOMAIN_ENDPOINT, [
                'headers' => $this->getRequestHeader()
            ]);
            $domains = json_decode($response->getBody());

            if (count($domains) !== 1) {
                throw new \Exception('Error reading domain from result');
            }

            return $domains[0];
        } catch(\GuzzleHttp\Exception\RequestException $e) {
            if ($e->getResponse()->getStatusCode() == 401 && $retry) {
                $this->refreshToken();
                return $this->domain(false);
            } else if ($e->getResponse()->getStatusCode() != 200) {
                throw new \Exception('Error getting domain: '.$e->getResponse()->getBody());
            }
        }

        return null;
    }

    public function performGet($url, Filter $filter, $retry = true) {
        try {
            if ($this->getAccessToken() === '' && $retry) {
                $this->refreshToken();
            }
            $filter = $filter->setId($this->domain->id);
            $query = $filter->toQuery();
            $response = $this->client->get($url.'?'.$query, [
                'headers' => $this->getRequestHeader()
            ]);
            return json_decode($response->getBody());
        } catch(\GuzzleHttp\Exception\RequestException $e) {
            if ($e->getResponse()->getStatusCode() == 401 && $retry) {
                $this->refreshToken();
                return $this->performGet($url, $filter, false);
            } else if ($e->getResponse()->getStatusCode() != 200) {
                throw new \Exception('Error getting result for '.$url.': '.$e->getResponse()->getBody());
            }
        }
    }

}
