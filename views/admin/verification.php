<!-- Formulario de Filtros -->
<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Filtros</h3>
            </div>
            <div class="card-body">
                <form action="<?= url('/admin/verificacion') ?>" method="GET" class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status">Estado</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">Todos</option>
                                <option value="pending" <?= isset($_GET['status']) && $_GET['status'] === 'pending' ? 'selected' : '' ?>>Pendientes</option>
                                <option value="approved" <?= isset($_GET['status']) && $_GET['status'] === 'approved' ? 'selected' : '' ?>>Aprobados</option>
                                <option value="rejected" <?= isset($_GET['status']) && $_GET['status'] === 'rejected' ? 'selected' : '' ?>>Rechazados</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="type">Tipo de verificación</label>
                            <select name="type" id="type" class="form-control">
                                <option value="">Todos</option>
                                <option value="id_card" <?= isset($_GET['type']) && $_GET['type'] === 'id_card' ? 'selected' : '' ?>>DNI/Documento</option>
                                <option value="selfie" <?= isset($_GET['type']) && $_GET['type'] === 'selfie' ? 'selected' : '' ?>>Selfie</option>
                                <option value="document" <?= isset($_GET['type']) && $_GET['type'] === 'document' ? 'selected' : '' ?>>Documento</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="search">Buscar</label>
                            <input type="text" name="search" id="search" class="form-control" placeholder="Nombre, teléfono, email..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
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

<!-- Lista de verificaciones pendientes -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-check-circle mr-1"></i>
            Solicitudes de Verificación
        </h3>
        <div class="card-tools">
            <span class="badge badge-warning"><?= $pendingCount ?? 0 ?> pendientes</span>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <?php if (empty($verifications)): ?>
            <div class="alert alert-info">
                No hay solicitudes de verificación que coincidan con los filtros seleccionados.
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($verifications as $verification): ?>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <a href="<?= url('/admin/usuario/' . $verification['user_id']) ?>">
                                        <?= htmlspecialchars($verification['user_name'] ?? 'Usuario ' . $verification['user_id']) ?>
                                    </a>
                                    <small class="text-muted">(<?= htmlspecialchars($verification['user_email'] ?? $verification['user_phone']) ?>)</small>
                                </h5>
                                <div class="card-tools">
                                    <span class="badge 
                                        <?= $verification['status'] === 'pending' ? 'badge-warning' : 
                                          ($verification['status'] === 'approved' ? 'badge-success' : 'badge-danger') ?>">
                                        <?= $verification['status'] === 'pending' ? 'Pendiente' : 
                                           ($verification['status'] === 'approved' ? 'Aprobado' : 'Rechazado') ?>
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Tipo:</strong> 
                                            <?= $verification['verification_type'] === 'id_card' ? 'DNI/Documento' : 
                                               ($verification['verification_type'] === 'selfie' ? 'Selfie' : 'Documento') ?>
                                        </p>
                                        <p><strong>Fecha:</strong> <?= formatDate($verification['created_at']) ?></p>
                                        <?php if (!empty($verification['notes'])): ?>
                                            <p><strong>Notas:</strong> <?= htmlspecialchars($verification['notes']) ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?php if (!empty($verification['document_path'])): ?>
                                            <div class="verification-image-container">
                                                <img src="<?= url('uploads/verification/' . $verification['document_path']) ?>" 
                                                     alt="Documento de verificación" 
                                                     class="img-fluid verification-image"
                                                     data-toggle="modal" 
                                                     data-target="#imageModal" 
                                                     data-img="<?= url('uploads/verification/' . $verification['document_path']) ?>"
                                                     data-title="Documento de verificación">
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-warning">
                                                No hay imagen disponible
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <?php if ($verification['status'] === 'pending'): ?>
                                    <div class="row mt-3">
                                        <div class="col-md-12">
                                            <form action="<?= url('/admin/verificacion/actualizar') ?>" method="POST" class="row">
                                                <input type="hidden" name="csrf_token" value="<?= getCsrfToken() ?>">
                                                <input type="hidden" name="verification_id" value="<?= $verification['id'] ?>">
                                                <input type="hidden" name="user_id" value="<?= $verification['user_id'] ?>">
                                                
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <textarea name="notes" class="form-control" rows="2" placeholder="Notas (opcional)"><?= htmlspecialchars($verification['notes'] ?? '') ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="btn-group w-100">
                                                        <button type="submit" name="status" value="approved" class="btn btn-success">
                                                            <i class="fas fa-check"></i> Aprobar
                                                        </button>
                                                        <button type="submit" name="status" value="rejected" class="btn btn-danger">
                                                            <i class="fas fa-times"></i> Rechazar
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                <?php elseif (!empty($verification['verified_by'])): ?>
                                    <div class="row mt-3">
                                        <div class="col-md-12">
                                            <p class="text-muted">
                                                Verificado por: <?= htmlspecialchars($verification['admin_name'] ?? 'Admin ' . $verification['verified_by']) ?> 
                                                el <?= formatDate($verification['updated_at']) ?>
                                            </p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Paginación -->
            <?php if (isset($totalPages) && $totalPages > 1): ?>
            <div class="row mt-3">
                <div class="col-md-12">
                    <ul class="pagination justify-content-center">
                        <?php 
                        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
                        $startPage = max(1, $page - 2);
                        $endPage = min($totalPages, $page + 2);
                        ?>
                        
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= url('/admin/verificacion?' . http_build_query(array_merge($_GET, ['page' => 1]))) ?>">«</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="<?= url('/admin/verificacion?' . http_build_query(array_merge($_GET, ['page' => $page - 1]))) ?>">‹</a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                            <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                <a class="page-link" href="<?= url('/admin/verificacion?' . http_build_query(array_merge($_GET, ['page' => $i]))) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= url('/admin/verificacion?' . http_build_query(array_merge($_GET, ['page' => $page + 1]))) ?>">›</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="<?= url('/admin/verificacion?' . http_build_query(array_merge($_GET, ['page' => $totalPages]))) ?>">»</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->

<!-- Modal para ver imagen ampliada -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Documento de verificación</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid" alt="Documento ampliado">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<style>
.verification-image-container {
    height: 150px;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.verification-image {
    max-height: 100%;
    object-fit: contain;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configurar modal para ver imagen ampliada
    $('#imageModal').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        const imageUrl = button.data('img');
        const title = button.data('title');
        
        const modal = $(this);
        modal.find('.modal-title').text(title);
        modal.find('#modalImage').attr('src', imageUrl);
    });
});
</script>