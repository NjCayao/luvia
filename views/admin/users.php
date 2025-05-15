<!-- Filtros -->
<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Filtros</h3>
            </div>
            <div class="card-body">
                <form action="<?= url('/admin/usuarios') ?>" method="GET" class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="type">Tipo de usuario</label>
                            <select name="type" id="type" class="form-control">
                                <option value="">Todos</option>
                                <option value="admin" <?= $userType === 'admin' ? 'selected' : '' ?>>Administradores</option>
                                <option value="advertiser" <?= $userType === 'advertiser' ? 'selected' : '' ?>>Anunciantes</option>
                                <option value="visitor" <?= $userType === 'visitor' ? 'selected' : '' ?>>Visitantes</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status">Estado</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">Todos</option>
                                <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Activos</option>
                                <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pendientes</option>
                                <option value="suspended" <?= $status === 'suspended' ? 'selected' : '' ?>>Suspendidos</option>
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

<!-- Tabla de usuarios -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-users mr-1"></i>
            Listado de Usuarios
        </h3>
        <div class="card-tools">
            <span class="badge badge-info"><?= $totalUsers ?> usuarios encontrados</span>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body p-0">
        <table class="table table-hover table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Tipo</th>
                    <th>Estado</th>
                    <th>Fecha de registro</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-3">No se encontraron usuarios con los filtros seleccionados</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['phone']) ?></td>
                            <td>
                                <span class="badge <?= $user['user_type'] === 'admin' ? 'badge-danger' : ($user['user_type'] === 'advertiser' ? 'badge-warning' : 'badge-info') ?>">
                                    <?= $user['user_type'] === 'admin' ? 'Admin' : ($user['user_type'] === 'advertiser' ? 'Anunciante' : 'Visitante') ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge <?= $user['status'] === 'active' ? 'badge-success' : ($user['status'] === 'pending' ? 'badge-warning' : 'badge-danger') ?>">
                                    <?= ucfirst($user['status']) ?>
                                </span>
                            </td>
                            <td><?= formatDate($user['created_at']) ?></td>
                            <td>
                                <div class="btn-group">
                                    <a href="<?= url('/admin/usuario/' . $user['id']) ?>" class="btn btn-sm btn-info" title="Ver detalle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?= url('/admin/usuario/' . $user['id'] . '/editar') ?>" class="btn btn-sm btn-primary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if ($user['user_type'] !== 'admin'): ?>
                                        <form action="<?= url('admin/usuario/cambiar-estado') ?>" method="POST" style="display: inline;">
                                            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                            <input type="hidden" name="status" value="<?= $user['status'] === 'active' ? 'suspended' : 'active' ?>">
                                            <button type="submit" class="btn btn-sm <?= $user['status'] === 'active' ? 'btn-danger' : 'btn-success' ?>"
                                                title="<?= $user['status'] === 'active' ? 'Suspender' : 'Activar' ?>">
                                                <i class="fas <?= $user['status'] === 'active' ? 'fa-ban' : 'fa-check' ?>"></i>
                                            </button>
                                        </form>
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
                        <a class="page-link" href="<?= url('/admin/usuarios?' . http_build_query(array_merge($_GET, ['page' => 1]))) ?>">«</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="<?= url('/admin/usuarios?' . http_build_query(array_merge($_GET, ['page' => $page - 1]))) ?>">‹</a>
                    </li>
                <?php endif; ?>

                <?php
                $startPage = max(1, $page - 2);
                $endPage = min($totalPages, $page + 2);

                for ($i = $startPage; $i <= $endPage; $i++):
                ?>
                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                        <a class="page-link" href="<?= url('/admin/usuarios?' . http_build_query(array_merge($_GET, ['page' => $i]))) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?= url('/admin/usuarios?' . http_build_query(array_merge($_GET, ['page' => $page + 1]))) ?>">›</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="<?= url('/admin/usuarios?' . http_build_query(array_merge($_GET, ['page' => $totalPages]))) ?>">»</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    <?php endif; ?>
</div>
<!-- /.card -->

<!-- Modal para cambiar estado -->
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
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
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
            // Para debugging
            console.log('Enviando formulario con datos:');
            console.log('user_id:', document.getElementById('userId').value);
            console.log('status:', document.getElementById('userStatus').value);

            document.getElementById('statusForm').submit();
        });
    });
</script>