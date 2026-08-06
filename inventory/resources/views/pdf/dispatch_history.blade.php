<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Logistics Distribution Report</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #4f46e5; padding-bottom: 10px; }
        .header h2 { margin: 0; color: #1e1b4b; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 4px 0 0 0; color: #6b7280; font-size: 11px; }
        .meta-table { width: 100%; margin-bottom: 15px; font-size: 11px; }
        .main-table { width: 100%; border-collapse: collapse; margin-top: 10px; page-break-inside: auto; }
        .main-table th { background-color: #f3f4f6; border: 1px solid #e5e7eb; padding: 6px; text-align: left; font-size: 10px; text-transform: uppercase; }
        .main-table td { border: 1px solid #e5e7eb; padding: 6px; vertical-align: top; }
        .child-table { width: 100%; border-collapse: collapse; margin: 2px 0; }
        .child-table th { background-color: #fff; border: none; border-bottom: 1px solid #cbd5e1; padding: 3px; font-size: 9px; color: #475569; }
        .child-table td { border: none; border-bottom: 1px solid #f1f5f9; padding: 4px; font-size: 10px; }
        .badge { padding: 2px 5px; font-size: 9px; font-weight: bold; border-radius: 3px; text-transform: uppercase; }
        .status-reconciled { background-color: #d1fae5; color: #065f46; }
        .status-unloaded { background-color: #e0f2fe; color: #0369a1; }
        .status-loaded { background-color: #fef3c7; color: #92400e; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-mono { font-family: monospace; }
    </style>
</head>
<body>

    <!-- Report Header Section -->
    <div class="header">
        <h2>Logistics Distribution Summary Report</h2>
        <p>Filtered Date Duration Scope: <strong>{{ $request->start_date }}</strong> to <strong>{{ $request->end_date }}</strong></p>
    </div>

    <!-- Metadata Section -->
    <table class="meta-table">
        <tr>
            <td><strong>Generated Timestamp:</strong> {{ date('Y-m-d H:i:s') }}</td>
            <td style="text-align: right;"><strong>Total Dispatched Voyages Checked:</strong> {{ count($history) }}</td>
        </tr>
    </table>

    <!-- Main Operational Records Table -->
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 15%;">Date & Route Details</th>
                <th style="width: 15%;">Personnel Context</th>
                <th style="width: 10%; text-align: center;">Status Log</th>
                <th style="width: 60%;">Detailed Payload Line Allocation Splits</th>
            </tr>
        </thead>
        <tbody>
            @if(count($history) === 0)
                <tr>
                    <td colspan="4" class="text-center" style="padding: 20px; color: #94a3b8;">No registered operational logistics records logged inside this date limit range boundaries.</td>
                </tr>
            @else
                @foreach($history as $dispatch)
                    <tr style="page-break-inside: avoid;">
                        <!-- Date, Gate Pass, Vehicle, and Route Context -->
                        <td>
                            <strong>{{ $dispatch->dispatch_date }}</strong><br>
                            <span style="color:#4f46e5; font-weight:bold;">{{ $dispatch->gate_pass_no }}</span><br>
                            <span style="color:#64748b;">Lorry: {{ $dispatch->vehicle ? $dispatch->vehicle->vehicle_no : 'N/A' }}</span><br>
                            <!-- Type-safe check to handle both object relation or direct string value -->
                            <span style="color:#64748b;">Route: {{ is_object($dispatch->route) ? ($dispatch->route->route_name ?? 'N/A') : ($dispatch->route ?? 'N/A') }}</span>
                        </td>
                        
                        <!-- Sales Representative Context -->
                        <td>
                            <strong>{{ $dispatch->salesRep ? $dispatch->salesRep->name : 'N/A' }}</strong><br>
                            <span style="color:#64748b;">Code: {{ $dispatch->salesRep ? $dispatch->salesRep->rep_code : '-' }}</span>
                        </td>
                        
                        <!-- Dispatch Status Badge -->
                        <td class="text-center">
                            <span class="badge status-{{ $dispatch->status }}">{{ $dispatch->status }}</span>
                        </td>
                        
                        <!-- Detailed Payload Item Breakdown -->
                        <td>
                            <table class="child-table">
                                <thead>
                                    <tr>
                                        <th style="text-align: left; width: 40%;">Product Item</th>
                                        <th class="text-center" style="width: 15%;">Total Load</th>
                                        <th class="text-center" style="width: 15%;">Actual Sales</th>
                                        <th class="text-center" style="width: 15%;">Damaged</th>
                                        <th class="text-center" style="width: 15%;">Remaining</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dispatch->items as $item)
                                        <tr>
                                            <td>
                                                <strong>{{ $item->item ? $item->item->item_name : 'Item ID: '.$item->final_item_id }}</strong><br>
                                                <span style="font-size: 8px; color: #94a3b8;">{{ $item->item ? $item->item->item_code : '-' }}</span>
                                            </td>
                                            <td class="text-center font-mono">{{ $item->total_qty }}</td>
                                            <td class="text-center font-mono" style="color: #4f46e5; font-weight: bold;">{{ $item->actual_sales ?? 0 }}</td>
                                            <td class="text-center font-mono" style="{{ ($item->damaged_qty > 0) ? 'color: #b91c1c; font-weight: bold;' : '' }}">{{ $item->damaged_qty ?? 0 }}</td>
                                            <td class="text-center font-mono" style="color: #16a34a;">{{ $item->remaining_qty ?? 0 }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>

</body>
</html>