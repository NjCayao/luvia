<?php
// config/routes.php

// Definición de rutas
$routes = [
    // Rutas públicas
    '/' => [
        'controller' => 'HomeController',
        'action' => 'index',
        
    ],
    '/categoria/{gender}' => [
        'controller' => 'HomeController',
        'action' => 'category'
    ],
    '/perfil/{id}' => [
        'controller' => 'HomeController',
        'action' => 'viewProfile'
    ],
    
    // Autenticación
    '/registro' => [
        'controller' => 'AuthController',
        'action' => 'register',
        'method' => 'GET'
    ],
    '/registro/procesar' => [
        'controller' => 'AuthController',
        'action' => 'processRegister',
        'method' => 'POST'
    ],
    '/login' => [
        'controller' => 'AuthController',
        'action' => 'login',
        'method' => 'GET'
    ],
    '/login/procesar' => [
        'controller' => 'AuthController',
        'action' => 'processLogin',
        'method' => 'POST'
    ],
    '/verificar/{token}' => [
        'controller' => 'AuthController',
        'action' => 'verify'
    ],
    '/logout' => [
        'controller' => 'AuthController',
        'action' => 'logout',
        'auth' => true
    ],
    
    // Rutas de usuario (requieren autenticación)
    '/usuario/perfil' => [
        'controller' => 'ProfileController',
        'action' => 'showProfile',
        'auth' => true
    ],
    '/usuario/editar' => [
        'controller' => 'ProfileController',
        'action' => 'showEdit',
        'auth' => true
    ],
    '/usuario/editar/procesar' => [
        'controller' => 'ProfileController',
        'action' => 'processEdit',
        'auth' => true,
        'method' => 'POST'
    ],
    '/usuario/medios' => [
        'controller' => 'ProfileController',
        'action' => 'showMedia',
        'auth' => true
    ],
    '/usuario/subir-foto' => [
        'controller' => 'ProfileController',
        'action' => 'uploadPhoto',
        'auth' => true,
        'method' => 'POST'
    ],
    
    // Rutas de pago
    '/pago/planes' => [
        'controller' => 'PaymentController',
        'action' => 'showPlans',
        'auth' => true
    ],
    '/pago/checkout/{planId}' => [
        'controller' => 'PaymentController',
        'action' => 'checkout',
        'auth' => true
    ],
    '/pago/procesar-tarjeta' => [
        'controller' => 'PaymentController',
        'action' => 'processCardPayment',
        'auth' => true,
        'method' => 'POST'
    ],
    '/pago/procesar-yape' => [
        'controller' => 'PaymentController',
        'action' => 'processYapePayment',
        'auth' => true,
        'method' => 'POST'
    ],
    '/pago/confirmacion' => [
        'controller' => 'PaymentController',
        'action' => 'confirmation'
    ],
    '/pago/exito' => [
        'controller' => 'PaymentController',
        'action' => 'success',
        'auth' => true
    ],
    '/pago/fallido' => [
        'controller' => 'PaymentController',
        'action' => 'failed',
        'auth' => true
    ],
    
    // Webhook de Izipay (sin autenticación)
    '/api/pago/ipn' => [
        'controller' => 'PaymentController',
        'action' => 'ipnHandler',
        'method' => 'POST'
    ],
    
    // Rutas de administración
    '/admin' => [
        'controller' => 'AdminController',
        'action' => 'dashboard',
        'auth' => true,
        'admin' => true
    ],
    '/admin/usuarios' => [
        'controller' => 'AdminController',
        'action' => 'users',
        'auth' => true,
        'admin' => true
    ],
    '/admin/perfiles' => [
        'controller' => 'AdminController',
        'action' => 'profiles',
        'auth' => true,
        'admin' => true
    ],
    '/admin/pagos' => [
        'controller' => 'AdminController',
        'action' => 'payments',
        'auth' => true,
        'admin' => true
    ],

    // Autenticación
    '/login' => [
        'controller' => 'AuthController',
        'action' => 'login',
        'method' => 'GET'
    ],
    '/login/procesar' => [
        'controller' => 'AuthController',
        'action' => 'processLogin',
        'method' => 'POST'
    ],
    '/registro' => [
        'controller' => 'AuthController',
        'action' => 'register',
        'method' => 'GET'
    ],
    '/registro/procesar' => [
        'controller' => 'AuthController',
        'action' => 'processRegister',
        'method' => 'POST'
    ],
    '/verificar' => [
        'controller' => 'AuthController',
        'action' => 'verify',
        'method' => 'GET'
    ],
    '/verificar/procesar' => [
        'controller' => 'AuthController',
        'action' => 'processVerify',
        'method' => 'POST'
    ],
    '/verificar/reenviar' => [
        'controller' => 'AuthController',
        'action' => 'resendCode',
        'method' => 'POST'
    ],
    '/forgot-password' => [
        'controller' => 'AuthController',
        'action' => 'forgotPassword',
        'method' => 'GET'
    ],
    '/forgot-password/procesar' => [
        'controller' => 'AuthController',
        'action' => 'processForgotPassword',
        'method' => 'POST'
    ],
    '/reset-password' => [
        'controller' => 'AuthController',
        'action' => 'resetPassword',
        'method' => 'GET'
    ],
    '/reset-password/procesar' => [
        'controller' => 'AuthController',
        'action' => 'processResetPassword',
        'method' => 'POST'
    ],
    '/logout' => [
        'controller' => 'AuthController',
        'action' => 'logout'
    ],

    // Usuario y Perfil
    '/usuario/dashboard' => [
        'controller' => 'ProfileController',
        'action' => 'dashboard',
        'auth' => true
    ],
    '/usuario/editar' => [
        'controller' => 'ProfileController',
        'action' => 'showEdit',
        'auth' => true
    ],
    '/usuario/editar/procesar' => [
        'controller' => 'ProfileController',
        'action' => 'processEdit',
        'auth' => true,
        'method' => 'POST'
    ],
    '/usuario/medios' => [
        'controller' => 'ProfileController',
        'action' => 'showMedia',
        'auth' => true
    ],
    '/usuario/subir-foto' => [
        'controller' => 'ProfileController',
        'action' => 'uploadPhoto',
        'auth' => true,
        'method' => 'POST'
    ],
    '/usuario/subir-video' => [
        'controller' => 'ProfileController',
        'action' => 'uploadVideo',
        'auth' => true,
        'method' => 'POST'
    ],
    '/usuario/set-primary-photo' => [
        'controller' => 'ProfileController',
        'action' => 'setPrimaryPhoto',
        'auth' => true,
        'method' => 'POST'
    ],
    '/usuario/eliminar-media' => [
        'controller' => 'ProfileController',
        'action' => 'deleteMedia',
        'auth' => true,
        'method' => 'POST'
    ],
    '/usuario/reordenar-media' => [
        'controller' => 'ProfileController',
        'action' => 'reorderMedia',
        'auth' => true,
        'method' => 'POST'
    ],
    '/usuario/tarifas' => [
        'controller' => 'ProfileController',
        'action' => 'showRates',
        'auth' => true
    ],
    '/usuario/tarifas/guardar' => [
        'controller' => 'ProfileController',
        'action' => 'saveRates',
        'auth' => true,
        'method' => 'POST'
    ],

    // Rutas públicas
    '/' => [
        'controller' => 'HomeController',
        'action' => 'index'
    ],
    '/categoria/{gender}' => [
        'controller' => 'HomeController',
        'action' => 'category'
    ],
    '/perfil/{id}' => [
        'controller' => 'HomeController',
        'action' => 'viewProfile'
    ],
    '/buscar' => [
        'controller' => 'HomeController',
        'action' => 'search'
    ],
    '/track-whatsapp' => [
        'controller' => 'HomeController',
        'action' => 'trackWhatsappClick',
        'method' => 'POST'
    ],
    '/acerca-de' => [
        'controller' => 'HomeController',
        'action' => 'about'
    ],
    '/terminos' => [
        'controller' => 'HomeController',
        'action' => 'terms'
    ],
    '/privacidad' => [
        'controller' => 'HomeController',
        'action' => 'privacy'
    ],
    '/contacto' => [
        'controller' => 'HomeController',
        'action' => 'contact'
    ],
    '/contacto/enviar' => [
        'controller' => 'HomeController',
        'action' => 'processContact',
        'method' => 'POST'
    ],

    // Suscripciones
    '/pago/planes' => [
        'controller' => 'SubscriptionController',
        'action' => 'showPlans',
        'auth' => true
    ],
    '/usuario/suscripciones' => [
        'controller' => 'SubscriptionController',
        'action' => 'history',
        'auth' => true
    ],
    '/usuario/suscripciones/cancelar' => [
        'controller' => 'SubscriptionController',
        'action' => 'cancel',
        'auth' => true,
        'method' => 'POST'
    ],
    '/usuario/suscripciones/activar-renovacion' => [
        'controller' => 'SubscriptionController',
        'action' => 'enableAutoRenew',
        'auth' => true,
        'method' => 'POST'
    ],
    '/usuario/suscripciones/renovar' => [
        'controller' => 'SubscriptionController',
        'action' => 'renew',
        'auth' => true
    ]
     
];