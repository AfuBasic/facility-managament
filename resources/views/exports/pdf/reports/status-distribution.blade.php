<x-exports.pdf.layout :title="$title" :dateRange="$dateRange" :generatedAt="$generatedAt" :clientName="$clientName">
    <!-- Summary Metrics -->
    <div class="summary-grid">
        <div class="summary-row">
            <div class="metric-box">
                <div class="metric-value">{{ number_format($data['total']) }}</div>
                <div class="metric-label">Total Work Orders</div>
            </div>
            <div class="metric-box">
                <div class="metric-value">{{ number_format($data['openOrders']) }}</div>
                <div class="metric-label">Open Orders</div>
            </div>
            <div class="metric-box">
                <div class="metric-value">{{ number_format($data['completedOrders']) }}</div>
                <div class="metric-label">Completed</div>
            </div>
            <div class="metric-box">
                <div class="metric-value">{{ $data['avgCompletionTime'] }}h</div>
                <div class="metric-label">Avg Completion Time</div>
            </div>
        </div>
    </div>

    <!-- Status Distribution Table -->
    <div class="section-title">Status Distribution</div>
    <table>
        <thead>
            <tr>
                <th>Status</th>
                <th class="text-right">Count</th>
                <th class="text-right">Percentage</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['statusData'] as $item)
                @if($item['count'] > 0)
                <tr>
                    <td>{{ $item['status'] }}</td>
                    <td class="text-right font-bold">{{ number_format($item['count']) }}</td>
                    <td class="text-right">{{ $item['percentage'] }}%</td>
                </tr>
                @endif
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td class="font-bold">Total</td>
                <td class="text-right font-bold">{{ number_format($data['total']) }}</td>
                <td class="text-right font-bold">100%</td>
            </tr>
        </tfoot>
    </table>

    <!-- Priority Distribution Table -->
    <div class="section-title">Priority Distribution</div>
    <table>
        <thead>
            <tr>
                <th>Priority</th>
                <th class="text-right">Count</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['priorityData'] as $item)
                <tr>
                    <td>
                        @if($item['priority'] === 'Critical')
                            <span class="badge badge-danger">{{ $item['priority'] }}</span>
                        @elseif($item['priority'] === 'High')
                            <span class="badge badge-warning">{{ $item['priority'] }}</span>
                        @elseif($item['priority'] === 'Medium')
                            <span class="badge badge-info">{{ $item['priority'] }}</span>
                        @else
                            <span class="badge badge-success">{{ $item['priority'] }}</span>
                        @endif
                    </td>
                    <td class="text-right font-bold">{{ number_format($item['count']) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-exports.pdf.layout>
