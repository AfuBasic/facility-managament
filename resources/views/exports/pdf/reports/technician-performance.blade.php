<x-exports.pdf.layout :title="$title" :dateRange="$dateRange" :generatedAt="$generatedAt" :clientName="$clientName">
    <!-- Summary Metrics -->
    <div class="summary-grid">
        <div class="summary-row">
            <div class="metric-box">
                <div class="metric-value">{{ number_format($data['totalTechnicians']) }}</div>
                <div class="metric-label">Active Technicians</div>
            </div>
            <div class="metric-box">
                <div class="metric-value">{{ number_format($data['totalAssigned']) }}</div>
                <div class="metric-label">Total Assigned</div>
            </div>
            <div class="metric-box">
                <div class="metric-value">{{ $data['avgCompletionRate'] }}%</div>
                <div class="metric-label">Avg Completion Rate</div>
            </div>
            <div class="metric-box">
                <div class="metric-value">{{ $data['avgSlaRate'] }}%</div>
                <div class="metric-label">Avg SLA Rate</div>
            </div>
        </div>
    </div>

    <!-- Technician Performance Table -->
    <div class="section-title">Technician Performance Details</div>
    <table>
        <thead>
            <tr>
                <th>Technician</th>
                <th class="text-right">Assigned</th>
                <th class="text-right">Completed</th>
                <th class="text-right">In Progress</th>
                <th class="text-right">Completion Rate</th>
                <th class="text-right">SLA Rate</th>
                <th class="text-right">Avg Time</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['technicians'] as $tech)
                <tr>
                    <td class="font-bold">{{ $tech['name'] }}</td>
                    <td class="text-right">{{ number_format($tech['total_assigned']) }}</td>
                    <td class="text-right text-success">{{ number_format($tech['completed']) }}</td>
                    <td class="text-right text-warning">{{ number_format($tech['in_progress']) }}</td>
                    <td class="text-right">
                        @if($tech['completion_rate'] >= 80)
                            <span class="badge badge-success">{{ $tech['completion_rate'] }}%</span>
                        @elseif($tech['completion_rate'] >= 50)
                            <span class="badge badge-warning">{{ $tech['completion_rate'] }}%</span>
                        @else
                            <span class="badge badge-danger">{{ $tech['completion_rate'] }}%</span>
                        @endif
                    </td>
                    <td class="text-right">
                        @if($tech['sla_rate'] >= 90)
                            <span class="badge badge-success">{{ $tech['sla_rate'] }}%</span>
                        @elseif($tech['sla_rate'] >= 70)
                            <span class="badge badge-warning">{{ $tech['sla_rate'] }}%</span>
                        @else
                            <span class="badge badge-danger">{{ $tech['sla_rate'] }}%</span>
                        @endif
                    </td>
                    <td class="text-right">{{ $tech['avg_completion_hours'] ? $tech['avg_completion_hours'] . 'h' : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-exports.pdf.layout>
