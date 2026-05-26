<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// ---------------------------------------------------------------
// Rutas públicas (sin sesión) — ARQUITECTURA.md §6
// Filtro 'noauth': redirige usuarios ya logueados
// ---------------------------------------------------------------
$routes->group('', ['filter' => 'noauth'], function ($routes) {
    $routes->get('/', 'Auth\LoginController::index');
    $routes->get('login', 'Auth\LoginController::index');
    $routes->post('login', 'Auth\LoginController::process');
    $routes->get('registro', 'Auth\RegisterController::index');
    $routes->post('registro', 'Auth\RegisterController::process');
    $routes->get('recuperar', 'Auth\RecoveryController::index');
    $routes->post('recuperar', 'Auth\RecoveryController::sendLink');
    $routes->get('reset/(:hash)', 'Auth\RecoveryController::resetForm/$1');
    $routes->post('reset', 'Auth\RecoveryController::resetProcess');
});

// ---------------------------------------------------------------
// Logout sin filter (puede acceder cualquiera)
// ---------------------------------------------------------------
$routes->get('logout', 'Auth\LoginController::logout');

// ---------------------------------------------------------------
// Rutas protegidas — ARQUITECTURA.md §6
// ---------------------------------------------------------------

// Dashboard usuarios (filter: auth)
$routes->group('dashboard', ['filter' => 'auth'], function ($routes) {
    $routes->get('', 'User\DashboardController::index');
    $routes->get('documentos', 'User\DocumentController::index');
    $routes->get('documentos/ver/(:num)', 'User\DocumentController::view/$1');
    $routes->post('documentos/subir', 'User\DocumentController::upload');
    $routes->post('documentos/inicial/subir', 'User\DocumentController::uploadInitial');
    $routes->post('documentos/enviar', 'User\DocumentController::submit');
    $routes->get('notificaciones', 'User\NotificationController::index');
    $routes->get('notificaciones/count', 'User\NotificationController::unreadCount');
    $routes->post('notificaciones/leer/(:num)', 'User\NotificationController::markRead/$1');
});

// Panel admin (filter: admin)
$routes->group('admin', ['filter' => 'admin'], function ($routes) {
    $routes->get('', 'Admin\DashboardController::index');
    $routes->get('tipos-documento', 'Admin\DocumentTypeController::index');
    $routes->get('tipos-documento/nuevo', 'Admin\DocumentTypeController::create');
    $routes->post('tipos-documento', 'Admin\DocumentTypeController::store');
    $routes->get('tipos-documento/(:num)/editar', 'Admin\DocumentTypeController::edit/$1');
    $routes->post('tipos-documento/(:num)', 'Admin\DocumentTypeController::update/$1');
    $routes->post('tipos-documento/(:num)/toggle', 'Admin\DocumentTypeController::toggle/$1');
    $routes->post('tipos-documento/reorder', 'Admin\DocumentTypeController::reorder');
    $routes->get('periodos', 'Admin\PeriodController::index');
    $routes->get('periodos/nuevo', 'Admin\PeriodController::create');
    $routes->post('periodos', 'Admin\PeriodController::store');
    $routes->get('periodos/(:num)/editar', 'Admin\PeriodController::edit/$1');
    $routes->post('periodos/(:num)', 'Admin\PeriodController::update/$1');
    $routes->post('periodos/(:num)/toggle', 'Admin\PeriodController::toggle/$1');

    // Gestión de usuarios
    $routes->get('usuarios', 'Admin\UserController::index');
    $routes->get('usuarios/(:num)', 'Admin\UserController::detail/$1');
    $routes->get('usuarios/(:num)/editar', 'Admin\UserController::edit/$1');
    $routes->post('usuarios/(:num)', 'Admin\UserController::update/$1');
    $routes->post('usuarios/(:num)/status', 'Admin\UserController::changeStatus/$1');
    $routes->post('usuarios/(:num)/reset-password', 'Admin\UserController::resetPassword/$1');
    $routes->post('usuarios/(:num)/validate-inscription', 'Admin\UserController::validateInscription/$1');
    $routes->post('usuarios/(:num)/eliminar', 'Admin\UserController::delete/$1');

    // Validación de documentos
    $routes->post('usuarios/(:num)/documento/(:num)', 'Admin\DocumentController::approveOrReject/$1/$2');
    $routes->get('documentos/ver/(:num)', 'Admin\DocumentController::view/$1');

    // Notificaciones admin
    $routes->get('notificaciones', 'Admin\NotificationController::index');
    $routes->post('notificaciones/send', 'Admin\NotificationController::send');
});

// ---------------------------------------------------------------
// Rutas de verificación de email (P06)
// ---------------------------------------------------------------
$routes->get('verificar-pendiente', 'Auth\VerifyController::pending');
$routes->get('verificar/(:hash)', 'Auth\VerifyController::confirm/$1');
$routes->post('verificar/reenviar', 'Auth\VerifyController::resend', ['filter' => 'auth']);
