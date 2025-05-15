<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-sync-alt mr-1"></i>
            Detalles de Suscripción #<?= $subscription['id'] ?>
        </h3>
        <div class="card-tools">
            <a href="<?= url('/admin/suscripciones') ?>" class="btn btn-tool">
                <i class="fas fa-arrow-left"></i> Volver a la lista
            </a>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <?php if (!$subscription): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> Suscripción no encontrada
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-md-6">
                    <!-- Detalles de la suscripción -->
                    <div class="card">
                        <div class="card-header bg-primary">
                            <h3 class="card-title">Información de la Suscripción</h3>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>ID:</b> <span class="float-right"><?= $subscription['id'] ?></span>
                                </li>
                                <li class="list-group-item">
                                    <b>Estado:</b>
                                    <span class="float-right badge 
                                        <?= $subscription['status'] === 'active' ? 'badge-success' : ($subscription['status'] === 'trial' ? 'badge-info' : ($subscription['status'] === 'expired' ? 'badge-warning' : 'badge-secondary')) ?>">
                                        <?= ucfirst($subscription['status']) ?>
                                    </span>
                                </li>
                                <li class="list-group-item">
                                    <b>Fecha de inicio:</b> <span class="float-right"><?= formatDate($subscription['start_date']) ?></span>
                                </li>
                                <li class="list-group-item">
                                    <b>Fecha de fin:</b> <span class="float-right"><?= formatDate($subscription['end_date']) ?></span>
                                </li>
                                <li class="list-group-item">
                                    <b>Auto-renovación:</b>
                                    <span class="float-right badge <?= $subscription['auto_renew'] ? 'badge-success' : 'badge-secondary' ?>">
                                        <?= $subscription['auto_renew'] ? 'Activada' : 'Desactivada' ?>
                                    </span>
                                </li>
                                <li class="list-group-item">
                                    <b>Creada:</b> <span class="float-right"><?= formatDate($subscription['created_at']) ?></span>
                                </li>
                                <li class="list-group-item">
                                    <b>Última actualización:</b> <span class="float-right"><?= formatDate($subscription['updated_at']) ?></span>
                                </li>
                            </ul>

                            <?php
                            // Calcular días restantes
                            $now = new DateTime();
                            $endDate = new DateTime($subscription['end_date']);
                            $daysLeft = $subscription['status'] === 'active' || $subscription['status'] === 'trial'
                                ? max(0, $now->diff($endDate)->days) : 0;

                            // Calcular progreso
                            $startDate = new DateTime($subscription['start_date']);
                            $totalDays = $startDate->diff($endDate)->days;
                            $daysUsed = $totalDays - $daysLeft;
                            $percent = $totalDays > 0 ? ($daysUsed / $totalDays) * 100 : 0;
                            ?>

                            <?php if ($subscription['status'] === 'active' || $subscription['status'] === 'trial'): ?>
                                <div class="progress-group">
                                    <span class="progress-text">Progreso de suscripción</span>
                                    <span class="float-right">
                                        <b><?= $daysUsed ?></b>/<?= $totalDays ?> días (<?= $daysLeft ?> días restantes)
                                    </span>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-primary" style="width: <?= $percent ?>%"></div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="mt-3">
                                <?php if ($subscription['status'] === 'active' || $subscription['status'] === 'trial'): ?>
                                    <button class="btn btn-warning btn-block toggle-status-btn" data-id="<?= $subscription['id'] ?>" data-status="expired">
                                        <i class="fas fa-ban"></i> Marcar como Expirada
                                    </button>
                                <?php elseif ($subscription['status'] === 'expired'): ?>
                                    <button class="btn btn-success btn-block toggle-status-btn" data-id="<?= $subscription['id'] ?>" data-status="active">
                                        <i class="fas fa-check"></i> Reactivar Suscripción
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <!-- Información del usuario -->
                    <div class="card mb-4">
                        <div class="card-header bg-info">
                            <h3 class="card-title">Información del Usuario</h3>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>ID del Usuario:</b> <span class="float-right"><?= $user['id'] ?></span>
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
                            </ul>
                            <a href="<?= url('/admin/usuario/' . $user['id']) ?>" class="btn btn-primary btn-block">
                                <i class="fas fa-user"></i> Ver Usuario
                            </a>
                        </div>
                    </div>

                    <!-- Información del plan -->
                    <div class="card">
                        <div class="card-header bg-success">
                            <h3 class="card-title">Información del Plan</h3>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>ID del Plan:</b> <span class="float-right"><?= $plan['id'] ?></span>
                                </li>
                                <li class="list-group-item">
                                    <b>Nombre:</b> <span class="float-right"><?= htmlspecialchars($plan['name']) ?></span>
                                </li>
                                <li class="list-group-item">
                                    <b>Tipo:</b>
                                    <span class="float-right">
                                        <?= $plan['user_type'] === 'advertiser' ? 'Anunciante' : 'Visitante' ?>
                                    </span>
                                </li>
                                <li class="list-group-item">
                                    <b>Duración:</b> <span class="float-right"><?= $plan['duration'] ?> días</span>
                                </li>
                                <li class="list-group-item">
                                    <b>Precio:</b> <span class="float-right">S/. <?= number_format($plan['price'], 2) ?></span>
                                </li>
                                <?php if ($plan['user_type'] === 'advertiser'): ?>
                                    <li class="list-group-item">
                                        <b>Máximo de fotos:</b> <span class="float-right"><?= $plan['max_photos'] ?></span>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Máximo de videos:</b> <span class="float-right"><?= $plan['max_videos'] ?></span>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pago relacionado -->
            <?php if ($payment): ?>
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header bg-warning">
                                <h3 class="card-title">Pago Asociado</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <p><strong>ID de Pago:</strong> <?= $payment['id'] ?></p>
                                        <p><strong>Monto:</strong> S/. <?= number_format($payment['amount'], 2) ?></p>
                                    </div>
                                    <div class="col-md-4">
                                        <p><strong>Método:</strong> <?= ucfirst($payment['payment_method']) ?></p>
                                        <p><strong>Estado:</strong>
                                            <span class="badge 
                                                <?= $payment['payment_status'] === 'completed' ? 'badge-success' : ($payment['payment_status'] === 'pending' ? 'badge-warning' : ($payment['payment_status'] === 'processing' ? 'badge-info' : 'badge-danger')) ?>">
                                                <?= ucfirst($payment['payment_status']) ?>
                                            </span>
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        <p><strong>Fecha:</strong> <?= formatDate($payment['created_at']) ?></p>
                                        <a href="<?= url('/admin/pago/' . $payment['id']) ?>" class="btn btn-info">
                                            <i class="fas fa-eye"></i> Ver Pago
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Modal para cambiar estado de suscripción -->
<div class="modal fade" id="subscriptionStatusModal" tabindex="-1" role="dialog" aria-labelledby="subscriptionStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="subscriptionStatusModalLabel">Cambiar Estado de Suscripción</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="subscriptionStatusMessage">¿Estás seguro que deseas cambiar el estado de esta suscripción?</p>
                <form id="subscriptionStatusForm" method="POST" action="<?= url('/admin/suscripcion/cambiar-estado') ?>" style="display: none;">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                    <input type="hidden" name="subscription_id" id="subscriptionId" value="<?= $subscription['id'] ?>">
                    <input type="hidden" name="status" id="subscriptionStatus" value="">
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
                const subscriptionId = this.getAttribute('data-id');
                const newStatus = this.getAttribute('data-status');

                document.getElementById('subscriptionId').value = subscriptionId;
                document.getElementById('subscriptionStatus').value = newStatus;

                let message = '';
                if (newStatus === 'active') {
                    message = '¿Estás seguro que deseas reactivar esta suscripción?';
                } else if (newStatus === 'expired') {
                    message = '¿Estás seguro que deseas marcar esta suscripción como expirada?';
                }

                document.getElementById('subscriptionStatusMessage').textContent = message;

                $('#subscriptionStatusModal').modal('show');
            });
        });

        // Confirmar cambio de estado
        document.getElementById('confirmStatusChange').addEventListener('click', function() {
            document.getElementById('subscriptionStatusForm').submit();
        });
    });
</script>