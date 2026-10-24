<?php

namespace App\Services;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class BookService
{
    public function listByUser(User $user): Collection
    {
        return $user->books()
            ->latest()
            ->get();
    }

    public function create(User $user, array $data): Book
    {
        return $user->books()->create($data);
    }

    public function findByUser(User $user, int $id): Book
    {
        return $user->books()->findOrFail($id);
    }

    public function update(Book $book, array $data): Book
    {
        $book->update($data);

        return $book->fresh();
    }

    public function delete(Book $book): void
    {
        $book->delete();
    }
}
