<?php
// database/seeds/PlansSeeder.php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Plan.php';

/**
 * Inicializa los planes predeterminados
 */
function seedPlans() {
    // Planes para anunciantes
    $advertiserPlans = [
        [
            'name' => 'Plan Básico',
            'user_type' => 'advertiser',
            'duration' => 30,
            'price' => 50.00,
            'max_photos' => 2,
            'max_videos' => 1,
            'featured' => false,
            'description' => "Plan básico para anunciantes\nIdeal para comenzar"
        ],
        [
            'name' => 'Plan Premium',
            'user_type' => 'advertiser',
            'duration' => 30,
            'price' => 100.00,
            'max_photos' => 5,
            'max_videos' => 2,
            'featured' => true,
            'description' => "Plan premium con mayores beneficios\nPerfil destacado en búsquedas"
        ],
        [
            'name' => 'Plan VIP',
            'user_type' => 'advertiser',
            'duration' => 30,
            'price' => 150.00,
            'max_photos' => 8,
            'max_videos' => 3,
            'featured' => false,
            'description' => "Plan exclusivo con todos los beneficios\nMáxima visibilidad y prioridad"
        ],
        [
            'name' => 'Plan Trimestral',
            'user_type' => 'advertiser',
            'duration' => 90,
            'price' => 250.00,
            'max_photos' => 5,
            'max_videos' => 2,
            'featured' => false,
            'description' => "Plan por 3 meses con descuento\nMismos beneficios que el Premium"
        ]
    ];
    
    // Planes para visitantes
    $visitorPlans = [
        [
            'name' => 'Acceso Básico',
            'user_type' => 'visitor',
            'duration' => 15,
            'price' => 5.00,
            'max_photos' => null,
            'max_videos' => null,
            'featured' => false,
            'description' => "Acceso básico por 15 días\nIdeal para probar el servicio"
        ],
        [
            'name' => 'Acceso Mensual',
            'user_type' => 'visitor',
            'duration' => 30,
            'price' => 15.00,
            'max_photos' => null,
            'max_videos' => null,
            'featured' => true,
            'description' => "Acceso completo por 30 días\nMejor relación calidad-precio"
        ],
        [
            'name' => 'Acceso Trimestral',
            'user_type' => 'visitor',
            'duration' => 90,
            'price' => 35.00,
            'max_photos' => null,
            'max_videos' => null,
            'featured' => false,
            'description' => "Acceso completo por 90 días\nAhorra con esta suscripción"
        ]
    ];
    
    // Unir todos los planes
    $allPlans = array_merge($advertiserPlans, $visitorPlans);
    
    // Insertar planes
    foreach ($allPlans as $planData) {
        // Verificar si ya existe un plan similar
        $existingPlan = Plan::getByDurationAndType($planData['duration'], $planData['user_type']);
        
        if (!$existingPlan) {
            Plan::create($planData);
            echo "Plan creado: " . $planData['name'] . " (" . $planData['user_type'] . ")\n";
        } else {
            echo "Plan ya existe: " . $planData['name'] . " (" . $planData['user_type'] . ")\n";
        }
    }
    
    echo "Planes inicializados correctamente\n";
}

// Ejecutar el seeder
seedPlans();