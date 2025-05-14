<!-- Small boxes (Stat box) -->
<div class="row">
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-info">
            <div class="inner">
                <h3><?= $totalUsers ?></h3>
                <p>Usuarios Registrados</p>
            </div>
            <div class="icon">
                <i class="ion ion-person-add"></i>
            </div>
            <a href="<?= url('/admin/usuarios') ?>" class="small-box-footer">Más información <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-success">
            <div class="inner">
                <h3><?= $totalProfiles ?></h3>
                <p>Perfiles Activos</p>
            </div>
            <div class="icon">
                <i class="ion ion-stats-bars"></i>
            </div>
            <a href="<?= url('/admin/perfiles') ?>" class="small-box-footer">Más información <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-warning">
            <div class="inner">
                <h3><?= $totalAdvertisers ?></h3>
                <p>Anunciantes</p>
            </div>
            <div class="icon">
                <i class="ion ion-person"></i>
            </div>
            <a href="<?= url('/admin/usuarios?type=advertiser') ?>" class="small-box-footer">Más información <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-danger">
            <div class="inner">
                <h3><?= $totalVisitors ?></h3>
                <p>Visitantes</p>
            </div>
            <div class="icon">
                <i class="ion ion-pie-graph"></i>
            </div>
            <a href="<?= url('/admin/usuarios?type=visitor') ?>" class="small-box-footer">Más información <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
</div>
<!-- /.row -->

<!-- Main row -->
<div class="row">
    <!-- Left col -->
    <section class="col-lg-7 connectedSortable">
        <!-- Users table -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-users mr-1"></i>
                    Usuarios Recientes
                </h3>
                <div class="card-tools">
                    <a href="<?= url('/admin/usuarios') ?>" class="btn btn-tool">
                        <i class="fas fa-users"></i> Ver todos
                    </a>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body p-0">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Tipo</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentUsers)): ?>
                            <tr>
                                <td colspan="6" class="text-center">No hay usuarios recientes</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentUsers as $user): ?>
                                <tr>
                                    <td><?= $user['id'] ?></td>
                                    <td><?= htmlspecialchars($user['phone']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td>
                                        <span class="badge <?= $user['user_type'] === 'admin' ? 'badge-danger' : ($user['user_type'] === 'advertiser' ? 'badge-warning' : 'badge-info') ?>">
                                            <?= $user['user_type'] === 'admin' ? 'Admin' : ($user['user_type'] === 'advertiser' ? 'Anunciante' : 'Visitante') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge <?= $user['status'] === 'active' ? 'badge-success' : ($user['status'] === 'pending' ? 'badge-warning' : 'badge-danger') ?>">
                                            <?= $user['status'] === 'active' ? 'Activo' : ($user['status'] === 'pending' ? 'Pendiente' : 'Suspendido') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?= url('/admin/usuario/' . $user['id']) ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
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

        <!-- Payments table -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-credit-card mr-1"></i>
                    Pagos Recientes
                </h3>
                <div class="card-tools">
                    <a href="<?= url('/admin/pagos') ?>" class="btn btn-tool">
                        <i class="fas fa-credit-card"></i> Ver todos
                    </a>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body p-0">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Monto</th>
                            <th>Método</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentPayments)): ?>
                            <tr>
                                <td colspan="6" class="text-center">No hay pagos recientes</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentPayments as $payment): ?>
                                <tr>
                                    <td><?= $payment['id'] ?></td>
                                    <td>
                                        <a href="<?= url('/admin/usuario/' . $payment['user_id']) ?>">
                                            <?= htmlspecialchars($payment['user_email'] ?? $payment['user_phone'] ?? 'Usuario ' . $payment['user_id']) ?>
                                        </a>
                                    </td>
                                    <td>S/. <?= number_format($payment['amount'], 2) ?></td>
                                    <td><?= ucfirst($payment['payment_method']) ?></td>
                                    <td>
                                        <span class="badge 
                                            <?= $payment['payment_status'] === 'completed' ? 'badge-success' : 
                                                ($payment['payment_status'] === 'pending' ? 'badge-warning' : 
                                                ($payment['payment_status'] === 'processing' ? 'badge-info' : 'badge-danger')) 
                                            ?>">
                                            <?= ucfirst($payment['payment_status']) ?>
                                        </span>
                                    </td>
                                    <td><?= formatDate($payment['created_at']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </section>
    <!-- /.Left col -->
    
    <!-- right col (We are only adding the ID to make the widgets sortable)-->
    <section class="col-lg-5 connectedSortable">
        <!-- Expiring Subscriptions -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-clock mr-1"></i>
                    Suscripciones por Vencer
                </h3>
                <div class="card-tools">
                    <a href="<?= url('/admin/suscripciones') ?>" class="btn btn-tool">
                        <i class="fas fa-sync-alt"></i> Ver todas
                    </a>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body p-0">
                <ul class="products-list product-list-in-card pl-2 pr-2">
                    <?php if (empty($expiringSubscriptions)): ?>
                        <li class="item">
                            <div class="product-info">
                                <div class="product-title">
                                    No hay suscripciones por vencer próximamente
                                </div>
                            </div>
                        </li>
                    <?php else: ?>
                        <?php foreach ($expiringSubscriptions as $subscription): ?>
                            <li class="item">
                                <div class="product-info">
                                    <a href="<?= url('/admin/usuario/' . $subscription['user_id']) ?>" class="product-title">
                                        <?= htmlspecialchars($subscription['email'] ?? $subscription['phone']) ?>
                                        <span class="badge badge-warning float-right">
                                            Vence: <?= formatDate($subscription['end_date'], 'd/m/Y') ?>
                                        </span>
                                    </a>
                                    <span class="product-description">
                                        Plan: <?= htmlspecialchars($subscription['plan_name']) ?> - 
                                        Tipo: <?= $subscription['user_type'] === 'advertiser' ? 'Anunciante' : 'Visitante' ?>
                                    </span>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
            <!-- /.card-body -->
            <div class="card-footer text-center">
                <a href="<?= url('/admin/suscripciones') ?>" class="uppercase">Ver Todas las Suscripciones</a>
            </div>
            <!-- /.card-footer -->
        </div>
        <!-- /.card -->

        <!-- Top Profiles -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-star mr-1"></i>
                    Perfiles Más Vistos
                </h3>
                <div class="card-tools">
                    <a href="<?= url('/admin/perfiles') ?>" class="btn btn-tool">
                        <i class="fas fa-id-card"></i> Ver todos
                    </a>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body p-0">
                <ul class="products-list product-list-in-card pl-2 pr-2">
                    <?php if (empty($topProfiles)): ?>
                        <li class="item">
                            <div class="product-info">
                                <div class="product-title">
                                    No hay perfiles con vistas
                                </div>
                            </div>
                        </li>
                    <?php else: ?>
                        <?php foreach ($topProfiles as $profile): ?>
                            <li class="item">
                                <div class="product-img">
                                    <?php if (!empty($profile['main_photo'])): ?>
                                        <img src="<?= url('uploads/photos/' . $profile['main_photo']) ?>" alt="Foto de perfil" class="img-size-50">
                                    <?php else: ?>
                                        <img src="<?= url('img/profile-placeholder.jpg') ?>" alt="Sin foto" class="img-size-50">
                                    <?php endif; ?>
                                </div>
                                <div class="product-info">
                                    <a href="<?= url('/admin/perfil/' . $profile['id']) ?>" class="product-title">
                                        <?= htmlspecialchars($profile['name']) ?>
                                        <span class="badge badge-info float-right">
                                            <?= $profile['views'] ?> vistas
                                        </span>
                                    </a>
                                    <span class="product-description">
                                        <?= htmlspecialchars($profile['city']) ?> - 
                                        <?= ucfirst($profile['gender']) ?> - 
                                        <?= $profile['is_verified'] ? 'Verificado' : 'No verificado' ?>
                                    </span>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
            <!-- /.card-body -->
            <div class="card-footer text-center">
                <a href="<?= url('/admin/perfiles') ?>" class="uppercase">Ver Todos los Perfiles</a>
            </div>
            <!-- /.card-footer -->
        </div>
        <!-- /.card -->
    </section>
    <!-- right col -->
</div>
<!-- /.row (main row) -->