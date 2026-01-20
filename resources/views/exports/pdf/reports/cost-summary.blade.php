<x-exports.pdf.layout :title="$title" :dateRange="$dateRange" :generatedAt="$generatedAt" :clientName="$clientName">
    <!-- Summary Metrics -->
    <div class="summary-grid">
        <div class="summary-row">
            <div class="metric-box">
                <div class="metric-value">₦{{ $data['summary']['total_cost'] }}</div>
                <div class="metric-label">Total Cost</div>
            </div>
            <div class="metric-box">
                <div class="metric-value">{{ number_format($data['summary']['total_orders']) }}</div>
                <div class="metric-label">Work Orders</div>
            </div>
            <div class="metric-box">
                <div class="metric-value">₦{{ $data['summary']['avg_cost'] }}</div>
                <div class="metric-label">Avg Cost/Order</div>
            </div>
            <div class="metric-box">
                <div class="metric-value">₦{{ $data['summary']['max_cost'] }}</div>
                <div class="metric-label">Highest Cost</div>
            </div>
        </div>
    </div>

    <!-- Cost by Facility -->
    <div class="section-title">Cost by Facility</div>
    <table>
        <thead>
            <tr>
                <th>Facility</th>
                <th class="text-right">Orders</th>
                <th class="text-right">Total Cost</th>
                <th class="text-right">Avg Cost</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['costByFacility'] as $facility)
            <tr>
                <td>{{ $facility['facility'] }}</td>
                <td class="text-right">{{ number_format($facility['order_count']) }}</td>
                <td class="text-right font-bold">₦{{ $facility['total_cost'] }}</td>
                <td class="text-right">₦{{ $facility['avg_cost'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Cost by Priority -->
    <div class="section-title">Cost by Priority</div>
    <table>
        <thead>
            <tr>
                <th>Priority</th>
                <th class="text-right">Count</th>
                <th class="text-right">Total Cost</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['costByPriority'] as $priority)
            <tr>
                <td>
                    @if($priority['priority'] === 'Critical')
                        <span class="badge badge-danger">{{ $priority['priority'] }}</span>
                    @elseif($priority['priority'] === 'High')
                        <span class="badge badge-warning">{{ $priority['priority'] }}</span>
                    @elseif($priority['priority'] === 'Medium')
                        <span class="badge badge-info">{{ $priority['priority'] }}</span>
                    @else
                        <span class="badge badge-success">{{ $priority['priority'] }}</span>
                    @endif
                </td>
                <td class="text-right">{{ number_format($priority['count']) }}</td>
                <td class="text-right font-bold">₦{{ $priority['total_cost'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Top Costly Work Orders -->
    <div class="section-title">Top 10 Costly Work Orders</div>
    <table>
        <thead>
            <tr>
                <th>Serial</th>
                <th>Title</th>
                <th>Facility</th>
                <th>Status</th>
                <th class="text-right">Cost</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['topCostlyOrders'] as $order)
            <tr>
                <td>{{ $order['serial'] }}</td>
                <td>{{ Str::limit($order['title'], 25) }}</td>
                <td>{{ $order['facility'] }}</td>
                <td>{{ $order['status'] }}</td>
                <td class="text-right font-bold">₦{{ $order['cost'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</x-exports.pdf.layout>
