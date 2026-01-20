<x-exports.pdf.layout title="Work Orders" dateRange="All Time" :generatedAt="$generatedAt" :clientName="$clientName">
    <table>
        <thead>
            <tr>
                <th>Serial #</th>
                <th>Title</th>
                <th>Facility</th>
                <th>Status</th>
                <th>Priority</th>
                <th>Reported By</th>
                <th>Assigned To</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            @foreach($workOrders as $wo)
            <tr>
                <td>{{ $wo->serial ?? $wo->id }}</td>
                <td>{{ $wo->title }}</td>
                <td>{{ $wo->facility?->name ?? 'N/A' }}</td>
                <td>
                    @if($wo->status === 'completed' || $wo->status === 'closed')
                        <span class="badge badge-success">{{ ucfirst($wo->status) }}</span>
                    @elseif($wo->status === 'in_progress')
                        <span class="badge badge-warning">In Progress</span>
                    @else
                        <span class="badge badge-info">{{ ucfirst(str_replace('_', ' ', $wo->status)) }}</span>
                    @endif
                </td>
                <td>
                    @if($wo->priority === 'critical')
                        <span class="badge badge-danger">Critical</span>
                    @elseif($wo->priority === 'high')
                        <span class="badge badge-warning">High</span>
                    @else
                        <span class="badge badge-info">{{ ucfirst($wo->priority) }}</span>
                    @endif
                </td>
                <td>{{ $wo->reportedBy?->name ?? 'N/A' }}</td>
                <td>{{ $wo->assignedTo?->name ?? 'Unassigned' }}</td>
                <td>{{ $wo->created_at?->format('M d, Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</x-exports.pdf.layout>
