<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ticket - {{ $booking->booking_code }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .ticket {
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #000;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .movie-title {
            font-size: 28px;
            font-weight: bold;
            margin: 20px 0;
        }
        .info-row {
            margin: 10px 0;
        }
        .label {
            font-weight: bold;
            margin-right: 10px;
        }
        .qr-code {
            text-align: center;
            margin: 30px 0;
        }
        .qr-code img {
            max-width: 200px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="ticket">
        <div class="header">
            <div class="logo">KAIZEN CINEMA</div>
            <div>Ticket de entrada</div>
        </div>

        <div class="movie-title">
            {{ $booking->function->movie->title }}
        </div>

        <div class="info-row">
            <span class="label">Código de reserva:</span>
            <span>{{ $booking->booking_code }}</span>
        </div>

        <div class="info-row">
            <span class="label">Fecha:</span>
            <span>{{ \Carbon\Carbon::parse($booking->function->date)->format('d/m/Y') }}</span>
        </div>

        <div class="info-row">
            <span class="label">Hora:</span>
            <span>{{ $booking->function->time }}</span>
        </div>

        <div class="info-row">
            <span class="label">Sala:</span>
            <span>{{ $booking->function->room->name }}</span>
        </div>

        <div class="info-row">
            <span class="label">Asientos:</span>
            <span>{{ $seats_info }}</span>
        </div>

        <div class="info-row">
            <span class="label">Precio total:</span>
            <span>{{ number_format($booking->total_price, 2) }}€</span>
        </div>

        <div class="qr-code">
            <img src="{{ $qr_path }}" alt="QR Code">
        </div>

        <div class="footer">
            <p>Este ticket es válido solo para la fecha y hora indicadas.</p>
            <p>Por favor, preséntelo en la entrada del cine.</p>
        </div>
    </div>
</body>
</html> 