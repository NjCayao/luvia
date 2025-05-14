<!-- Tabla de planes para Anunciantes -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-id-card mr-1"></i>
            Planes para Anunciantes
        </h3>
        <div class="card-tools">
            <a href="<?= url('/admin/planes/crear') ?>" class="btn btn-sm btn-success">
                <i class="fas fa-plus"></i> Nuevo Plan
            </a>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body p-0">
        <table class="table table-hover table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Duración</th>
                    <th>Precio (S/.)</th>
                    <th>Fotos</th>
                    <th>Videos</th>
                    <th>Destacado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $advertiserPlans = array_filter($plans, function($plan) {
                    return $plan['user_type'] === 'advertiser';
                });
                
                if (empty($advertiserPlans)): 
                ?>
                    <tr>
                        <td colspan="8" class="text-center py-3">No hay planes para anunciantes</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($advertiserPlans as $plan): ?>
                        <tr>
                            <td><?= $plan['id'] ?></td>
                            <td><?= htmlspecialchars($plan['name']) ?></td>
                            <td><?= $plan['duration'] ?> días</td>
                            <td><?= number_format($plan['price'], 2) ?></td>
                            <td><?= $plan['max_photos'] ?? '-' ?></td>
                            <td><?= $plan['max_videos'] ?? '-' ?></td>
                            <td>
                                <?php if ($plan['featured']): ?>
                                    <span class="badge badge-success">Sí</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">No</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="<?= url('/admin/plan/' . $plan['id'] . '/editar') ?>" class="btn btn-sm btn-primary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger delete-plan-btn" 
                                            data-id="<?= $plan['id'] ?>" 
                                            data-name="<?= htmlspecialchars($plan['name']) ?>"
                                            title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->

<!-- Tabla de planes para Visitantes -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-eye mr-1"></i>
            Planes para Visitantes
        </h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body p-0">
        <table class="table table-hover table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Duración</th>
                    <th>Precio (S/.)</th>
                    <th>Destacado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $visitorPlans = array_filter($plans, function($plan) {
                    return $plan['user_type'] === 'visitor';
                });
                
                if (empty($visitorPlans)): 
                ?>
                    <tr>
                        <td colspan="6" class="text-center py-3">No hay planes para visitantes</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($visitorPlans as $plan): ?>
                        <tr>
                            <td><?= $plan['id'] ?></td>
                            <td><?= htmlspecialchars($plan['name']) ?></td>
                            <td><?= $plan['duration'] ?> días</td>
                            <td><?= number_format($plan['price'], 2) ?></td>
                            <td>
                                <?php if ($plan['featured']): ?>
                                    <span class="badge badge-success">Sí</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">No</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="<?= url('/admin/plan/' . $plan['id'] . '/editar') ?>" class="btn btn-sm btn-primary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger delete-plan-btn" 
                                            data-id="<?= $plan['id'] ?>" 
                                            data-name="<?= htmlspecialchars($plan['name']) ?>"
                                            title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->

<!-- Modal para eliminar plan -->
<div class="modal fade" id="deletePlanModal" tabindex="-1" role="dialog" aria-labelledby="deletePlanModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deletePlanModalLabel">Eliminar Plan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro que deseas eliminar el plan "<span id="planName"></span>"?</p>
                <p class="text-danger">Esta acción no se puede deshacer. Solo se puede eliminar un plan si no hay usuarios que lo estén utilizando.</p>
                <form id="deletePlanForm" method="POST" action="<?= url('/admin/plan/eliminar') ?>">
                    <input type="hidden" name="csrf_token" value="<?= getCsrfToken() ?>">
                    <input type="hidden" name="plan_id" id="planId" value="">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmDeletePlan">Eliminar Plan</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manejar botones de eliminación de plan
    document.querySelectorAll('.delete-plan-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const planId = this.getAttribute('data-id');
            const planName = this.getAttribute('data-name');
            
            document.getElementById('planId').value = planId;
            document.getElementById('planName').textContent = planName;
            
            $('#deletePlanModal').modal('show');
        });
    });
    
    // Confirmar eliminación de plan
    document.getElementById('confirmDeletePlan').addEventListener('click', function() {
        document.getElementById('deletePlanForm').submit();
    });
});
</script>