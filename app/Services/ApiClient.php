<?php

namespace App\Services;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Str;
use Throwable;

class ApiClient
{
    public function __construct(
        protected HttpFactory $http,
        protected Kernel $kernel,
    ) {}

    /**
     * Perform a JSON API request.
     *
     * @param  string  $method  HTTP method (GET, POST, PUT, DELETE)
     * @param  string  $uri  Path including the /api prefix, e.g. /api/jobs
     * @param  array<string, mixed>  $payload  Body payload for non-GET requests
     * @param  array<string, mixed>  $query  Query parameters for the request
     * @param  string|null  $token  Bearer token
     */
    public function request(
        string $method,
        string $uri,
        array $payload = [],
        array $query = [],
        ?string $token = null,
    ): ApiResponse {
        $method = strtolower($method);
        $baseUrl = config('services.job_api.base_url');

        if ($this->shouldUseHttpClient($baseUrl)) {
            $pending = $this->http->baseUrl(rtrim($baseUrl, '/'))
                ->acceptJson()
                ->asJson()
                ->timeout(15);

            if ($token) {
                $pending = $pending->withToken($token);
            }

            try {
                $response = match ($method) {
                    'get' => $pending->get($uri, $query ?: $payload),
                    'delete' => $pending->delete($uri, $payload),
                    'patch' => $pending->patch($uri, $payload),
                    'put' => $pending->put($uri, $payload),
                    default => $pending->post($uri, $payload),
                };

                return ApiResponse::fromHttpClient($response);
            } catch (ConnectionException|RequestException $e) {
                report($e);
            } catch (Throwable $e) {
                report($e);
            }
        }

        return $this->dispatchThroughKernel($method, $uri, $payload, $query, $token);
    }

    protected function dispatchThroughKernel(
        string $method,
        string $uri,
        array $payload,
        array $query,
        ?string $token
    ): ApiResponse {
        $uri = Str::start($uri, '/');
        $isGet = $method === 'get';

        $server = $this->serverParameters();

        $request = HttpRequest::create(
            $uri,
            strtoupper($method),
            $isGet ? ($query ?: $payload) : [],
            [],
            [],
            $server,
            $isGet ? null : json_encode($payload, JSON_THROW_ON_ERROR)
        );

        $request->headers->set('Accept', 'application/json');

        if (! $isGet) {
            $request->headers->set('Content-Type', 'application/json');
        }

        if ($token) {
            $request->headers->set('Authorization', 'Bearer '.$token);
        }

        if ($isGet && ! empty($query)) {
            $request->query->add($query);
        }

        $response = $this->kernel->handle($request);
        $this->kernel->terminate($request, $response);

        $decoded = null;

        if ($content = $response->getContent()) {
            try {
                $decoded = json_decode($content, true, flags: JSON_THROW_ON_ERROR);
            } catch (\JsonException) {
                $decoded = null;
            }
        }

        return new ApiResponse($response->getStatusCode(), $decoded, $response->getContent());
    }

    public function get(string $uri, array $query = [], ?string $token = null): ApiResponse
    {
        return $this->request('GET', $uri, [], $query, $token);
    }

    public function post(string $uri, array $payload = [], ?string $token = null): ApiResponse
    {
        return $this->request('POST', $uri, $payload, [], $token);
    }

    public function put(string $uri, array $payload = [], ?string $token = null): ApiResponse
    {
        return $this->request('PUT', $uri, $payload, [], $token);
    }

    protected function shouldUseHttpClient(?string $baseUrl): bool
    {
        if (! $baseUrl) {
            return false;
        }

        $target = $this->normalizeUrlComponents($baseUrl);

        if (! $target) {
            return false;
        }

        $currentRequest = request();

        if ($currentRequest) {
            $current = $this->normalizeUrlComponents(
                $currentRequest->getScheme().'://'.$currentRequest->getHost().':'.$currentRequest->getPort()
            );

            if ($current && $target === $current) {
                return false;
            }
        }

        $appUrl = config('app.url');

        if ($appUrl) {
            $appComponents = $this->normalizeUrlComponents($appUrl);
            if ($appComponents && $target === $appComponents) {
                return false;
            }
        }

        return true;
    }

    protected function normalizeUrlComponents(?string $url): ?array
    {
        if (! $url) {
            return null;
        }

        $parts = parse_url($url);

        if ($parts === false || empty($parts['host'])) {
            return null;
        }

        $scheme = strtolower($parts['scheme'] ?? 'http');
        $host = strtolower($parts['host']);
        $port = $parts['port'] ?? ($scheme === 'https' ? 443 : 80);

        return [$scheme, $host, (int) $port];
    }

    protected function serverParameters(): array
    {
        $server = [];

        if (app()->bound('request')) {
            $server = request()->server->all();
        }

        $components = $this->normalizeUrlComponents(config('app.url')) ?? ['http', 'localhost', 80];

        $server['HTTP_HOST'] ??= $components[1].($this->shouldAppendPort($components[0], $components[2]) ? ':'.$components[2] : '');
        $server['SERVER_PORT'] ??= $components[2];
        $server['HTTPS'] ??= $components[0] === 'https' ? 'on' : 'off';

        return $server;
    }

    protected function shouldAppendPort(string $scheme, int $port): bool
    {
        return ! (($scheme === 'http' && $port === 80) || ($scheme === 'https' && $port === 443));
    }
}
