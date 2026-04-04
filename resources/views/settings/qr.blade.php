<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>QR Codes — {{ $posConfig->name }}</title>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background: #0d0d0d;
            font-family: 'Figtree', sans-serif;
            color: #fff;
            padding: 32px;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 32px;
        }
        .header h1 { font-size: 20px; font-weight: 700; color: #f97316; }
        .header p { font-size: 13px; color: #6b7280; margin-top: 4px; }
        .print-btn {
            padding: 10px 20px;
            background: #f97316;
            color: #fff;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
        }
        .floor-section { margin-bottom: 40px; }
        .floor-title {
            font-size: 14px;
            font-weight: 600;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .tables-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 24px;
        }
        .table-card {
            background: #1a1a1a;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 16px;
            padding: 20px;
            text-align: center;
            width: 180px;
        }
        .table-card .table-name {
            font-size: 14px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 12px;
        }
        .table-card .qr-wrap {
            background: #fff;
            border-radius: 10px;
            padding: 8px;
            display: inline-block;
        }
        .table-card .qr-url {
            font-size: 9px;
            color: #4b5563;
            margin-top: 10px;
            font-family: monospace;
            word-break: break-all;
        }
        .table-card .seats {
            font-size: 11px;
            color: #6b7280;
            margin-top: 4px;
        }
        @media print {
            body { background: #fff; color: #000; padding: 16px; }
            .print-btn { display: none; }
            .header h1 { color: #f97316; }
            .floor-title { color: #374151; border-color: #e5e7eb; }
            .table-card { background: #fff; border-color: #e5e7eb; break-inside: avoid; }
            .table-card .table-name { color: #111; }
            .table-card .qr-url { color: #9ca3af; }
            .table-card .seats { color: #6b7280; }
        }
    </style>
</head>
<body>

    <div class="header">
        <div>
            <h1>{{ $posConfig->name }} — Table QR Codes</h1>
            <p>Scan to order from your table · URL format: {{ url('/s/{token}') }}</p>
        </div>
        <button class="print-btn" onclick="window.print()">🖨 Print / Save PDF</button>
    </div>

    @forelse($floors as $floor)
    @if($floor->tables->count())
    <div class="floor-section">
        <p class="floor-title">{{ $floor->name }}</p>
        <div class="tables-grid">
            @foreach($floor->tables as $table)
            @php $url = url('/s/' . $table->qr_token); @endphp
            <div class="table-card">
                <p class="table-name">Table {{ $table->number }}</p>
                <div class="qr-wrap">
                    <div id="qr-{{ $table->id }}"></div>
                </div>
                <p class="seats">{{ $table->seats }} seats</p>
                <p class="qr-url">{{ $url }}</p>
            </div>
            @endforeach
        </div>
    </div>
    @endif
    @empty
    <p style="color:#6b7280;text-align:center;padding:40px;">No floors or tables found.</p>
    @endforelse

    <script>
    const tables = @json($floors->flatMap(fn($f) => $f->tables->map(fn($t) => ['id' => $t->id, 'url' => url('/s/'.$t->qr_token)])));

    tables.forEach(function(table) {
        new QRCode(document.getElementById('qr-' + table.id), {
            text: table.url,
            width: 140,
            height: 140,
            colorDark: '#000000',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.H
        });
    });
    </script>
</body>
</html>
