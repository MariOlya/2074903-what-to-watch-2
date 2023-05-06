<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Repositories\Interfaces\CommentsApiRepositoryInterface;
use App\Services\Interfaces\ApiHandlerInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Carbon;

class CommentsApiRepository implements ApiHandlerInterface, CommentsApiRepositoryInterface
{
    protected string $host = 'http://guide.phpdemo.ru/api';
    protected string $commentsSearchPath = '/comments/';

    public function __construct(
        readonly ClientInterface $client
    )
    {
    }


    public function fetch(string $requiredKeyword = null): string|array
    {
        return $this->host . $this->commentsSearchPath;
    }

    public function getCommentsByFilmImdbId(string $imdbId): array
    {
        $uri = $this->fetch();

        try {
            $response = $this->client->get($uri);

            $code    = $response->getStatusCode();
            $message = $response->getReasonPhrase();

            $body = $response->getBody();
            $data = (array)json_decode($body->getContents(), false, 512, JSON_THROW_ON_ERROR);

            foreach ($data as $key => $comment) {
                if ($comment->imdb_id !== $imdbId) {
                    unset($data[$key]);
                }
            }
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

    public function getAllNewComments(): array
    {
        $uri = $this->fetch();

        try {
            $response = $this->client->get($uri);

            $code    = $response->getStatusCode();
            $message = $response->getReasonPhrase();

            $body = $response->getBody();
            $data = (array)json_decode($body->getContents(), false, 512, JSON_THROW_ON_ERROR);

            foreach ($data as $key => $comment) {
                $commentDate = date('Y-m-d h:i:s', strtotime($comment->date));
                if ($commentDate < Carbon::now()->subDay()->toDateTimeString()) {
                    unset($data[$key]);
                }
            }
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
