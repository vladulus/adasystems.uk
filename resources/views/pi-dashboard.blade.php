<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pi Dashboard - ADA Systems</title>
    <link href="https://fonts.bunny.net/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .header {
            background: white;
            padding: 20px 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 24px;
        }

        .header-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            display: inline-block;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5568d3;
        }

        .btn-secondary {
            background: #e74c3c;
            color: white;
            border: none;
            cursor: pointer;
        }

        .btn-secondary:hover {
            background: #c0392b;
        }

        .devices-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
        }

        .device-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .device-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 12px rgba(0,0,0,0.2);
        }

        .device-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .device-name {
            font-size: 20px;
            font-weight: 700;
            color: #333;
        }

        .device-status {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-online {
            background: #d1fae5;
            color: #065f46;
        }

        .status-offline {
            background: #fee2e2;
            color: #991b1b;
        }

        .device-info {
            margin: 15px 0;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .info-label {
            color: #666;
            font-size: 14px;
        }

        .info-value {
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }

        .device-actions {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }

        .btn-access {
            flex: 1;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            text-align: center;
            text-decoration: none;
            display: block;
        }

        .btn-access:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(102, 126, 234, 0.4);
        }

        .empty-state {
            background: white;
            border-radius: 10px;
            padding: 60px 20px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .empty-state-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }

        .empty-state h2 {
            color: #333;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: #666;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 Pi Dashboard</h1>
            <div class="header-actions">
                <span>{{ auth()->user()->name }}</span>
                <a href="{{ route('dashboard') }}" class="btn btn-primary">← Back</a>
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-secondary">Logout</button>
                </form>
            </div>
        </div>

        @if($devices->count() > 0)
        <div class="devices-grid">
            @foreach($devices as $device)
            <div class="device-card">
                <div class="device-header">
                    <div class="device-name">{{ $device->device_name }}</div>
                    <span class="device-status {{ $device->isOnline() ? 'status-online' : 'status-offline' }}">
                        {{ $device->isOnline() ? 'Online' : 'Offline' }}
                    </span>
                </div>

                <div class="device-info">
                    @if($device->vehicle)
                    <div class="info-row">
                        <span class="info-label">Vehicle:</span>
                        <span class="info-value">{{ $device->vehicle->registration_number }}</span>
                    </div>
                    @endif

                    @if($device->vehicle && $device->vehicle->owner)
                    <div class="info-row">
                        <span class="info-label">Owner:</span>
                        <span class="info-value">{{ $device->vehicle->owner->name }}</span>
                    </div>
                    @endif

                    <div class="info-row">
                        <span class="info-label">Status:</span>
                        <span class="info-value">{{ ucfirst($device->status) }}</span>
                    </div>

                    @if($device->last_online)
                    <div class="info-row">
                        <span class="info-label">Last Seen:</span>
                        <span class="info-value">{{ $device->last_online->diffForHumans() }}</span>
                    </div>
                    @endif

                    @if($device->ip_address)
                    <div class="info-row">
                        <span class="info-label">IP Address:</span>
                        <span class="info-value">{{ $device->ip_address }}</span>
                    </div>
                    @endif
                </div>

                <div class="device-actions">
                    <a href="http://{{ $device->ip_address }}:8000?token=xxx" 
                       class="btn-access" 
                       target="_blank">
                        Access Device Dashboard →
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-icon">📊</div>
            <h2>No Devices Available</h2>
            <p>You don't have any Pi devices assigned to you yet.</p>
            @if(auth()->user()->hasRole(['super-admin', 'admin']))
            <a href="{{ route('management.devices.create') }}" class="btn btn-primary">Add Your First Device</a>
            @endif
        </div>
        @endif
    </div>
</body>
</html>
