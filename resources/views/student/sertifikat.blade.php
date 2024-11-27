<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}">
    <title>Document</title>
</head>
<body>

    <style>
    
    .table {
        width: 100%;
        table-layout: fixed;  /* Menentukan lebar kolom tetap */
        border-collapse: collapse;
    }

    .table th, .table td {
        padding: 8px;
        text-align: left;
        border: 1px solid #ddd;
        word-wrap: break-word; /* Memecah kata yang panjang */
    }

    .table th {
        background-color: #f2f2f2;
        font-weight: bold;
    }

    .table td {
        max-width: 150px; /* Membatasi lebar maksimum per kolom */
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .lead {
        font-size: 16px;
        color: #555;
        margin-top: 20px;
    }

    </style>


    <div class="container my-5 border-sertifikat p-5" style="min-height: 180mm; width: 297mm;">
        <div class="mb-4">
            <h5>Nama : {{ $student->users->full_name }}</h5>
            <span>NISN : {{ $student->nisn }}</span>
            <br>
            <span>Jurusan : {{ $student->majors->major_name }}</span>
            <p>Telah melaksanakan Ujian dengan hasil  {{ $status }}</p>
        </div>

        <div class="text-center judul mt-5">
            <span class="display-4" style="font-size: 20px;">DAFTAR NILAI</span>
        </div>
        
        <div class="container">
            <div class="mt-2">
                @foreach ($examgroup as $key => $exam)
                    <h5>Competency Standard: {{ $exam->first()->competency_elements->competency_standards->unit_title }}</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Nama Ujian</th>
                                <th scope="col">Tanggal</th>
                                <th scope="col">Hasil</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($exam as $key => $item)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td>{{ $item->competency_elements->criteria }}</td>
                                <td>{{ $item->exam_date }}</td>
                                <td>
                                    @if ($item->status == 1)
                                        <span>Kompeten</span>
                                    @else
                                        <span>Tidak Kompeten</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endforeach
            </div>
        </div>
    </div>
    <script src="{{ asset('/assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('/assets/js/core/bootstrap.min.js') }}"></script>
</body>
</html>