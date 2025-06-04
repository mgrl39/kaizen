<!-- SeatMap.svelte -->
<script lang="ts">
    import { createEventDispatcher } from 'svelte';
    
    export let rows: number;
    export let seatsPerRow: number;
    export let selectedSeats: string[] = [];
    export let occupiedSeats: string[] = [];
    export let seatsData: any[] = []; // Matriz de asientos del backend
    
    const dispatch = createEventDispatcher();
    
    // Matriz para almacenar el estado de los asientos
    $: seatMatrix = Array(rows).fill(null).map((_, rowIndex) => 
        Array(seatsPerRow).fill(null).map((_, seatIndex) => {
            // Encontrar el asiento en los datos del backend
            const seatData = seatsData[rowIndex]?.[seatIndex];
            
            if (!seatData) {
                return null;
            }

            return {
                id: seatData.id.toString(), // Siempre usar el ID real como string
                row: rowIndex + 1, // Fila basada en el índice + 1
                number: seatData.number,
                isSelected: selectedSeats.includes(seatData.id.toString()),
                isOccupied: seatData.is_occupied,
                isSelectable: !seatData.is_occupied // Inicialmente seleccionable si no está ocupado
            };
        })
    );

    // Función para verificar si un asiento está conectado a los asientos seleccionados
    function isConnectedToSelected(row: number, seat: number): boolean {
        if (selectedSeats.length === 0) return true;
        
        const adjacentPositions = [
            [row, seat - 1],     // Izquierda
            [row, seat + 1],     // Derecha
            [row - 1, seat],     // Arriba
            [row + 1, seat]      // Abajo
        ];

        return adjacentPositions.some(([r, s]) => {
            if (r < 0 || r >= rows || s < 0 || s >= seatsPerRow) return false;
            const adjacentSeat = seatMatrix[r][s];
            return adjacentSeat && selectedSeats.includes(adjacentSeat.id);
        });
    }

    // Actualizar asientos seleccionables
    $: {
        seatMatrix.forEach((row, rowIndex) => {
            row.forEach((seat, seatIndex) => {
                if (seat) {
                    seat.isSelectable = !seat.isOccupied && 
                        (!seat.isSelected && (selectedSeats.length === 0 || isConnectedToSelected(rowIndex, seatIndex)));
                }
            });
        });
    }

    function handleSeatClick(seat: any) {
        if (!seat || seat.isOccupied) return;
        
        const seatId = seat.id;
        let newSelectedSeats;

        if (selectedSeats.includes(seatId)) {
            // Deseleccionar asiento
            newSelectedSeats = selectedSeats.filter(id => id !== seatId);
        } else if (seat.isSelectable) {
            // Seleccionar nuevo asiento
            newSelectedSeats = [...selectedSeats, seatId];
        } else {
            return;
        }

        dispatch('seatsChange', { seats: newSelectedSeats });
    }
</script>

<div class="text-center mb-5">
    <div class="bg-light text-center p-3 mb-4 rounded">
        <span class="text-uppercase fw-bold">PANTALLA</span>
    </div>
    
    <div class="seat-map">
        {#each seatMatrix as row, rowIndex}
            <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                <span class="fw-bold text-secondary" style="width: 30px;">
                    {String.fromCharCode(65 + rowIndex)}
                </span>
                {#each row as seat, seatIndex}
                    {#if seat}
                        <button
                            class="btn btn-sm {seat.isOccupied ? 'btn-danger' : 
                                             seat.isSelected ? 'btn-success' : 
                                             seat.isSelectable ? 'btn-outline-success' : 'btn-outline-secondary'}"
                            style="width: 40px; height: 40px;"
                            disabled={seat.isOccupied || (!seat.isSelectable && !seat.isSelected)}
                            on:click={() => handleSeatClick(seat)}
                            title="Fila {String.fromCharCode(65 + rowIndex)} Asiento {seat.number}"
                        >
                            {seat.number}
                        </button>
                    {:else}
                        <button class="btn btn-sm btn-outline-secondary opacity-50" 
                                style="width: 40px; height: 40px;" 
                                disabled>
                            -
                        </button>
                    {/if}
                {/each}
            </div>
        {/each}
    </div>
</div>

<!-- Leyenda -->
<div class="d-flex justify-content-center gap-4 mt-3">
    <div class="d-flex align-items-center">
        <div class="btn btn-sm btn-outline-secondary me-2" style="width: 30px; height: 30px;">
        </div>
        <span class="text-secondary">Disponible</span>
    </div>
    <div class="d-flex align-items-center">
        <div class="btn btn-sm btn-success me-2" style="width: 30px; height: 30px;">
        </div>
        <span class="text-secondary">Seleccionado</span>
    </div>
    <div class="d-flex align-items-center">
        <div class="btn btn-sm btn-danger me-2" style="width: 30px; height: 30px;">
        </div>
        <span class="text-secondary">Ocupado</span>
    </div>
</div> 