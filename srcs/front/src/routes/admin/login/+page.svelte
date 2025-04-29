<script lang="ts">
  import { goto } from '$app/navigation';
  import { API_URL } from '$lib/config';
  import { onMount } from 'svelte';
  
  let email = '';
  let password = '';
  let errorMessage = '';
  let loading = false;
  let rememberMe = false;
  
  onMount(() => {
    // Verificar si ya hay sesión de admin
    const adminToken = localStorage.getItem('admin_token');
    if (adminToken) {
      goto('/admin/dashboard');
    }
  });
  
  async function handleLogin(e: SubmitEvent) {
    e.preventDefault();
    loading = true;
    errorMessage = '';
    
    try {
      const response = await fetch(`${API_URL}/admin/login`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({ email, password })
      });
      
      const data = await response.json();
      
      if (response.ok && data.token) {
        // Guardar token en localStorage
        localStorage.setItem('admin_token', data.token);
        
        // Guardar info del usuario admin si está disponible
        if (data.user) {
          localStorage.setItem('admin_user', JSON.stringify(data.user));
        }
        
        // Redirigir al dashboard
        goto('/admin/dashboard');
      } else {
        errorMessage = data.message || 'Credenciales inválidas';
      }
    } catch (error) {
      errorMessage = 'Error de conexión con el servidor';
    } finally {
      loading = false;
    }
  }
</script>

<svelte:head>
  <title>Admin Login - Kaizen Cinema</title>
</svelte:head>

<div class="admin-login-container">
  <div class="admin-login-card">
    <div class="login-header">
      <div class="logo-container">
        <img src="/images/kaizen_logo_transparent.png" alt="Kaizen Logo" width="80">
      </div>
      <h2>Panel de Administración</h2>
      <p class="text-muted">Accede para gestionar el sistema</p>
    </div>
    
    <form on:submit={handleLogin} class="admin-login-form">
      {#if errorMessage}
        <div class="alert alert-danger">{errorMessage}</div>
      {/if}
      
      <div class="form-group">
        <label for="email">Correo electrónico</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-envelope"></i></span>
          <input 
            type="email" 
            id="email" 
            class="form-control" 
            placeholder="admin@ejemplo.com" 
            required
            bind:value={email}
            disabled={loading}
          >
        </div>
      </div>
      
      <div class="form-group">
        <div class="d-flex justify-content-between align-items-center">
          <label for="password">Contraseña</label>
          <a href="/admin/reset-password" class="text-sm text-purple">¿Olvidaste tu contraseña?</a>
        </div>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-lock"></i></span>
          <input 
            type="password" 
            id="password" 
            class="form-control" 
            placeholder="••••••••" 
            required
            bind:value={password}
            disabled={loading}
          >
        </div>
      </div>
      
      <div class="form-check">
        <input 
          type="checkbox" 
          class="form-check-input" 
          id="rememberMe" 
          bind:checked={rememberMe}
        >
        <label class="form-check-label" for="rememberMe">Recordar sesión</label>
      </div>
      
      <button 
        type="submit" 
        class="btn btn-primary btn-block w-100" 
        disabled={loading}
      >
        {#if loading}
          <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
          Accediendo...
        {:else}
          <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
        {/if}
      </button>
    </form>
    
    <div class="mt-4 text-center">
      <a href="/" class="text-muted">
        <i class="bi bi-arrow-left me-1"></i>Volver al sitio principal
      </a>
    </div>
  </div>
  
  <div class="admin-login-info">
    <div class="info-content">
      <h3>Panel de Administración de Kaizen Cinema</h3>
      <p>Este panel te permite gestionar todos los aspectos del sistema de cines:</p>
      <ul>
        <li><i class="bi bi-film text-purple"></i> Gestión de películas y funciones</li>
        <li><i class="bi bi-building text-purple"></i> Administración de cines y salas</li>
        <li><i class="bi bi-people text-purple"></i> Control de usuarios y permisos</li>
        <li><i class="bi bi-graph-up text-purple"></i> Estadísticas y reportes</li>
        <li><i class="bi bi-gear text-purple"></i> Configuración del sistema</li>
      </ul>
      <div class="mt-4">
        <p class="text-sm text-muted">
          ¿Necesitas ayuda? Contacta al equipo de soporte técnico:
          <a href="mailto:soporte@kaizen.com" class="text-purple">soporte@kaizen.com</a>
        </p>
      </div>
    </div>
  </div>
</div>

<style>
  .admin-login-container {
    min-height: 100vh;
    display: flex;
    background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
  }
  
  .admin-login-card {
    width: 50%;
    padding: 3rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
    background-color: #121212;
    color: #e0e0e0;
  }
  
  .admin-login-info {
    width: 50%;
    background: url('/images/cinema-admin-bg.jpg') center/cover no-repeat;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    color: white;
  }
  
  .admin-login-info::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(3px);
  }
  
  .info-content {
    position: relative;
    z-index: 1;
    max-width: 500px;
  }
  
  .info-content h3 {
    font-weight: 700;
    margin-bottom: 1.5rem;
    color: white;
  }
  
  .info-content ul {
    padding-left: 1rem;
    margin-top: 1.5rem;
  }
  
  .info-content li {
    margin-bottom: 1rem;
    font-size: 1.1rem;
  }
  
  .info-content li i {
    margin-right: 0.5rem;
  }
  
  .login-header {
    text-align: center;
    margin-bottom: 2rem;
  }
  
  .login-header h2 {
    font-weight: 700;
    margin: 1rem 0;
    background: linear-gradient(90deg, #6d28d9, #8b5cf6);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }
  
  .logo-container {
    margin-bottom: 1rem;
  }
  
  .admin-login-form {
    max-width: 400px;
    margin: 0 auto;
    width: 100%;
  }
  
  .admin-login-form .form-group {
    margin-bottom: 1.5rem;
  }
  
  .admin-login-form .form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
  }
  
  .admin-login-form .input-group {
    background-color: #1e1e1e;
    border-radius: 8px;
    overflow: hidden;
  }
  
  .admin-login-form .input-group-text {
    background-color: #2d2d2d;
    border: none;
    color: #6d28d9;
  }
  
  .admin-login-form input {
    background-color: #1e1e1e;
    border: none;
    color: white;
    padding: 0.7rem 1rem;
  }
  
  .admin-login-form input:focus {
    box-shadow: none;
    background-color: #2a2a2a;
  }
  
  .admin-login-form .form-check {
    margin: 1rem 0 1.5rem;
  }
  
  .admin-login-form .btn-primary {
    padding: 0.7rem 0;
    font-weight: 600;
    background: linear-gradient(90deg, #6d28d9, #8b5cf6);
    border: none;
  }
  
  .admin-login-form .btn-primary:hover {
    background: linear-gradient(90deg, #5b21b6, #7c3aed);
  }
  
  .text-purple {
    color: #8b5cf6;
  }
  
  /* Responsive styles */
  @media (max-width: 992px) {
    .admin-login-container {
      flex-direction: column;
    }
    
    .admin-login-card,
    .admin-login-info {
      width: 100%;
    }
    
    .admin-login-info {
      padding: 3rem 1rem;
    }
  }
  
  @media (max-width: 576px) {
    .admin-login-card {
      padding: 2rem 1rem;
    }
  }
</style> 