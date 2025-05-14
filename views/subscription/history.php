<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><?= $pageHeader ?></h2>
            <p class="lead">Revisa el historial completo de tus suscripciones y pagos</p>
        </div>
        <div class="col-md-4 text-right">
            <a href="<?= url('/pago/planes') ?>" class="btn btn-primary">
                <i class="fas fa-shopping-cart"></i> Ver Planes
            </a>
        </div>
    </div>
    
    <?php if ($activeSubscription): ?>
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card border-success">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0"><i class="fas fa-check-circle"></i> Suscripción Activa</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h5><?= htmlspecialchars($activeSubscription['plan_name']) ?></h5>
                                <p class="mb-1">
                                    <strong>Estado:</strong> 
                                    <span class="badge badge-success">Activa</span>
                                </p>
                                <p class="mb-1">
                                    <strong>Período:</strong> 
                                    <?= formatDate($activeSubscription['start_date']) ?> - 
                                    <?= formatDate($activeSubscription['end_date']) ?>
                                </p>
                                <p class="mb-1">
                                    <strong>Renovación automática:</strong> 
                                    <?php if ($activeSubscription['auto_renew']): ?>
                                        <span class="text-success">Activada</span>
                                    <?php else: ?>
                                        <span class="text-danger">Desactivada</span>
                                    <?php endif; ?>
                                </p>
                                <p class="mb-0">
                                    <strong>Precio:</strong> 
                                    S/. <?= number_format($activeSubscription['price'], 2) ?>
                                </p>
                            </div>
                            <div class="col-md-4 text-right mt-3">
                                <?php if ($activeSubscription['auto_renew']): ?>
                                    <button class="btn btn-warning cancel-renewal-btn" 
                                            data-subscription-id="<?= $activeSubscription['id'] ?>">
                                        <i class="fas fa-times-circle"></i> Cancelar Renovación
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-success enable-renewal-btn"
                                            data-subscription-id="<?= $activeSubscription['id'] ?>">
                                        <i class="fas fa-sync-alt"></i> Activar Renovación
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <div class="row align-items-center">
                            <div class="col-md-9">
                                <?php
                                $now = new DateTime();
                                $endDate = new DateTime($activeSubscription['end_date']);
                                $daysLeft = $now->diff($endDate)->days;
                                ?>
                                <div class="subscription-progress">
                                    <div class="progress">
                                        <?php
                                        $startDate = new DateTime($activeSubscription['start_date']);
                                        $totalDays = $startDate->diff($endDate)->days;
                                        $daysUsed = $totalDays - $daysLeft;
                                        $percent = ($daysUsed / $totalDays) * 100;
                                        ?>
                                        <div class="progress-bar bg-success" role="progressbar" 
                                             style="width: <?= $percent ?>%" 
                                             aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                    </div>
                                    <div class="text-center mt-1">
                                        Te quedan <strong><?= $daysLeft ?> días</strong> de suscripción
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 text-right">
                                <a href="<?= url('/pago/checkout/' . $activeSubscription['plan_id']) ?>" class="btn btn-outline-primary">
                                    <i class="fas fa-redo"></i> Renovar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Historial Completo</h4>
                </div>
                <div class="card-body">
                    <?php if (empty($subscriptions)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No tienes suscripciones previas.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Plan</th>
                                        <th>Período</th>
                                        <th>Estado</th>
                                        <th>Precio</th>
                                        <th>Renovación</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($subscriptions as $subscription): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($subscription['plan_name']) ?></td>
                                            <td>
                                                <?= formatDate($subscription['start_date'], 'd/m/Y') ?> - 
                                                <?= formatDate($subscription['end_date'], 'd/m/Y') ?>
                                            </td>
                                            <td>
                                                <?php
                                                $statusClass = '';
                                                $statusText = '';
                                                
                                                switch ($subscription['status']) {
                                                    case 'active':
                                                        $statusClass = 'success';
                                                        $statusText = 'Activa';
                                                        break;
                                                    case 'trial':
                                                        $statusClass = 'info';
                                                        $statusText = 'Prueba';
                                                        break;
                                                    case 'expired':
                                                        $statusClass = 'secondary';
                                                        $statusText = 'Expirada';
                                                        break;
                                                    case 'cancelled':
                                                        $statusClass = 'danger';
                                                        $statusText = 'Cancelada';
                                                        break;
                                                    default:
                                                        $statusClass = 'secondary';
                                                        $statusText = $subscription['status'];
                                                }
                                                ?>
                                                <span class="badge badge-<?= $statusClass ?>">
                                                    <?= $statusText ?>
                                                </span>
                                            </td>
                                            <td>S/. <?= number_format($subscription['price'], 2) ?></td>
                                            <td>
                                                <?php if ($subscription['auto_renew']): ?>
                                                    <span class="text-success">Activada</span>
                                                <?php else: ?>
                                                    <span class="text-danger">Desactivada</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($subscription['status'] === 'active'): ?>
                                                    <?php if ($subscription['auto_renew']): ?>
                                                        <button class="btn btn-sm btn-warning cancel-renewal-btn" 
                                                                data-subscription-id="<?= $subscription['id'] ?>">
                                                            Cancelar Renovación
                                                        </button>
                                                    <?php else: ?>
                                                        <button class="btn btn-sm btn-success enable-renewal-btn"
                                                                data-subscription-id="<?= $subscription['id'] ?>">
                                                            Activar Renovación
                                                        </button>
                                                    <?php endif; ?>
                                                <?php elseif ($subscription['status'] === 'expired' || $subscription['status'] === 'cancelled'): ?>
                                                    <a href="<?= url('/usuario/suscripciones/renovar?id=' . $subscription['id']) ?>" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        Renovar
                                                    </a>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación -->
<div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmationTitle">Confirmar Acción</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="confirmationMessage">
                ¿Estás seguro de realizar esta acción?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmActionBtn">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Cancelar renovación automática
    const cancelButtons = document.querySelectorAll('.cancel-renewal-btn');
    cancelButtons.forEach(button => {
        button.addEventListener('click', function() {
            const subscriptionId = this.getAttribute('data-subscription-id');
            
            // Configurar modal de confirmación
            document.getElementById('confirmationTitle').textContent = 'Cancelar Renovación Automática';
            document.getElementById('confirmationMessage').textContent = 
                '¿Estás seguro de que deseas cancelar la renovación automática? ' +
                'Tu suscripción seguirá activa hasta la fecha de finalización, pero no se renovará automáticamente.';
            
            const confirmBtn = document.getElementById('confirmActionBtn');
            confirmBtn.className = 'btn btn-warning';
            confirmBtn.textContent = 'Cancelar Renovación';
            
            // Configurar acción del botón de confirmación
            confirmBtn.onclick = function() {
                const formData = new FormData();
                formData.append('csrf_token', '<?= generateCsrfToken() ?>');
                formData.append('subscription_id', subscriptionId);
                
                fetch('<?= url('/usuario/suscripciones/cancelar') ?>', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    // Cerrar modal
                    $('#confirmationModal').modal('hide');
                    
                    // Recargar página al éxito
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert(data.error || 'Error al cancelar la renovación');
                    }
                })
                .catch(error => {
                    $('#confirmationModal').modal('hide');
                    alert('Error de conexión');
                });
            };
            
            // Mostrar modal
            $('#confirmationModal').modal('show');
        });
    });
    
    // Activar renovación automática
    const enableButtons = document.querySelectorAll('.enable-renewal-btn');
    enableButtons.forEach(button => {
        button.addEventListener('click', function() {
            const subscriptionId = this.getAttribute('data-subscription-id');
            
            // Configurar modal de confirmación
            document.getElementById('confirmationTitle').textContent = 'Activar Renovación Automática';
            document.getElementById('confirmationMessage').textContent = 
                '¿Estás seguro de que deseas activar la renovación automática? ' +
                'Tu suscripción se renovará automáticamente al finalizar el período actual.';
            
            const confirmBtn = document.getElementById('confirmActionBtn');
            confirmBtn.className = 'btn btn-success';
            confirmBtn.textContent = 'Activar Renovación';
            
            // Configurar acción del botón de confirmación
            confirmBtn.onclick = function() {
                const formData = new FormData();
                formData.append('csrf_token', '<?= generateCsrfToken() ?>');
                formData.append('subscription_id', subscriptionId);
                
                fetch('<?= url('/usuario/suscripciones/activar-renovacion') ?>', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    // Cerrar modal
                    $('#confirmationModal').modal('hide');
                    
                    // Recargar página al éxito
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert(data.error || 'Error al activar la renovación');
                    }
                })
                .catch(error => {
                    $('#confirmationModal').modal('hide');
                    alert('Error de conexión');
                });
            };
            
            // Mostrar modal
            $('#confirmationModal').modal('show');
        });
    });
});
</script>