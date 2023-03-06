<?php

declare(strict_types=1);

namespace App\Custom;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

class OmdbMovieInfoRepository implements MovieInfoRepository, ApiHandlerInterface
{
    protected string $host = 'https://www.omdbapi.com';
    protected ClientInterface $client;
    protected string $apiKey = 'de1e75bf';
    protected string $type = 'i';

    /**
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     */
    public function fetch(string $requiredKeyword = null): string|array
    {
        if (!isset($requiredKeyword)) {
            return $this->output('400', 'Missing required fields');
        }

        $parameters[$this->type] = $requiredKeyword;

        return $this->host . '/?apikey=' . $this->apiKey . '&' . http_build_query($parameters);
    }

    public function getMovieInfoById(string $id) : array
    {
        $uri = $this->fetch($id);

        try {
            $response = $this->client->get($uri);

            $code    = $response->getStatusCode();
            $message = $response->getReasonPhrase();

            $body = $response->getBody();
            $data = json_decode($body->getContents(), false, 512, JSON_THROW_ON_ERROR);
        } catch (RequestException|\JsonException $e) {
            $code    = 500;
            $message = 'RequestException';
            $data    = $e->getMessage();
        } catch (GuzzleException $e) {
            $code    = 500;
            $message = 'RequestException';
            $data    = $e->getMessage();
        }

        return $this->output($code, $message, $data);
    }

    private function output($code, $message, $data = null) : array
    {
        return [
            'code'    => $code,
            'message' => $message,
            'data'    => $data,
        ];
    }
}
