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
                console.error(`No se encontró datos para el asiento en fila ${rowIndex}, posición ${seatIndex}`);
                return null;
            }

            return {
                id: seatData.id.toString(), // Siempre usar el ID real como string
                row: seatData.row,
                number: seatData.number,
                isSelected: selectedSeats.includes(seatData.id.toString()),
                isOccupied: seatData.is_occupied,
                isSelectable: false
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

<div class="seat-map-container">
    <div class="screen">PANTALLA</div>
    <div class="seats-container">
        {#each seatMatrix as row, rowIndex}
            <div class="seat-row">
                <span class="row-label">{String.fromCharCode(65 + rowIndex)}</span>
                {#each row as seat, seatIndex}
                    {#if seat}
                        <button
                            class="seat"
                            class:occupied={seat.isOccupied}
                            class:selected={seat.isSelected}
                            class:selectable={seat.isSelectable}
                            disabled={seat.isOccupied || (!seat.isSelectable && !seat.isSelected)}
                            on:click={() => handleSeatClick(seat)}
                            title="Fila {seat.row} Asiento {seat.number}"
                        >
                            {seat.number}
                        </button>
                    {:else}
                        <button class="seat" disabled>-</button>
                    {/if}
                {/each}
            </div>
        {/each}
    </div>
</div>

<style>
    .seat-map-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 2rem;
        padding: 2rem;
    }

    .screen {
        width: 80%;
        padding: 0.5rem;
        background: #ddd;
        text-align: center;
        border-radius: 4px;
        margin-bottom: 2rem;
    }

    .seats-container {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .seat-row {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    .row-label {
        width: 1.5rem;
        text-align: center;
        font-weight: bold;
    }

    .seat {
        width: 2.5rem;
        height: 2.5rem;
        border: 2px solid #ccc;
        border-radius: 4px;
        background: white;
        cursor: pointer;
        transition: all 0.2s;
    }

    .seat:disabled {
        cursor: not-allowed;
    }

    .seat.occupied {
        background: #ff4444;
        border-color: #cc0000;
        color: white;
    }

    .seat.selected {
        background: #44ff44;
        border-color: #00cc00;
        color: black;
    }

    .seat.selectable {
        border-color: #44ff44;
    }

    .seat:not(.occupied):not(.selected):hover {
        background: #eee;
    }
</style> 