<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Production Intake History Report</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #10b981; padding-bottom: 10px; }
        .header h2 { margin: 0; color: #065f46; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 4px 0 0 0; color: #6b7280; }
        .main-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .main-table th { background-color: #f3f4f6; border: 1px solid #e5e7eb; padding: 8px; text-align: left; font-size: 10px; text-transform: uppercase; }
        .main-table td { border: 1px solid #e5e7eb; padding: 8px; vertical-align: middle; }
        .text-center { text-align: center; }
        .font-mono { font-family: monospace; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Daily Production History Report</h2>
        <p>Duration Scope: <strong>{{ $request->start_date }}</strong> to <strong>{{ $request->end_date }}</strong></p>
    </div>

    <table style="width:100%; margin-bottom:10px;">
        <tr>
            <td><strong>Generated:</strong> {{ date('Y-m-d H:i:s') }}</td>
            <td style="text-align:right;"><strong>Total Logs Found:</strong> {{ count($history) }}</td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 15%;">Date / Time</th>
                <th style="width: 20%;">Item Code</th>
                <th style="width: 35%;">Product Item Name</th>
                <th style="width: 15%; text-align: center;">Quantity</th>
                <th style="width: 15%;">Operator/Handler</th>
            </tr>
        </thead>
        <tbody>
            @if(count($history) === 0)
                <tr>
                    <td colspan="5" class="text-center" style="padding: 20px; color: #94a3b8;">No registered production records found within this date range.</td>
                </tr>
            @else
                @foreach($history as $log)
                    <tr>
                        <td><strong>{{ $log->intake_date }}</strong><br><span style="color:#64748b;">{{ $log->created_at->format('H:i A') }}</span></td>
                        <td class="font-mono">{{ $log->item ? $log->item->item_code : '-' }}</td>
                        <td><strong>{{ $log->item ? $log->item->item_name : 'N/A' }}</strong></td>
                        <td class="text-center font-mono" style="color: #10b981; font-weight: bold;">{{ $log->quantity }}</td>
                        <td>{{ $log->handler ? $log->handler->name : 'N/A' }}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</body>
</html>