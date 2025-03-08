<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $insight->judul }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-4">
    <h1 class="text-center">{{ $insight->judul }}</h1>
    <p class="text-muted">Penulis: {{ $insight->penulis }} | Terbit: {{ $insight->TanggalTerbit }}</p>

    @if($insight->image_url)
        <img src="{{ $insight->image_url }}" class="img-fluid mb-4" alt="{{ $insight->judul }}">
    @endif

    <div>{!! $insight->isi !!}</div>
    <a href="{{ route('insights.index') }}" class="btn btn-secondary">Kembali</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
