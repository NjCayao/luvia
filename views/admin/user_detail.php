<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-user mr-1"></i>
            Detalles del Usuario
        </h3>
        <div class="card-tools">
            <a href="<?= url('/admin/usuarios') ?>" class="btn btn-tool">
                <i class="fas fa-arrow-left"></i> Volver a la lista
            </a>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <?php if (!$user): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> Usuario no encontrado
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-md-4">
                    <!-- Información básica -->
                    <div class="card">
                        <div class="card-header bg-primary">
                            <h3 class="card-title">Información Básica</h3>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-unbordered">
                                <li class="list-group-item">
                                    <b>ID:</b> <span class="float-right"><?= $user['id'] ?></span>
                                </li>
                                <li class="list-group-item">
                                    <b>Email:</b> <span class="float-right"><?= htmlspecialchars($user['email']) ?></span>
                                </li>
                                <li class="list-group-item">
                                    <b>Teléfono:</b> <span class="float-right"><?= htmlspecialchars($user['phone']) ?></span>
                                </li>
                                <li class="list-group-item">
                                    <b>Tipo:</b> 
                                    <span class="float-right badge <?= $user['user_type'] === 'admin' ? 'badge-danger' : ($user['user_type'] === 'advertiser' ? 'badge-warning' : 'badge-info') ?>">
                                        <?= $user['user_type'] === 'admin' ? 'Admin' : ($user['user_type'] === 'advertiser' ? 'Anunciante' : 'Visitante') ?>
                                    </span>
                                </li>
                                <li class="list-group-item">
                                    <b>Estado:</b> 
                                    <span class="float-right badge <?= $user['status'] === 'active' ? 'badge-success' : ($user['status'] === 'pending' ? 'badge-warning' : 'badge-danger') ?>">
                                        <?= ucfirst($user['status']) ?>
                                    </span>
                                </li>
                                <li class="list-group-item">
                                    <b>Fecha de registro:</b> <span class="float-right"><?= formatDate($user['created_at']) ?></span>
                                </li>
                                <li class="list-group-item">
                                    <b>Último acceso:</b> <span class="float-right"><?= formatDate($user['last_login']) ?></span>
                                </li>
                            </ul>
                        </div>
                        <div class="card-footer">
                            <div class="btn-group w-100">
                                <a href="<?= url('/admin/usuario/' . $user['id'] . '/editar') ?>" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <?php if ($user['user_type'] !== 'admin'): ?>
                                    <button type="button" class="btn btn-<?= $user['status'] === 'active' ? 'warning' : 'success' ?> toggle-status-btn" 
                                            data-id="<?= $user['id'] ?>" 
                                            data-status="<?= $user['status'] === 'active' ? 'suspended' : 'active' ?>"
                                            data-current="<?= $user['status'] ?>">
                                        <i class="fas <?= $user['status'] === 'active' ? 'fa-ban' : 'fa-check' ?>"></i> 
                                        <?= $user['status'] === 'active' ? 'Suspender' : 'Activar' ?>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-8">
                    <?php if ($profile): ?>
                        <!-- Información del perfil -->
                        <div class="card mb-4">
                            <div class="card-header bg-info">
                                <h3 class="card-title">Información del Perfil</h3>
                                <div class="card-tools">
                                    <a href="<?= url('/admin/perfil/' . $profile['id']) ?>" class="btn btn-tool">
                                        <i class="fas fa-eye"></i> Ver perfil completo
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 text-center">
                                        <?php if (!empty($profile['main_photo'])): ?>
                                            <img src="<?= url('uploads/photos/' . $profile['main_photo']) ?>" 
                                                alt="Foto de perfil" class="img-fluid img-thumbnail" style="max-height: 200px;">
                                        <?php else: ?>
                                            <img src="<?= url('img/profile-placeholder.jpg') ?>" 
                                                alt="Sin foto" class="img-fluid img-thumbnail" style="max-height: 200px;">
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-8">
                                        <h4><?= htmlspecialchars($profile['name']) ?></h4>
                                        <p><strong>Género:</strong> <?= ucfirst($profile['gender']) ?></p>
                                        <p><strong>Ciudad:</strong> <?= htmlspecialchars($profile['city']) ?></p>
                                        <p><strong>WhatsApp:</strong> <?= htmlspecialchars($profile['whatsapp']) ?></p>
                                        <p><strong>Verificado:</strong> 
                                            <?php if ($profile['is_verified']): ?>
                                                <span class="badge badge-success">Sí</span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary">No</span>
                                            <?php endif; ?>
                                        </p>
                                        <p><strong>Vistas:</strong> <?= $profile['views'] ?></p>
                                        <p><strong>Contactos:</strong> <?= $profile['whatsapp_clicks'] ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Suscripciones -->
                    <div class="card mb-4">
                        <div class="card-header bg-success">
                            <h3 class="card-title">Suscripciones</h3>
                        </div>
                        <div class="card-body p-0">
                            <?php if (empty($subscriptions)): ?>
                                <div class="p-3">
                                    <p class="text-muted mb-0">Este usuario no tiene suscripciones.</p>
                                </div>
                            <?php else: ?>
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Plan</th>
                                            <th>Estado</th>
                                            <th>Inicio</th>
                                            <th>Fin</th>
                                            <th>Auto-renovación</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($subscriptions as $subscription): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($subscription['plan_name']) ?></td>
                                                <td>
                                                    <span class="badge 
                                                        <?= $subscription['status'] === 'active' ? 'badge-success' : 
                                                           ($subscription['status'] === 'trial' ? 'badge-info' : 'badge-secondary') ?>">
                                                        <?= ucfirst($subscription['status']) ?>
                                                    </span>
                                                </td>
                                                <td><?= formatDate($subscription['start_date']) ?></td>
                                                <td><?= formatDate($subscription['end_date']) ?></td>
                                                <td>
                                                    <?php if ($subscription['auto_renew']): ?>
                                                        <span class="badge badge-success">Activada</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary">Desactivada</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="<?= url('/admin/suscripcion/' . $subscription['id']) ?>" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Pagos -->
                    <div class="card">
                        <div class="card-header bg-warning">
                            <h3 class="card-title">Pagos</h3>
                        </div>
                        <div class="card-body p-0">
                            <?php if (empty($payments)): ?>
                                <div class="p-3">
                                    <p class="text-muted mb-0">Este usuario no ha realizado pagos.</p>
                                </div>
                            <?php else: ?>
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Plan</th>
                                            <th>Monto</th>
                                            <th>Método</th>
                                            <th>Estado</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($payments as $payment): ?>
                                            <tr>
                                                <td><?= formatDate($payment['created_at']) ?></td>
                                                <td><?= htmlspecialchars($payment['plan_name']) ?></td>
                                                <td>S/. <?= number_format($payment['amount'], 2) ?></td>
                                                <td><?= ucfirst($payment['payment_method']) ?></td>
                                                <td>
                                                    <span class="badge 
                                                        <?= $payment['payment_status'] === 'completed' ? 'badge-success' : 
                                                           ($payment['payment_status'] === 'pending' ? 'badge-warning' : 
                                                           ($payment['payment_status'] === 'processing' ? 'badge-info' : 'badge-danger')) ?>">
                                                        <?= ucfirst($payment['payment_status']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="<?= url('/admin/pago/' . $payment['id']) ?>" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal de Cambio de Estado -->
<div class="modal fade" id="statusModal" tabindex="-1" role="dialog" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalLabel">Cambiar Estado de Usuario</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="statusMessage">¿Estás seguro que deseas cambiar el estado de este usuario?</p>
                <form id="statusForm" method="POST" action="<?= url('/admin/usuario/cambiar-estado') ?>">
                    <input type="hidden" name="csrf_token" value="<?= getCsrfToken() ?>">
                    <input type="hidden" name="user_id" id="userId" value="">
                    <input type="hidden" name="status" id="userStatus" value="">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmStatusChange">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manejar botones de cambio de estado
    document.querySelectorAll('.toggle-status-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const userId = this.getAttribute('data-id');
            const newStatus = this.getAttribute('data-status');
            const currentStatus = this.getAttribute('data-current');
            
            document.getElementById('userId').value = userId;
            document.getElementById('userStatus').value = newStatus;
            
            let message = '';
            if (newStatus === 'active') {
                message = '¿Estás seguro que deseas activar este usuario?';
            } else if (newStatus === 'suspended') {
                message = '¿Estás seguro que deseas suspender este usuario? Esto bloqueará su acceso a la plataforma.';
            }
            
            document.getElementById('statusMessage').textContent = message;
            
            $('#statusModal').modal('show');
        });
    });
    
    // Confirmar cambio de estado
    document.getElementById('confirmStatusChange').addEventListener('click', function() {
        document.getElementById('statusForm').submit();
    });
});
</script>