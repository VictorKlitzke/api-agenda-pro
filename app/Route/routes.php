<?php

declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use App\Application\Actions\Auth\RegisterAction;
use App\Application\Actions\Auth\LoginAction;
use App\Application\Actions\Company\RegisterCompanyAction;
use App\Application\Actions\Company\ListCompaniesAction;
use App\Application\Actions\Company\ViewCompanyAction;
use App\Application\Actions\Company\UpdateCompanyAction;
use App\Application\Actions\Company\DeactivateCompanyAction;
use App\Application\Actions\Company\GetCompanyByUserAction;
use App\Application\Actions\Product\RegisterProductAction;
use App\Application\Actions\Product\ListProductsAction;
use App\Application\Actions\Product\ViewProductAction;
use App\Application\Actions\Product\UpdateProductAction;
use App\Application\Actions\Product\DeleteProductAction;
use App\Application\Actions\Service\RegisterServiceAction;
use App\Application\Actions\Service\ListServicesAction;
use App\Application\Actions\Service\ViewServiceAction;
use App\Application\Actions\Service\UpdateServiceAction;
use App\Application\Actions\Service\DeleteServiceAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        return $response;
    });

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });

    $app->group('/auth', function (Group $group) {
        $group->post('/register', RegisterAction::class);
        $group->post('/login', LoginAction::class);
    });

    $app->group('/companies', function (Group $group) {
        $group->get('', ListCompaniesAction::class);
        $group->post('', RegisterCompanyAction::class);
        $group->get('/{id}', ViewCompanyAction::class);
        $group->put('/{id}', UpdateCompanyAction::class);
        $group->patch('/{id}/inactivate', DeactivateCompanyAction::class);
        $group->get('/user/{userId}', GetCompanyByUserAction::class);
    });

    $app->group('/products', function (Group $group) {
        $group->get('', ListProductsAction::class);
        $group->post('', RegisterProductAction::class);
        $group->get('/{id}', ViewProductAction::class);
        $group->put('/{id}', UpdateProductAction::class);
        $group->delete('/{id}', DeleteProductAction::class);
    });

    $app->group('/services', function (Group $group) {
        $group->get('', ListServicesAction::class);
        $group->post('', RegisterServiceAction::class);
        $group->get('/{id}', ViewServiceAction::class);
        $group->put('/{id}', UpdateServiceAction::class);
        $group->delete('/{id}', DeleteServiceAction::class);
    });
};
