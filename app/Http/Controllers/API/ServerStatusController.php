<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ServerStatusController extends Controller
{
    public function status()
    {
        $memory = $this->getMemoryUsage();
        $cpu = $this->getCpuUsage();
        $disk = $this->getDiskSpace();
    
        $status = 'healthy';
    
        $stats = [
            'server_time' => date('Y-m-d H:i:s'),
            'php_version' => phpversion(),
            'laravel_version' => app()->version(),
            'memory_usage' => $memory,
            'cpu_usage' => $cpu,
            'disk_space' => $disk,
            'database_stats' => $this->getDatabaseStats(),
            // TODO: Quitarle el hardcodeo de healthy
            'status' => $status
        ];
        
        return response()->json(['data' => $stats]);
    }    
    
    private function getMemoryUsage()
    {
        $free = shell_exec('free -m');
        $mem = explode("\n", $free)[1];
        $mem = preg_split('/\s+/', $mem);
        
        return [
            'total' => $mem[1] . 'MB',
            'used' => $mem[2] . 'MB',
            'free' => $mem[3] . 'MB'
        ];
    }
    
    private function getCpuUsage()
    {
        $load = sys_getloadavg();
        return [
            '1min' => $load[0],
            '5min' => $load[1], 
            '15min' => $load[2]
        ];
    }
    
    private function getDiskSpace()
    {
        $disk_total = disk_total_space('/');
        $disk_free = disk_free_space('/');
        
        return [
            'total' => $this->formatBytes($disk_total),
            'free' => $this->formatBytes($disk_free),
            'used' => $this->formatBytes($disk_total - $disk_free),
            'percent_used' => round(($disk_total - $disk_free) / $disk_total * 100, 2) . '%'
        ];
    }
    
    private function getDatabaseStats()
    {
        $tables = \Illuminate\Support\Facades\DB::select('SHOW TABLE STATUS');
        $size = 0;
        $rowCount = 0;
        
        foreach ($tables as $table) {
            $size += $table->Data_length + $table->Index_length;
            $rowCount += $table->Rows;
        }
        
        return [
            'size' => $this->formatBytes($size),
            'tables' => count($tables),
            'rows' => $rowCount
        ];
    }
    
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
} 