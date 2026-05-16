@extends('statamic::layout')

@section('title', 'Activity logs')

@section('content')
    <header class="mb-6">
        <h1 class="text-2xl font-bold">User activity logs</h1>
        <p class="text-gray-600 text-sm mt-1">Read-only audit trail for login, register, and logout events.</p>
    </header>

    <div class="card p-0 overflow-hidden">
        <table class="data-table w-full">
            <thead>
                <tr>
                    <th>When</th>
                    <th>Event</th>
                    <th>User</th>
                    <th>IP</th>
                    <th>User agent</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                    <tr>
                        <td>{{ $log->created_at?->toDateTimeString() }}</td>
                        <td><code>{{ $log->event }}</code></td>
                        <td>
                            @if ($log->user)
                                {{ $log->user->name }}<br>
                                <span class="text-xs text-gray-500">{{ $log->user->email }}</span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td>{{ $log->ip_address ?? '—' }}</td>
                        <td class="text-xs max-w-md truncate" title="{{ $log->user_agent }}">{{ $log->user_agent ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-gray-500 py-8">No activity recorded yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
