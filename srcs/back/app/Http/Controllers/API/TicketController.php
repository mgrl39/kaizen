<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use PDF; // Requiere instalar laravel-dompdf

class TicketController extends Controller
{
    public function download($token)
    {
        $ticket = Ticket::where('download_token', $token)
            ->with(['booking.function.movie', 'booking.seats'])
            ->firstOrFail();

        if ($ticket->expires_at->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'Este ticket ha expirado'
            ], 400);
        }

        $pdf = PDF::loadView('tickets.show', [
            'ticket' => $ticket
        ]);

        return $pdf->download("ticket-{$ticket->ticket_code}.pdf");
    }
} 