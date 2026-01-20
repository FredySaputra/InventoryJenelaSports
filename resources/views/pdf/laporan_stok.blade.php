<!DOCTYPE html>
<html>
<head>
    <title>Laporan Stok Barang</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; }

        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; padding: 0; color: #333; }
        .header h3 { margin: 5px 0; color: #555; }

        .info-bar {
            background-color: #ffc107; /* Warna Orange/Kuning */
            padding: 5px 10px;
            border: 1px solid #d39e00;
            margin-bottom: 10px;
            font-weight: bold;
            display: flex; justify-content: space-between;
        }

        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: center; }

        /* Styling Header Table Hijau a la Excel */
        thead th {
            background-color: #d1e7dd; /* Hijau muda */
            color: #0f5132;
            font-weight: bold;
        }

        .text-left { text-align: left; }
        .category-header {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: left;
            padding-left: 10px;
        }
    </style>
</head>
<body>

<div class="header">
    <h2>STOCK JENELA SPORTS</h2>
    <h3>Laporan Posisi Stok</h3>
</div>

<table style="width: 100%; border: none; margin-bottom: 10px;">
    <tr style="background-color: #fff3cd; border: 1px solid #ffecb5;">
        <td style="text-align: left; border: none; padding: 8px;">
            <strong>Keterangan:</strong> Jumlah Stok (Pcs)
        </td>
        <td style="text-align: right; border: none; padding: 8px;">
            <strong>Update Terbaru:</strong> {{ $tanggalUpdate }}
        </td>
    </tr>
</table>

@foreach($kategoris as $cat)
    @if($cat->produks->count() > 0)
        <table class="table-stok">
            <thead>
            <tr>
                <th colspan="{{ $cat->sizes->count() + 1 }}" class="category-header" style="background-color: #cfe2ff; color: #084298;">
                    KATEGORI: {{ strtoupper($cat->nama) }}
                </th>
            </tr>
            <tr>
                <th style="width: 40%;" class="text-left">Nama Barang</th>
                @foreach($cat->sizes as $size)
                    <th>{{ $size->tipe }}</th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            @foreach($cat->produks as $prod)
                <tr>
                    <td class="text-left">
                        {{ $prod->nama }} {{ $prod->warna }} {{ $prod->bahan ? $prod->bahan->nama : '' }}
                    </td>
                    @foreach($cat->sizes as $size)
                        @php
                            // Cari stok di PHP (karena di view blade)
                            $stokItem = $prod->stoks->first(function($s) use ($size) {
                                return ($s->idSize == $size->id) || ($s->id_size == $size->id);
                            });
                            $jumlah = $stokItem ? $stokItem->stok : 0;
                        @endphp
                        <td>{{ $jumlah }}</td>
                    @endforeach
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
@endforeach

</body>
</html>
