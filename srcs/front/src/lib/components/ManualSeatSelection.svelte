<!-- ManualSeatSelection.svelte -->
<script lang="ts">
    import { createEventDispatcher } from 'svelte';

    export let rows: number;
    export let seatsPerRow: number;
    export let selectedSeats: string[] = [];
    export let occupiedSeats: string[] = [];

    const dispatch = createEventDispatcher();

    let selectedRow = 'A';
    let selectedNumber = '1';
    let error = '';

    // Generar opciones para las filas
    $: rowOptions = Array(rows)
        .fill(null)
        .map((_, i) => String.fromCharCode(65 + i));

    // Generar opciones para los números de asiento
    $: numberOptions = Array(seatsPerRow)
        .fill(null)
        .map((_, i) => (i + 1).toString());

    function handleAdd() {
        const seatId = `${selectedRow}${selectedNumber}`;

        // Validar si el asiento ya está seleccionado
        if (selectedSeats.includes(seatId)) {
            error = 'Este asiento ya está seleccionado';
            return;
        }

        // Validar si el asiento está ocupado
        if (occupiedSeats.includes(seatId)) {
            error = 'Este asiento está ocupado';
            return;
        }

        // Validar si el asiento está conectado a los seleccionados
        if (selectedSeats.length > 0) {
            const rowIndex = selectedRow.charCodeAt(0) - 65;
            const seatIndex = parseInt(selectedNumber) - 1;
            
            const isConnected = [
                [rowIndex, seatIndex - 1],     // Izquierda
                [rowIndex, seatIndex + 1],     // Derecha
                [rowIndex - 1, seatIndex],     // Arriba
                [rowIndex + 1, seatIndex]      // Abajo
            ].some(([r, s]) => {
                if (r < 0 || r >= rows || s < 0 || s >= seatsPerRow) return false;
                const adjacentSeatId = `${String.fromCharCode(65 + r)}${s + 1}`;
                return selectedSeats.includes(adjacentSeatId);
            });

            if (!isConnected) {
                error = 'El asiento debe estar conectado a los ya seleccionados';
                return;
            }
        }

        error = '';
        dispatch('seatsChange', { seats: [...selectedSeats, seatId] });
    }
</script>

<div class="card p-4 mb-4">
    <div class="row g-3 align-items-end">
        <div class="col-md-4">
            <label for="row" class="form-label text-secondary">Fila:</label>
            <select id="row" class="form-select" bind:value={selectedRow}>
                {#each rowOptions as row}
                    <option value={row}>{row}</option>
                {/each}
            </select>
        </div>

        <div class="col-md-4">
            <label for="number" class="form-label text-secondary">Asiento:</label>
            <select id="number" class="form-select" bind:value={selectedNumber}>
                {#each numberOptions as number}
                    <option value={number}>{number}</option>
                {/each}
            </select>
        </div>

        <div class="col-md-4">
            <button on:click={handleAdd} 
                    class="btn btn-primary w-100">
                <i class="bi bi-plus-circle me-2"></i>
                Añadir asiento
            </button>
        </div>
    </div>

    {#if error}
        <div class="alert alert-danger mt-3">
            <i class="bi bi-exclamation-triangle me-2"></i>
            {error}
        </div>
    {/if}
</div> 