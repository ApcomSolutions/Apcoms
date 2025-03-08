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
    <a href="{{ url()->previous() }}" class="btn btn-secondary mb-3">Kembali</a>

    <div class="card shadow-sm">
        @if($insight->image_url)
            <img src="{{ $insight->image_url }}" class="card-img-top" alt="{{ $insight->judul }}">
        @endif
        <div class="card-body">
            <h1 class="card-title">{{ $insight->judul }}</h1>
            <p class="text-muted">Penulis: {{ $insight->penulis }} | Terbit: {{ $insight->TanggalTerbit }}</p>
            <p class="card-text">{{ $insight->isi }}</p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
