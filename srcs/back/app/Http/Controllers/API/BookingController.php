<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Functions;
use App\Models\Seat;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function __construct()
    {
        // Solo requerir autenticación para ver y cancelar reservas
        $this->middleware('auth:sanctum')->only(['index', 'show', 'cancel']);
    }

    /**
     * Crear una nueva reserva
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'function_id' => 'required|exists:functions,id',
                'seats' => 'required|array|min:1',
                'seats.*' => 'required|integer|exists:seats,id',
                'buyer' => 'required|array',
                'buyer.name' => 'required|string|max:255',
                'buyer.email' => 'required|email|max:255',
                'buyer.phone' => 'nullable|string|max:20'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Verificar que la función existe y está disponible
            $function = Functions::findOrFail($request->function_id);
            
            // Verificar que los asientos están disponibles
            $seats = Seat::whereIn('id', $request->seats)
                ->where('function_id', $function->id)
                ->where('status', Seat::STATUS_AVAILABLE)
                ->get();

            if ($seats->count() !== count($request->seats)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Algunos asientos seleccionados no están disponibles'
                ], 400);
            }

            // Calcular precio total
            $totalPrice = $seats->sum('price');

            DB::beginTransaction();

            try {
                // Crear la reserva (eliminados los campos customer_*)
                $booking = Booking::create([
                    'user_id' => Auth::id(), // Será null si no hay usuario autenticado
                    'function_id' => $function->id,
                    'uuid' => Str::uuid(),
                    'booking_code' => Booking::generateBookingCode(),
                    'total_price' => $totalPrice,
                    'seats' => $request->seats,
                    'status' => Booking::STATUS_PENDING,
                    'payment_status' => Booking::PAYMENT_STATUS_PENDING,
                    'payment_method' => 'pending',
                    'buyer_name' => $request->buyer['name'],
                    'buyer_email' => $request->buyer['email'],
                    'buyer_phone' => $request->buyer['phone'] ?? null
                ]);

                // Asociar asientos
                foreach ($seats as $seat) {
                    $booking->seats()->attach($seat->id, [
                        'price' => $seat->price
                    ]);
                    
                    // Actualizar estado del asiento
                    $seat->update(['status' => Seat::STATUS_RESERVED]);
                }

                // Generar el ticket
                $ticketData = [
                    'booking_id' => $booking->id,
                    'ticket_code' => uniqid('TK-'),
                    'buyer_email' => $request->buyer['email'],
                    'download_token' => bin2hex(random_bytes(32)),
                    'expires_at' => now()->addYears(1)
                ];

                $ticket = $booking->ticket()->create($ticketData);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Reserva creada exitosamente',
                    'data' => [
                        'booking' => $booking->load('seats', 'function.movie'),
                        'ticket' => [
                            'download_url' => url("/api/v1/tickets/{$ticket->download_token}")
                        ]
                    ]
                ], 201);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la reserva',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Confirmar una reserva después del pago
     */
    public function confirm(Request $request, Booking $booking)
    {
        try {
            // Verificar que la reserva pertenece al usuario
            if ($booking->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autorizado'
                ], 403);
            }

            if ($booking->status !== Booking::STATUS_PENDING) {
                return response()->json([
                    'success' => false,
                    'message' => 'La reserva no está en estado pendiente'
                ], 400);
            }

            $booking->update([
                'status' => Booking::STATUS_CONFIRMED,
                'payment_status' => Booking::PAYMENT_STATUS_COMPLETED
            ]);

            // Actualizar estado de los asientos
            foreach ($booking->seats as $seat) {
                $seat->update(['status' => Seat::STATUS_OCCUPIED]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Reserva confirmada exitosamente',
                'data' => [
                    'booking' => $booking->load('seats', 'function.movie')
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al confirmar la reserva',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancelar una reserva
     */
    public function cancel(Booking $booking)
    {
        try {
            // Verificar que la reserva pertenece al usuario
            if ($booking->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autorizado'
                ], 403);
            }

            if (!in_array($booking->status, [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])) {
                return response()->json([
                    'success' => false,
                    'message' => 'La reserva no puede ser cancelada'
                ], 400);
            }

            DB::beginTransaction();

            try {
                // Actualizar estado de la reserva
                $booking->update([
                    'status' => Booking::STATUS_CANCELLED,
                    'payment_status' => Booking::PAYMENT_STATUS_REFUNDED
                ]);

                // Liberar asientos
                foreach ($booking->seats as $seat) {
                    $seat->update(['status' => Seat::STATUS_AVAILABLE]);
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Reserva cancelada exitosamente'
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cancelar la reserva',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener las reservas del usuario autenticado
     */
    public function index()
    {
        try {
            $bookings = Auth::user()
                ->bookings()
                ->with(['function.movie', 'function.room.cinema', 'seats'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'bookings' => $bookings
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las reservas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener una reserva específica
     */
    public function show(Booking $booking)
    {
        try {
            // Verificar que la reserva pertenece al usuario
            if ($booking->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autorizado'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'booking' => $booking->load(['function.movie', 'function.room.cinema', 'seats'])
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la reserva',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Nuevo método para buscar tickets por email
    public function findTickets(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $tickets = Ticket::where('buyer_email', $request->email)
            ->with(['booking.function.movie', 'booking.seats'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tickets->map(function($ticket) {
                return [
                    'ticket_code' => $ticket->ticket_code,
                    'movie' => $ticket->booking->function->movie->title,
                    'date' => $ticket->booking->function->date,
                    'time' => $ticket->booking->function->time,
                    'seats' => $ticket->booking->seats->map(function($seat) {
                        return "Fila {$seat->row} - Asiento {$seat->number}";
                    }),
                    'download_url' => url("/api/v1/tickets/{$ticket->download_token}")
                ];
            })
        ]);
    }
} 