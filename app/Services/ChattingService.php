<?php

declare(strict_types=1);

namespace App\Services;

use App\Http\Interface\Repository\IRepository;
use Exception;
use Illuminate\Http\Request;

class ChattingService
{
    private IRepository $repository;
    public function __construct(IRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAllMessages(Request $request): object
    {
        $entries = $request->entries ?? 10;
        $data = $this->repository->index()
            ->with('user')->paginate($entries);

        if (!$data) {
            throw new Exception("Chatt is empty", 400);
        }

        return $data;
    }

    public function storeMessage(Request $request): void
    {
        $this->repository->store([
            'user_id' => $request->user()->id,
            'subject' => $request->subject,
            'message' => $request->message,
        ]);
    }

    public function showMessage(int $id): object
    {
        $data = $this->repository->show($id);

        if (!$data) {
            throw new Exception("Message not found", 404);
        }

        return $data;
    }

    public function deleteMessage(int $id): void
    {
        $data = $this->repository->delete($id);
        if (!$data) {
            throw new Exception("Message not found", 404);
        }
        $data->delete();
    }
}
