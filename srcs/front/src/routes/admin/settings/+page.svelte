<script lang="ts">
  import { t } from '$lib/i18n';
  import { onMount } from 'svelte';
  
  // Configuraciones de ejemplo (en un entorno real se cargarían desde una API)
  let settings = {
    // Configuración general
    siteName: 'Cineplex Central',
    siteDescription: 'Tu cine de confianza',
    contactEmail: 'info@cineplexcentral.com',
    contactPhone: '+34 912 345 678',
    
    // Configuración de reservas
    allowOnlineBooking: true,
    advanceBookingDays: 7,
    maxTicketsPerBooking: 10,
    bookingFee: 0.50,
    
    // Configuración de notificaciones
    sendEmailConfirmations: true,
    sendSmsReminders: false,
    reminderHoursBefore: 24,
    
    // Configuración de precios
    standardTicketPrice: 12,
    childrenTicketPrice: 8,
    seniorTicketPrice: 9,
    discountTuesday: true
  };
  
  // Estado para el formulario
  let formStatus = {
    saving: false,
    modified: false,
    success: false
  };
  
  // Monitorear cambios en la configuración
  let originalSettings = JSON.stringify(settings);
  
  $: {
    formStatus.modified = JSON.stringify(settings) !== originalSettings;
    if (formStatus.success && formStatus.modified) {
      formStatus.success = false;
    }
  }
  
  // Función para guardar configuración
  function saveSettings() {
    formStatus.saving = true;
    
    // En un entorno real, aquí harías una llamada a la API
    setTimeout(() => {
      console.log('Guardando configuración:', settings);
      formStatus.saving = false;
      formStatus.success = true;
      formStatus.modified = false;
      originalSettings = JSON.stringify(settings);
    }, 800);
  }
  
  // Reiniciar configuración
  function resetSettings() {
    settings = JSON.parse(originalSettings);
  }
</script>

<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">{$t('settings')}</h1>
    <div class="d-flex align-items-center">
      {#if formStatus.modified}
        <span class="badge bg-warning me-2">
          <i class="bi bi-asterisk me-1"></i>
          {$t('unsavedChanges')}
        </span>
      {/if}
      {#if formStatus.success}
        <span class="badge bg-success me-2">
          <i class="bi bi-check-circle me-1"></i>
          {$t('settingsSaved')}
        </span>
      {/if}
    </div>
  </div>
  
  <form on:submit|preventDefault={saveSettings} class="mb-4">
    <div class="row g-4">
      <!-- Configuración general -->
      <div class="col-12">
        <div class="card">
          <div class="card-header bg-white">
            <h2 class="h5 mb-0">
              <i class="bi bi-gear me-2 text-primary"></i>
              {$t('generalSettings')}
            </h2>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label for="site-name" class="form-label">{$t('siteName')}</label>
                <input 
                  type="text" 
                  id="site-name" 
                  bind:value={settings.siteName}
                  class="form-control"
                />
              </div>
              
              <div class="col-md-6">
                <label for="site-description" class="form-label">{$t('siteDescription')}</label>
                <input 
                  type="text" 
                  id="site-description" 
                  bind:value={settings.siteDescription}
                  class="form-control"
                />
              </div>
              
              <div class="col-md-6">
                <label for="contact-email" class="form-label">{$t('contactEmail')}</label>
                <div class="input-group">
                  <span class="input-group-text">
                    <i class="bi bi-envelope"></i>
                  </span>
                  <input 
                    type="email" 
                    id="contact-email" 
                    bind:value={settings.contactEmail}
                    class="form-control"
                  />
                </div>
              </div>
              
              <div class="col-md-6">
                <label for="contact-phone" class="form-label">{$t('contactPhone')}</label>
                <div class="input-group">
                  <span class="input-group-text">
                    <i class="bi bi-telephone"></i>
                  </span>
                  <input 
                    type="tel" 
                    id="contact-phone" 
                    bind:value={settings.contactPhone}
                    class="form-control"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Configuración de precios -->
      <div class="col-md-6">
        <div class="card h-100">
          <div class="card-header bg-white">
            <h2 class="h5 mb-0">
              <i class="bi bi-currency-dollar me-2 text-success"></i>
              {$t('pricingSettings')}
            </h2>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label for="standard-price" class="form-label">{$t('standardTicketPrice')}</label>
                <div class="input-group">
                  <span class="input-group-text">$</span>
                  <input 
                    type="number" 
                    id="standard-price" 
                    bind:value={settings.standardTicketPrice}
                    class="form-control"
                    min="0"
                    step="0.01"
                  />
                </div>
              </div>
              
              <div class="col-md-6">
                <label for="children-price" class="form-label">{$t('childrenTicketPrice')}</label>
                <div class="input-group">
                  <span class="input-group-text">$</span>
                  <input 
                    type="number" 
                    id="children-price" 
                    bind:value={settings.childrenTicketPrice}
                    class="form-control"
                    min="0"
                    step="0.01"
                  />
                </div>
              </div>
              
              <div class="col-md-6">
                <label for="senior-price" class="form-label">{$t('seniorTicketPrice')}</label>
                <div class="input-group">
                  <span class="input-group-text">$</span>
                  <input 
                    type="number" 
                    id="senior-price" 
                    bind:value={settings.seniorTicketPrice}
                    class="form-control"
                    min="0"
                    step="0.01"
                  />
                </div>
              </div>
              
              <div class="col-md-6">
                <label class="form-label d-block">{$t('discounts')}</label>
                <div class="form-check form-switch">
                  <input 
                    type="checkbox" 
                    id="discount-tuesday" 
                    bind:checked={settings.discountTuesday}
                    class="form-check-input"
                  />
                  <label for="discount-tuesday" class="form-check-label">
                    {$t('discountTuesday')}
                  </label>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Configuración de reservas -->
      <div class="col-md-6">
        <div class="card h-100">
          <div class="card-header bg-white">
            <h2 class="h5 mb-0">
              <i class="bi bi-ticket-perforated me-2 text-info"></i>
              {$t('bookingSettings')}
            </h2>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label d-block">{$t('bookingOptions')}</label>
                <div class="form-check form-switch">
                  <input 
                    type="checkbox" 
                    id="allow-online-booking" 
                    bind:checked={settings.allowOnlineBooking}
                    class="form-check-input"
                  />
                  <label for="allow-online-booking" class="form-check-label">
                    {$t('allowOnlineBooking')}
                  </label>
                </div>
              </div>
              
              <div class="col-md-6">
                <label for="advance-booking-days" class="form-label">{$t('advanceBookingDays')}</label>
                <input 
                  type="number" 
                  id="advance-booking-days" 
                  bind:value={settings.advanceBookingDays}
                  class="form-control"
                  min="1"
                  max="30"
                />
              </div>
              
              <div class="col-md-6">
                <label for="max-tickets" class="form-label">{$t('maxTicketsPerBooking')}</label>
                <input 
                  type="number" 
                  id="max-tickets" 
                  bind:value={settings.maxTicketsPerBooking}
                  class="form-control"
                  min="1"
                  max="20"
                />
              </div>
              
              <div class="col-md-6">
                <label for="booking-fee" class="form-label">{$t('bookingFee')}</label>
                <div class="input-group">
                  <span class="input-group-text">$</span>
                  <input 
                    type="number" 
                    id="booking-fee" 
                    bind:value={settings.bookingFee}
                    class="form-control"
                    min="0"
                    step="0.01"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Configuración de notificaciones -->
      <div class="col-12">
        <div class="card">
          <div class="card-header bg-white">
            <h2 class="h5 mb-0">
              <i class="bi bi-bell me-2 text-danger"></i>
              {$t('notificationSettings')}
            </h2>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-4">
                <div class="form-check form-switch">
                  <input 
                    type="checkbox" 
                    id="send-email-confirmations" 
                    bind:checked={settings.sendEmailConfirmations}
                    class="form-check-input"
                  />
                  <label for="send-email-confirmations" class="form-check-label">
                    {$t('sendEmailConfirmations')}
                  </label>
                </div>
              </div>
              
              <div class="col-md-4">
                <div class="form-check form-switch">
                  <input 
                    type="checkbox" 
                    id="send-sms-reminders" 
                    bind:checked={settings.sendSmsReminders}
                    class="form-check-input"
                  />
                  <label for="send-sms-reminders" class="form-check-label">
                    {$t('sendSmsReminders')}
                  </label>
                </div>
              </div>
              
              <div class="col-md-4">
                <label for="reminder-hours-before" class="form-label">{$t('reminderHoursBefore')}</label>
                <input 
                  type="number" 
                  id="reminder-hours-before" 
                  bind:value={settings.reminderHoursBefore}
                  class="form-control"
                  min="1"
                  max="72"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Botones de acción -->
    <div class="d-flex justify-content-end gap-2 mt-4">
      <button
        type="button"
        class="btn btn-outline-secondary"
        on:click={resetSettings}
        disabled={!formStatus.modified}
      >
        <i class="bi bi-arrow-counterclockwise me-1"></i>
        {$t('cancel')}
      </button>
      <button
        type="submit"
        class="btn btn-primary"
        disabled={!formStatus.modified || formStatus.saving}
      >
        {#if formStatus.saving}
          <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
          {$t('saving')}
        {:else}
          <i class="bi bi-save me-1"></i>
          {$t('saveSettings')}
        {/if}
      </button>
    </div>
  </form>
</div> 