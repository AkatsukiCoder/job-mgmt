<?php

namespace App\Services;

use Illuminate\Support\Arr;

class ApiResponse
{
    public function __construct(
        protected int $status,
        protected mixed $data,
        protected ?string $rawBody = null,
    ) {}

    public static function fromHttpClient(\Illuminate\Http\Client\Response $response): self
    {
        $json = null;

        try {
            $json = $response->json();
        } catch (\Throwable) {
            $json = null;
        }

        return new self(
            $response->status(),
            $json,
            $response->body()
        );
    }

    public function status(): int
    {
        return $this->status;
    }

    public function successful(): bool
    {
        return $this->status >= 200 && $this->status < 300;
    }

    public function failed(): bool
    {
        return ! $this->successful();
    }

    public function notFound(): bool
    {
        return $this->status === 404;
    }

    public function unauthorized(): bool
    {
        return $this->status === 401;
    }

    public function json(?string $key = null, mixed $default = null): mixed
    {
        if ($this->data === null) {
            return $default;
        }

        if ($key === null) {
            return $this->data;
        }

        return Arr::get($this->data, $key, $default);
    }

    public function body(): ?string
    {
        return $this->rawBody;
    }
}
