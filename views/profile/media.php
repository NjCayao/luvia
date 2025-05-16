<!-- Contenido principal -->
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary card-outline card-tabs">
            <div class="card-header p-0 pt-1 border-bottom-0">
                <ul class="nav nav-tabs" id="media-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="photos-tab" data-toggle="tab" href="#photos" role="tab" aria-controls="photos" aria-selected="true">
                            <i class="fas fa-image"></i> Fotos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="videos-tab" data-toggle="tab" href="#videos" role="tab" aria-controls="videos" aria-selected="false">
                            <i class="fas fa-video"></i> Videos
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="media-tabContent">
                    <!-- Tab de Fotos -->
                    <div class="tab-pane fade show active" id="photos" role="tabpanel" aria-labelledby="photos-tab">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h4>Mis Fotos (<?= count($photos) ?>/<?= $maxPhotos ?>)</h4>
                            </div>
                            <div class="col-md-6 text-right">
                                <?php if (count($photos) < $maxPhotos): ?>
                                    <button type="button" class="btn btn-primary" id="upload-photo-btn">
                                        <i class="fas fa-upload"></i> Subir Foto
                                    </button>
                                    <form id="photo-upload-form" style="display: none;" enctype="multipart/form-data">
                                        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                                        <input type="file" name="photo" id="photo-input" accept="image/jpeg,image/png,image/webp">
                                    </form>
                                <?php else: ?>
                                    <button type="button" class="btn btn-secondary" disabled>
                                        Límite de fotos alcanzado
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="icon fas fa-info-circle"></i>
                            Puede subir hasta <?= $maxPhotos ?> fotos. La primera foto subida será la principal.
                            Para cambiar la foto principal, haga clic en el botón "Establecer como principal".
                        </div>

                        <div class="row photos-container" id="photos-container">
                            <?php if (empty($photos)): ?>
                                <div class="col-12">
                                    <div class="alert alert-warning">
                                        <i class="icon fas fa-exclamation-triangle"></i>
                                        No has subido ninguna foto. Sube al menos una foto para que tu perfil sea visible.
                                    </div>
                                </div>
                            <?php else: ?>
                                <?php foreach ($photos as $photo): ?>
                                    <div class="col-md-4 col-sm-6 photo-item" data-id="<?= $photo['id'] ?>">
                                        <div class="card">
                                            <div class="card-img-top photo-preview" style="background-image: url('<?= url('uploads/photos/' . $photo['filename']) ?>')"></div>
                                            <div class="card-body">
                                                <div class="btn-group w-100">
                                                    <?php if (!$photo['is_primary']): ?>
                                                        <button type="button" class="btn btn-sm btn-info set-primary-btn" data-id="<?= $photo['id'] ?>">
                                                            <i class="fas fa-star"></i> Principal
                                                        </button>
                                                    <?php else: ?>
                                                        <button type="button" class="btn btn-sm btn-success" disabled>
                                                            <i class="fas fa-check"></i> Principal
                                                        </button>
                                                    <?php endif; ?>
                                                    <button type="button" class="btn btn-sm btn-danger delete-photo-btn" data-id="<?= $photo['id'] ?>">
                                                        <i class="fas fa-trash"></i> Eliminar
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Tab de Videos -->
                    <div class="tab-pane fade" id="videos" role="tabpanel" aria-labelledby="videos-tab">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h4>Mis Videos (<?= count($videos) ?>/<?= $maxVideos ?>)</h4>
                            </div>
                            <div class="col-md-6 text-right">
                                <?php if (count($videos) < $maxVideos): ?>
                                    <button type="button" class="btn btn-primary" id="upload-video-btn">
                                        <i class="fas fa-upload"></i> Subir Video
                                    </button>
                                    <form id="video-upload-form" style="display: none;" enctype="multipart/form-data">
                                        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                                        <input type="file" name="video" id="video-input" accept="video/mp4,video/webm">
                                    </form>
                                <?php else: ?>
                                    <button type="button" class="btn btn-secondary" disabled>
                                        Límite de videos alcanzado
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="icon fas fa-info-circle"></i>
                            Puede subir hasta <?= $maxVideos ?> videos. Los videos deben ser breves (máximo 2 minutos) y de buena calidad.
                        </div>

                        <div class="row videos-container" id="videos-container">
                            <?php if (empty($videos)): ?>
                                <div class="col-12">
                                    <div class="alert alert-warning">
                                        <i class="icon fas fa-exclamation-triangle"></i>
                                        No has subido ningún video. Los videos son opcionales pero aumentan la visibilidad de tu perfil.
                                    </div>
                                </div>
                            <?php else: ?>
                                <?php foreach ($videos as $video): ?>
                                    <div class="col-md-6 video-item" data-id="<?= $video['id'] ?>">
                                        <div class="card">
                                            <div class="card-body">
                                                <video controls class="w-100 rounded">
                                                    <source src="<?= url('uploads/videos/' . $video['filename']) ?>"
                                                        type="<?= getMimeType(UPLOAD_DIR . 'videos/' . $video['filename']) ?>">
                                                    Tu navegador no soporta la reproducción de videos.
                                                </video>
                                                <div class="mt-2">
                                                    <button type="button" class="btn btn-sm btn-danger delete-video-btn" data-id="<?= $video['id'] ?>">
                                                        <i class="fas fa-trash"></i> Eliminar Video
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
</div>

<!-- Modal de carga -->
<div class="modal fade" id="loading-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="sr-only">Cargando...</span>
                </div>
                <h5 id="loading-message">Subiendo archivo...</h5>
                <div class="progress mt-3">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%" id="upload-progress"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CSS adicional -->
<style>
    .photo-preview {
        height: 200px;
        background-size: contain;
        background-position: center;
        background-repeat: no-repeat;
    }

    .photo-item,
    .video-item {
        margin-bottom: 20px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM cargado correctamente');
        
        // Referencias a elementos DOM
        const photoUploadBtn = document.getElementById('upload-photo-btn');
        const photoInput = document.getElementById('photo-input');
        const photoUploadForm = document.getElementById('photo-upload-form');
        const photoContainer = document.getElementById('photos-container');

        const videoUploadBtn = document.getElementById('upload-video-btn');
        const videoInput = document.getElementById('video-input');
        const videoUploadForm = document.getElementById('video-upload-form');
        const videoContainer = document.getElementById('videos-container');

        const loadingModal = document.getElementById('loading-modal');
        const uploadProgress = document.getElementById('upload-progress');
        const loadingMessage = document.getElementById('loading-message');
        
        // Verificar elementos
        console.log('Botón de subir foto:', photoUploadBtn ? 'encontrado' : 'NO encontrado');
        console.log('Input de foto:', photoInput ? 'encontrado' : 'NO encontrado');
        console.log('Form de subir foto:', photoUploadForm ? 'encontrado' : 'NO encontrado');
        console.log('Botón de subir video:', videoUploadBtn ? 'encontrado' : 'NO encontrado');
        console.log('Modal de carga:', loadingModal ? 'encontrado' : 'NO encontrado');

        // Inicializar modal Bootstrap
        const modal = new bootstrap.Modal(loadingModal);

        // Subida de fotos
        if (photoUploadBtn && photoInput) {
            photoUploadBtn.addEventListener('click', function() {
                console.log('Botón de subir foto clickeado');
                photoInput.click();
            });

            photoInput.addEventListener('change', function() {
                console.log('Archivo de foto seleccionado:', this.files.length);
                if (this.files.length > 0) {
                    uploadFile(photoUploadForm, 'photo', '/usuario/subir-foto');
                }
            });
        }

        // Subida de videos
        if (videoUploadBtn && videoInput) {
            videoUploadBtn.addEventListener('click', function() {
                console.log('Botón de subir video clickeado');
                videoInput.click();
            });

            videoInput.addEventListener('change', function() {
                console.log('Archivo de video seleccionado:', this.files.length);
                if (this.files.length > 0) {
                    uploadFile(videoUploadForm, 'video', '/usuario/subir-video');
                }
            });
        }

        // Función para subir archivos
        function uploadFile(form, fileType, urlPath) {
            console.log(`Iniciando subida de ${fileType} a ${urlPath}`);
            const formData = new FormData(form);

            // Mostrar modal de carga
            loadingMessage.textContent = `Subiendo ${fileType === 'photo' ? 'foto' : 'video'}...`;
            uploadProgress.style.width = '0%';
            modal.show();

            // Simular progreso
            let progress = 0;
            const progressInterval = setInterval(() => {
                progress += Math.random() * 5;
                if (progress > 90) {
                    progress = 90;
                    clearInterval(progressInterval);
                }
                uploadProgress.style.width = `${progress}%`;
            }, 300);

            // Construir URL completa
            const fullUrl = '<?= url('/') ?>' + urlPath;
            console.log('URL completa:', fullUrl);

            fetch(fullUrl, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Respuesta recibida:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Datos recibidos:', data);
                clearInterval(progressInterval);

                if (data.success) {
                    // Completar barra de progreso
                    uploadProgress.style.width = '100%';
                    loadingMessage.textContent = `${fileType === 'photo' ? 'Foto' : 'Video'} subido correctamente`;

                    // Cerrar modal después de un breve retraso
                    setTimeout(() => {
                        modal.hide();
                        // Recargar página para ver los cambios (solución temporal)
                        window.location.reload();
                    }, 1000);
                } else {
                    // Mostrar error
                    loadingMessage.textContent = data.error || `Error al subir ${fileType === 'photo' ? 'foto' : 'video'}`;
                    uploadProgress.classList.remove('bg-primary');
                    uploadProgress.classList.add('bg-danger');

                    // Cerrar modal después de un breve retraso
                    setTimeout(() => {
                        modal.hide();
                        uploadProgress.classList.remove('bg-danger');
                        uploadProgress.classList.add('bg-primary');
                    }, 2000);
                }
            })
            .catch(error => {
                console.error('Error en la subida:', error);
                clearInterval(progressInterval);

                // Mostrar error
                loadingMessage.textContent = `Error de conexión: ${error.message}`;
                uploadProgress.classList.remove('bg-primary');
                uploadProgress.classList.add('bg-danger');

                // Cerrar modal después de un breve retraso
                setTimeout(() => {
                    modal.hide();
                    uploadProgress.classList.remove('bg-danger');
                    uploadProgress.classList.add('bg-primary');
                }, 2000);
            });
        }

        // Función para añadir eventos a los botones de fotos
        function addPhotoButtonEvents() {
            // Establecer como principal
            document.querySelectorAll('.set-primary-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const mediaId = this.getAttribute('data-id');

                    const formData = new FormData();
                    formData.append('csrf_token', '<?= generateCsrfToken() ?>');
                    formData.append('media_id', mediaId);

                    fetch('<?= url('/usuario/set-primary-photo') ?>', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.reload();
                        } else {
                            alert(data.error || 'Error al establecer como principal');
                        }
                    })
                    .catch(error => {
                        alert('Error de conexión');
                    });
                });
            });

            // Eliminar foto
            document.querySelectorAll('.delete-photo-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    if (confirm('¿Está seguro de eliminar esta foto?')) {
                        const mediaId = this.getAttribute('data-id');

                        const formData = new FormData();
                        formData.append('csrf_token', '<?= generateCsrfToken() ?>');
                        formData.append('media_id', mediaId);

                        fetch('<?= url('/usuario/eliminar-media') ?>', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                window.location.reload();
                            } else {
                                alert(data.error || 'Error al eliminar foto');
                            }
                        })
                        .catch(error => {
                            alert('Error de conexión');
                        });
                    }
                });
            });
        }

        // Función para añadir eventos a los botones de videos
        function addVideoButtonEvents() {
            // Eliminar video
            document.querySelectorAll('.delete-video-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    if (confirm('¿Está seguro de eliminar este video?')) {
                        const mediaId = this.getAttribute('data-id');

                        const formData = new FormData();
                        formData.append('csrf_token', '<?= generateCsrfToken() ?>');
                        formData.append('media_id', mediaId);

                        fetch('<?= url('/usuario/eliminar-media') ?>', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                window.location.reload();
                            } else {
                                alert(data.error || 'Error al eliminar video');
                            }
                        })
                        .catch(error => {
                            alert('Error de conexión');
                        });
                    }
                });
            });
        }

        // Inicializar eventos
        addPhotoButtonEvents();
        addVideoButtonEvents();
        
        // Log final
        console.log('Script cargado completamente');
    });
</script>