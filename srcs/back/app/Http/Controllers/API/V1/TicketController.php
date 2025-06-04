<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Support\Facades\Storage;
use PDF;

class TicketController extends Controller
{
    public function download($uuid)
    {
        $ticket = Ticket::where('uuid', $uuid)->firstOrFail();
        
        if (!Storage::exists('public/' . $ticket->pdf_path)) {
            // Si el PDF no existe, lo regeneramos
            $pdf = PDF::loadView('tickets.show', [
                'booking' => $ticket->booking->load('function.movie', 'function.room', 'seats'),
                'qr_path' => storage_path("app/public/{$ticket->qr_path}"),
                'seats_info' => $ticket->booking->seats->map(function($seat) {
                    return "Fila {$seat->row} - Asiento {$seat->number}";
                })->join(', ')
            ]);

            Storage::put('public/' . $ticket->pdf_path, $pdf->output());
        }

        return Storage::download('public/' . $ticket->pdf_path, "ticket-{$ticket->booking->booking_code}.pdf");
    }
} 