<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_a_book(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/books', [
                'name' => 'Clean Code',
                'isbn' => '9780132350884',
                'value' => 99.90,
            ]);

        $response
            ->assertCreated()
            ->assertJsonPath('name', 'Clean Code')
            ->assertJsonPath('isbn', '9780132350884');

        $this->assertDatabaseHas('books', [
            'user_id' => $user->id,
            'name' => 'Clean Code',
            'isbn' => '9780132350884',
        ]);
    }

    public function test_book_creation_fails_when_isbn_contains_letters(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/books', [
                'name' => 'Clean Code',
                'isbn' => '978ABC',
                'value' => 99.90,
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['isbn']);

        $this->assertDatabaseCount('books', 0);
    }

    public function test_user_cannot_view_another_users_book(): void
    {
        $owner = User::factory()->create();
        $anotherUser = User::factory()->create();

        $book = $owner->books()->create([
            'name' => 'Clean Code',
            'isbn' => '9780132350884',
            'value' => 99.90,
        ]);

        $response = $this->actingAs($anotherUser, 'sanctum')
            ->getJson("/api/books/{$book->id}");

        $response->assertNotFound();
    }

    public function test_user_can_list_only_their_own_books(): void
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();

        $user->books()->create([
            'name' => 'Clean Code',
            'isbn' => '9780132350884',
            'value' => 99.90,
        ]);

        $anotherUser->books()->create([
            'name' => 'Domain-Driven Design',
            'isbn' => '9780321125217',
            'value' => 120.00,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/books');

        $response
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment([
                'name' => 'Clean Code',
            ])
            ->assertJsonMissing([
                'name' => 'Domain-Driven Design',
            ]);
    }

    public function test_user_can_update_their_own_book(): void
    {
        $user = User::factory()->create();

        $book = $user->books()->create([
            'name' => 'Clean Code',
            'isbn' => '9780132350884',
            'value' => 99.90,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/api/books/{$book->id}", [
                'name' => 'Clean Code - Updated',
                'isbn' => '9780132350884',
                'value' => 89.90,
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('name', 'Clean Code - Updated')
            ->assertJsonPath('value', '89.90');

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'user_id' => $user->id,
            'name' => 'Clean Code - Updated',
        ]);
    }

    public function test_user_cannot_update_another_users_book(): void
    {
        $owner = User::factory()->create();
        $anotherUser = User::factory()->create();

        $book = $owner->books()->create([
            'name' => 'Clean Code',
            'isbn' => '9780132350884',
            'value' => 99.90,
        ]);

        $response = $this->actingAs($anotherUser, 'sanctum')
            ->putJson("/api/books/{$book->id}", [
                'name' => 'Book modified by another user',
                'isbn' => '9780132350884',
                'value' => 50.00,
            ]);

        $response->assertNotFound();

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'user_id' => $owner->id,
            'name' => 'Clean Code',
        ]);
    }

    public function test_user_can_delete_their_own_book(): void
    {
        $user = User::factory()->create();

        $book = $user->books()->create([
            'name' => 'Clean Code',
            'isbn' => '9780132350884',
            'value' => 99.90,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/books/{$book->id}");

        $response
            ->assertOk()
            ->assertJson([
                'message' => 'Livro excluído com sucesso.',
            ]);

        $this->assertDatabaseMissing('books', [
            'id' => $book->id,
        ]);
    }
    
    public function test_user_cannot_delete_another_users_book(): void
    {
        $owner = User::factory()->create();
        $anotherUser = User::factory()->create();

        $book = $owner->books()->create([
            'name' => 'Clean Code',
            'isbn' => '9780132350884',
            'value' => 99.90,
        ]);

        $response = $this->actingAs($anotherUser, 'sanctum')
            ->deleteJson("/api/books/{$book->id}");

        $response->assertNotFound();

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'user_id' => $owner->id,
            'name' => 'Clean Code',
        ]);
    }
    
    public function test_book_creation_fails_when_name_is_missing(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/books', [
                'isbn' => '9780132350884',
                'value' => 99.90,
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);

        $this->assertDatabaseCount('books', 0);
    }

    public function test_book_creation_fails_when_value_is_negative(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/books', [
                'name' => 'Clean Code',
                'isbn' => '9780132350884',
                'value' => -10.00,
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['value']);

        $this->assertDatabaseCount('books', 0);
    }
    
    public function test_book_creation_preserves_leading_zeros_in_isbn(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/books', [
                'name' => 'Livro de teste',
                'isbn' => '0012345678901',
                'value' => 29.90,
            ]);

        $response
            ->assertCreated()
            ->assertJsonPath('isbn', '0012345678901');

        $this->assertDatabaseHas('books', [
            'user_id' => $user->id,
            'isbn' => '0012345678901',
        ]);
    }

    public function test_user_can_partially_update_their_own_book(): void
    {
        $user = User::factory()->create();

        $book = $user->books()->create([
            'name' => 'Clean Code',
            'isbn' => '9780132350884',
            'value' => 99.90,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->patchJson("/api/books/{$book->id}", [
                'name' => 'Clean Code - Revised',
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('name', 'Clean Code - Revised')
            ->assertJsonPath('isbn', '9780132350884')
            ->assertJsonPath('value', '99.90');

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'name' => 'Clean Code - Revised',
            'isbn' => '9780132350884',
        ]);
    }

    public function test_book_endpoints_require_authentication(): void
    {
        $endpoints = [
            ['GET', '/api/books'],
            ['POST', '/api/books'],
            ['GET', '/api/books/1'],
            ['PUT', '/api/books/1'],
            ['PATCH', '/api/books/1'],
            ['DELETE', '/api/books/1'],
        ];

        foreach ($endpoints as [$method, $uri]) {
            $response = $this->json($method, $uri, [
                'name' => 'Clean Code',
                'isbn' => '9780132350884',
                'value' => 99.90,
            ]);

            $response->assertUnauthorized();
        }
    }

    public function test_book_update_fails_when_value_is_negative(): void
    {
        $user = User::factory()->create();

        $book = $user->books()->create([
            'name' => 'Clean Code',
            'isbn' => '9780132350884',
            'value' => 99.90,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->patchJson("/api/books/{$book->id}", [
                'value' => -10.00,
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['value']);

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'name' => 'Clean Code',
            'value' => 99.90,
        ]);
    }

    public function test_book_update_fails_when_name_is_empty(): void
    {
        $user = User::factory()->create();

        $book = $user->books()->create([
            'name' => 'Clean Code',
            'isbn' => '9780132350884',
            'value' => 99.90,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->patchJson("/api/books/{$book->id}", [
                'name' => '',
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'name' => 'Clean Code',
        ]);
    }

    public function test_book_deletion_returns_success_message(): void
    {
        $user = User::factory()->create();

        $book = $user->books()->create([
            'name' => 'Clean Code',
            'isbn' => '9780132350884',
            'value' => 99.90,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/books/{$book->id}");

        $response
            ->assertOk()
            ->assertJsonStructure(['message'])
            ->assertJsonPath(
                'message',
                'Livro excluído com sucesso.'
            );
    }

}
