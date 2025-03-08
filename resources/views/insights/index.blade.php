<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Insights</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-4">
    <h1 class="text-center mb-4">Daftar Insights</h1>

    <div class="row">
        @foreach($insights as $insight)
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                    {{-- Tampilkan gambar hanya jika ada --}}
                    @if(isset($insight['image_url']) && $insight['image_url'])
                        <img src="{{ $insight['image_url'] }}" class="card-img-top" alt="{{ $insight['judul'] }}">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $insight['judul'] }}</h5>
                        <p class="card-text">{{ Str::limit($insight['isi'], 100) }}</p>
                        <p class="text-muted">Penulis: {{ $insight['penulis'] }}</p>
                        <p class="text-muted">Terbit: {{ \Carbon\Carbon::parse($insight['TanggalTerbit'])->format('Y-m-d') }}
                        </p>

                        <a href="{{ route('insights.show', $insight['slug']) }}" class="btn btn-primary">Baca Selengkapnya</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
