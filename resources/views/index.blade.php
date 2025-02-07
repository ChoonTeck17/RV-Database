<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import, Display, Download Files</title>
</head>
<body>
    <h1>File Import and Display</h1>

    <!-- Success message after uploading -->
    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif

    <!-- Import form -->
    <form action="{{ url('/import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" required>
        <button type="submit">Import File</button>
    </form>

    <h2>Uploaded Files</h2>
    <ul>
        @foreach ($files as $file)
            <li>
                <a href="{{ url('/download/' . basename($file)) }}">{{ basename($file) }}</a>
            </li>
        @endforeach
    </ul>
</body>
</html>
