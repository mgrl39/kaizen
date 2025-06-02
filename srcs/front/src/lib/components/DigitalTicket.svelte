<!-- DigitalTicket.svelte -->
<script lang="ts">
    import { onMount } from 'svelte';
    
    export let booking: any;
    let qrCode = '';
    
    onMount(async () => {
        try {
            // Importar dinámicamente el módulo QRCode
            const QRCode = await import('qrcode');
            
            // Generar código QR con los datos de la reserva
            qrCode = await QRCode.toDataURL(JSON.stringify({
                bookingId: booking.id,
                bookingCode: booking.booking_code,
                seats: booking.seats.map((s: any) => s.id),
                functionId: booking.function.id
            }));
        } catch (e) {
            console.error('Error al generar el código QR:', e);
        }
    });

    // Formatear la fecha y hora
    const formatDateTime = (dateString: string) => {
        const date = new Date(dateString);
        return new Intl.DateTimeFormat('es-ES', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        }).format(date);
    };

    // Formatear el precio
    const formatPrice = (price: number) => {
        return new Intl.NumberFormat('es-ES', {
            style: 'currency',
            currency: 'EUR'
        }).format(price);
    };
</script>

<div class="ticket-container">
    <div class="ticket">
        <div class="ticket-header">
            <h1>Kaizen Cinema</h1>
            <p class="booking-code">Código: {booking.booking_code}</p>
        </div>

        <div class="movie-details">
            <h2>{booking.function.movie.title}</h2>
            <p>Sala: {booking.function.room.name}</p>
            <p>Fecha y hora: {formatDateTime(booking.function.datetime)}</p>
        </div>

        <div class="seats-list">
            <h3>Asientos:</h3>
            <ul>
                {#each booking.seats as seat}
                    <li>{seat.name}</li>
                {/each}
            </ul>
        </div>

        {#if qrCode}
            <div class="qr-code">
                <img src={qrCode} alt="Código QR de la entrada" />
            </div>
        {/if}

        <div class="ticket-footer">
            <p class="total">Total pagado: {formatPrice(booking.total_price)}</p>
            <p class="buyer">
                <strong>Comprador:</strong><br>
                {booking.buyer_name}<br>
                {booking.buyer_email}<br>
                {#if booking.buyer_phone}
                    {booking.buyer_phone}
                {/if}
            </p>
            <p class="small">Esta entrada es válida hasta el inicio de la función</p>
        </div>
    </div>

    <div class="actions">
        <button class="download-button" on:click={() => window.print()}>
            Descargar entrada
        </button>
    </div>
</div>

<style>
    .ticket-container {
        max-width: 800px;
        margin: 2rem auto;
        padding: 1rem;
    }

    .ticket {
        background: white;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .ticket-header {
        text-align: center;
        border-bottom: 2px solid #4CAF50;
        padding-bottom: 1rem;
        margin-bottom: 2rem;
    }

    .ticket-header h1 {
        margin: 0;
        color: #4CAF50;
        font-size: 2rem;
    }

    .booking-code {
        font-family: monospace;
        font-size: 1.2rem;
        color: #666;
        margin-top: 0.5rem;
    }

    .movie-details {
        margin-bottom: 2rem;
    }

    .movie-details h2 {
        color: #333;
        margin: 0 0 1rem 0;
        font-size: 1.5rem;
    }

    .movie-details p {
        margin: 0.5rem 0;
        color: #666;
    }

    .seats-list {
        margin-bottom: 2rem;
    }

    .seats-list h3 {
        color: #333;
        margin-bottom: 1rem;
    }

    .seats-list ul {
        list-style: none;
        padding: 0;
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .seats-list li {
        background: #f5f5f5;
        padding: 0.5rem 1rem;
        border-radius: 4px;
        font-weight: 500;
    }

    .qr-code {
        text-align: center;
        margin: 2rem 0;
    }

    .qr-code img {
        max-width: 200px;
        border: 1px solid #ddd;
        padding: 1rem;
        border-radius: 8px;
    }

    .ticket-footer {
        text-align: center;
        border-top: 1px solid #ddd;
        padding-top: 1rem;
        margin-top: 2rem;
    }

    .ticket-footer .total {
        font-size: 1.25rem;
        font-weight: bold;
        color: #4CAF50;
        margin-bottom: 1rem;
    }

    .ticket-footer .buyer {
        margin: 1rem 0;
        line-height: 1.5;
    }

    .ticket-footer .small {
        font-size: 0.9rem;
        color: #666;
        margin-top: 1rem;
    }

    .actions {
        text-align: center;
    }

    .download-button {
        padding: 0.75rem 2rem;
        background: #4CAF50;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 1.1rem;
        transition: background 0.2s;
    }

    .download-button:hover {
        background: #45a049;
    }

    @media print {
        .actions {
            display: none;
        }

        .ticket {
            box-shadow: none;
            border: none;
        }

        .ticket-container {
            margin: 0;
            padding: 0;
        }
    }
</style> 