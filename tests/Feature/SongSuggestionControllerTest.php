<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Song;
use App\Models\SongSuggestion;
use App\Models\Style;
use App\Models\Category;
use App\Models\SongLanguage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SongSuggestionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $style;
    protected $category;
    protected $songLanguage;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test admin user for authentication
        $this->admin = Admin::factory()->create([
            'status' => 'active',
            'password' => Hash::make('password')
        ]);

        // Create test data
        $this->style = Style::factory()->create(['name' => 'Test Style']);
        $this->category = Category::factory()->create(['name' => 'Test Category']);
        $this->songLanguage = SongLanguage::factory()->create(['name' => 'English']);
    }

    public function test_can_list_song_suggestions()
    {
        SongSuggestion::factory()->count(3)->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->getJson('/api/admin/song-suggestions');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'current_page',
                'data' => [
                    '*' => ['id', 'title', 'lyrics', 'status', 'style', 'categories', 'song_languages']
                ]
            ]);
    }

    public function test_can_filter_song_suggestions_by_status()
    {
        SongSuggestion::factory()->create(['status' => 'pending']);
        SongSuggestion::factory()->create(['status' => 'approved']);
        SongSuggestion::factory()->create(['status' => 'cancelled']);

        $response = $this->actingAs($this->admin, 'admin')
            ->getJson('/api/admin/song-suggestions?status=pending');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('pending', $data[0]['status']);
    }

    public function test_can_create_song_suggestion()
    {
        $data = [
            'title' => 'New Suggestion',
            'lyrics' => 'Test lyrics for the song',
            'youtube' => 'https://youtube.com/watch?v=test',
            'description' => 'Test description',
            'song_writer' => 'Test Writer',
            'style_id' => $this->style->id,
            'category_ids' => [$this->category->id],
            'song_language_ids' => [$this->songLanguage->id],
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->postJson('/api/admin/song-suggestions', $data);

        $response->assertStatus(201)
            ->assertJsonPath('title', 'New Suggestion')
            ->assertJsonPath('lyrics', 'Test lyrics for the song');

        $this->assertDatabaseHas('song_suggestions', [
            'title' => 'New Suggestion',
            'status' => 'pending'
        ]);
    }

    public function test_can_show_song_suggestion()
    {
        $suggestion = SongSuggestion::factory()->create([
            'title' => 'Test Suggestion',
            'style_id' => $this->style->id
        ]);
        $suggestion->categories()->attach($this->category);

        $response = $this->actingAs($this->admin, 'admin')
            ->getJson("/api/admin/song-suggestions/{$suggestion->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $suggestion->id,
                'title' => 'Test Suggestion'
            ]);
    }

    public function test_can_update_song_suggestion()
    {
        $suggestion = SongSuggestion::factory()->create([
            'title' => 'Original Title',
            'status' => 'pending'
        ]);

        $data = [
            'title' => 'Updated Title',
            'status' => 'pending'
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->putJson("/api/admin/song-suggestions/{$suggestion->id}", $data);

        $response->assertStatus(200)
            ->assertJson([
                'title' => 'Updated Title'
            ]);

        $this->assertDatabaseHas('song_suggestions', [
            'id' => $suggestion->id,
            'title' => 'Updated Title'
        ]);
    }

    public function test_can_delete_song_suggestion()
    {
        $suggestion = SongSuggestion::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->deleteJson("/api/admin/song-suggestions/{$suggestion->id}");

        $response->assertStatus(204);

        // Check that the suggestion was soft deleted
        $this->assertDatabaseHas('song_suggestions', [
            'id' => $suggestion->id
        ]);
        
        $deletedSuggestion = SongSuggestion::withTrashed()->find($suggestion->id);
        $this->assertNotNull($deletedSuggestion->deleted_at);
    }

    public function test_can_approve_song_suggestion()
    {
        $suggestion = SongSuggestion::factory()->create([
            'title' => 'Approved Song',
            'lyrics' => 'Test lyrics',
            'status' => 'pending',
            'style_id' => $this->style->id
        ]);
        $suggestion->categories()->attach($this->category);
        $suggestion->songLanguages()->attach($this->songLanguage);

        $initialSongCount = Song::count();

        $response = $this->actingAs($this->admin, 'admin')
            ->postJson("/api/admin/song-suggestions/{$suggestion->id}/approve");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Suggestion approved and song created successfully'
            ]);

        // Check suggestion status updated
        $this->assertDatabaseHas('song_suggestions', [
            'id' => $suggestion->id,
            'status' => 'approved'
        ]);

        // Check song was created
        $this->assertEquals($initialSongCount + 1, Song::count());
        
        $song = Song::latest()->first();
        $this->assertEquals('Approved Song', $song->title);
        $this->assertEquals('Test lyrics', $song->lyrics);
    }

    public function test_cannot_approve_already_approved_suggestion()
    {
        $suggestion = SongSuggestion::factory()->create([
            'status' => 'approved'
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->postJson("/api/admin/song-suggestions/{$suggestion->id}/approve");

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Suggestion is already approved'
            ]);
    }

    public function test_can_cancel_song_suggestion()
    {
        $suggestion = SongSuggestion::factory()->create([
            'status' => 'pending'
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->postJson("/api/admin/song-suggestions/{$suggestion->id}/cancel");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Suggestion cancelled successfully'
            ]);

        $this->assertDatabaseHas('song_suggestions', [
            'id' => $suggestion->id,
            'status' => 'cancelled'
        ]);
    }

    public function test_cannot_cancel_already_cancelled_suggestion()
    {
        $suggestion = SongSuggestion::factory()->create([
            'status' => 'cancelled'
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->postJson("/api/admin/song-suggestions/{$suggestion->id}/cancel");

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Suggestion is already cancelled'
            ]);
    }

    public function test_cannot_create_suggestion_without_required_fields()
    {
        $data = [
            'title' => '', // Required
            'lyrics' => '', // Required
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->postJson('/api/admin/song-suggestions', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'lyrics']);
    }
}
