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

<div class="manual-selection">
    <div class="inputs">
        <div class="input-group">
            <label for="row">Fila:</label>
            <select id="row" bind:value={selectedRow}>
                {#each rowOptions as row}
                    <option value={row}>{row}</option>
                {/each}
            </select>
        </div>

        <div class="input-group">
            <label for="number">Asiento:</label>
            <select id="number" bind:value={selectedNumber}>
                {#each numberOptions as number}
                    <option value={number}>{number}</option>
                {/each}
            </select>
        </div>

        <button on:click={handleAdd} class="add-button">
            Añadir asiento
        </button>
    </div>

    {#if error}
        <p class="error">{error}</p>
    {/if}
</div>

<style>
    .manual-selection {
        padding: 1rem;
        border: 1px solid #ddd;
        border-radius: 4px;
        margin: 1rem 0;
    }

    .inputs {
        display: flex;
        gap: 1rem;
        align-items: flex-end;
    }

    .input-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    label {
        font-size: 0.9rem;
        color: #666;
    }

    select {
        padding: 0.5rem;
        border: 1px solid #ccc;
        border-radius: 4px;
        min-width: 80px;
    }

    .add-button {
        padding: 0.5rem 1rem;
        background: #4CAF50;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background 0.2s;
    }

    .add-button:hover {
        background: #45a049;
    }

    .error {
        color: #ff4444;
        margin-top: 0.5rem;
        font-size: 0.9rem;
    }
</style> 