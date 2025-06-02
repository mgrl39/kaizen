<!-- DigitalTicket.svelte -->
<script lang="ts">
    import { onMount } from 'svelte';
    import { goto } from '$app/navigation';
    import QRCode from 'qrcode';
    
    export let bookingId: string;
    
    let loading = true;
    let error = '';
    let bookingData: any = null;
    let qrCode = '';
    
    onMount(async () => {
        try {
            // Verificar autenticación
            const authResponse = await fetch('/api/auth/check');
            if (!authResponse.ok) {
                goto('/login');
                return;
            }

            // Cargar datos de la reserva
            const response = await fetch(`/api/bookings/${bookingId}`);
            if (!response.ok) {
                if (response.status === 401) {
                    goto('/login');
                    return;
                }
                throw new Error('Error al cargar los datos de la reserva');
            }
            
            bookingData = await response.json();
            
            // Generar código QR
            qrCode = await QRCode.toDataURL(JSON.stringify({
                bookingId,
                seats: bookingData.seats.map((s: any) => s.id),
                function: bookingData.function.id
            }));
            
            loading = false;
        } catch (e) {
            error = e.message;
            loading = false;
        }
    });
</script>

{#if loading}
    <div class="loading">Cargando entrada...</div>
{:else if error}
    <div class="error">{error}</div>
{:else}
    <div class="ticket-container">
        <div class="ticket">
            <div class="ticket-header">
                <h1>Kaizen Cinema</h1>
                <p class="booking-code">Código: {bookingData.booking_code}</p>
            </div>

            <div class="movie-details">
                <h2>{bookingData.function.movie.title}</h2>
                <p>Sala: {bookingData.function.room.name}</p>
                <p>Fecha: {new Date(bookingData.function.date).toLocaleDateString()}</p>
                <p>Hora: {bookingData.function.time}</p>
            </div>

            <div class="seats-list">
                <h3>Asientos:</h3>
                <ul>
                    {#each bookingData.seats as seat}
                        <li>Fila {seat.row} - Asiento {seat.number}</li>
                    {/each}
                </ul>
            </div>

            <div class="qr-code">
                <img src={qrCode} alt="Código QR de la entrada" />
            </div>

            <div class="ticket-footer">
                <p>Total pagado: {bookingData.total_price}€</p>
                <p class="small">Esta entrada es válida hasta el inicio de la función</p>
            </div>
        </div>

        <div class="actions">
            <button class="download-button" on:click={() => window.print()}>
                Descargar entrada
            </button>
        </div>
    </div>
{/if}

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
    }

    .booking-code {
        font-family: monospace;
        font-size: 1.2rem;
        color: #666;
    }

    .movie-details {
        margin-bottom: 2rem;
    }

    .movie-details h2 {
        color: #333;
        margin: 0 0 1rem 0;
    }

    .seats-list {
        margin-bottom: 2rem;
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
    }

    .qr-code {
        text-align: center;
        margin: 2rem 0;
    }

    .qr-code img {
        max-width: 200px;
    }

    .ticket-footer {
        text-align: center;
        border-top: 1px solid #ddd;
        padding-top: 1rem;
        margin-top: 2rem;
    }

    .ticket-footer .small {
        font-size: 0.9rem;
        color: #666;
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
    }

    .loading, .error {
        text-align: center;
        padding: 2rem;
        font-size: 1.2rem;
    }

    .error {
        color: #ff4444;
    }
</style> 