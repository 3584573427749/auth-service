<?php

declare(strict_types=1);

use App\Http\Actions\Role\CreateRoleAction;
use App\Http\Actions\Role\DeleteRoleAction;
use App\Http\Actions\Role\GetAllRolesAction;
use App\Http\Actions\Role\GetRoleAction;
use App\Http\Actions\Role\UpdateRoleAction;
use App\Http\Actions\User\CreateUserAction;
use App\Http\Actions\User\GetAllUsersAction;
use App\Http\Actions\User\GetUserAction;
use App\Http\Actions\User\RemoveUserAction;
use App\Http\Actions\User\SoftDeleteUserAction;
use App\Http\Actions\User\UpdateUserAction;
use App\Http\Actions\UserRole\CreateUserRoleAction;
use App\Http\Actions\UserRole\DeleteUserRoleAction;
use App\Http\Actions\UserRole\GetRoleUsersAction;
use App\Http\Actions\UserRole\GetUserRolesAction;
use Slim\App;

return function (App $app) : void {

    $app->post('/users', CreateUserAction::class);
    $app->get('/users', GetAllUsersAction::class);
    $app->get('/users/{id}', GetUserAction::class);
    $app->put('/users/{id}', UpdateUserAction::class);
    $app->delete('/users/{id}', SoftDeleteUserAction::class);
    $app->delete('/users/{id}/permanent', RemoveUserAction::class);
    $app->post('/roles', CreateRoleAction::class);
    $app->get('/roles', GetAllRolesAction::class);
    $app->get('/roles/{id}', GetRoleAction::class);
    $app->put('/roles/{id}', UpdateRoleAction::class);
    $app->delete('/roles/{id}', DeleteRoleAction::class);

    $app->get('/users/{id}/roles', GetUserRolesAction::class);
    $app->post('/users/{id}/roles', CreateUserRoleAction::class);
    $app->post('/users/{id}/roles/{roleId}', DeleteUserRoleAction::class);
    $app->get('/roles/{id}/users', GetRoleUsersAction::class);

};
