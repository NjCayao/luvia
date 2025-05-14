<!-- Filtros -->
<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Filtros</h3>
            </div>
            <div class="card-body">
                <form action="<?= url('/admin/pagos') ?>" method="GET" class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="status">Estado</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">Todos</option>
                                <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pendiente</option>
                                <option value="processing" <?= $status === 'processing' ? 'selected' : '' ?>>Procesando</option>
                                <option value="completed" <?= $status === 'completed' ? 'selected' : '' ?>>Completado</option>
                                <option value="failed" <?= $status === 'failed' ? 'selected' : '' ?>>Fallido</option>
                                <option value="refunded" <?= $status === 'refunded' ? 'selected' : '' ?>>Reembolsado</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="method">Método</label>
                            <select name="method" id="method" class="form-control">
                                <option value="">Todos</option>
                                <option value="card" <?= $method === 'card' ? 'selected' : '' ?>>Tarjeta</option>
                                <option value="yape" <?= $method === 'yape' ? 'selected' : '' ?>>Yape</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="start_date">Fecha inicio</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="<?= htmlspecialchars($startDate) ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="end_date">Fecha fin</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" value="<?= htmlspecialchars($endDate) ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="search">Buscar</label>
                            <input type="text" name="search" id="search" class="form-control" placeholder="ID, transacción..." value="<?= htmlspecialchars($search) ?>">
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

<!-- Resumen de pagos -->
<div class="row">
    <div class="col-md-3">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>S/. <?= number_format($stats['total_amount'], 2) ?></h3>
                <p>Total Recaudado</p>
            </div>
            <div class="icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="small-box bg-info">
            <div class="inner">
                <h3><?= $stats['completed_count'] ?></h3>
                <p>Pagos Completados</p>
            </div>
            <div class="icon">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3><?= $stats['pending_count'] ?></h3>
                <p>Pagos Pendientes</p>
            </div>
            <div class="icon">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3><?= $stats['failed_count'] ?></h3>
                <p>Pagos Fallidos</p>
            </div>
            <div class="icon">
                <i class="fas fa-times-circle"></i>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de pagos -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-credit-card mr-1"></i>
            Listado de Pagos
        </h3>
        <div class="card-tools">
            <span class="badge badge-info"><?= $totalPayments ?> pagos encontrados</span>
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
                    <th>Monto</th>
                    <th>Método</th>
                    <th>Estado</th>
                    <th>ID Transacción</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($payments)): ?>
                    <tr>
                        <td colspan="9" class="text-center py-3">No se encontraron pagos con los filtros seleccionados</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($payments as $payment): ?>
                        <tr>
                            <td><?= $payment['id'] ?></td>
                            <td>
                                <a href="<?= url('/admin/usuario/' . $payment['user_id']) ?>">
                                    <?= htmlspecialchars($payment['user_email'] ?? $payment['user_phone'] ?? 'Usuario ' . $payment['user_id']) ?>
                                </a>
                            </td>
                            <td><?= htmlspecialchars($payment['plan_name'] ?? 'Plan ' . $payment['plan_id']) ?></td>
                            <td>S/. <?= number_format($payment['amount'], 2) ?></td>
                            <td><?= ucfirst($payment['payment_method']) ?></td>
                            <td>
                                <span class="badge 
                                    <?= $payment['payment_status'] === 'completed' ? 'badge-success' : 
                                        ($payment['payment_status'] === 'pending' ? 'badge-warning' : 
                                        ($payment['payment_status'] === 'processing' ? 'badge-info' : 
                                        ($payment['payment_status'] === 'refunded' ? 'badge-secondary' : 'badge-danger'))) 
                                    ?>">
                                    <?= ucfirst($payment['payment_status']) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($payment['transaction_id'] ?? '-') ?></td>
                            <td><?= formatDate($payment['created_at']) ?></td>
                            <td>
                                <div class="btn-group">
                                    <a href="<?= url('/admin/pago/' . $payment['id']) ?>" class="btn btn-sm btn-info" title="Ver detalle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if ($payment['payment_status'] === 'pending' || $payment['payment_status'] === 'processing'): ?>
                                        <button type="button" class="btn btn-sm btn-success update-payment-btn" 
                                                data-id="<?= $payment['id'] ?>" 
                                                data-status="completed"
                                                title="Marcar como completado">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger update-payment-btn" 
                                                data-id="<?= $payment['id'] ?>" 
                                                data-status="failed"
                                                title="Marcar como fallido">
                                            <i class="fas fa-times"></i>
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
                    <a class="page-link" href="<?= url('/admin/pagos?' . http_build_query(array_merge($_GET, ['page' => 1]))) ?>">«</a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="<?= url('/admin/pagos?' . http_build_query(array_merge($_GET, ['page' => $page - 1]))) ?>">‹</a>
                </li>
            <?php endif; ?>
            
            <?php
            $startPage = max(1, $page - 2);
            $endPage = min($totalPages, $page + 2);
            
            for ($i = $startPage; $i <= $endPage; $i++):
            ?>
                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                    <a class="page-link" href="<?= url('/admin/pagos?' . http_build_query(array_merge($_GET, ['page' => $i]))) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
            
            <?php if ($page < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= url('/admin/pagos?' . http_build_query(array_merge($_GET, ['page' => $page + 1]))) ?>">›</a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="<?= url('/admin/pagos?' . http_build_query(array_merge($_GET, ['page' => $totalPages]))) ?>">»</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
    <?php endif; ?>
</div>
<!-- /.card -->

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
                    <div class="form-group" id="errorMessageGroup" style="display: none;">
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
                document.getElementById('errorMessageGroup').style.display = 'none';
            } else if (newStatus === 'failed') {
                message = '¿Estás seguro que deseas marcar este pago como FALLIDO?';
                document.getElementById('errorMessageGroup').style.display = 'block';
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