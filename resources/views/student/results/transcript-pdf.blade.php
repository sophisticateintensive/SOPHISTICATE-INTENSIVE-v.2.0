<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Official Academic Transcript - {{ $student->reg_number }}</title>
    <style>
        @page {
            margin: 25mm 20mm 25mm 20mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #1a1a1a;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .institution-name {
            font-size: 18pt;
            font-weight: bold;
            color: #1e3a8a;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
        }
        .institution-sub {
            font-size: 10pt;
            color: #4b5563;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-top: 3px;
        }
        .document-title {
            font-size: 13pt;
            font-weight: bold;
            color: #111827;
            text-transform: uppercase;
            margin-top: 10px;
            letter-spacing: 1px;
        }
        .student-info-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .student-info-table td {
            padding: 4px 6px;
            font-size: 10pt;
        }
        .student-info-table .label {
            font-weight: bold;
            color: #4b5563;
            width: 22%;
        }
        .student-info-table .value {
            color: #111827;
            font-weight: 600;
            width: 28%;
        }
        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .results-table th {
            background-color: #1e3a8a;
            color: #ffffff;
            font-size: 9.5pt;
            font-weight: bold;
            text-transform: uppercase;
            padding: 8px 6px;
            text-align: left;
            border: 1px solid #1e3a8a;
        }
        .results-table td {
            padding: 7px 6px;
            font-size: 9.5pt;
            border: 1px solid #e5e7eb;
        }
        .results-table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        
        .grade-pass { color: #059669; font-weight: bold; }
        .grade-fail { color: #dc2626; font-weight: bold; }

        .summary-box {
            width: 100%;
            border: 1px solid #e5e7eb;
            background-color: #f8fafc;
            border-radius: 4px;
            padding: 10px;
            margin-bottom: 25px;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary-table td {
            padding: 3px 6px;
            font-size: 10pt;
        }

        .signatures {
            margin-top: 40px;
            width: 100%;
        }
        .signature-cell {
            width: 45%;
            text-align: center;
            vertical-align: top;
        }
        .signature-line {
            border-top: 1px solid #111827;
            margin-top: 45px;
            padding-top: 4px;
            font-size: 9.5pt;
            font-weight: bold;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            font-size: 8pt;
            color: #6b7280;
            text-align: center;
            border-top: 1px solid #e5e7eb;
            padding-top: 6px;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <div class="institution-name">Sophisticate Intensive Classes</div>
        <div class="institution-sub">Excellence in Science & Mathematics Instruction &bull; Malawi</div>
        <div class="document-title">Official Statement of Academic Record</div>
    </div>

    <!-- Student Bio Data -->
    <table class="student-info-table">
        <tr>
            <td class="label">Full Name:</td>
            <td class="value">{{ $student->user->name ?? 'N/A' }}</td>
            <td class="label">Reg. Number:</td>
            <td class="value">{{ $student->reg_number }}</td>
        </tr>
        <tr>
            <td class="label">Programme:</td>
            <td class="value">{{ $student->programme ?: 'Science & Mathematics' }}</td>
            <td class="label">Period / Term:</td>
            <td class="value">{{ $selectedTerm ? $selectedTerm->term_name : 'All Enrolled Terms' }}</td>
        </tr>
        <tr>
            <td class="label">Academic Year:</td>
            <td class="value">{{ $selectedYear ? $selectedYear->name : 'Cumulative Record' }}</td>
            <td class="label">Funding Source:</td>
            <td class="value">{{ $student->display_funding_source ?? 'Private' }}</td>
        </tr>
    </table>

    <!-- Results Table -->
    <table class="results-table">
        <thead>
            <tr>
                <th style="width: 15%;">Course Code</th>
                <th style="width: 35%;">Course Title</th>
                <th class="text-center" style="width: 10%;">Credits</th>
                <th class="text-center" style="width: 12%;">Marks (%)</th>
                <th class="text-center" style="width: 12%;">Grade</th>
                <th class="text-center" style="width: 16%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($results as $res)
                @php
                    $isPass = $res->marks >= 40;
                @endphp
                <tr>
                    <td class="font-bold">{{ $res->subject->code ?? 'N/A' }}</td>
                    <td>{{ $res->subject->name ?? 'N/A' }}</td>
                    <td class="text-center">{{ $res->subject->credit_hours ?? '-' }}</td>
                    <td class="text-center font-bold">{{ number_format($res->marks, 1) }}%</td>
                    <td class="text-center font-bold">{{ $res->grade ?? ($isPass ? 'P' : 'F') }}</td>
                    <td class="text-center">
                        <span class="{{ $isPass ? 'grade-pass' : 'grade-fail' }}">
                            {{ $isPass ? 'PASSED' : 'FAILED' }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 20px; color: #6b7280;">
                        No academic assessment results recorded for this session.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Academic Summary -->
    @if($results->count() > 0)
        <div class="summary-box">
            <table class="summary-table">
                <tr>
                    <td style="width: 25%;"><strong>Total Courses:</strong> {{ $results->count() }}</td>
                    <td style="width: 25%;"><strong>Courses Passed:</strong> {{ $passedCount }}</td>
                    <td style="width: 25%;"><strong>Average Score:</strong> {{ number_format($averageMarks, 1) }}%</td>
                    <td style="width: 25%;"><strong>Credit Total:</strong> {{ $totalCredits }}</td>
                </tr>
            </table>
        </div>
    @endif

    <!-- Official Signatures -->
    <table class="signatures">
        <tr>
            <td class="signature-cell">
                <div class="signature-line">
                    Registrar / Academic Director<br>
                    <span style="font-size: 8pt; font-weight: normal; color: #4b5563;">Sophisticate Intensive Classes</span>
                </div>
            </td>
            <td style="width: 10%;"></td>
            <td class="signature-cell">
                <div class="signature-line">
                    Official Stamp &amp; Date<br>
                    <span style="font-size: 8pt; font-weight: normal; color: #4b5563;">{{ $generatedAt }}</span>
                </div>
            </td>
        </tr>
    </table>

    <!-- Footer -->
    <div class="footer">
        This document is an authentic electronic transcript issued by Sophisticate Intensive Classes, Malawi. Generated on {{ $generatedAt }}.
    </div>

</body>
</html>
