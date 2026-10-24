<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Services\BookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function __construct(
        private readonly BookService $bookService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $books = $this->bookService->listByUser(
            $request->user()
        );

        return response()->json($books);
    }

    public function store(StoreBookRequest $request): JsonResponse
    {
        $book = $this->bookService->create(
            $request->user(),
            $request->validated()
        );

        return response()->json($book, 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $book = $this->bookService->findByUser(
            $request->user(),
            $id
        );

        return response()->json($book);
    }

    public function update(
        UpdateBookRequest $request,
        int $id
    ): JsonResponse {
        $book = $this->bookService->findByUser(
            $request->user(),
            $id
        );

        $updatedBook = $this->bookService->update(
            $book,
            $request->validated()
        );

        return response()->json($updatedBook);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $book = $this->bookService->findByUser(
            $request->user(),
            $id
        );

        $this->bookService->delete($book);

        return response()->json([
            'message' => 'Livro excluído com sucesso.',
        ]);
    }
}
