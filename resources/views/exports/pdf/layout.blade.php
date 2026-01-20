<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Report' }}</title>
    <style>
        @page {
            margin: 1.5cm;
            size: A4 portrait;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            color: #1e293b;
            line-height: 1.4;
        }

        .header {
            border-bottom: 2px solid #14b8a6;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }

        .header h1 {
            color: #0f766e;
            margin: 0;
            font-size: 18pt;
            font-weight: bold;
        }

        .header .meta {
            color: #64748b;
            font-size: 9pt;
            margin-top: 6px;
        }

        .summary-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .summary-row {
            display: table-row;
        }

        .metric-box {
            display: table-cell;
            width: 25%;
            padding: 12px;
            text-align: center;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
        }

        .metric-value {
            font-size: 20pt;
            font-weight: bold;
            color: #0f766e;
        }

        .metric-label {
            font-size: 8pt;
            color: #64748b;
            text-transform: uppercase;
            margin-top: 4px;
        }

        .section-title {
            font-size: 12pt;
            font-weight: bold;
            color: #334155;
            margin: 20px 0 10px 0;
            padding-bottom: 6px;
            border-bottom: 1px solid #e2e8f0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        th, td {
            border: 1px solid #e2e8f0;
            padding: 8px 10px;
            text-align: left;
        }

        th {
            background-color: #f1f5f9;
            font-weight: 600;
            color: #334155;
            font-size: 9pt;
            text-transform: uppercase;
        }

        tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .font-bold {
            font-weight: bold;
        }

        .text-success {
            color: #059669;
        }

        .text-danger {
            color: #dc2626;
        }

        .text-warning {
            color: #d97706;
        }

        .text-muted {
            color: #64748b;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 8pt;
            font-weight: 600;
        }

        .badge-success {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
        }

        .badge-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .badge-info {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8pt;
            color: #94a3b8;
            padding: 10px 0;
            border-top: 1px solid #e2e8f0;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <div class="meta">
            {{ $clientName ?? 'Optima FM' }} | {{ $dateRange ?? 'All Time' }} | Generated: {{ ($generatedAt ?? now())->format('M d, Y H:i') }}
        </div>
    </div>

    {{ $slot }}

    <div class="footer">
        Optima FM Reports | Confidential
    </div>
</body>
</html>
