<?php

namespace App\Console\Commands;

use App\Models\Device;
use App\Models\DeviceTelemetry;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CleanOldTelemetry extends Command
{
    protected $signature = 'telemetry:clean';
    protected $description = 'Delete telemetry records older than device retention period';

    public function handle()
    {
        $devices = Device::whereNotNull('retention_days')->get();
        $totalDeleted = 0;

        foreach ($devices as $device) {
            $cutoff = Carbon::now()->subDays($device->retention_days);
            
            $deleted = DeviceTelemetry::where('device_id', $device->id)
                ->where('created_at', '<', $cutoff)
                ->delete();

            if ($deleted > 0) {
                $this->info("Device {$device->device_name}: deleted {$deleted} records older than {$device->retention_days} days");
                $totalDeleted += $deleted;
            }
        }

        $this->info("Total deleted: {$totalDeleted} records");
        
        return Command::SUCCESS;
    }
}