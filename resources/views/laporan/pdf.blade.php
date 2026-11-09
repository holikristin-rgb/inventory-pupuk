<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #222; }
        h1 { font-size: 15px; margin: 0; }
        .sub { color: #666; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #e5e7eb; text-align: left; }
        th, td { border: 1px solid #999; padding: 4px 6px; }
        .foot { margin-top: 12px; color: #666; }
    </style>
</head>
<body>
    <h1>{{ $judul }}</h1>
    <div class="sub">Pupuk Tani Makmur &mdash; {{ $periode }}</div>
    <table>
        <thead><tr>@foreach ($kolom as $k)<th>{{ $k }}</th>@endforeach</tr></thead>
        <tbody>
        @foreach ($baris as $b)
            <tr>@foreach ($b as $sel)<td>{{ $sel }}</td>@endforeach</tr>
        @endforeach
        </tbody>
    </table>
    <div class="foot">Dicetak {{ now()->format('d/m/Y H:i') }} oleh {{ auth()->user()->name }} ({{ auth()->user()->role_label }})</div>
</body>
</html>
