<?php

declare(strict_types=1);

namespace App\Service\Challonge\Collection;

use App\Service\Interface\Arrayable;

class Headers implements Arrayable
{
    private array $headers;

    public function __construct(array $initialHeaders = [])
    {
        $this->headers = $initialHeaders;
    }

    public function get(string $header): string
    {
        return $this->headers[$header];
    }

    public function add(string $header, string $value): bool
    {
        if ($this->isHeaderSet($header)) {
            return false;
        }

        $this->set($header, $value);

        return true;
    }

    public function set(string $header, string $value)
    {
        $this->headers[$header] = $value;
    }

    public function remove(string $header)
    {
        unset($this->headers[$header]);
    }

    public function isHeaderSet(string $header): bool
    {
        return isset($this->headers[$header]);
    }

    public function toArray(): array
    {
        return $this->headers;
    }
}