<x-exports.pdf.layout :title="$title" :dateRange="$dateRange" :generatedAt="$generatedAt" :clientName="$clientName">
    <!-- Summary Metrics -->
    <div class="summary-grid">
        <div class="summary-row">
            <div class="metric-box">
                <div class="metric-value">{{ number_format($data['total']) }}</div>
                <div class="metric-label">Total Work Orders</div>
            </div>
            <div class="metric-box">
                <div class="metric-value" style="color: {{ $data['overallComplianceRate'] >= 90 ? '#059669' : ($data['overallComplianceRate'] >= 70 ? '#d97706' : '#dc2626') }}">{{ $data['overallComplianceRate'] }}%</div>
                <div class="metric-label">Overall Compliance</div>
            </div>
            <div class="metric-box">
                <div class="metric-value">{{ $data['responseComplianceRate'] }}%</div>
                <div class="metric-label">Response Compliance</div>
            </div>
            <div class="metric-box">
                <div class="metric-value">{{ $data['resolutionComplianceRate'] }}%</div>
                <div class="metric-label">Resolution Compliance</div>
            </div>
        </div>
    </div>

    <!-- Breach Summary -->
    <div class="section-title">SLA Breach Summary</div>
    <table>
        <thead>
            <tr>
                <th>Metric</th>
                <th class="text-right">Count</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Response Breaches</td>
                <td class="text-right font-bold {{ $data['responseBreached'] > 0 ? 'text-danger' : '' }}">{{ number_format($data['responseBreached']) }}</td>
            </tr>
            <tr>
                <td>Resolution Breaches</td>
                <td class="text-right font-bold {{ $data['resolutionBreached'] > 0 ? 'text-danger' : '' }}">{{ number_format($data['resolutionBreached']) }}</td>
            </tr>
            <tr>
                <td>Currently Overdue</td>
                <td class="text-right font-bold {{ $data['currentlyOverdue'] > 0 ? 'text-warning' : '' }}">{{ number_format($data['currentlyOverdue']) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- By Priority Table -->
    <div class="section-title">Compliance by Priority</div>
    <table>
        <thead>
            <tr>
                <th>Priority</th>
                <th class="text-right">Total</th>
                <th class="text-right">Response Breached</th>
                <th class="text-right">Resolution Breached</th>
                <th class="text-right">Compliance Rate</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['byPriority'] as $item)
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
                    <td class="text-right font-bold">{{ number_format($item['total']) }}</td>
                    <td class="text-right {{ $item['response_breached'] > 0 ? 'text-danger' : '' }}">{{ number_format($item['response_breached']) }}</td>
                    <td class="text-right {{ $item['resolution_breached'] > 0 ? 'text-danger' : '' }}">{{ number_format($item['resolution_breached']) }}</td>
                    <td class="text-right">
                        @if($item['compliance_rate'] >= 90)
                            <span class="badge badge-success">{{ $item['compliance_rate'] }}%</span>
                        @elseif($item['compliance_rate'] >= 70)
                            <span class="badge badge-warning">{{ $item['compliance_rate'] }}%</span>
                        @else
                            <span class="badge badge-danger">{{ $item['compliance_rate'] }}%</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-exports.pdf.layout>
