<?php

use App\Admin\Controllers\API\V1\AdministratorApiController;
use App\Admin\Controllers\API\V1\RoomApiController;
use Illuminate\Routing\Router;
use App\Admin\Controllers\UserController;
use App\Admin\Controllers\RoomTypeController;
use App\Admin\Controllers\RoomController;
use App\Admin\Controllers\BorrowRoomController;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('home');
    $router->get('auth/users', [UserController::class, 'index'])->name('auth.users.index');

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
