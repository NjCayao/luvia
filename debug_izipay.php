<!DOCTYPE html>
<html>
<head>
    <title>Debug Izipay KR Object</title>
    
    <!-- CSS de Izipay -->
    <link rel="stylesheet" href="https://api.micuentaweb.pe/static/js/krypton-client/V4.0/ext/classic-reset.css">
    
    <!-- Script principal de Izipay -->
    <script src="https://api.micuentaweb.pe/static/js/krypton-client/V4.0/stable/kr-payment-form.min.js"
            kr-public-key="13448745:testpublickey_XxLY9Q0zcRG18WNjf5ah1GUhhlliqNRicaaJiWhXDp2Tb"
            kr-post-url-success="https://erophia.com/pago/confirmacion"
            kr-post-url-refused="https://erophia.com/pago/fallido">
    </script>
    
    <!-- Tema clásico -->
    <script src="https://api.micuentaweb.pe/static/js/krypton-client/V4.0/ext/classic.js"></script>
</head>
<body>
    <h1>Debug Izipay KR Object</h1>
    
    <div id="results"></div>
    
    <script>
        const results = document.getElementById('results');
        
        function addResult(message, status = 'info') {
            const div = document.createElement('div');
            div.style.padding = '10px';
            div.style.margin = '5px 0';
            div.style.border = '1px solid #ccc';
            div.style.borderRadius = '5px';
            
            if (status === 'success') {
                div.style.backgroundColor = '#d4edda';
                div.style.color = '#155724';
                div.style.borderColor = '#c3e6cb';
            } else if (status === 'error') {
                div.style.backgroundColor = '#f8d7da';
                div.style.color = '#721c24';
                div.style.borderColor = '#f5c6cb';
            } else {
                div.style.backgroundColor = '#d1ecf1';
                div.style.color = '#0c5460';
                div.style.borderColor = '#bee5eb';
            }
            
            div.innerHTML = message;
            results.appendChild(div);
        }
        
        // Verificar inmediatamente
        addResult('Verificando carga inicial...', 'info');
        
        if (typeof KR !== 'undefined') {
            addResult('✅ KR está disponible inmediatamente', 'success');
            addResult('KR methods: ' + Object.keys(KR).join(', '), 'info');
        } else {
            addResult('❌ KR no está disponible inmediatamente', 'error');
        }
        
        // Verificar después de que la página cargue
        document.addEventListener('DOMContentLoaded', function() {
            addResult('--- Verificando después de DOMContentLoaded ---', 'info');
            
            if (typeof KR !== 'undefined') {
                addResult('✅ KR está disponible después de DOMContentLoaded', 'success');
                addResult('KR methods: ' + Object.keys(KR).join(', '), 'info');
                
                // Verificar si KR está listo
                if (typeof KR.onFormReady === 'function') {
                    addResult('✅ KR.onFormReady está disponible', 'success');
                    
                    KR.onFormReady(function() {
                        addResult('✅ KR.onFormReady fue ejecutado - Formulario listo', 'success');
                    });
                } else {
                    addResult('❌ KR.onFormReady no está disponible', 'error');
                }
                
            } else {
                addResult('❌ KR aún no está disponible después de DOMContentLoaded', 'error');
            }
        });
        
        // Verificar después de un delay
        setTimeout(function() {
            addResult('--- Verificando después de 3 segundos ---', 'info');
            
            if (typeof KR !== 'undefined') {
                addResult('✅ KR está disponible después de 3 segundos', 'success');
            } else {
                addResult('❌ KR no está disponible después de 3 segundos', 'error');
                addResult('Scripts cargados en head:', 'info');
                
                const scripts = document.head.querySelectorAll('script');
                scripts.forEach(function(script, index) {
                    if (script.src) {
                        addResult(`Script ${index + 1}: ${script.src}`, 'info');
                    }
                });
            }
        }, 3000);
        
        // Verificar errores de red
        window.addEventListener('error', function(e) {
            if (e.target.tagName === 'SCRIPT') {
                addResult('❌ Error cargando script: ' + e.target.src, 'error');
            }
        });
        
        // Log de consola
        console.log('Debug Izipay - KR disponible:', typeof KR !== 'undefined');
    </script>
</body>
</html>