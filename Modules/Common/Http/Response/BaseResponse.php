<?php

declare(strict_types=1);

namespace Modules\Common\Http\Response;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class BaseResponse implements Responsable
{

    private bool $success = true;

    private mixed $data = null;

    private ?string $message = null;

    //     private string $previousUrl = '';

    //     private string $nextUrl = '';

    private bool $withInput = false;

    private array $additional = [];

    private int $code = 200;

    public function setData(mixed $data): self
    {
        $this->data = $data;

        return $this;
    }

    //     public function setPreviousUrl(string $previousUrl): self
    //     {
    //         $this->previousUrl = $previousUrl;

    //         return $this;
    //     }

    //     /**
    //      * @param string $nextUrl
    //      * @return BaseHttpResponse
    //      */
    //     public function setNextUrl(string $nextUrl): self
    //     {
    //         $this->nextUrl = $nextUrl;

    //         return $this;
    //     }

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

    public function toApiResponse(): JsonResponse|JsonResource
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

        if (!$this->success || !isset($data['data'])) {
            unset($data['data']);
        }

        return response()
            ->json($data, $this->code);
    }

    public function toResponse($request)
    {
        # code...
    }

    // /**
    //  * @param Request $request
    //  * @return JsonResponse|RedirectResponse
    //  */
    // public function toResponse($request)
    // {
    //     if ($request->expectsJson()) {
    //         $data = [
    //             'error' => $this->error,
    //             'data' => $this->data,
    //             'message' => $this->message,
    //         ];

    //         if ($this->additional) {
    //             $data = array_merge($data, ['additional' => $this->additional]);
    //         }

    //         return response()
    //             ->json($data, $this->code);
    //     }

    //     if ($request->input('submit') === 'save' && !empty($this->previousUrl)) {
    //         return $this->responseRedirect($this->previousUrl);
    //     } elseif (!empty($this->nextUrl)) {
    //         return $this->responseRedirect($this->nextUrl);
    //     }

    //     return $this->responseRedirect(URL::previous());
    // }

    //     /**
    //      * @param string $url
    //      * @return RedirectResponse
    //      */
    //     protected function responseRedirect(string $url): RedirectResponse
    //     {
    //         if ($this->withInput) {
    //             return redirect()
    //                 ->to($url)
    //                 ->with($this->error ? 'error_msg' : 'success_msg', $this->message)
    //                 ->withInput();
    //         }

    //         return redirect()
    //             ->to($url)
    //             ->with($this->error ? 'error_msg' : 'success_msg', $this->message);
    //     }
}