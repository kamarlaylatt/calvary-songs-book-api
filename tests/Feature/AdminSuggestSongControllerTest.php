<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Song;
use App\Models\SuggestSong;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminSuggestSongControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::factory()->create([
            'status' => 'active',
            'password' => Hash::make('TestPass!234'),
        ]);
    }

    public function test_admin_can_list_suggestions_with_status_filter(): void
    {
        SuggestSong::create([
            'title' => 'Pending Song',
            'lyrics' => 'Lyrics',
            'status' => 1,
        ]);

        SuggestSong::create([
            'title' => 'Approved Song',
            'lyrics' => 'Lyrics',
            'status' => 2,
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->getJson('/api/admin/suggest-songs?status=1');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('Pending Song', $response->json('data')[0]['title']);
    }

    public function test_admin_can_update_suggestion(): void
    {
        $suggestion = SuggestSong::create([
            'title' => 'Original',
            'lyrics' => 'Lyrics',
            'status' => 1,
        ]);

        $payload = [
            'title' => 'Updated Title',
            'youtube' => 'https://youtube.com/watch?v=test',
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->putJson("/api/admin/suggest-songs/{$suggestion->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'title' => 'Updated Title',
                'youtube' => 'https://youtube.com/watch?v=test',
            ]);

        $this->assertDatabaseHas('suggest_songs', [
            'id' => $suggestion->id,
            'title' => 'Updated Title',
        ]);
    }

    public function test_admin_can_approve_suggestion_and_create_song(): void
    {
        $suggestion = SuggestSong::create([
            'title' => 'Approve Me',
            'lyrics' => 'Lyrics',
            'status' => 1,
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->postJson("/api/admin/suggest-songs/{$suggestion->id}/approve");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Suggestion approved and song created',
            ]);

        $this->assertDatabaseHas('suggest_songs', [
            'id' => $suggestion->id,
            'status' => 2,
        ]);

        $this->assertDatabaseHas('songs', [
            'title' => 'Approve Me',
            'code' => 1,
            'slug' => 'approve-me-1',
        ]);

        $this->assertEquals(1, Song::count());
    }

    public function test_admin_can_cancel_suggestion(): void
    {
        $suggestion = SuggestSong::create([
            'title' => 'Cancel Me',
            'lyrics' => 'Lyrics',
            'status' => 1,
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->postJson("/api/admin/suggest-songs/{$suggestion->id}/cancel");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Suggestion cancelled',
            ]);

        $this->assertDatabaseHas('suggest_songs', [
            'id' => $suggestion->id,
            'status' => 0,
        ]);
    }
}
