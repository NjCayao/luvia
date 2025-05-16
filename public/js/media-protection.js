/**
 * media-protection.js - Protección y marcas de agua para fotos y videos
 * Parte del sistema Luvia.pe
 */

// Namespace para evitar conflictos con otros scripts
const LuviaMediaProtection = (function() {
    // Configuración personalizable
    const config = {
        watermarkText: 'Luvia.pe',
        photoContainerSelector: '.protected-photo-container',
        videoContainerSelector: '.protected-video-container',
        watermarkOpacity: 0.8,
        enableCarousel: true,
        enableFullscreenProtection: true,
        enableContextMenuBlocking: true
    };
    
    // Inicialización general
    function init(customConfig = {}) {
        // Combinar configuración predeterminada con la personalizada
        Object.assign(config, customConfig);
        
        // Inicializar protección para fotos
        initPhotoProtection();
        
        // Inicializar protección para videos
        initVideoProtection();
        
        // Crear carrusel modal para fotos si está habilitado
        if (config.enableCarousel) {
            createPhotoCarousel();
        }
        
        // Aplicar protecciones generales
        if (config.enableContextMenuBlocking) {
            addGeneralProtections();
        }
        
        console.log('Luvia Media Protection inicializado');
    }
    
    // Protección para fotos
    function initPhotoProtection() {
        const photoContainers = document.querySelectorAll(config.photoContainerSelector);
        
        photoContainers.forEach(container => {
            // Obtener elementos
            const img = container.querySelector('img');
            if (!img) return;
            
            // Crear capa de protección si no existe
            let protectionLayer = container.querySelector('.photo-protection-layer');
            if (!protectionLayer) {
                protectionLayer = document.createElement('div');
                protectionLayer.className = 'photo-protection-layer';
                container.appendChild(protectionLayer);
            }
            
            // Crear marca de agua si no existe
            let watermark = container.querySelector('.photo-watermark');
            if (!watermark) {
                watermark = document.createElement('div');
                watermark.className = 'photo-watermark';
                watermark.textContent = config.watermarkText;
                container.appendChild(watermark);
            }
            
            // Prevenir clic derecho y arrastrar
            applyBasicProtection(container, img);
            
            // Habilitar vista ampliada al hacer clic
            if (config.enableCarousel) {
                protectionLayer.addEventListener('click', () => {
                    const photoId = img.getAttribute('data-photo-id') || '';
                    openPhotoCarousel(photoId, img.src);
                });
            }
        });
    }
    
    // Protección para videos
    function initVideoProtection() {
        const videoContainers = document.querySelectorAll(config.videoContainerSelector);
        
        videoContainers.forEach(container => {
            // Obtener elementos
            const video = container.querySelector('video');
            if (!video) return;
            
            // Crear capa de protección si no existe
            let protectionLayer = container.querySelector('.video-protection-layer');
            if (!protectionLayer) {
                protectionLayer = document.createElement('div');
                protectionLayer.className = 'video-protection-layer';
                container.appendChild(protectionLayer);
            }
            
            // Crear marca de agua si no existe
            let watermark = container.querySelector('.video-watermark');
            if (!watermark) {
                watermark = document.createElement('div');
                watermark.className = 'video-watermark';
                watermark.textContent = config.watermarkText;
                container.appendChild(watermark);
            }
            
            // Prevenir clic derecho y arrastrar
            applyBasicProtection(container, video);
            
            // Manejar reproducción/pausa al hacer clic
            protectionLayer.addEventListener('click', () => {
                if (video.paused) {
                    video.play();
                } else {
                    video.pause();
                }
            });
            
            // Manejar marca de agua en pantalla completa
            if (config.enableFullscreenProtection) {
                handleFullScreen(container, video, watermark);
            }
        });
    }
    
    // Aplicar protecciones básicas a cualquier elemento
    function applyBasicProtection(container, element) {
        // Prevenir clic derecho
        container.addEventListener('contextmenu', e => {
            e.preventDefault();
            return false;
        });
        
        // Prevenir arrastrar
        element.addEventListener('dragstart', e => {
            e.preventDefault();
            return false;
        });
        
        // Deshabilitar salvar imagen
        element.addEventListener('copy', e => {
            e.preventDefault();
            return false;
        });
        
        // Deshabilitar inspección (para imágenes)
        element.setAttribute('draggable', 'false');
        element.style.userSelect = 'none';
        element.style.webkitUserSelect = 'none';
    }
    
    // Crear el carrusel modal para fotos
    function createPhotoCarousel() {
        // Verificar si ya existe
        if (document.getElementById('photo-carousel')) return;
        
        // Crear estructura del carrusel
        const modal = document.createElement('div');
        modal.id = 'photo-carousel';
        modal.className = 'photo-carousel-modal';
        
        const carouselContainer = document.createElement('div');
        carouselContainer.className = 'carousel-container';
        
        // Controles del carrusel
        const controls = document.createElement('div');
        controls.className = 'carousel-controls';
        
        const prevBtn = document.createElement('button');
        prevBtn.className = 'carousel-control prev';
        prevBtn.innerHTML = '<i class="fas fa-chevron-left"></i>';
        prevBtn.addEventListener('click', function() {
            navigateCarousel(-1);
        });
        
        const nextBtn = document.createElement('button');
        nextBtn.className = 'carousel-control next';
        nextBtn.innerHTML = '<i class="fas fa-chevron-right"></i>';
        nextBtn.addEventListener('click', function() {
            navigateCarousel(1);
        });
        
        controls.appendChild(prevBtn);
        controls.appendChild(nextBtn);
        
        // Botón para cerrar
        const closeBtn = document.createElement('button');
        closeBtn.className = 'carousel-close';
        closeBtn.innerHTML = '&times;';
        closeBtn.addEventListener('click', closePhotoCarousel);
        
        // Contador de fotos
        const counter = document.createElement('div');
        counter.className = 'carousel-counter';
        counter.id = 'carousel-counter';
        
        // Marca de agua
        const watermark = document.createElement('div');
        watermark.className = 'carousel-watermark';
        watermark.textContent = config.watermarkText;
        
        // Ensamblar el carrusel
        carouselContainer.appendChild(controls);
        modal.appendChild(carouselContainer);
        modal.appendChild(closeBtn);
        modal.appendChild(counter);
        modal.appendChild(watermark);
        
        // Prevenir clic derecho y arrastrar
        modal.addEventListener('contextmenu', e => {
            e.preventDefault();
            return false;
        });
        
        // Cerrar con tecla Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && document.getElementById('photo-carousel').style.display === 'block') {
                closePhotoCarousel();
            } else if (e.key === 'ArrowLeft' && document.getElementById('photo-carousel').style.display === 'block') {
                navigateCarousel(-1);
            } else if (e.key === 'ArrowRight' && document.getElementById('photo-carousel').style.display === 'block') {
                navigateCarousel(1);
            }
        });
        
        // Añadir al documento
        document.body.appendChild(modal);
    }
    
    // Variables para el carrusel
    let currentSlideIndex = 0;
    let totalSlides = 0;
    let photoSlides = [];
    let photoSources = [];
    
    // Abrir el carrusel con la foto seleccionada
    function openPhotoCarousel(selectedPhotoId, selectedPhotoSrc = null) {
        const modal = document.getElementById('photo-carousel');
        const container = modal.querySelector('.carousel-container');
        
        // Limpiar slides existentes
        container.querySelectorAll('.carousel-slide').forEach(slide => slide.remove());
        
        // Si tenemos el src pero no el ID, crear directamente
        if (selectedPhotoSrc && !selectedPhotoId) {
            const slide = createCarouselSlide('single', selectedPhotoSrc);
            container.appendChild(slide);
            photoSlides = [slide];
            totalSlides = 1;
            currentSlideIndex = 0;
            
            // Ocultar controles de navegación si solo hay una foto
            modal.querySelector('.carousel-controls').style.display = 'none';
            modal.querySelector('#carousel-counter').style.display = 'none';
        } else {
            // Obtener todas las fotos para el carrusel
            const allPhotos = document.querySelectorAll(config.photoContainerSelector + ' img');
            photoSlides = [];
            photoSources = [];
            
            // Recopilar fuentes de las fotos
            allPhotos.forEach((photo, index) => {
                const photoId = photo.getAttribute('data-photo-id') || '';
                const src = photo.src;
                
                photoSources.push({
                    id: photoId,
                    src: src
                });
                
                // Guardar índice de la foto seleccionada
                if (photoId === selectedPhotoId) {
                    currentSlideIndex = index;
                }
            });
            
            // Crear slides para cada foto
            photoSources.forEach(photoData => {
                const slide = createCarouselSlide(photoData.id, photoData.src);
                container.appendChild(slide);
                photoSlides.push(slide);
            });
            
            totalSlides = photoSlides.length;
            
            // Mostrar controles de navegación
            modal.querySelector('.carousel-controls').style.display = 'flex';
            modal.querySelector('#carousel-counter').style.display = 'block';
        }
        
        // Actualizar contador
        updateCounter();
        
        // Mostrar la foto seleccionada
        showSlide(currentSlideIndex);
        
        // Mostrar el modal
        modal.style.display = 'block';
    }
    
    // Crear un slide para el carrusel
    function createCarouselSlide(id, src) {
        const slide = document.createElement('div');
        slide.className = 'carousel-slide';
        if (id !== 'single') {
            slide.setAttribute('data-photo-id', id);
        }
        
        const img = document.createElement('img');
        img.src = src;
        img.className = 'carousel-image';
        img.draggable = false;
        
        // Prevenir clic derecho
        img.addEventListener('contextmenu', e => {
            e.preventDefault();
            return false;
        });
        
        slide.appendChild(img);
        return slide;
    }
    
    // Mostrar una foto específica en el carrusel
    function showSlide(index) {
        // Ocultar todas las fotos
        photoSlides.forEach(slide => {
            slide.classList.remove('active');
        });
        
        // Asegurar que el índice esté dentro del rango
        if (index >= totalSlides) {
            currentSlideIndex = 0;
        } else if (index < 0) {
            currentSlideIndex = totalSlides - 1;
        } else {
            currentSlideIndex = index;
        }
        
        // Mostrar la foto actual
        photoSlides[currentSlideIndex].classList.add('active');
        
        // Actualizar contador
        updateCounter();
    }
    
    // Navegar por el carrusel
    function navigateCarousel(direction) {
        showSlide(currentSlideIndex + direction);
    }
    
    // Actualizar contador de fotos
    function updateCounter() {
        const counter = document.getElementById('carousel-counter');
        if (counter) {
            counter.textContent = `${currentSlideIndex + 1} / ${totalSlides}`;
        }
    }
    
    // Cerrar el carrusel
    function closePhotoCarousel() {
        const modal = document.getElementById('photo-carousel');
        if (modal) {
            modal.style.display = 'none';
        }
    }
    
    // Manejar pantalla completa para videos
    function handleFullScreen(container, video, watermark) {
        // Eventos de cambio de pantalla completa
        document.addEventListener('fullscreenchange', updateFullScreenWatermark);
        document.addEventListener('webkitfullscreenchange', updateFullScreenWatermark);
        document.addEventListener('mozfullscreenchange', updateFullScreenWatermark);
        document.addEventListener('MSFullscreenChange', updateFullScreenWatermark);
        
        function updateFullScreenWatermark() {
            // Eliminar marca de agua existente de pantalla completa
            const existingMark = document.querySelector('.fullscreen-watermark');
            if (existingMark) {
                existingMark.remove();
            }
            
            // Verificar si estamos en pantalla completa
            if (document.fullscreenElement || 
                document.webkitFullscreenElement || 
                document.mozFullScreenElement || 
                document.msFullscreenElement) {
                
                // Crear marca de agua para pantalla completa
                const fsWatermark = document.createElement('div');
                fsWatermark.className = 'fullscreen-watermark';
                fsWatermark.textContent = config.watermarkText;
                document.body.appendChild(fsWatermark);
            }
        }
    }
    
    // Añadir protecciones generales
    function addGeneralProtections() {
        // Prevenir clic derecho en todo el documento para fotos y videos
        document.addEventListener('contextmenu', function(e) {
            if (e.target.closest('.photo-item, .video-item, .photo-carousel-modal, .protected-photo-container, .protected-video-container')) {
                e.preventDefault();
                return false;
            }
        });
        
        // Detectar herramientas de desarrollador (básico)
        if (config.enableFullscreenProtection) {
            window.addEventListener('keydown', function(e) {
                // Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+Shift+C, F12
                if ((e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'J' || e.key === 'C')) || 
                    e.key === 'F12') {
                    console.log('Developer tools detected');
                }
            });
        }
    }
    
    // APIs públicas
    return {
        init: init,
        openPhotoCarousel: openPhotoCarousel,
        applyWatermark: initPhotoProtection
    };
})();

// Inicializar automáticamente cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Verificar si existe la configuración global
    const customConfig = window.luviaMediaConfig || {};
    LuviaMediaProtection.init(customConfig);
});