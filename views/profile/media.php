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
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
}
.photo-item, .video-item {
    margin-bottom: 20px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const photoUploadBtn = document.getElementById('upload-photo-btn');
    const photoInput = document.getElementById('photo-input');
    const photoUploadForm = document.getElementById('photo-upload-form');
    const photoContainer = document.getElementById('photos-container');
    
    const videoUploadBtn = document.getElementById('upload-video-btn');
    const videoInput = document.getElementById('video-input');
    const videoUploadForm = document.getElementById('video-upload-form');
    const videoContainer = document.getElementById('videos-container');
    
    const loadingModal = new bootstrap.Modal(document.getElementById('loading-modal'), {
        backdrop: 'static',
        keyboard: false
    });
    
    const uploadProgress = document.getElementById('upload-progress');
    const loadingMessage = document.getElementById('loading-message');
    
    // Subida de fotos
    photoUploadBtn.addEventListener('click', function() {
        photoInput.click();
    });
    
    photoInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            uploadFile(photoUploadForm, 'photo', '/usuario/subir-foto');
        }
    });
    
    // Subida de videos
    videoUploadBtn.addEventListener('click', function() {
        videoInput.click();
    });
    
    videoInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            uploadFile(videoUploadForm, 'video', '/usuario/subir-video');
        }
    });
    
    // Función para subir archivos
    function uploadFile(form, fileType, url) {
        const formData = new FormData(form);
        
        // Mostrar modal de carga
        loadingMessage.textContent = `Subiendo ${fileType === 'photo' ? 'foto' : 'video'}...`;
        uploadProgress.style.width = '0%';
        loadingModal.show();
        
        // Simular progreso (ya que fetch no proporciona progreso real)
        let progress = 0;
        const progressInterval = setInterval(() => {
            progress += Math.random() * 5;
            if (progress > 90) {
                progress = 90; // Dejar el 10% final para cuando se complete
                clearInterval(progressInterval);
            }
            uploadProgress.style.width = `${progress}%`;
        }, 300);
        
        fetch(url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            clearInterval(progressInterval);
            
            if (data.success) {
                // Completar barra de progreso
                uploadProgress.style.width = '100%';
                
                // Actualizar mensaje
                loadingMessage.textContent = `${fileType === 'photo' ? 'Foto' : 'Video'} subido correctamente`;
                
                // Cerrar modal después de un breve retraso
                setTimeout(() => {
                    loadingModal.hide();
                    
                    // Actualizar interfaz
                    if (fileType === 'photo') {
                        updatePhotosList();
                    } else {
                        updateVideosList();
                    }
                    
                    // Resetear formulario
                    form.reset();
                }, 1000);
                
            } else {
                // Mostrar error
                loadingMessage.textContent = data.error || `Error al subir ${fileType === 'photo' ? 'foto' : 'video'}`;
                uploadProgress.classList.remove('bg-primary');
                uploadProgress.classList.add('bg-danger');
                
                // Cerrar modal después de un breve retraso
                setTimeout(() => {
                    loadingModal.hide();
                    uploadProgress.classList.remove('bg-danger');
                    uploadProgress.classList.add('bg-primary');
                }, 2000);
            }
        })
        .catch(error => {
            clearInterval(progressInterval);
            
            // Mostrar error
            loadingMessage.textContent = `Error de conexión: ${error.message}`;
            uploadProgress.classList.remove('bg-primary');
            uploadProgress.classList.add('bg-danger');
            
            // Cerrar modal después de un breve retraso
            setTimeout(() => {
                loadingModal.hide();
                uploadProgress.classList.remove('bg-danger');
                uploadProgress.classList.add('bg-primary');
            }, 2000);
        });
    }
    
    // Función para actualizar lista de fotos
    function updatePhotosList() {
        fetch('/usuario/medios/fotos')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Actualizar contador
                    document.querySelector('#photos-tab').innerHTML = `<i class="fas fa-image"></i> Fotos (${data.photos.length}/${data.max_photos})`;
                    
                    // Actualizar botón de subida
                    if (data.photos.length >= data.max_photos) {
                        photoUploadBtn.disabled = true;
                        photoUploadBtn.classList.remove('btn-primary');
                        photoUploadBtn.classList.add('btn-secondary');
                        photoUploadBtn.textContent = 'Límite de fotos alcanzado';
                    }
                    
                    // Actualizar lista de fotos
                    if (data.photos.length === 0) {
                        photoContainer.innerHTML = `
                            <div class="col-12">
                                <div class="alert alert-warning">
                                    <i class="icon fas fa-exclamation-triangle"></i>
                                    No has subido ninguna foto. Sube al menos una foto para que tu perfil sea visible.
                                </div>
                            </div>
                        `;
                    } else {
                        photoContainer.innerHTML = '';
                        
                        data.photos.forEach(photo => {
                            const photoElement = document.createElement('div');
                            photoElement.className = 'col-md-4 col-sm-6 photo-item';
                            photoElement.setAttribute('data-id', photo.id);
                            
                            const isPrimary = photo.is_primary ? `
                                <button type="button" class="btn btn-sm btn-success" disabled>
                                    <i class="fas fa-check"></i> Principal
                                </button>
                            ` : `
                                <button type="button" class="btn btn-sm btn-info set-primary-btn" data-id="${photo.id}">
                                    <i class="fas fa-star"></i> Principal
                                </button>
                            `;
                            
                            photoElement.innerHTML = `
                                <div class="card">
                                    <div class="card-img-top photo-preview" style="background-image: url('${url('uploads/photos/' + photo.filename)}')"></div>
                                    <div class="card-body">
                                        <div class="btn-group w-100">
                                            ${isPrimary}
                                            <button type="button" class="btn btn-sm btn-danger delete-photo-btn" data-id="${photo.id}">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            `;
                            
                            photoContainer.appendChild(photoElement);
                        });
                        
                        // Añadir eventos a los nuevos botones
                        addPhotoButtonEvents();
                    }
                }
            });
    }
    
    // Función para actualizar lista de videos
    function updateVideosList() {
        fetch('/usuario/medios/videos')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Actualizar contador
                    document.querySelector('#videos-tab').innerHTML = `<i class="fas fa-video"></i> Videos (${data.videos.length}/${data.max_videos})`;
                    
                    // Actualizar botón de subida
                    if (data.videos.length >= data.max_videos) {
                        videoUploadBtn.disabled = true;
                        videoUploadBtn.classList.remove('btn-primary');
                        videoUploadBtn.classList.add('btn-secondary');
                        videoUploadBtn.textContent = 'Límite de videos alcanzado';
                    }
                    
                    // Actualizar lista de videos
                    if (data.videos.length === 0) {
                        videoContainer.innerHTML = `
                            <div class="col-12">
                                <div class="alert alert-warning">
                                    <i class="icon fas fa-exclamation-triangle"></i>
                                    No has subido ningún video. Los videos son opcionales pero aumentan la visibilidad de tu perfil.
                                </div>
                            </div>
                        `;
                    } else {
                        videoContainer.innerHTML = '';
                        
                        data.videos.forEach(video => {
                            const videoElement = document.createElement('div');
                            videoElement.className = 'col-md-6 video-item';
                            videoElement.setAttribute('data-id', video.id);
                            
                            videoElement.innerHTML = `
                                <div class="card">
                                    <div class="card-body">
                                        <video controls class="w-100 rounded">
                                            <source src="${url('uploads/videos/' + video.filename)}" 
                                                    type="${video.mime_type}">
                                            Tu navegador no soporta la reproducción de videos.
                                        </video>
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-danger delete-video-btn" data-id="${video.id}">
                                                <i class="fas fa-trash"></i> Eliminar Video
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            `;
                            
                            videoContainer.appendChild(videoElement);
                        });
                        
                        // Añadir eventos a los nuevos botones
                        addVideoButtonEvents();
                    }
                }
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
                
                fetch('/usuario/set-primary-photo', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updatePhotosList();
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
                    
                    fetch('/usuario/eliminar-media', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            updatePhotosList();
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
                    
                    fetch('/usuario/eliminar-media', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            updateVideosList();
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
});
</script>