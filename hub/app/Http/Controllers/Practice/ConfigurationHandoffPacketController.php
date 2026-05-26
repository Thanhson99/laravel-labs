<?php

declare(strict_types=1);

namespace App\Http\Controllers\Practice;

use App\Http\Controllers\Controller;
use App\Services\Practice\ConfigurationHandoffPacketService;
use Illuminate\Contracts\View\View;

final class ConfigurationHandoffPacketController extends Controller
{
    /**
     * Show the final handoff packet for configuration practice.
     */
    public function __invoke(ConfigurationHandoffPacketService $packet): View
    {
        return view('practice.configuration-handoff-packet', [
            'packet' => $packet->build(),
        ]);
    }
}
