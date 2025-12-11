@extends('layouts.app-mobile')

@section('content')
<div class="pi-dashboard-mobile">
    {{-- Header --}}
    <div class="dashboard-header">
        <a href="{{ route('app.home') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1>Pi Dashboard</h1>
        <button class="btn-refresh" onclick="location.reload()">
            <i class="fas fa-sync-alt"></i>
        </button>
    </div>

    {{-- Status Summary --}}
    <div class="status-summary">
        @php
            $onlineCount = $devices->filter(fn($d) => $d->isOnline())->count();
            $offlineCount = $devices->count() - $onlineCount;
        @endphp
        <div class="status-badge online">
            <i class="fas fa-circle"></i>
            <span>{{ $onlineCount }} Online</span>
        </div>
        <div class="status-badge offline">
            <i class="fas fa-circle"></i>
            <span>{{ $offlineCount }} Offline</span>
        </div>
    </div>

    {{-- Device List --}}
    <div class="device-list">
        @forelse($devices as $device)
        <a href="{{ route('ada-pi.devices.show', $device) }}" class="device-card {{ $device->isOnline() ? 'online' : 'offline' }}">
            <div class="device-status">
                <span class="status-dot {{ $device->isOnline() ? 'online' : 'offline' }}"></span>
            </div>
            
            <div class="device-info">
                <div class="device-name">{{ $device->name ?: $device->serial_number }}</div>
                <div class="device-serial">{{ $device->serial_number }}</div>
                
                @if($device->vehicle)
                <div class="device-vehicle">
                    <i class="fas fa-truck"></i>
                    <span>{{ $device->vehicle->registration }}</span>
                </div>
                @endif
            </div>

            <div class="device-meta">
                @if($device->isOnline())
                    <span class="last-seen online">
                        <i class="fas fa-signal"></i>
                        Live
                    </span>
                @else
                    <span class="last-seen offline">
                        <i class="fas fa-clock"></i>
                        {{ $device->last_seen_at ? $device->last_seen_at->diffForHumans(null, true, true) : 'Never' }}
                    </span>
                @endif
            </div>

            <div class="device-arrow">
                <i class="fas fa-chevron-right"></i>
            </div>
        </a>
        @empty
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-microchip"></i>
            </div>
            <h3>No devices</h3>
            <p>No devices have been assigned to you yet.</p>
        </div>
        @endforelse
    </div>
</div>

<style>
    .pi-dashboard-mobile {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%);
    }

    /* HEADER */
    .dashboard-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        padding-top: calc(16px + var(--safe-top, 0px));
        background: var(--bg-white);
        border-bottom: 1px solid var(--border);
        position: sticky;
        top: 0;
        z-index: 100;
    }

    .dashboard-header h1 {
        font-size: 18px;
        font-weight: 600;
        color: var(--text-dark);
    }

    .btn-back, .btn-refresh {
        width: 40px;
        height: 40px;
        border: none;
        background: var(--bg-light);
        border-radius: 12px;
        color: var(--text-gray);
        font-size: 16px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.2s;
    }

    .btn-back:active, .btn-refresh:active {
        background: var(--border);
        transform: scale(0.95);
    }

    /* STATUS SUMMARY */
    .status-summary {
        display: flex;
        gap: 12px;
        padding: 16px 20px;
    }

    .status-badge {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        border-radius: 100px;
        font-size: 13px;
        font-weight: 500;
    }

    .status-badge.online {
        background: #d1fae5;
        color: #059669;
    }

    .status-badge.offline {
        background: #fee2e2;
        color: #dc2626;
    }

    .status-badge i {
        font-size: 8px;
    }

    /* DEVICE LIST */
    .device-list {
        flex: 1;
        padding: 0 20px 20px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .device-card {
        display: flex;
        align-items: center;
        gap: 14px;
        background: var(--bg-white);
        border-radius: 16px;
        padding: 16px;
        text-decoration: none;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        transition: all 0.2s;
        border: 2px solid transparent;
    }

    .device-card:active {
        transform: scale(0.98);
        border-color: var(--primary-light);
    }

    .device-card.online {
        border-left: 4px solid #10b981;
    }

    .device-card.offline {
        border-left: 4px solid #ef4444;
    }

    .device-status {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .status-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
    }

    .status-dot.online {
        background: #10b981;
        box-shadow: 0 0 8px rgba(16, 185, 129, 0.5);
        animation: pulse-green 2s infinite;
    }

    .status-dot.offline {
        background: #ef4444;
    }

    @keyframes pulse-green {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    .device-info {
        flex: 1;
        min-width: 0;
    }

    .device-name {
        font-size: 16px;
        font-weight: 600;
        color: var(--text-dark);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .device-serial {
        font-size: 12px;
        color: var(--text-light);
        font-family: monospace;
        margin-top: 2px;
    }

    .device-vehicle {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 8px;
        padding: 4px 10px;
        background: #f1f5f9;
        border-radius: 6px;
        font-size: 12px;
        color: var(--text-gray);
    }

    .device-vehicle i {
        font-size: 10px;
    }

    .device-meta {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
    }

    .last-seen {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        font-weight: 500;
        padding: 6px 10px;
        border-radius: 8px;
    }

    .last-seen.online {
        background: #d1fae5;
        color: #059669;
    }

    .last-seen.offline {
        background: #f1f5f9;
        color: var(--text-gray);
    }

    .device-arrow {
        color: var(--text-light);
        font-size: 14px;
    }

    /* EMPTY STATE */
    .empty-state {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 60px 20px;
    }

    .empty-icon {
        width: 80px;
        height: 80px;
        background: var(--bg-white);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        color: var(--text-light);
        margin-bottom: 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
    }

    .empty-state h3 {
        font-size: 18px;
        color: var(--text-dark);
        margin-bottom: 8px;
    }

    .empty-state p {
        font-size: 14px;
        color: var(--text-gray);
    }

    /* PULL TO REFRESH HINT */
    .device-list::before {
        content: '';
        display: block;
        height: 0;
    }
</style>
@endsection
