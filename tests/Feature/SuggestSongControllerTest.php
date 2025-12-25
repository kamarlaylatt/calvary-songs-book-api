<?php

namespace Tests\Feature;

use App\Models\Style;
use App\Models\SuggestSong;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuggestSongControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $style;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test style
        $this->style = Style::create(['name' => 'Gospel']);
    }

    public function test_can_submit_song_suggestion_with_all_fields()
    {
        $data = [
            'title' => 'Amazing Grace',
            'youtube' => 'https://youtube.com/watch?v=test',
            'description' => 'A beautiful hymn',
            'song_writer' => 'John Newton',
            'style_id' => $this->style->id,
            'key' => 'G',
            'lyrics' => 'Amazing grace how sweet the sound',
            'music_notes' => 'Some musical notes here',
            'popular_rating' => 5,
            'email' => 'user@example.com',
        ];

        $response = $this->postJson('/api/suggest-songs', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'title',
                    'email',
                    'status',
                    'created_at',
                    'updated_at'
                ]
            ])
            ->assertJson([
                'message' => 'Song suggestion submitted successfully',
                'data' => [
                    'title' => 'Amazing Grace',
                    'email' => 'user@example.com',
                    'status' => 1, // pending
                ]
            ]);

        $this->assertDatabaseHas('suggest_songs', [
            'title' => 'Amazing Grace',
            'email' => 'user@example.com',
            'status' => 1,
        ]);
    }

    public function test_can_submit_song_suggestion_with_required_fields_only()
    {
        $data = [
            'title' => 'How Great Thou Art',
            'lyrics' => 'O Lord my God, when I in awesome wonder',
        ];

        $response = $this->postJson('/api/suggest-songs', $data);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Song suggestion submitted successfully',
            ]);

        $this->assertDatabaseHas('suggest_songs', [
            'title' => 'How Great Thou Art',
            'email' => null,
            'status' => 1,
        ]);
    }

    public function test_cannot_submit_without_required_fields()
    {
        $data = [
            // Missing title, lyrics
        ];

        $response = $this->postJson('/api/suggest-songs', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'lyrics']);
    }

    public function test_email_must_be_valid_format()
    {
        $data = [
            'title' => 'Test Song',
            'lyrics' => 'Test lyrics',
            'email' => 'invalid-email',
        ];

        $response = $this->postJson('/api/suggest-songs', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_style_id_must_exist_in_database()
    {
        $data = [
            'title' => 'Test Song',
            'lyrics' => 'Test lyrics',
            'email' => 'user@example.com',
            'style_id' => 99999, // Non-existent style
        ];

        $response = $this->postJson('/api/suggest-songs', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['style_id']);
    }

    public function test_popular_rating_must_be_between_0_and_5()
    {
        $data = [
            'title' => 'Test Song',
            'lyrics' => 'Test lyrics',
            'email' => 'user@example.com',
            'popular_rating' => 10, // Invalid rating
        ];

        $response = $this->postJson('/api/suggest-songs', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['popular_rating']);
    }

    public function test_status_defaults_to_pending()
    {
        $data = [
            'title' => 'Test Song',
            'lyrics' => 'Test lyrics',
            'email' => 'user@example.com',
        ];

        $response = $this->postJson('/api/suggest-songs', $data);

        $response->assertStatus(201);

        $suggestSong = SuggestSong::where('email', 'user@example.com')->first();
        $this->assertEquals(1, $suggestSong->status);
    }
}
