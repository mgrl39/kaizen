<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Functions;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
                'message' => 'Algunos asientos seleccionados no están disponibles',
                'errors' => ['seats' => ['Uno o más asientos no están disponibles']]
            ], 422);
        }

        // Verificar que ningún asiento esté ocupado
        $occupiedSeats = $seats->filter(function ($seat) {
            return $seat->pivot->is_occupied;
        });

        if ($occupiedSeats->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Algunos asientos seleccionados ya están ocupados',
                'errors' => ['seats' => ['Uno o más asientos ya han sido reservados']]
            ], 422);
        }

        // Calcular precio total
        $totalPrice = $seats->sum('price');
        if ($function->is_3d) {
            $totalPrice += count($validated['seats']) * 2; // 2€ extra por asiento en 3D
        }

        // Crear la reserva
        $booking = new Booking();
        $booking->uuid = Str::uuid();
        $booking->user_id = Auth::id(); // Será null si el usuario no está autenticado
        $booking->function_id = $validated['function_id'];
        $booking->total_price = $totalPrice;
        $booking->booking_code = Booking::generateBookingCode();
        $booking->buyer_name = $validated['buyer']['name'];
        $booking->buyer_email = $validated['buyer']['email'];
        $booking->buyer_phone = $validated['buyer']['phone'];
        $booking->status = 'confirmed';
        $booking->payment_status = 'completed';
        $booking->payment_method = 'card';
        $booking->save();

        // Asociar los asientos a la reserva
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
            'seats' => $booking->seats,
            'total' => number_format($booking->total_price, 2) . '€'
        ];

        Storage::makeDirectory('public/qrcodes');
        
        $qrCode = QrCode::format('png')
            ->size(300)
            ->errorCorrection('H')
            ->margin(1)
            ->generate(json_encode($qrData));
        
        $qrPath = "qrcodes/{$booking->uuid}.png";
        Storage::put("public/" . $qrPath, $qrCode);

        return response()->json([
            'success' => true,
            'message' => '¡Reserva completada con éxito!',
            'data' => [
                'booking' => $booking->load('function.movie', 'function.room', 'seats'),
                'qr_url' => asset("storage/" . $qrPath),
                'ticket_url' => route('bookings.ticket', ['uuid' => $booking->uuid])
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
        $booking = Booking::with(['function.movie', 'function.room'])
            ->where('uuid', $uuid)
            ->firstOrFail();
        
        // Verificar que el QR existe, si no, regenerarlo
        $qrPath = "public/qrcodes/{$booking->uuid}.png";
        if (!Storage::exists($qrPath)) {
            $qrData = [
                'booking_id' => $booking->uuid,
                'booking_code' => $booking->booking_code,
                'movie' => $booking->function->movie->title,
                'date' => $booking->function->date,
                'time' => $booking->function->time,
                'room' => $booking->function->room->name,
                'seats' => $booking->seats,
                'total' => number_format($booking->total_price, 2) . '€'
            ];

            Storage::makeDirectory('public/qrcodes');
            
            $qrCode = QrCode::format('png')
                ->size(300)
                ->errorCorrection('H')
                ->margin(1)
                ->generate(json_encode($qrData));
            
            Storage::put($qrPath, $qrCode);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'booking' => $booking,
                'qr_url' => asset("storage/qrcodes/{$booking->uuid}.png")
            ]
        ]);
    }
} 