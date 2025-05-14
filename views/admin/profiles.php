<!-- Filtros -->
<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Filtros</h3>
            </div>
            <div class="card-body">
                <form action="<?= url('/admin/perfiles') ?>" method="GET" class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="gender">Género</label>
                            <select name="gender" id="gender" class="form-control">
                                <option value="">Todos</option>
                                <option value="female" <?= $gender === 'female' ? 'selected' : '' ?>>Mujer</option>
                                <option value="male" <?= $gender === 'male' ? 'selected' : '' ?>>Hombre</option>
                                <option value="trans" <?= $gender === 'trans' ? 'selected' : '' ?>>Trans</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="city">Ciudad</label>
                            <select name="city" id="city" class="form-control">
                                <option value="">Todas</option>
                                <?php foreach($cities as $cityOption): ?>
                                    <option value="<?= $cityOption ?>" <?= $city === $cityOption ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cityOption) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="verified">Verificación</label>
                            <select name="verified" id="verified" class="form-control">
                                <option value="">Todos</option>
                                <option value="1" <?= $verified === true ? 'selected' : '' ?>>Verificados</option>
                                <option value="0" <?= $verified === false ? 'selected' : '' ?>>No verificados</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="search">Buscar</label>
                            <input type="text" name="search" id="search" class="form-control" placeholder="Nombre, descripción..." value="<?= htmlspecialchars($search) ?>">
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

<!-- Tabla de perfiles -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-id-card mr-1"></i>
            Listado de Perfiles
        </h3>
        <div class="card-tools">
            <span class="badge badge-info"><?= $totalProfiles ?> perfiles encontrados</span>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body p-0">
        <table class="table table-hover table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Foto</th>
                    <th>Nombre</th>
                    <th>Ciudad</th>
                    <th>Género</th>
                    <th>Verificado</th>
                    <th>Vistas</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($profiles)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-3">No se encontraron perfiles con los filtros seleccionados</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($profiles as $profile): ?>
                        <tr>
                            <td><?= $profile['id'] ?></td>
                            <td>
                                <?php if (!empty($profile['main_photo'])): ?>
                                    <img src="<?= url('uploads/photos/' . $profile['main_photo']) ?>" alt="Foto de perfil" class="img-circle img-size-50">
                                <?php else: ?>
                                    <img src="<?= url('img/profile-placeholder.jpg') ?>" alt="Sin foto" class="img-circle img-size-50">
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($profile['name']) ?></td>
                            <td><?= htmlspecialchars($profile['city']) ?></td>
                            <td>
                                <span class="badge <?= $profile['gender'] === 'female' ? 'badge-pink' : ($profile['gender'] === 'male' ? 'badge-blue' : 'badge-purple') ?>">
                                    <?= $profile['gender'] === 'female' ? 'Mujer' : ($profile['gender'] === 'male' ? 'Hombre' : 'Trans') ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($profile['is_verified']): ?>
                                    <span class="badge badge-success">Verificado</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">No verificado</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $profile['views'] ?? 0 ?></td>
                            <td>
                                <div class="btn-group">
                                    <a href="<?= url('/admin/perfil/' . $profile['id']) ?>" class="btn btn-sm btn-info" title="Ver detalle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?= url('/admin/perfil/' . $profile['id'] . '/editar') ?>" class="btn btn-sm btn-primary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if (!$profile['is_verified']): ?>
                                        <button type="button" class="btn btn-sm btn-success verify-profile-btn" 
                                                data-id="<?= $profile['id'] ?>" 
                                                title="Verificar perfil">
                                            <i class="fas fa-check-circle"></i>
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
                    <a class="page-link" href="<?= url('/admin/perfiles?' . http_build_query(array_merge($_GET, ['page' => 1]))) ?>">«</a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="<?= url('/admin/perfiles?' . http_build_query(array_merge($_GET, ['page' => $page - 1]))) ?>">‹</a>
                </li>
            <?php endif; ?>
            
            <?php
            $startPage = max(1, $page - 2);
            $endPage = min($totalPages, $page + 2);
            
            for ($i = $startPage; $i <= $endPage; $i++):
            ?>
                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                    <a class="page-link" href="<?= url('/admin/perfiles?' . http_build_query(array_merge($_GET, ['page' => $i]))) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
            
            <?php if ($page < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= url('/admin/perfiles?' . http_build_query(array_merge($_GET, ['page' => $page + 1]))) ?>">›</a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="<?= url('/admin/perfiles?' . http_build_query(array_merge($_GET, ['page' => $totalPages]))) ?>">»</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
    <?php endif; ?>
</div>
<!-- /.card -->

<!-- Modal para verificar perfil -->
<div class="modal fade" id="verifyModal" tabindex="-1" role="dialog" aria-labelledby="verifyModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verifyModalLabel">Verificar Perfil</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro que deseas verificar este perfil?</p>
                <p>Al verificarlo, se mostrará como "Verificado" en la plataforma y aparecerá el símbolo de verificación junto a su nombre.</p>
                <form id="verifyForm" method="POST" action="<?= url('/admin/perfil/verificar') ?>">
                    <input type="hidden" name="csrf_token" value="<?= getCsrfToken() ?>">
                    <input type="hidden" name="profile_id" id="profileId" value="">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="confirmVerify">Verificar Perfil</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manejar botones de verificación de perfil
    document.querySelectorAll('.verify-profile-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const profileId = this.getAttribute('data-id');
            document.getElementById('profileId').value = profileId;
            $('#verifyModal').modal('show');
        });
    });
    
    // Confirmar verificación
    document.getElementById('confirmVerify').addEventListener('click', function() {
        document.getElementById('verifyForm').submit();
    });
});
</script>

<style>
.badge-pink {
    background-color: #f27eb5;
    color: white;
}
.badge-blue {
    background-color: #3490dc;
    color: white;
}
.badge-purple {
    background-color: #8e44ad;
    color: white;
}
</style>