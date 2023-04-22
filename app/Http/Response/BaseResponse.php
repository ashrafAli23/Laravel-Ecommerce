<?php

declare(strict_types=1);

namespace App\Http\Response;


use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class BaseResponse implements Responsable
{

    private bool $success = true;

    private mixed $data = null;

    private ?string $message = null;

    private bool $withInput = false;

    private array $additional = [];

    private int $code = 200;

    public function setData(mixed $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function withInput(bool $withInput = true): self
    {
        $this->withInput = $withInput;

        return $this;
    }

    public function setCode(int $code): self
    {
        if ($code < 100 || $code >= 600) {
            return $this;
        }

        $this->code = $code;

        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function isError(): bool
    {
        return $this->success;
    }

    public function setSuccess(bool $success = false): self
    {
        $this->success = $success;

        return $this;
    }

    public function setAdditional(array $additional): self
    {
        $this->additional = $additional;

        return $this;
    }

    public function toResponse($request): JsonResponse|JsonResource
    {
        if ($this->data instanceof JsonResource) {
            return $this->data->additional(
                array_merge(
                    [
                        'success' => $this->success,
                        'message' => $this->message,
                    ],
                    $this->additional
                )
            );
        }

        $data = [
            'success' => $this->success,
            'data' => $this->data,
            'message' => $this->message,
        ];

        if ($this->additional) {
            $data = array_merge($data, ['additional' => $this->additional]);
        }

        return response()
            ->json($data, $this->code);
    }
}