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
        .border-sertifikat{
            position: relative;
            padding: 40px;
            background-color: rgb(235, 240, 255);
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
            border: 15px double #1a4b8f;
            border-radius: 10px;
        }
    
        .border-dalam{
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
        }
    
        .border-dalam::before{
            content: '';
            position: absolute;
            top: 30px;
            left: 30px;
            border-top: 10px solid #35459c44;
            border-left: 10px solid #1a4b8f;
            width: 150px;
            height: 150px;
        }
    
        .border-dalam::after{
            content: '';
            position: absolute;
            bottom: 30px;
            right: 30px;
            border-bottom: 10px solid #35459c44;
            border-right: 10px solid #1a4b8f;
            width: 150px;
            height: 150px;
        }
    
        .judul{
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 100px;
        }
    
        .judul img{
            width: 80px;
            height: auto;
        }
    
        .lead {
            font-size: 18px;
            font-weight: normal;
            color: #6c757d;
        }
    
        .table {
            background-color: transparent;
            table-layout: fixed;
            width: 100%;
            border: 1px solid black;
            border-collapse: collapse;
        }
    
        .table th,
        .table td {
            text-align: center;
            padding: 8px;
            font-size: 14px;
            background-color: transparent; 
            border: 1px solid black;
        }
    
        .table th {
            background-color: #c3d1eb; 
            color: black;
            font-size: 16px;
        }
    
        .table tr:nth-child(even) {
            background-color: #7dbcff;
        }
    
    
    
        .nilai-title {
            text-align: center;
            font-size: 20px; 
            font-weight: bold;
            margin-top: 20px;
        }
    
    </style>

    <div class="container my-5 border-sertifikat p-5" style="height: 180mm; width: 297mm;">
        <div class="border-dalam"></div>
            <div class="text-center mb-4 judul m-5">
                <img src="{{ asset('assets/img/background/logoypc.png') }}" alt="Logo Kiri">
                <h1 class="display-4">Sertifikat</h1>
                <img src="{{ asset('assets/img/background/logoypc.png') }}" alt="Logo Kiri">
            </div>

            <div class="text-center mb-4">
                <p class="lead">Diberikan kepada</p>
                <h2>{{ $student->users->full_name }}</h2>
                <p>Telah melaksanakan Ujian</p>
            </div>

            <div class="text-center mb-5">
                <h3>{{ $student->majors->major_name }}</h3>
            </div>

            <div class="text-center mb-5">
                <h5>{{ $status }}</h3>
            </div>
        </div>

    <div class="container my-5 border-sertifikat p-5" style="min-height: 180mm; width: 297mm;">
        <div class="border-dalam"></div>
        <div class="text-center judul mt-5">
            <span class="display-4" style="font-size: 25px;">DAFTAR NILAI</span>
        </div>

        <div class="nilai-title">
            <span>Nilai Ujian</span>
        </div>
        
        <div class="container">
            <div class="mt-2">
                @foreach ($examgroup as $key => $exam)
                    <h5>Competency Standard: {{ $exam->first()->competency_elements->competency_standards->unit_title }}</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col" style="width: 10%;">No</th>
                                <th scope="col" style="width: 30%;">Nama Ujian</th>
                                <th scope="col" style="width: 30%;">Tanggal</th>
                                <th scope="col" style="width: 30%;">Hasil</th>
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