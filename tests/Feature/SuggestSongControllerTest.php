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
            'code' => 123,
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
                    'code',
                    'title',
                    'slug',
                    'email',
                    'status',
                    'created_at',
                    'updated_at'
                ]
            ])
            ->assertJson([
                'message' => 'Song suggestion submitted successfully',
                'data' => [
                    'code' => 123,
                    'title' => 'Amazing Grace',
                    'slug' => 'amazing-grace',
                    'email' => 'user@example.com',
                    'status' => 1, // pending
                ]
            ]);

        $this->assertDatabaseHas('suggest_songs', [
            'code' => 123,
            'title' => 'Amazing Grace',
            'slug' => 'amazing-grace',
            'email' => 'user@example.com',
            'status' => 1,
        ]);
    }

    public function test_can_submit_song_suggestion_with_required_fields_only()
    {
        $data = [
            'code' => 456,
            'title' => 'How Great Thou Art',
            'lyrics' => 'O Lord my God, when I in awesome wonder',
            'email' => 'user2@example.com',
        ];

        $response = $this->postJson('/api/suggest-songs', $data);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Song suggestion submitted successfully',
            ]);

        $this->assertDatabaseHas('suggest_songs', [
            'code' => 456,
            'title' => 'How Great Thou Art',
            'email' => 'user2@example.com',
            'status' => 1,
        ]);
    }

    public function test_cannot_submit_without_required_fields()
    {
        $data = [
            'title' => 'Test Song',
            // Missing code, lyrics, email
        ];

        $response = $this->postJson('/api/suggest-songs', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['code', 'lyrics', 'email']);
    }

    public function test_email_must_be_valid_format()
    {
        $data = [
            'code' => 789,
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
            'code' => 999,
            'title' => 'Test Song',
            'lyrics' => 'Test lyrics',
            'email' => 'user@example.com',
            'style_id' => 99999, // Non-existent style
        ];

        $response = $this->postJson('/api/suggest-songs', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['style_id']);
    }

    public function test_slug_is_generated_from_title()
    {
        $data = [
            'code' => 111,
            'title' => 'Test Song With Spaces',
            'lyrics' => 'Test lyrics',
            'email' => 'user@example.com',
        ];

        $response = $this->postJson('/api/suggest-songs', $data);

        $response->assertStatus(201);

        $this->assertDatabaseHas('suggest_songs', [
            'title' => 'Test Song With Spaces',
            'slug' => 'test-song-with-spaces',
        ]);
    }

    public function test_duplicate_slugs_are_handled_with_counter()
    {
        // Create first song
        $data1 = [
            'code' => 111,
            'title' => 'Duplicate Title',
            'lyrics' => 'Test lyrics',
            'email' => 'user1@example.com',
        ];

        $this->postJson('/api/suggest-songs', $data1)->assertStatus(201);

        // Create second song with same title
        $data2 = [
            'code' => 222,
            'title' => 'Duplicate Title',
            'lyrics' => 'Different lyrics',
            'email' => 'user2@example.com',
        ];

        $response = $this->postJson('/api/suggest-songs', $data2);

        $response->assertStatus(201);

        $this->assertDatabaseHas('suggest_songs', [
            'title' => 'Duplicate Title',
            'slug' => 'duplicate-title',
        ]);

        $this->assertDatabaseHas('suggest_songs', [
            'title' => 'Duplicate Title',
            'slug' => 'duplicate-title-1',
        ]);
    }

    public function test_popular_rating_must_be_between_0_and_5()
    {
        $data = [
            'code' => 333,
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
            'code' => 444,
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
