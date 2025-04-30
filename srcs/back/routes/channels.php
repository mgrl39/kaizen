<?php

/**
 * @file channels.php
 * Este archivo proporciona las definiciones de canales de difusión para comunicación en tiempo real
 */

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Canales de Difusión
|--------------------------------------------------------------------------
|
| Aquí puedes registrar todos los canales de difusión de eventos que tu
| aplicación soporta. Las funciones de autorización de canales proporcionadas
| se utilizan para verificar si un usuario autenticado puede escuchar el canal.
|
*/

Broadcast::channel(
    'App.Models.User.{id}',
    function ($user, $id) {
        return (int) $user->id === (int) $id;
    }
);
