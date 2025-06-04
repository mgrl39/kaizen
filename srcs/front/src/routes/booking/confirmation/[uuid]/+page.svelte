<script lang="ts">
    import { onMount } from 'svelte';
    import { API_URL } from '$lib/config';
    
    export let data;
    
    let booking = null;
    let loading = true;
    let error = '';
    let downloadStatus = '';
    let printStatus = '';
    
    // Función para obtener la URL base del API sin el prefijo /api/v1
    function getBaseUrl() {
        return API_URL.replace('/api/v1', '');
    }
    
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
    /* Global styles moved to direct selectors */
    :global(body) {
        --primary-color: #4F46E5;
        --success-color: #10B981;
        --background-light: #F9FAFB;
        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    }

    /* Container styles */
    :global(.container) {
        max-width: 1200px;
        margin: 0 auto;
    }

    /* Card styles */
    .confirmation-card {
        box-shadow: var(--shadow-lg);
        border: none;
        border-radius: 1.5rem;
        margin: 2rem auto;
        max-width: 1200px;
        background: white;
        transition: transform 0.3s ease;
        overflow: hidden;
    }

    /* Success icon styles */
    .success-icon {
        color: var(--success-color);
        animation: scale-in 0.7s cubic-bezier(0.16, 1, 0.3, 1);
        font-size: 5rem;
        margin-bottom: 1.5rem;
        filter: drop-shadow(0 4px 6px rgb(0 0 0 / 0.1));
        display: inline-block;
    }

    /* Section styles */
    .detail-section {
        background-color: var(--background-light);
        border-radius: 1rem;
        padding: 2rem;
        margin-bottom: 1.5rem;
        height: 100%;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        position: relative;
        z-index: 1;
    }

    .detail-section:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    /* QR container styles */
    .qr-container {
        background-color: white;
        padding: 2rem;
        border-radius: 1.5rem;
        box-shadow: var(--shadow-md);
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        transition: transform 0.2s ease;
        position: relative;
        z-index: 1;
    }

    .qr-container:hover {
        transform: translateY(-2px);
    }

    /* QR image styles */
    .qr-image {
        max-width: 280px;
        width: 100%;
        height: auto;
        margin: 1.5rem auto;
        padding: 1.5rem;
        background: white;
        border-radius: 1rem;
        box-shadow: var(--shadow-sm);
        transition: transform 0.3s ease;
    }

    .qr-image:hover {
        transform: scale(1.02);
    }

    /* Status message styles */
    .status-message {
        font-size: 0.875rem;
        margin-top: 0.75rem;
        height: 1.2em;
        text-align: center;
        color: var(--primary-color);
        font-weight: 500;
    }

    /* Action buttons container */
    .action-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
        margin-top: 2rem;
    }

    /* Button styles */
    .action-button {
        min-width: 200px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        margin: 0.75rem 0;
        border-radius: 0.75rem;
        padding: 1rem 2rem;
        font-weight: 600;
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .action-button i {
        font-size: 1.25rem;
    }

    .action-button:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .action-button.download {
        background-color: var(--primary-color);
        color: white;
        border: none;
    }

    .action-button.print {
        background-color: white;
        color: var(--primary-color);
        border: 2px solid var(--primary-color);
    }

    /* Booking details styles */
    .booking-details {
        display: grid;
        gap: 1.5rem;
    }

    /* Badge styles */
    :global(.badge) {
        padding: 0.5rem 1rem;
        border-radius: 2rem;
        font-weight: 600;
        letter-spacing: 0.025em;
        background-color: var(--primary-color);
        color: white;
    }

    /* List styles */
    .list-unstyled li {
        padding: 0.75rem 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .list-unstyled li:last-child {
        border-bottom: none;
    }

    .list-unstyled strong {
        color: #374151;
        min-width: 120px;
        display: inline-block;
        font-weight: 600;
    }

    /* Loading spinner styles */
    :global(.spinner-border) {
        color: var(--primary-color);
        width: 3rem;
        height: 3rem;
    }

    /* Alert styles */
    :global(.alert-danger) {
        background-color: #FEE2E2;
        border-color: #FCA5A5;
        color: #991B1B;
        border-radius: 0.75rem;
        padding: 1rem 1.5rem;
    }

    /* Responsive styles */
    @media (max-width: 768px) {
        .confirmation-card {
            margin: 1rem;
            border-radius: 1rem;
        }

        .detail-section {
            padding: 1.5rem;
        }

        .qr-container {
            margin-top: 1.5rem;
            padding: 1.5rem;
        }

        .action-buttons {
            flex-direction: column;
        }

        .action-button {
            width: 100%;
            min-width: unset;
        }

        .list-unstyled strong {
            min-width: 100px;
        }
    }

    /* Animation */
    @keyframes scale-in {
        0% {
            transform: scale(0.8);
            opacity: 0;
        }
        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    /* Print styles */
    @media print {
        @page {
            size: landscape;
            margin: 0;
        }

        body {
            margin: 1cm;
        }

        .no-print {
            display: none !important;
        }
        
        .confirmation-card {
            box-shadow: none;
            margin: 0;
            width: 100%;
            max-width: none;
        }

        .card-body {
            display: flex;
            flex-direction: row;
            gap: 2rem;
            padding: 0 !important;
        }
        
        .detail-section, .qr-container {
            break-inside: avoid;
            box-shadow: none;
            background: none;
            page-break-inside: avoid;
            margin: 0;
        }

        .qr-container {
            flex: 0 0 40%;
            justify-content: center;
        }

        .booking-details {
            flex: 1;
        }

        .qr-image {
            box-shadow: none;
            max-width: 200px;
        }

        .success-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        h2 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .list-unstyled li {
            padding: 0.5rem 0;
        }
    }
</style>

<div class="container py-4 px-3">
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
                        <div class="booking-details">
                            <div class="detail-section">
                                <h4 class="fw-bold mb-3">
                                    <i class="bi bi-ticket-detailed me-2"></i>
                                    Detalles de la Reserva
                                </h4>
                                <ul class="list-unstyled">
                                    <li><strong>Código:</strong> <span class="badge">{booking.booking_code}</span></li>
                                    <li><strong>Película:</strong> {booking.function.movie.title}</li>
                                    <li><strong>Sala:</strong> {booking.function.room_id}</li>
                                    <li><strong>Fecha:</strong> {new Date(booking.function.date).toLocaleDateString('es-ES')}</li>
                                    <li><strong>Hora:</strong> {booking.function.time}</li>
                                    <li><strong>Asientos:</strong> {booking.seats.map(s => `Fila ${String.fromCharCode(65 + s.row)} - Asiento ${s.number}`).join(', ')}</li>
                                    <li><strong>Precio total:</strong> <span class="text-success fw-bold">{booking.total_price}€</span></li>
                                </ul>
                            </div>
                            
                            <div class="detail-section">
                                <h4 class="fw-bold mb-3">
                                    <i class="bi bi-person me-2"></i>
                                    Datos del Comprador
                                </h4>
                                <ul class="list-unstyled">
                                    <li><strong>Nombre:</strong> {booking.buyer_name}</li>
                                    <li><strong>Email:</strong> {booking.buyer_email}</li>
                                    <li><strong>Teléfono:</strong> {booking.buyer_phone}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="qr-container">
                            <h4 class="fw-bold mb-3">
                                <i class="bi bi-qr-code me-2"></i>
                                Código QR de la Entrada
                            </h4>
                            {#if booking.ticket?.qr_path}
                                <img src={`${API_URL}/qr/${booking.ticket.qr_path.split('/').pop()}`} 
                                     alt="QR Code" 
                                     class="qr-image"
                                     loading="lazy">
                            {:else}
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    QR Code no disponible
                                </div>
                            {/if}
                            <p class="text-muted small text-center mb-4">
                                <i class="bi bi-info-circle me-1"></i>
                                Muestra este código en la entrada del cine
                            </p>
                            
                            <div class="action-buttons no-print">
                                <button class="action-button download" 
                                        on:click={handleDownload}
                                        disabled={!booking.ticket?.download_url}>
                                    <i class="bi bi-download"></i>
                                    Descargar Entrada
                                </button>
                                <button class="action-button print" 
                                        on:click={handlePrint}>
                                    <i class="bi bi-printer"></i>
                                    Imprimir
                                </button>
                            </div>
                            <div class="status-message text-muted">
                                {downloadStatus || printStatus}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {/if}
</div> 