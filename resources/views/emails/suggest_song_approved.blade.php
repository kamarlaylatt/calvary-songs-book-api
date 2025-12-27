<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Song Suggestion Approved</title>
</head>

<body>
    <p>Dear Beloved Contributor,</p>

    <p>We are delighted to inform you that your song suggestion "{{ $suggestSong->title }}" has been approved and added to our Calvary Songs Book.</p>

    <p><strong>Song Details:</strong><br>
        Title: {{ $song->title }}<br>
        Code: {{ $song->code }}<br>
        @if($song->youtube)
        YouTube: {{ $song->youtube }}<br>
        @endif
        @if($song->description)
        Description: {{ $song->description }}<br>
        @endif
    </p>

    <p>May the Lord bless you abundantly for your contribution to spreading His word through music. Thank you for being a part of our ministry.</p>
</body>

</html>