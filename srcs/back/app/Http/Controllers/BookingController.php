<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Functions;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PDF;
use App\Models\Ticket;

class BookingController extends Controller
{
    public function __construct()
    {
        // Solo aplicar autenticación a las rutas que lo requieren
        $this->middleware('api.auth')->only(['userHistory', 'cancel']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'function_id' => 'required|exists:functions,id',
            'seats' => 'required|array',
            'buyer' => 'required|array',
            'buyer.name' => 'required|string',
            'buyer.email' => 'required|email',
            'buyer.phone' => 'required|string'
        ]);

        // Obtener la función y verificar asientos disponibles
        $function = Functions::with(['room', 'seats'])->findOrFail($validated['function_id']);
        
        // Verificar que los asientos existen y están disponibles
        $seats = $function->seats()->whereIn('id', $validated['seats'])->get();
        
        if ($seats->count() !== count($validated['seats'])) {
            return response()->json([
                'success' => false,
                'message' => 'Algunos asientos seleccionados no están disponibles'
            ], 422);
        }

        // Verificar que ningún asiento esté ocupado
        $occupiedSeats = $seats->filter(function ($seat) {
            return $seat->pivot->is_occupied;
        });

        if ($occupiedSeats->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Algunos asientos seleccionados ya están ocupados'
            ], 422);
        }

        // Calcular precio total
        $totalPrice = $seats->sum('price');
        if ($function->is_3d) {
            $totalPrice += count($validated['seats']) * 2;
        }

        // Crear la reserva
        $booking = new Booking();
        $booking->fill([
            'uuid' => Str::uuid(),
            'user_id' => Auth::id(),
            'function_id' => $validated['function_id'],
            'total_price' => $totalPrice,
            'booking_code' => Booking::generateBookingCode(),
            'buyer_name' => $validated['buyer']['name'],
            'buyer_email' => $validated['buyer']['email'],
            'buyer_phone' => $validated['buyer']['phone']
        ]);
        $booking->save();

        // Asociar los asientos
        $booking->seats()->attach($validated['seats'], [
            'price' => $function->is_3d ? $function->room->price + 2 : $function->room->price
        ]);

        // Marcar los asientos como ocupados
        $function->seats()->whereIn('id', $validated['seats'])->update(['is_occupied' => true]);

        // Generar QR
        $qrData = [
            'booking_id' => $booking->uuid,
            'booking_code' => $booking->booking_code,
            'movie' => $function->movie->title,
            'date' => $function->date,
            'time' => $function->time,
            'room' => $function->room->name,
            'seats' => $booking->seats->map(function($seat) {
                return "Fila {$seat->row} - Asiento {$seat->number}";
            })->join(', ')
        ];

        Storage::makeDirectory('public/qrcodes');
        
        $qrCode = QrCode::format('png')
            ->size(300)
            ->errorCorrection('H')
            ->margin(1)
            ->generate(json_encode($qrData));
        
        $qrPath = "qrcodes/{$booking->uuid}.png";
        Storage::put("public/" . $qrPath, $qrCode);

        // Generar PDF
        $pdf = PDF::loadView('tickets.show', [
            'booking' => $booking->load('function.movie', 'function.room', 'seats'),
            'qr_path' => storage_path("app/public/{$qrPath}"),
            'seats_info' => $booking->seats->map(function($seat) {
                return "Fila {$seat->row} - Asiento {$seat->number}";
            })->join(', ')
        ]);

        $pdfPath = "tickets/{$booking->uuid}.pdf";
        Storage::makeDirectory('public/tickets');
        Storage::put("public/" . $pdfPath, $pdf->output());

        // Crear el ticket
        $ticket = new Ticket();
        $ticket->booking_id = $booking->id;
        $ticket->uuid = Str::uuid();
        $ticket->qr_path = $qrPath;
        $ticket->pdf_path = $pdfPath;
        $ticket->save();

        return response()->json([
            'success' => true,
            'message' => '¡Reserva completada con éxito!',
            'data' => [
                'booking' => $booking->load('function.movie', 'function.room', 'seats'),
                'qr_url' => asset("storage/" . $qrPath),
                'ticket_url' => route('tickets.download', ['uuid' => $ticket->uuid])
            ]
        ]);
    }

    public function show($uuid)
    {
        $booking = Booking::with(['function.movie', 'function.room'])
            ->where('uuid', $uuid)
            ->firstOrFail();
        
        return response()->json([
            'success' => true,
            'data' => [
                'booking' => $booking,
                'qr_url' => asset("storage/qrcodes/{$booking->uuid}.png"),
                'ticket_url' => route('bookings.ticket', ['uuid' => $booking->uuid])
            ]
        ]);
    }

    public function ticket($uuid)
    {
        $booking = Booking::with(['function.movie', 'function.room', 'seats'])
            ->where('uuid', $uuid)
            ->firstOrFail();
        
        // Generar QR si no existe
        $qrPath = "public/qrcodes/{$booking->uuid}.png";
        if (!Storage::exists($qrPath)) {
            $qrData = [
                'booking_id' => $booking->uuid,
                'booking_code' => $booking->booking_code,
                'movie' => $booking->function->movie->title,
                'date' => $booking->function->date,
                'time' => $booking->function->time,
                'room' => $booking->function->room->name,
                'seats' => $booking->seats->map(function($seat) {
                    return "Fila {$seat->row} - Asiento {$seat->number}";
                })->join(', ')
            ];

            Storage::makeDirectory('public/qrcodes');
            
            $qrCode = QrCode::format('png')
                ->size(300)
                ->errorCorrection('H')
                ->margin(1)
                ->generate(json_encode($qrData));
            
            Storage::put($qrPath, $qrCode);
        }

        // Generar PDF
        $pdf = PDF::loadView('tickets.show', [
            'booking' => $booking,
            'qr_path' => storage_path("app/{$qrPath}"),
            'seats_info' => $booking->seats->map(function($seat) {
                return "Fila {$seat->row} - Asiento {$seat->number}";
            })->join(', ')
        ]);

        return $pdf->download("ticket-{$booking->booking_code}.pdf");
    }
} 