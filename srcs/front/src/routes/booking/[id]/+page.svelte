# Booking Page

<script lang="ts">
    import type { PageData } from './$types';
    import BookingPage from '$lib/components/BookingPage.svelte';
    import { onMount } from 'svelte';

    export let data: PageData;
    let mounted = false;

    onMount(() => {
        mounted = true;
    });
</script>

<svelte:head>
    <title>Reserva - {data.functionData.movie.title} - Kaizen Cinema</title>
</svelte:head>

<div class="booking-page">
    {#if mounted && data.functionData}
        <BookingPage 
            functionId={data.functionId}
            functionData={data.functionData}
        />
    {:else if mounted}
        <div class="error-container">
            <h2>Error</h2>
            <p>No se pudo cargar la información de la función</p>
            <button class="btn-primary" on:click={() => window.history.back()}>
                Volver atrás
            </button>
        </div>
    {:else}
        <div class="loading-container">
            <div class="spinner"></div>
            <p>Cargando...</p>
        </div>
    {/if}
</div>

<style>
    .booking-page {
        min-height: 100vh;
        background-color: #f5f5f5;
        padding: 2rem 1rem;
    }

    .error-container, .loading-container {
        max-width: 600px;
        margin: 2rem auto;
        padding: 2rem;
        background: white;
        border-radius: 8px;
        text-align: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .error-container h2 {
        color: #dc3545;
        margin-bottom: 1rem;
    }

    .loading-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
    }

    .spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #4CAF50;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .btn-primary {
        background: #4CAF50;
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 4px;
        cursor: pointer;
        font-size: 1rem;
        margin-top: 1rem;
    }

    .btn-primary:hover {
        background: #45a049;
    }

    @media (max-width: 768px) {
        .booking-page {
            padding: 1rem 0.5rem;
    }

        .error-container, .loading-container {
            margin: 1rem;
            padding: 1rem;
        }
    }
</style> 