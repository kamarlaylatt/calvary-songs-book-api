<?php

namespace App\Mail;

use App\Models\Song;
use App\Models\SuggestSong;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SuggestSongApproved extends Mailable
{
    use Queueable, SerializesModels;

    public Song $song;
    public SuggestSong $suggestSong;

    public function __construct(SuggestSong $suggestSong, Song $song)
    {
        $this->suggestSong = $suggestSong;
        $this->song = $song;
    }

    public function build(): self
    {
        return $this->subject('Your song suggestion was approved')
            ->view('emails.suggest_song_approved')
            ->with([
                'suggestSong' => $this->suggestSong,
                'song' => $this->song,
            ]);
    }
}
