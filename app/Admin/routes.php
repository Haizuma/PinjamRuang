<?php

use App\Admin\Controllers\API\V1\AdministratorApiController;
use App\Admin\Controllers\API\V1\RoomApiController;
use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('home');

    $router->resource('room-types', RoomTypeController::class);
    $router->resource('rooms', RoomController::class);
    $router->resource('borrow-rooms', BorrowRoomController::class);

    $router->group(['prefix' => 'api'], function (Router $router) {
        // AdministratorApiController
        $router->get('employees', [AdministratorApiController::class, 'getEmployees']);
        $router->get('section-heads', [AdministratorApiController::class, 'getSectionHeads']);
        $router->get('general-admins', [AdministratorApiController::class, 'getGeneralAdmins']);

        // RoomApiController
        $router->get('rooms', [RoomApiController::class, 'getRooms']);
    });
});
