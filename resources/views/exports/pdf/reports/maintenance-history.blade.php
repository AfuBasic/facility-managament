<x-exports.pdf.layout :title="$title" :dateRange="$dateRange" :generatedAt="$generatedAt" :clientName="$clientName">
    <!-- Summary Metrics -->
    <div class="summary-grid">
        <div class="summary-row">
            <div class="metric-box">
                <div class="metric-value">{{ number_format($data['summary']['total_orders']) }}</div>
                <div class="metric-label">Total Work Orders</div>
            </div>
            <div class="metric-box">
                <div class="metric-value">{{ number_format($data['summary']['completed']) }}</div>
                <div class="metric-label">Completed</div>
            </div>
            <div class="metric-box">
                <div class="metric-value">₦{{ $data['summary']['total_cost'] }}</div>
                <div class="metric-label">Total Cost</div>
            </div>
            <div class="metric-box">
                <div class="metric-value">{{ $data['summary']['avg_hours'] }}h</div>
                <div class="metric-label">Avg Completion</div>
            </div>
        </div>
    </div>

    <!-- Facility Breakdown Table -->
    <div class="section-title">Maintenance by Facility</div>
    <table>
        <thead>
            <tr>
                <th>Facility</th>
                <th class="text-right">Total Orders</th>
                <th class="text-right">Completed</th>
                <th class="text-right">Open</th>
                <th class="text-right">Total Cost</th>
                <th class="text-right">Avg Completion</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['facilityData'] as $facility)
            <tr>
                <td>{{ $facility['name'] }}</td>
                <td class="text-right font-bold">{{ number_format($facility['total_orders']) }}</td>
                <td class="text-right text-success">{{ number_format($facility['completed']) }}</td>
                <td class="text-right text-warning">{{ number_format($facility['open']) }}</td>
                <td class="text-right">₦{{ $facility['total_cost'] }}</td>
                <td class="text-right">{{ $facility['avg_completion_hours'] }}h</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</x-exports.pdf.layout>
