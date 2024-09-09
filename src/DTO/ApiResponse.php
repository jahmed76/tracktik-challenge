<?php

namespace App\DTO;

class ApiResponse
{
    private int $statusCode;
    private bool $success;
    private ?string $message;
    private ?array $data;

    public function __construct(int $statusCode, bool $success, ?string $message = null, ?array $data = null)
    {
        $this->statusCode = $statusCode;
        $this->success = $success;
        $this->message = $message;
        $this->data = $data;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getData(): ?array
    {
        return $this->data;
    }

    public function setStatusCode(int $statusCode): self
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function setSuccess(bool $success): self
    {
        $this->success = $success;
        return $this;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function setData(?array $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'statusCode' => $this->statusCode,
            'success' => $this->success,
            'message' => $this->message,
            'data' => $this->data,
        ];
    }
}
