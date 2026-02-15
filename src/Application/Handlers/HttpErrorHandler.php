<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\Actions\ActionError;
use App\Application\Actions\ActionPayload;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpException;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpNotImplementedException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Handlers\ErrorHandler as SlimErrorHandler;
use Throwable;
use App\Infrastructure\Exceptions\CustomException;

class HttpErrorHandler extends SlimErrorHandler
{
    /**
     * @inheritdoc
     */
    protected function respond(): Response
    {
        $exception = $this->exception;

        // Default internal server error
        $statusCode = 500;
        $error = new ActionError(
            ActionError::SERVER_ERROR,
            'An internal error has occurred while processing your request.'
        );

        // If it's our custom exception, use its status and errors
        if ($exception instanceof CustomException) {
            $statusCode = $exception->getStatusCode();
            $error->setDescription($exception->getMessage());
            $payload = new ActionPayload($statusCode, ['errors' => $exception->getErrors()], $error);
        } else {
            if ($exception instanceof HttpException) {
                $statusCode = $exception->getCode();
                $error->setDescription($exception->getMessage());

                if ($exception instanceof HttpNotFoundException) {
                    $error->setType(ActionError::RESOURCE_NOT_FOUND);
                } elseif ($exception instanceof HttpMethodNotAllowedException) {
                    $error->setType(ActionError::NOT_ALLOWED);
                } elseif ($exception instanceof HttpUnauthorizedException) {
                    $error->setType(ActionError::UNAUTHENTICATED);
                } elseif ($exception instanceof HttpForbiddenException) {
                    $error->setType(ActionError::INSUFFICIENT_PRIVILEGES);
                } elseif ($exception instanceof HttpBadRequestException) {
                    $error->setType(ActionError::BAD_REQUEST);
                } elseif ($exception instanceof HttpNotImplementedException) {
                    $error->setType(ActionError::NOT_IMPLEMENTED);
                }
            }

            if (
                !($exception instanceof HttpException)
                && $exception instanceof Throwable
                && $this->displayErrorDetails
            ) {
                $error->setDescription($exception->getMessage());
            }

            $payload = new ActionPayload($statusCode, null, $error);
        }
        $encodedPayload = json_encode($payload, JSON_PRETTY_PRINT);

        $response = $this->responseFactory->createResponse($statusCode);
        $response->getBody()->write($encodedPayload);

        $response = $response->withHeader('Content-Type', 'application/json');

        return $this->withCorsHeaders($response);
    }

    private function withCorsHeaders(Response $response): Response
    {
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
        if ($origin === '') {
            return $response;
        }

        $allowedOrigins = array_values(array_filter(array_map(
            'trim',
            explode(',', $_ENV['CORS_ALLOWED_ORIGINS'] ?? '')
        )));

        $resolvedOrigin = null;
        if (empty($allowedOrigins) || in_array('*', $allowedOrigins, true)) {
            $resolvedOrigin = '*';
        } elseif (in_array($origin, $allowedOrigins, true)) {
            $resolvedOrigin = $origin;
        }

        if ($resolvedOrigin === null) {
            return $response;
        }

        $response = $response
            ->withHeader('Access-Control-Allow-Origin', $resolvedOrigin)
            ->withHeader('Access-Control-Allow-Headers', $_ENV['CORS_ALLOWED_HEADERS'] ?? 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', $_ENV['CORS_ALLOWED_METHODS'] ?? 'GET, POST, PUT, PATCH, DELETE, OPTIONS');

        $allowCredentials = filter_var($_ENV['CORS_ALLOW_CREDENTIALS'] ?? 'false', FILTER_VALIDATE_BOOLEAN);
        if ($resolvedOrigin !== '*' && $allowCredentials) {
            $response = $response->withHeader('Access-Control-Allow-Credentials', 'true');
        }

        if ($resolvedOrigin !== '*') {
            $response = $response->withHeader('Vary', 'Origin');
        }

        return $response;
    }
}
