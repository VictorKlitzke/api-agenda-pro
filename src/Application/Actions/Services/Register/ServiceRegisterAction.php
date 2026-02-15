<?php


namespace App\Application\Actions\Services\Register;

use App\Application\Actions\Action;
use App\Domain\Services\Services\ServiceServices;
use App\Domain\Services\Data\DTOs\Request\ServiceRequest;

final class ServiceRegisterAction extends Action
{

    public function __construct(private readonly ServiceServices $serviceServices)
    {
    }

    public function action(): \Psr\Http\Message\ResponseInterface
    {
        $data = $this->getFormData();
        $serviceRequest = ServiceRequest::fromArray($data);
        $service = $this->serviceServices->register($serviceRequest);

        return $this->respondWithData($service, 201);
    }

}