<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ServerController extends Controller
{
    /**
     * Display server information: uptime, memory and disk.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $osFamily = defined('PHP_OS_FAMILY') ? PHP_OS_FAMILY : php_uname('s');

        if (strtolower($osFamily) === 'windows') {
            // Windows fallbacks (may require appropriate permissions/tools)
            $uptime = trim(@shell_exec('systeminfo 2>&1'));
            $memory = trim(@shell_exec('wmic OS get FreePhysicalMemory,TotalVisibleMemorySize /Value 2>&1'));
            $disk = trim(@shell_exec('wmic logicaldisk get size,freespace,caption /Format:List 2>&1'));
        } else {
            // Common Linux/Unix commands
            $uptime = trim(@shell_exec('uptime 2>&1'));
            $memory = trim(@shell_exec('free -m 2>&1'));
            $disk = trim(@shell_exec('df -h 2>&1'));
        }

        $data = [
            'os' => $osFamily,
            'uptime' => $uptime ?: 'Not available or command disabled',
            'memory' => $memory ?: 'Not available or command disabled',
            'disk' => $disk ?: 'Not available or command disabled',
        ];

        return view('server.server', compact('data'));
    }
}
