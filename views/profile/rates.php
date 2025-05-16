<!-- Contenido principal -->
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Gestionar Tarifas</h3>
            </div>
            <!-- /.card-header -->
            
            <!-- form start -->
            <form id="rates-form" method="post" action="<?= url('/usuario/tarifas/guardar') ?>">
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="icon fas fa-info-circle"></i>
                        Configure sus tarifas para que los clientes conozcan sus precios. Puede definir tarifas 
                        para diferentes servicios o duraciones.
                    </div>
                    
                    <div id="rates-container">
                        <?php
                        // Mapear tarifas existentes por tipo
                        $ratesByType = [];
                        foreach ($rates as $rate) {
                            $ratesByType[$rate['rate_type']] = $rate;
                        }
                        ?>
                        
                        <!-- Tarifa por hora -->
                        <div class="card card-outline card-secondary mb-4">
                            <div class="card-header">
                                <h3 class="card-title">Tarifa por Hora</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="rate-hour">Precio (S/.)</label>
                                            <input type="number" class="form-control" id="rate-hour" 
                                                   placeholder="Ej: 150" min="0" step="1" 
                                                   value="<?= isset($ratesByType['hour']) ? $ratesByType['hour']['price'] : '' ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="rate-hour-desc">Descripción (opcional)</label>
                                            <input type="text" class="form-control" id="rate-hour-desc" 
                                                   placeholder="Ej: Incluye hotel" 
                                                   value="<?= isset($ratesByType['hour']) ? $ratesByType['hour']['description'] : '' ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tarifa por media hora -->
                        <div class="card card-outline card-secondary mb-4">
                            <div class="card-header">
                                <h3 class="card-title">Tarifa por Media Hora</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="rate-half-hour">Precio (S/.)</label>
                                            <input type="number" class="form-control" id="rate-half-hour" 
                                                   placeholder="Ej: 80" min="0" step="1" 
                                                   value="<?= isset($ratesByType['half_hour']) ? $ratesByType['half_hour']['price'] : '' ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="rate-half-hour-desc">Descripción (opcional)</label>
                                            <input type="text" class="form-control" id="rate-half-hour-desc" 
                                                   placeholder="Ej: Servicio rápido" 
                                                   value="<?= isset($ratesByType['half_hour']) ? $ratesByType['half_hour']['description'] : '' ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tarifa extra -->
                        <div class="card card-outline card-secondary mb-4">
                            <div class="card-header">
                                <h3 class="card-title">Tarifa Extra/Especial</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="rate-extra">Precio (S/.)</label>
                                            <input type="number" class="form-control" id="rate-extra" 
                                                   placeholder="Ej: 300" min="0" step="1" 
                                                   value="<?= isset($ratesByType['extra']) ? $ratesByType['extra']['price'] : '' ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="rate-extra-desc">Descripción</label>
                                            <input type="text" class="form-control" id="rate-extra-desc" 
                                                   placeholder="Ej: Toda la noche, salidas, etc." 
                                                   value="<?= isset($ratesByType['extra']) ? $ratesByType['extra']['description'] : '' ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Botón para añadir tarifa personalizada -->
                        <div class="text-center mb-4">
                            <button type="button" class="btn btn-outline-primary" id="add-custom-rate">
                                <i class="fas fa-plus-circle"></i> Añadir Tarifa Personalizada
                            </button>
                        </div>
                        
                        <!-- Tarifas personalizadas -->
                        <div id="custom-rates-container">
                            <?php foreach ($rates as $rate): ?>
                                <?php if (!in_array($rate['rate_type'], ['hour', 'half_hour', 'extra'])): ?>
                                    <div class="card card-outline card-secondary mb-4 custom-rate">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h3 class="card-title">Tarifa Personalizada</h3>
                                            <button type="button" class="btn btn-sm btn-danger remove-rate">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Tipo</label>
                                                        <input type="text" class="form-control custom-rate-type" 
                                                               placeholder="Ej: noche_completa" 
                                                               value="<?= $rate['rate_type'] ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Precio (S/.)</label>
                                                        <input type="number" class="form-control custom-rate-price" 
                                                               placeholder="Ej: 500" min="0" step="1" 
                                                               value="<?= $rate['price'] ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Descripción</label>
                                                        <input type="text" class="form-control custom-rate-desc" 
                                                               placeholder="Ej: Noche completa" 
                                                               value="<?= $rate['description'] ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                        
                        <input type="hidden" name="rates" id="rates-json" value="">
                    </div>
                </div>
                <!-- /.card-body -->
                
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary" id="submit-btn">
                        <i class="fas fa-save"></i> Guardar Tarifas
                    </button>                   
                    <a href="<?= url('/usuario/dashboard') ?>" class="btn btn-success">
                        Cancelar
                    </a>
                    <a href="<?= url('/usuario/dashboard') ?>" class="btn btn-primary float-right">
                        Ir a Mi Cuenta
                    </a>
                </div>
            </form>
        </div>
        <!-- /.card -->
    </div>
</div>

<div class="alert alert-danger mt-3 d-none" id="form-error"></div>

<!-- Template para nuevas tarifas personalizadas -->
<template id="custom-rate-template">
    <div class="card card-outline card-secondary mb-4 custom-rate">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Tarifa Personalizada</h3>
            <button type="button" class="btn btn-sm btn-danger remove-rate">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Tipo</label>
                        <input type="text" class="form-control custom-rate-type" 
                               placeholder="Ej: noche_completa">
                        <small class="form-text text-muted">Identificador único (sin espacios)</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Precio (S/.)</label>
                        <input type="number" class="form-control custom-rate-price" 
                               placeholder="Ej: 500" min="0" step="1">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Descripción</label>
                        <input type="text" class="form-control custom-rate-desc" 
                               placeholder="Ej: Noche completa">
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ratesForm = document.getElementById('rates-form');
    const submitBtn = document.getElementById('submit-btn');
    const formError = document.getElementById('form-error');
    const customRatesContainer = document.getElementById('custom-rates-container');
    const addCustomRateBtn = document.getElementById('add-custom-rate');
    const customRateTemplate = document.getElementById('custom-rate-template');
    
    // Añadir tarifa personalizada
    addCustomRateBtn.addEventListener('click', function() {
        const newRate = document.importNode(customRateTemplate.content, true);
        customRatesContainer.appendChild(newRate);
        
        // Añadir evento para eliminar
        const removeBtn = customRatesContainer.querySelector('.custom-rate:last-child .remove-rate');
        removeBtn.addEventListener('click', function() {
            this.closest('.custom-rate').remove();
        });
    });
    
    // Añadir evento a los botones de eliminar existentes
    document.querySelectorAll('.remove-rate').forEach(btn => {
        btn.addEventListener('click', function() {
            this.closest('.custom-rate').remove();
        });
    });
    
    // Envío del formulario
    ratesForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Resetear mensajes de error
        formError.classList.add('d-none');
        formError.textContent = '';
        
        // Recopilar tarifas
        const rates = [];
        
        // Tarifa por hora
        const rateHour = document.getElementById('rate-hour').value;
        const rateHourDesc = document.getElementById('rate-hour-desc').value;
        if (rateHour && parseFloat(rateHour) > 0) {
            rates.push({
                rate_type: 'hour',
                price: parseFloat(rateHour),
                description: rateHourDesc || '1 hora'
            });
        }
        
        // Tarifa por media hora
        const rateHalfHour = document.getElementById('rate-half-hour').value;
        const rateHalfHourDesc = document.getElementById('rate-half-hour-desc').value;
        if (rateHalfHour && parseFloat(rateHalfHour) > 0) {
            rates.push({
                rate_type: 'half_hour',
                price: parseFloat(rateHalfHour),
                description: rateHalfHourDesc || 'Media hora'
            });
        }
        
        // Tarifa extra
        const rateExtra = document.getElementById('rate-extra').value;
        const rateExtraDesc = document.getElementById('rate-extra-desc').value;
        if (rateExtra && parseFloat(rateExtra) > 0) {
            rates.push({
                rate_type: 'extra',
                price: parseFloat(rateExtra),
                description: rateExtraDesc || 'Extra'
            });
        }
        
        // Tarifas personalizadas
        document.querySelectorAll('.custom-rate').forEach(rateEl => {
            const type = rateEl.querySelector('.custom-rate-type').value;
            const price = rateEl.querySelector('.custom-rate-price').value;
            const desc = rateEl.querySelector('.custom-rate-desc').value;
            
            if (type && price && parseFloat(price) > 0) {
                rates.push({
                    rate_type: type,
                    price: parseFloat(price),
                    description: desc || type
                });
            }
        });
        
        // Validar que haya al menos una tarifa
        if (rates.length === 0) {
            formError.classList.remove('d-none');
            formError.textContent = 'Debe definir al menos una tarifa';
            return;
        }
        
        // Guardar tarifas en el campo oculto
        document.getElementById('rates-json').value = JSON.stringify(rates);
        
        // Cambiar estado del botón
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
        
        // Enviar formulario
        const formData = new FormData(ratesForm);
        
        fetch(ratesForm.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.errors) {
                // Mostrar errores de validación
                formError.classList.remove('d-none');
                formError.textContent = Object.values(data.errors).join('. ');
                
            } else if (data.error) {
                // Mostrar error general
                formError.classList.remove('d-none');
                formError.textContent = data.error;
                
            } else if (data.success) {
                // Mostrar mensaje de éxito
                formError.classList.remove('d-none');
                formError.classList.remove('alert-danger');
                formError.classList.add('alert-success');
                formError.textContent = data.message || 'Tarifas guardadas correctamente';
                
                // Redireccionar si es necesario
                if (data.redirect) {
                    setTimeout(function() {
                        window.location.href = data.redirect;
                    }, 1500);
                }
            }
            
            // Restaurar botón
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-save"></i> Guardar Tarifas';
        })
        .catch(error => {
            console.error('Error:', error);
            
            // Mostrar error de conexión
            formError.classList.remove('d-none');
            formError.textContent = 'Error de conexión. Intente nuevamente.';
            
            // Restaurar botón
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-save"></i> Guardar Tarifas';
        });
    });
});
</script>