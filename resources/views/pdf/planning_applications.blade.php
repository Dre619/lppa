<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lusaka Province Planning Authority - Schedule of Applications</title>
    <style>
        @page {
            margin: 50px 25px 70px 25px;
            size: A4 landscape;
        }

        @font-face {
            font-family: 'times';
            src: url('{{ asset('assets/fonts/times.ttf') }}') format('ttf');
            font-weight: normal;
            font-style: normal;
        }

        body {
            font-family: times;
            margin: 20px;
            font-size: 12px;
            line-height: 1.5;
            color: #000;
        }

        h1, h2 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
        }

        td {
            font-size: 12px;
            vertical-align: top;
        }

        th {
            text-align: center;
            border-bottom: 1px solid #000;
            background-color: #ffffff;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 50px;
            text-align: right;
            font-size: 12px;
            font-style: italic;
            padding-right: 20px;
            padding-top: 20px;
        }

        .footer .page:after {
            content: counter(page);
        }

        .footer .pages:after {
            content: counter(pages);
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <h1 style="margin-bottom: -20px;">LUSAKA PROVINCE PLANNING AUTHORITY</h1>
    <h2 style="margin-bottom: -20px;">SCHEDULE OF APPLICATIONS FOR PLANNING PERMISSION TO DEVELOPMENT LAND</h2>
    <h2>FOR THE MEETING OF {{ $meetingDate }}</h2>

    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>REGISTRATION OF APPLICATION</th>
                <th>NAME AND ADDRESS OF APPLICANT</th>
                <th>DETAILS OF APPLICATIONS</th>
                <th>DATE OF RECEIPT</th>
                <th>TECHNICAL COMMITTEE RECOMMENDATION</th>
            </tr>
        </thead>
        <tbody>
            @foreach($applications as $index => $application)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $application->application_id }}</td>
                <td>
                    @foreach($application->applicationApplicants as $applicant)
                        {{ $applicant->applicant_title->name ?? '' }} {{ $applicant->first_name }} {{ $applicant->last_name }}<br>
                        {{ $applicant->address }}
                    @endforeach
                    @if($application->is_institution)
                        {{ $application->institution_name }}
                    @endif
                </td>
                <td>
                    {{ $application->applicationSubmissions->first()->application_text }}<br>
                    <b>{{ $application->registration_area->name }},{{ $application->district->name }}</b>
                </td>
                <td>{{ $application->created_at->format('d-m-Y') }}</td>
                <td>
                     @foreach($application->applicationResolutions as $resolution)
                        <strong>{{ $loop->iteration }}. {{ $resolution->resolution->resolution_type }}</strong><br>
                        @if($resolution->resolution_details)
                            @foreach(explode("\n", preg_replace('/<br\s*\/?>/i', "\n",$resolution->resolution_details)) as $condition)
                                {{ $loop->iteration }}. {{ $condition }}<br>
                            @endforeach
                        @endif
                    @endforeach
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
<center><span class="pagenumx">{{$pdf->getCanvas()->page_text($x, $y, '{PAGE_NUM}/{PAGE_COUNT}', $font, $size)}}</span></center>
</body>
</html>
