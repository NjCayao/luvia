<!-- Contenido principal -->
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title"><?= $profile ? 'Editar' : 'Crear' ?> Perfil</h3>
            </div>
            <!-- /.card-header -->
            
            <!-- form start -->
            <form id="profile-form" method="post" action="<?= url('/usuario/editar/procesar') ?>">
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Nombre / Alias *</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       placeholder="Ingrese su nombre o alias" 
                                       value="<?= htmlspecialchars($profile['name'] ?? '') ?>" required>
                                <div class="invalid-feedback" id="name-error"></div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="whatsapp">WhatsApp *</label>
                                <input type="tel" class="form-control" id="whatsapp" name="whatsapp" 
                                       placeholder="Ej: 999999999" 
                                       value="<?= htmlspecialchars($profile['whatsapp'] ?? '') ?>" required>
                                <div class="invalid-feedback" id="whatsapp-error"></div>
                                <small class="form-text text-muted">Este número será visible para los contactos.</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="city">Ciudad *</label>
                                <select class="form-control" id="city" name="city" required>
                                    <option value="">Seleccione una ciudad</option>
                                    <?php foreach ($cities as $city): ?>
                                        <option value="<?= htmlspecialchars($city) ?>" 
                                                <?= ($profile['city'] ?? '') === $city ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($city) ?>
                                        </option>
                                    <?php endforeach; ?>
                                    <option value="other" 
                                            <?= !in_array($profile['city'] ?? '', $cities) && !empty($profile['city'] ?? '') ? 'selected' : '' ?>>
                                        Otra
                                    </option>
                                </select>
                                <div class="invalid-feedback" id="city-error"></div>
                            </div>
                            
                            <div class="form-group" id="other-city-container" style="display: none;">
                                <label for="other-city">Especifique la ciudad *</label>
                                <input type="text" class="form-control" id="other-city" 
                                       placeholder="Ingrese el nombre de la ciudad">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="location">Ubicación específica *</label>
                                <input type="text" class="form-control" id="location" name="location" 
                                       placeholder="Ej: Miraflores, Surco, etc." 
                                       value="<?= htmlspecialchars($profile['location'] ?? '') ?>" required>
                                <div class="invalid-feedback" id="location-error"></div>
                                <small class="form-text text-muted">Zona o distrito dentro de la ciudad.</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Descripción *</label>
                        <textarea class="form-control" id="description" name="description" rows="4" 
                                  placeholder="Describe tus servicios, características, etc." required><?= htmlspecialchars($profile['description'] ?? '') ?></textarea>
                        <div class="invalid-feedback" id="description-error"></div>
                        <small class="form-text text-muted">Mínimo 50 caracteres. Describe tus servicios, características, preferencias, etc.</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="schedule">Horario de atención *</label>
                       <textarea class="form-control" id="schedule" name="schedule" rows="3" 
                                 placeholder="Ej: Lunes a Viernes de 10:00 a 22:00, fines de semana 24 horas" required><?= htmlspecialchars($profile['schedule'] ?? '') ?></textarea>
                       <div class="invalid-feedback" id="schedule-error"></div>
                   </div>
                   
                   <!-- Sección de Tarifas -->
                   <div class="card card-secondary">
                       <div class="card-header">
                           <h3 class="card-title">Tarifas</h3>
                       </div>
                       <div class="card-body">
                           <div class="row">
                               <div class="col-md-4">
                                   <div class="form-group">
                                       <label for="rate-hour">Tarifa por Hora (S/.)</label>
                                       <input type="number" class="form-control rate-input" id="rate-hour" 
                                              data-type="hour" placeholder="Ej: 150" min="0" step="1">
                                   </div>
                               </div>
                               <div class="col-md-4">
                                   <div class="form-group">
                                       <label for="rate-half-hour">Tarifa Media Hora (S/.)</label>
                                       <input type="number" class="form-control rate-input" id="rate-half-hour" 
                                              data-type="half_hour" placeholder="Ej: 80" min="0" step="1">
                                   </div>
                               </div>
                               <div class="col-md-4">
                                   <div class="form-group">
                                       <label for="rate-extra">Tarifa Extra (S/.)</label>
                                       <input type="number" class="form-control rate-input" id="rate-extra" 
                                              data-type="extra" placeholder="Ej: 200" min="0" step="1">
                                   </div>
                               </div>
                           </div>
                           
                           <div class="row">
                               <div class="col-md-12">
                                   <div class="form-group">
                                       <label for="rate-extra-desc">Descripción de tarifa extra</label>
                                       <input type="text" class="form-control" id="rate-extra-desc" 
                                              placeholder="Ej: Salidas, toda la noche, etc.">
                                   </div>
                               </div>
                           </div>
                           
                           <input type="hidden" name="rates" id="rates-json" value="">
                       </div>
                   </div>
               </div>
               <!-- /.card-body -->
               
               <div class="card-footer">
                   <button type="submit" class="btn btn-primary" id="submit-btn">
                       <i class="fas fa-save"></i> Guardar Perfil
                   </button>
                   <a href="<?= url('/usuario/dashboard') ?>" class="btn btn-default float-right">
                       Cancelar
                   </a>
               </div>
           </form>
       </div>
       <!-- /.card -->
   </div>
</div>

<div class="alert alert-danger mt-3 d-none" id="form-error"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
   // Cargar tarifas existentes
   <?php
   $rates = [];
   if ($profile) {
       $existingRates = Rate::getByProfileId($profile['id']);
       foreach ($existingRates as $rate) {
           $rates[] = [
               'rate_type' => $rate['rate_type'],
               'price' => $rate['price'],
               'description' => $rate['description']
           ];
       }
   }
   ?>
   
   const existingRates = <?= json_encode($rates) ?>;
   
   // Llenar campos de tarifas con datos existentes
   existingRates.forEach(rate => {
       if (rate.rate_type === 'hour') {
           document.getElementById('rate-hour').value = rate.price;
       } else if (rate.rate_type === 'half_hour') {
           document.getElementById('rate-half-hour').value = rate.price;
       } else if (rate.rate_type === 'extra') {
           document.getElementById('rate-extra').value = rate.price;
           document.getElementById('rate-extra-desc').value = rate.description;
       }
   });
   
   // Manejar selección de ciudad
   const citySelect = document.getElementById('city');
   const otherCityContainer = document.getElementById('other-city-container');
   const otherCityInput = document.getElementById('other-city');
   
   function toggleOtherCity() {
       if (citySelect.value === 'other') {
           otherCityContainer.style.display = 'block';
           otherCityInput.required = true;
       } else {
           otherCityContainer.style.display = 'none';
           otherCityInput.required = false;
       }
   }
   
   // Inicializar
   toggleOtherCity();
   
   // Eventos
   citySelect.addEventListener('change', toggleOtherCity);
   
   // Envío del formulario
   const profileForm = document.getElementById('profile-form');
   const submitBtn = document.getElementById('submit-btn');
   const formError = document.getElementById('form-error');
   
   profileForm.addEventListener('submit', function(e) {
       e.preventDefault();
       
       // Resetear mensajes de error
       document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
       document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
       formError.classList.add('d-none');
       formError.textContent = '';
       
       // Recopilar tarifas
       const rates = [];
       
       const rateHour = document.getElementById('rate-hour').value;
       if (rateHour && parseFloat(rateHour) > 0) {
           rates.push({
               rate_type: 'hour',
               price: parseFloat(rateHour),
               description: '1 hora'
           });
       }
       
       const rateHalfHour = document.getElementById('rate-half-hour').value;
       if (rateHalfHour && parseFloat(rateHalfHour) > 0) {
           rates.push({
               rate_type: 'half_hour',
               price: parseFloat(rateHalfHour),
               description: 'Media hora'
           });
       }
       
       const rateExtra = document.getElementById('rate-extra').value;
       const rateExtraDesc = document.getElementById('rate-extra-desc').value;
       if (rateExtra && parseFloat(rateExtra) > 0) {
           rates.push({
               rate_type: 'extra',
               price: parseFloat(rateExtra),
               description: rateExtraDesc || 'Extra'
           });
       }
       
       // Guardar tarifas en el campo oculto
       document.getElementById('rates-json').value = JSON.stringify(rates);
       
       // Manejar ciudad personalizada
       if (citySelect.value === 'other') {
           const customCity = otherCityInput.value.trim();
           if (customCity) {
               citySelect.value = customCity;
           } else {
               document.getElementById('city-error').textContent = 'Debe especificar la ciudad';
               citySelect.classList.add('is-invalid');
               return;
           }
       }
       
       // Cambiar estado del botón
       submitBtn.disabled = true;
       submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
       
       // Enviar formulario
       const formData = new FormData(profileForm);
       
       fetch(profileForm.action, {
           method: 'POST',
           body: formData
       })
       .then(response => response.json())
       .then(data => {
           if (data.errors) {
               // Mostrar errores de validación
               Object.keys(data.errors).forEach(field => {
                   const input = document.getElementById(field);
                   const error = document.getElementById(field + '-error');
                   
                   if (input && error) {
                       input.classList.add('is-invalid');
                       error.textContent = data.errors[field];
                   }
               });
               
               // Restaurar botón
               submitBtn.disabled = false;
               submitBtn.innerHTML = '<i class="fas fa-save"></i> Guardar Perfil';
               
           } else if (data.error) {
               // Mostrar error general
               formError.classList.remove('d-none');
               formError.textContent = data.error;
               
               // Restaurar botón
               submitBtn.disabled = false;
               submitBtn.innerHTML = '<i class="fas fa-save"></i> Guardar Perfil';
               
           } else if (data.success) {
               // Redireccionar
               if (data.redirect) {
                   window.location.href = data.redirect;
               }
           }
       })
       .catch(error => {
           console.error('Error:', error);
           
           // Mostrar error de conexión
           formError.classList.remove('d-none');
           formError.textContent = 'Error de conexión. Intente nuevamente.';
           
           // Restaurar botón
           submitBtn.disabled = false;
           submitBtn.innerHTML = '<i class="fas fa-save"></i> Guardar Perfil';
       });
   });
});
</script>