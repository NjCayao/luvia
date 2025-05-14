# Estructura del Proyecto: Plataforma de luvia con Izipay
 proyecto PHP con AdminLTE3. Esta estructura seguirá un patrón MVC (Modelo-Vista-Controlador) para mantener el código organizado y mantenible.

# Estructura de Carpetas

luvia/
│
├── config/                      # Configuraciones
│   ├── database.php             # Conexión a la base de datos
│   ├── app.php                  # Configuración general
│   ├── izipay.php               # Configuración de Izipay
│   └── routes.php               # Definición de rutas
│
├── includes/                    # Archivos de inclusión
│   ├── functions.php            # Funciones generales
│   ├── auth.php                 # Funciones de autenticación
│   ├── validation.php           # Funciones de validación
│   └── database.php             # Funciones de base de datos
│
├── controllers/                 # Controladores
│   ├── HomeController.php       # Controlador de inicio
│   ├── AuthController.php       # Controlador de autenticación
│   ├── ProfileController.php    # Controlador de perfiles
│   ├── PaymentController.php    # Controlador de pagos
│   └── AdminController.php      # Controlador de administración
│
├── models/                      # Modelos
│   ├── User.php                 # Modelo de usuario
│   ├── Profile.php              # Modelo de perfil
│   ├── Payment.php              # Modelo de pago
│   ├── Subscription.php         # Modelo de suscripción
│   └── Plan.php                 # Modelo de plan
│
├── views/                       # Vistas
│   ├── layouts/                 # Plantillas base
│   │   ├── main.php             # Plantilla principal
│   │   ├── admin.php            # Plantilla de administración
│   │   └── auth.php             # Plantilla de autenticación
│   │
│   ├── home/                    # Vistas de inicio
│   │   ├── index.php            # Página de inicio
│   │   ├── category.php         # Categoría (mujer/hombre/trans)
│   │   └── view_profile.php     # Ver perfil
│   │
│   ├── auth/                    # Vistas de autenticación
│   │   ├── login.php            # Inicio de sesión
│   │   ├── register.php         # Registro
│   │   └── verify.php           # Verificación
│   │
│   ├── profile/                 # Vistas de perfil
│   │   ├── edit.php             # Editar perfil
│   │   ├── media.php            # Gestionar fotos/videos
│   │   └── dashboard.php        # Panel de usuario
│   │
│   ├── payment/                 # Vistas de pago
│   │   ├── plans.php            # Planes disponibles
│   │   ├── checkout.php         # Página de pago
│   │   ├── success.php          # Pago exitoso
│   │   └── failed.php           # Pago fallido
│   │
│   └── admin/                   # Vistas de administración
│       ├── dashboard.php        # Panel de control
│       ├── users.php            # Gestión de usuarios
│       └── payments.php         # Gestión de pagos
│
├── services/                    # Servicios 
│   ├── IzipayService.php        # Servicio de Izipay
│   └── SmsService.php           # Servicio de SMS
│
├── public/                      # Archivos públicos
│   ├── index.php                # Punto de entrada
│   ├── .htaccess                # Configuración del servidor web
│   ├── css/                     # Archivos CSS
│   ├── js/                      # Archivos JavaScript
│   ├── img/                     # Imágenes
│   └── plugins/                 # Plugins (AdminLTE, etc.)
│
└── uploads/                     # Archivos subidos
    ├── photos/                  # Fotos de perfiles
    ├── videos/                  # Videos de perfiles
    └── verification/            # Documentos de verificación



Iniciar sesión como administrador:

Email: admin@example.com
Contraseña: password


Iniciar sesión como anunciante:

Email: maria@example.com
Contraseña: password


Iniciar sesión como visitante:

Email: cliente@example.com
Contraseña: password