<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Main Inventory Stock Report</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #3b82f6; padding-bottom: 10px; }
        .header h2 { margin: 0; color: #1e3a8a; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 4px 0 0 0; color: #6b7280; }
        .main-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .main-table th { background-color: #f3f4f6; border: 1px solid #e5e7eb; padding: 8px; text-align: left; font-size: 10px; text-transform: uppercase; }
        .main-table td { border: 1px solid #e5e7eb; padding: 8px; }
        .text-center { text-align: center; }
        .font-mono { font-family: monospace; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Main Inventory Stock Status Report</h2>
        <p>Current Real-time Stock Balance Sheet</p>
    </div>

    <table style="width:100%; margin-bottom:10px;">
        <tr>
            <td><strong>As of Date:</strong> {{ date('Y-m-d H:i:s') }}</td>
            <td style="text-align:right;"><strong>Total Distinct SKUs:</strong> {{ count($stockItems) }}</td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 20%;">Item Code</th>
                <th style="width: 50%;">Product Item Description</th>
                <th style="width: 30%; text-align: center;">Available Stock On Hand</th>
            </tr>
        </thead>
        <tbody>
            @if(count($stockItems) === 0)
                <tr>
                    <td colspan="3" class="text-center" style="padding: 20px; color: #94a3b8;">Main stock inventory is currently empty.</td>
                </tr>
            @else
                @foreach($stockItems as $stock)
                    <tr>
                        <td class="font-mono">{{ $stock->item ? $stock->item->item_code : '-' }}</td>
                        
                        <td><strong>{{ $stock->item ? $stock->item->item_name : $stock->item_name }}</strong></td>
                        
                        <td class="text-center font-mono" style="color: #2563eb; font-weight: bold; font-size: 12px;">{{ $stock->available_qty ?? 0 }}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</body>
</html>