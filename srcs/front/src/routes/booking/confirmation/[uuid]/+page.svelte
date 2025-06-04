<script lang="ts">
    import { onMount } from 'svelte';
    import { API_URL } from '$lib/config';
    import QRCode from 'qrcode';
    
    export let data;
    
    let booking = null;
    let qrCode = '';
    let loading = true;
    let error = '';
    let downloadStatus = '';
    let printStatus = '';
    
    $: {
        if (data?.uuid) {
            loadBookingData(data.uuid);
        } else {
            error = 'UUID no válido';
            loading = false;
        }
    }

    async function loadBookingData(uuid: string) {
        try {
            loading = true;
            error = '';
            console.log('Cargando reserva con UUID:', uuid);
            
            const response = await fetch(`${API_URL}/bookings/uuid/${uuid}`);
            const result = await response.json();
            
            if (!response.ok || !result.success) {
                throw new Error(result.message || 'Error al cargar la reserva');
            }
            
            booking = result.data.booking;
            
            // Generar QR con los datos de la reserva
            const qrData = {
                uuid: booking.uuid,
                booking_code: booking.booking_code,
                buyer: {
                    name: booking.buyer_name,
                    email: booking.buyer_email,
                    phone: booking.buyer_phone
                },
                seats: booking.seats,
                function: booking.function
            };
            
            qrCode = await QRCode.toDataURL(JSON.stringify(qrData));
        } catch (e: any) {
            console.error('Error al cargar la reserva:', e);
            error = e.message;
        } finally {
            loading = false;
        }
    }

    async function handleDownload() {
        if (!booking?.ticket?.download_url) {
            downloadStatus = 'Error: URL de descarga no disponible';
            return;
        }

        try {
            downloadStatus = 'Descargando...';
            const response = await fetch(booking.ticket.download_url);
            if (!response.ok) throw new Error('Error al descargar el ticket');
            
            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `ticket-${booking.booking_code}.pdf`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
            
            downloadStatus = 'Descarga completada';
            setTimeout(() => downloadStatus = '', 3000);
        } catch (e) {
            console.error('Error al descargar:', e);
            downloadStatus = 'Error en la descarga';
        }
    }

    function handlePrint() {
        printStatus = 'Preparando impresión...';
        setTimeout(() => {
            window.print();
            printStatus = 'Listo para imprimir';
            setTimeout(() => printStatus = '', 3000);
        }, 100);
    }
</script>

<style>
    .confirmation-card {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border: none;
        border-radius: 1rem;
    }

    .success-icon {
        color: #28a745;
        animation: scale-in 0.5s ease-out;
    }

    .detail-section {
        background-color: #f8f9fa;
        border-radius: 0.5rem;
        padding: 1.5rem;
        margin-bottom: 1rem;
    }

    .qr-container {
        background-color: white;
        padding: 1.5rem;
        border-radius: 1rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .status-message {
        font-size: 0.875rem;
        margin-top: 0.5rem;
        height: 1.2em;
    }

    .action-button {
        min-width: 160px;
        transition: all 0.3s ease;
    }

    .action-button:hover {
        transform: translateY(-2px);
    }

    @keyframes scale-in {
        from {
            transform: scale(0);
            opacity: 0;
        }
        to {
            transform: scale(1);
            opacity: 1;
        }
    }

    @media print {
        .no-print {
            display: none !important;
        }
    }
</style>

<div class="container py-5">
    {#if loading}
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
        </div>
    {:else if error}
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle me-2"></i>
            {error}
        </div>
    {:else if booking}
        <div class="card confirmation-card">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <i class="bi bi-check-circle-fill display-1 success-icon"></i>
                    <h2 class="mt-3 fw-bold">¡Reserva Confirmada!</h2>
                    <p class="text-muted">Tu reserva se ha completado con éxito</p>
                </div>
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="detail-section">
                            <h4 class="fw-bold mb-3">
                                <i class="bi bi-ticket-detailed me-2"></i>
                                Detalles de la Reserva
                            </h4>
                            <ul class="list-unstyled">
                                <li class="mb-2"><strong>Código:</strong> <span class="badge bg-primary">{booking.booking_code}</span></li>
                                <li class="mb-2"><strong>Película:</strong> {booking.function.movie.title}</li>
                                <li class="mb-2"><strong>Sala:</strong> {booking.function.room_id}</li>
                                <li class="mb-2"><strong>Fecha:</strong> {new Date(booking.function.date).toLocaleDateString('es-ES')}</li>
                                <li class="mb-2"><strong>Hora:</strong> {booking.function.time}</li>
                                <li class="mb-2"><strong>Asientos:</strong> {booking.seats.map(s => `Fila ${s.row} - Asiento ${s.number}`).join(', ')}</li>
                                <li class="mb-2"><strong>Precio total:</strong> <span class="text-success fw-bold">{booking.total_price}€</span></li>
                            </ul>
                        </div>
                        
                        <div class="detail-section">
                            <h4 class="fw-bold mb-3">
                                <i class="bi bi-person me-2"></i>
                                Datos del Comprador
                            </h4>
                            <ul class="list-unstyled">
                                <li class="mb-2"><strong>Nombre:</strong> {booking.buyer_name}</li>
                                <li class="mb-2"><strong>Email:</strong> {booking.buyer_email}</li>
                                <li class="mb-2"><strong>Teléfono:</strong> {booking.buyer_phone}</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="qr-container text-center">
                            <h4 class="fw-bold mb-3">
                                <i class="bi bi-qr-code me-2"></i>
                                Código QR de la Entrada
                            </h4>
                            <img src={qrCode} alt="QR Code" class="img-fluid mb-3" style="max-width: 250px;">
                            <p class="text-muted small">
                                <i class="bi bi-info-circle me-1"></i>
                                Muestra este código en la entrada del cine
                            </p>
                            
                            <div class="mt-4 d-flex flex-column gap-3 no-print">
                                <button class="btn btn-primary action-button w-100" 
                                        on:click={handleDownload}
                                        disabled={!booking.ticket?.download_url}>
                                    <i class="bi bi-ticket-perforated me-2"></i>
                                    Descargar Entrada
                                </button>
                                <div class="status-message text-muted">
                                    {downloadStatus}
                                </div>
                                
                                <button class="btn btn-outline-primary action-button w-100" 
                                        on:click={handlePrint}>
                                    <i class="bi bi-printer me-2"></i>
                                    Imprimir
                                </button>
                                <div class="status-message text-muted">
                                    {printStatus}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {/if}
</div> 