<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-credit-card mr-1"></i>
            Detalles del Pago #<?= $payment['id'] ?>
        </h3>
        <div class="card-tools">
            <a href="<?= url('/admin/pagos') ?>" class="btn btn-tool">
                <i class="fas fa-arrow-left"></i> Volver a la lista
            </a>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <?php if (!$payment): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> Pago no encontrado
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-md-6">
                    <!-- Detalles del pago -->
                    <div class="card">
                        <div class="card-header bg-primary">
                            <h3 class="card-title">Información del Pago</h3>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>ID:</b> <span class="float-right"><?= $payment['id'] ?></span>
                                </li>
                                <li class="list-group-item">
                                    <b>Monto:</b> <span class="float-right">S/. <?= number_format($payment['amount'], 2) ?></span>
                                </li>
                                <li class="list-group-item">
                                    <b>Método:</b> <span class="float-right"><?= ucfirst($payment['payment_method']) ?></span>
                                </li>
                                <li class="list-group-item">
                                    <b>Estado:</b> 
                                    <span class="float-right badge 
                                        <?= $payment['payment_status'] === 'completed' ? 'badge-success' : 
                                           ($payment['payment_status'] === 'pending' ? 'badge-warning' : 
                                           ($payment['payment_status'] === 'processing' ? 'badge-info' : 'badge-danger')) ?>">
                                        <?= ucfirst($payment['payment_status']) ?>
                                    </span>
                                </li>
                                <li class="list-group-item">
                                    <b>Fecha:</b> <span class="float-right"><?= formatDate($payment['created_at']) ?></span>
                                </li>
                                <li class="list-group-item">
                                    <b>Orden:</b> <span class="float-right"><?= htmlspecialchars($payment['order_id']) ?></span>
                                </li>
                                <?php if (!empty($payment['transaction_id'])): ?>
                                    <li class="list-group-item">
                                        <b>ID de Transacción:</b> <span class="float-right"><?= htmlspecialchars($payment['transaction_id']) ?></span>
                                    </li>
                                <?php endif; ?>
                                <?php if (!empty($payment['izipay_session_id'])): ?>
                                    <li class="list-group-item">
                                        <b>ID de Sesión (Izipay):</b> <span class="float-right"><?= htmlspecialchars($payment['izipay_session_id']) ?></span>
                                    </li>
                                <?php endif; ?>
                                <?php if (!empty($payment['error_message'])): ?>
                                    <li class="list-group-item">
                                        <b>Mensaje de Error:</b> <span class="float-right text-danger"><?= htmlspecialchars($payment['error_message']) ?></span>
                                    </li>
                                <?php endif; ?>
                            </ul>
                            
                            <?php if ($payment['payment_status'] === 'pending' || $payment['payment_status'] === 'processing'): ?>
                                <div class="btn-group w-100">
                                    <button type="button" class="btn btn-success update-payment-btn" 
                                            data-id="<?= $payment['id'] ?>" 
                                            data-status="completed"
                                            title="Marcar como completado">
                                        <i class="fas fa-check"></i> Marcar como Completado
                                    </button>
                                    <button type="button" class="btn btn-danger update-payment-btn" 
                                            data-id="<?= $payment['id'] ?>" 
                                            data-status="failed"
                                            title="Marcar como fallido">
                                        <i class="fas fa-times"></i> Marcar como Fallido
                                    </button>
                                </div>
                            <?php endif; ?>
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
            
            <!-- Suscripción relacionada -->
            <?php
            // Buscar suscripción relacionada con este pago
            $relatedSubscription = null;
            if (!empty($subscriptions)) {
                foreach ($subscriptions as $sub) {
                    if ($sub['payment_id'] == $payment['id']) {
                        $relatedSubscription = $sub;
                        break;
                    }
                }
            }
            ?>
            
            <?php if ($relatedSubscription): ?>
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header bg-warning">
                                <h3 class="card-title">Suscripción Asociada</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <p><strong>ID de Suscripción:</strong> <?= $relatedSubscription['id'] ?></p>
                                        <p><strong>Estado:</strong> 
                                            <span class="badge 
                                                <?= $relatedSubscription['status'] === 'active' ? 'badge-success' : 
                                                   ($relatedSubscription['status'] === 'trial' ? 'badge-info' : 'badge-secondary') ?>">
                                                <?= ucfirst($relatedSubscription['status']) ?>
                                            </span>
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        <p><strong>Inicio:</strong> <?= formatDate($relatedSubscription['start_date']) ?></p>
                                        <p><strong>Fin:</strong> <?= formatDate($relatedSubscription['end_date']) ?></p>
                                    </div>
                                    <div class="col-md-4">
                                        <p><strong>Auto-renovación:</strong> 
                                            <?= $relatedSubscription['auto_renew'] ? 'Activada' : 'Desactivada' ?>
                                        </p>
                                        <a href="<?= url('/admin/suscripcion/' . $relatedSubscription['id']) ?>" class="btn btn-info">
                                            <i class="fas fa-eye"></i> Ver Suscripción
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

<!-- Modal para actualizar estado de pago -->
<div class="modal fade" id="updatePaymentModal" tabindex="-1" role="dialog" aria-labelledby="updatePaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updatePaymentModalLabel">Actualizar Estado de Pago</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="updatePaymentMessage">¿Estás seguro que deseas cambiar el estado de este pago?</p>
                <form id="updatePaymentForm" method="POST" action="<?= url('/admin/pago/actualizar-estado') ?>">
                    <input type="hidden" name="csrf_token" value="<?= getCsrfToken() ?>">
                    <input type="hidden" name="payment_id" id="paymentId" value="">
                    <input type="hidden" name="payment_status" id="paymentStatus" value="">
                    <div class="form-group d-none" id="errorMessageGroup">
                        <label for="errorMessage">Mensaje de error (opcional):</label>
                        <textarea class="form-control" id="errorMessage" name="error_message" rows="3" placeholder="Detalles sobre el motivo del fallo"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmUpdatePayment">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manejar botones de actualización de pago
    document.querySelectorAll('.update-payment-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const paymentId = this.getAttribute('data-id');
            const newStatus = this.getAttribute('data-status');
            
            document.getElementById('paymentId').value = paymentId;
            document.getElementById('paymentStatus').value = newStatus;
            
            let message = '';
            if (newStatus === 'completed') {
                message = '¿Estás seguro que deseas marcar este pago como COMPLETADO?';
                document.getElementById('errorMessageGroup').classList.add('d-none');
            } else if (newStatus === 'failed') {
                message = '¿Estás seguro que deseas marcar este pago como FALLIDO?';
                document.getElementById('errorMessageGroup').classList.remove('d-none');
            }
            
            document.getElementById('updatePaymentMessage').textContent = message;
            
            $('#updatePaymentModal').modal('show');
        });
    });
    
    // Confirmar actualización de pago
    document.getElementById('confirmUpdatePayment').addEventListener('click', function() {
        document.getElementById('updatePaymentForm').submit();
    });
});
</script>