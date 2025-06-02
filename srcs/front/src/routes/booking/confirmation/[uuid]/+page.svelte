<script lang="ts">
    import { onMount } from 'svelte';
    import { API_URL } from '$lib/config';
    import QRCode from 'qrcode';
    
    export let data;
    const { uuid } = data;
    
    let booking = null;
    let qrCode = '';
    let loading = true;
    let error = '';
    
    onMount(async () => {
        try {
            const response = await fetch(`${API_URL}/bookings/${uuid}`);
            const result = await response.json();
            
            if (!response.ok || !result.success) {
                throw new Error(result.message || 'Error al cargar la reserva');
            }
            
            booking = result.data;
            
            // Generar QR con los datos de la reserva
            const qrData = {
                uuid: uuid,
                buyer: booking.buyer,
                seats: booking.seats,
                function: booking.function
            };
            
            qrCode = await QRCode.toDataURL(JSON.stringify(qrData));
        } catch (e) {
            error = e.message;
        } finally {
            loading = false;
        }
    });
</script>

<div class="container py-5">
    {#if loading}
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
        </div>
    {:else if error}
        <div class="alert alert-danger">
            {error}
        </div>
    {:else}
        <div class="card">
            <div class="card-body">
                <div class="text-center mb-4">
                    <i class="bi bi-check-circle-fill text-success display-1"></i>
                    <h2 class="mt-3">¡Reserva Confirmada!</h2>
                    <p class="text-muted">Tu reserva se ha completado con éxito</p>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <h4>Detalles de la Reserva</h4>
                        <ul class="list-unstyled">
                            <li><strong>Película:</strong> {booking.function.movie.title}</li>
                            <li><strong>Sala:</strong> {booking.function.room.name}</li>
                            <li><strong>Fecha:</strong> {new Date(booking.function.date).toLocaleDateString('es-ES')}</li>
                            <li><strong>Hora:</strong> {new Date(booking.function.time).toLocaleTimeString('es-ES', {hour: '2-digit', minute: '2-digit'})}</li>
                            <li><strong>Asientos:</strong> {booking.seats.map(s => `Fila ${s.row} - Asiento ${s.number}`).join(', ')}</li>
                        </ul>
                        
                        <h4 class="mt-4">Datos del Comprador</h4>
                        <ul class="list-unstyled">
                            <li><strong>Nombre:</strong> {booking.buyer.name}</li>
                            <li><strong>Email:</strong> {booking.buyer.email}</li>
                            <li><strong>Teléfono:</strong> {booking.buyer.phone}</li>
                        </ul>
                    </div>
                    
                    <div class="col-md-6 text-center">
                        <h4>Código QR de la Entrada</h4>
                        <img src={qrCode} alt="QR Code" class="img-fluid mb-3" style="max-width: 200px;">
                        <p class="text-muted small">Muestra este código en la entrada del cine</p>
                        
                        <div class="mt-4">
                            <a href="/tickets/{uuid}" class="btn btn-primary me-2">
                                <i class="bi bi-ticket-perforated me-2"></i>
                                Ver Entrada
                            </a>
                            <button class="btn btn-outline-primary" on:click={() => window.print()}>
                                <i class="bi bi-printer me-2"></i>
                                Imprimir
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {/if}
</div> 