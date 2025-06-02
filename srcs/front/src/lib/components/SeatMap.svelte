<!-- SeatMap.svelte -->
<script lang="ts">
    import { createEventDispatcher } from 'svelte';
    
    export let rows: number;
    export let seatsPerRow: number;
    export let selectedSeats: string[] = [];
    export let occupiedSeats: string[] = [];
    
    const dispatch = createEventDispatcher();
    
    // Matriz para almacenar el estado de los asientos
    $: seatMatrix = Array(rows).fill(null).map((_, rowIndex) => 
        Array(seatsPerRow).fill(null).map((_, seatIndex) => ({
            id: `${String.fromCharCode(65 + rowIndex)}${seatIndex + 1}`,
            row: String.fromCharCode(65 + rowIndex),
            number: seatIndex + 1,
            isSelected: selectedSeats.includes(`${String.fromCharCode(65 + rowIndex)}${seatIndex + 1}`),
            isOccupied: occupiedSeats.includes(`${String.fromCharCode(65 + rowIndex)}${seatIndex + 1}`),
            isSelectable: false
        }))
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
            const seatId = `${String.fromCharCode(65 + r)}${s + 1}`;
            return selectedSeats.includes(seatId);
        });
    }

    // Actualizar asientos seleccionables
    $: {
        seatMatrix.forEach((row, rowIndex) => {
            row.forEach((seat, seatIndex) => {
                seat.isSelectable = !seat.isOccupied && 
                    (!seat.isSelected && (selectedSeats.length === 0 || isConnectedToSelected(rowIndex, seatIndex)));
            });
        });
    }

    function handleSeatClick(seat: any) {
        if (seat.isOccupied) return;
        
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
                {#each row as seat}
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