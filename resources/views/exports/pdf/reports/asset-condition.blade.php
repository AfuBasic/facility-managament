<x-exports.pdf.layout :title="$title" :dateRange="$dateRange" :generatedAt="$generatedAt" :clientName="$clientName">
    <!-- Summary Metrics -->
    <div class="summary-grid">
        <div class="summary-row">
            <div class="metric-box">
                <div class="metric-value">{{ number_format($data['summary']['total']) }}</div>
                <div class="metric-label">Total Assets</div>
            </div>
            <div class="metric-box">
                <div class="metric-value">{{ number_format($data['summary']['available']) }}</div>
                <div class="metric-label">Available</div>
            </div>
            <div class="metric-box">
                <div class="metric-value">{{ number_format($data['summary']['checked_out']) }}</div>
                <div class="metric-label">Checked Out</div>
            </div>
            <div class="metric-box">
                <div class="metric-value">{{ number_format($data['summary']['types_count']) }}</div>
                <div class="metric-label">Asset Types</div>
            </div>
        </div>
    </div>

    <!-- Assets by Type -->
    <div class="section-title">Assets by Type</div>
    <table>
        <thead>
            <tr>
                <th>Type</th>
                <th class="text-right">Count</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['byType'] as $type)
            <tr>
                <td>{{ $type['type'] }}</td>
                <td class="text-right font-bold">{{ number_format($type['count']) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Distribution by Facility -->
    <div class="section-title">Distribution by Facility</div>
    <table>
        <thead>
            <tr>
                <th>Facility</th>
                <th class="text-right">Total</th>
                <th class="text-right">Available</th>
                <th class="text-right">Checked Out</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['byFacility'] as $facility)
            <tr>
                <td>{{ $facility['facility'] }}</td>
                <td class="text-right font-bold">{{ number_format($facility['total']) }}</td>
                <td class="text-right text-success">{{ number_format($facility['available']) }}</td>
                <td class="text-right text-warning">{{ number_format($facility['checked_out']) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Asset Details -->
    <div class="section-title">Asset Details</div>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Serial</th>
                <th>Type</th>
                <th>Facility</th>
                <th>Status</th>
                <th>Assigned To</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['assetList'] as $asset)
            <tr>
                <td>{{ $asset['name'] }}</td>
                <td>{{ $asset['serial'] }}</td>
                <td>{{ $asset['type'] }}</td>
                <td>{{ $asset['facility'] }}</td>
                <td>
                    @if($asset['status'] === 'Available')
                        <span class="badge badge-success">Available</span>
                    @else
                        <span class="badge badge-warning">Checked Out</span>
                    @endif
                </td>
                <td>{{ $asset['assigned_to'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</x-exports.pdf.layout>
