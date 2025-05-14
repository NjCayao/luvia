<!-- Filtros -->
<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Filtros</h3>
            </div>
            <div class="card-body">
                <form action="<?= url('/admin/suscripciones') ?>" method="GET" class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status">Estado</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">Todos</option>
                                <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Activas</option>
                                <option value="trial" <?= $status === 'trial' ? 'selected' : '' ?>>Prueba</option>
                                <option value="expired" <?= $status === 'expired' ? 'selected' : '' ?>>Expiradas</option>
                                <option value="cancelled" <?= $status === 'cancelled' ? 'selected' : '' ?>>Canceladas</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="user_type">Tipo de usuario</label>
                            <select name="user_type" id="user_type" class="form-control">
                                <option value="">Todos</option>
                                <option value="advertiser" <?= $userType === 'advertiser' ? 'selected' : '' ?>>Anunciantes</option>
                                <option value="visitor" <?= $userType === 'visitor' ? 'selected' : '' ?>>Visitantes</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="search">Buscar</label>
                            <input type="text" name="search" id="search" class="form-control" placeholder="Email, teléfono..." value="<?= htmlspecialchars($search) ?>">
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="form-group mb-0 w-100">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-search"></i> Filtrar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Resumen de suscripciones -->
<div class="row">
    <div class="col-md-3">
        <div class="small-box bg-success">
            <div class="inner">
                <h3><?= $stats['active_count'] ?></h3>
                <p>Suscripciones Activas</p>
            </div>
            <div class="icon">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="small-box bg-info">
            <div class="inner">
                <h3><?= $stats['trial_count'] ?></h3>
                <p>Períodos de Prueba</p>
            </div>
            <div class="icon">
                <i class="fas fa-hourglass-half"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3><?= $stats['expired_count'] ?></h3>
                <p>Suscripciones Expiradas</p>
            </div>
            <div class="icon">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="small-box bg-secondary">
            <div class="inner">
                <h3><?= $stats['auto_renew_count'] ?></h3>
                <p>Renovación Automática</p>
            </div>
            <div class="icon">
                <i class="fas fa-sync-alt"></i>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de suscripciones -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-sync-alt mr-1"></i>
            Listado de Suscripciones
        </h3>
        <div class="card-tools">
            <span class="badge badge-info"><?= $totalSubscriptions ?> suscripciones encontradas</span>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body p-0">
        <table class="table table-hover table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Usuario</th>
                    <th>Plan</th>
                    <th>Estado</th>
                    <th>Inicio</th>
                    <th>Fin</th>
                    <th>Renovación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($subscriptions)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-3">No se encontraron suscripciones con los filtros seleccionados</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($subscriptions as $subscription): ?>
                        <tr>
                            <td><?= $subscription['id'] ?></td>
                            <td>
                                <a href="<?= url('/admin/usuario/' . $subscription['user_id']) ?>">
                                    <?= htmlspecialchars($subscription['user_email'] ?? $subscription['user_phone'] ?? 'Usuario ' . $subscription['user_id']) ?>
                                </a>
                            </td>
                            <td><?= htmlspecialchars($subscription['plan_name'] ?? 'Plan ' . $subscription['plan_id']) ?></td>
                            <td>
                                <span class="badge 
                                    <?= $subscription['status'] === 'active' ? 'badge-success' : 
                                        ($subscription['status'] === 'trial' ? 'badge-info' : 
                                        ($subscription['status'] === 'expired' ? 'badge-warning' : 'badge-secondary')) 
                                    ?>">
                                    <?= $subscription['status'] === 'active' ? 'Activa' : 
                                       ($subscription['status'] === 'trial' ? 'Prueba' : 
                                       ($subscription['status'] === 'expired' ? 'Expirada' : 'Cancelada')) 
                                    ?>
                                </span>
                            </td>
                            <td><?= formatDate($subscription['start_date']) ?></td>
                            <td><?= formatDate($subscription['end_date']) ?></td>
                            <td>
                                <?php if ($subscription['auto_renew']): ?>
                                    <span class="badge badge-success">Automática</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Manual</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="<?= url('/admin/suscripcion/' . $subscription['id']) ?>" class="btn btn-sm btn-info" title="Ver detalle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if ($subscription['status'] === 'active'): ?>
                                        <button type="button" class="btn btn-sm btn-warning cancel-subscription-btn" 
                                                data-id="<?= $subscription['id'] ?>" 
                                                title="Cancelar suscripción">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
    
    <?php if ($totalPages > 1): ?>
    <div class="card-footer clearfix">
        <ul class="pagination pagination-sm m-0 float-right">
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= url('/admin/suscripciones?' . http_build_query(array_merge($_GET, ['page' => 1]))) ?>">«</a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="<?= url('/admin/suscripciones?' . http_build_query(array_merge($_GET, ['page' => $page - 1]))) ?>">‹</a>
                </li>
            <?php endif; ?>
            
            <?php
            $startPage = max(1, $page - 2);
            $endPage = min($totalPages, $page + 2);
            
            for ($i = $startPage; $i <= $endPage; $i++):
            ?>
                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                    <a class="page-link" href="<?= url('/admin/suscripciones?' . http_build_query(array_merge($_GET, ['page' => $i]))) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
            
            <?php if ($page < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= url('/admin/suscripciones?' . http_build_query(array_merge($_GET, ['page' => $page + 1]))) ?>">›</a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="<?= url('/admin/suscripciones?' . http_build_query(array_merge($_GET, ['page' => $totalPages]))) ?>">»</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
    <?php endif; ?>
</div>
<!-- /.card -->

<!-- Modal para cancelar suscripción -->
<div class="modal fade" id="cancelSubscriptionModal" tabindex="-1" role="dialog" aria-labelledby="cancelSubscriptionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelSubscriptionModalLabel">Cancelar Suscripción</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro que deseas cancelar esta suscripción?</p>
                <p>La suscripción se mantendrá activa hasta la fecha de finalización, pero no se renovará automáticamente.</p>
                <form id="cancelSubscriptionForm" method="POST" action="<?= url('/admin/suscripcion/cancelar') ?>">
                    <input type="hidden" name="csrf_token" value="<?= getCsrfToken() ?>">
                    <input type="hidden" name="subscription_id" id="subscriptionId" value="">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning" id="confirmCancelSubscription">Confirmar Cancelación</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manejar botones de cancelación de suscripción
    document.querySelectorAll('.cancel-subscription-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const subscriptionId = this.getAttribute('data-id');
            document.getElementById('subscriptionId').value = subscriptionId;
            $('#cancelSubscriptionModal').modal('show');
        });
    });
    
    // Confirmar cancelación
    document.getElementById('confirmCancelSubscription').addEventListener('click', function() {
        document.getElementById('cancelSubscriptionForm').submit();
    });
});
</script>