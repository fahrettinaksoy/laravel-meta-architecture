<?php

declare(strict_types=1);

namespace App\Traits;

use App\DataTransferObjects\BaseDTO;
use App\Http\Requests\BaseRequest;
use App\Models\BaseModel;
use App\Services\BaseService;
use App\Support\DTOFactory;
use App\Support\DynamicServiceFactory;

trait HasActionResolver
{
    protected static array $validRequestActions = ['index', 'show', 'store', 'update', 'fieldUpdate', 'destroy'];

    protected static array $validDtoActions = ['store', 'update'];

    protected function validateActionKeys(): void
    {
        $invalidRequestKeys = array_diff(array_keys($this->requests), static::$validRequestActions);

        if (! empty($invalidRequestKeys)) {
            throw new \RuntimeException(
                __('api.controller.invalid_request_keys', [
                    'keys' => implode(', ', $invalidRequestKeys),
                    'valid' => implode(', ', static::$validRequestActions),
                ]),
            );
        }

        $invalidDtoKeys = array_diff(array_keys($this->dtos), static::$validDtoActions);

        if (! empty($invalidDtoKeys)) {
            throw new \RuntimeException(
                __('api.controller.invalid_dto_keys', [
                    'keys' => implode(', ', $invalidDtoKeys),
                    'valid' => implode(', ', static::$validDtoActions),
                ]),
            );
        }
    }

    protected function getService(): BaseService
    {
        if ($this->service !== null) {
            return $this->service;
        }

        $modelClass = request()->attributes->get('modelClass');

        if (! $modelClass) {
            throw new \RuntimeException(
                __('api.controller.service_not_initialized'),
            );
        }

        $this->service = app(DynamicServiceFactory::class)->make($modelClass);

        return $this->service;
    }

    protected function resolveRequest(string $action): BaseRequest
    {
        if (! isset($this->requests[$action])) {
            throw new \RuntimeException(
                __('api.controller.request_not_defined', ['action' => $action]),
            );
        }

        $requestClass = $this->requests[$action];

        if (! is_subclass_of($requestClass, BaseRequest::class)) {
            throw new \RuntimeException(
                __('api.controller.request_must_extend_base', ['class' => $requestClass]),
            );
        }

        return app($requestClass);
    }

    protected function createDTO(BaseRequest $request, string $type): array
    {
        $dtoClass = $this->dtos[$type] ?? $this->resolveModelDtoClass();

        if ($dtoClass !== null) {
            return DTOFactory::fromRequest($dtoClass, $request, $type)->only();
        }

        return $request->validated();
    }

    /**
     * Resolve the DTO class from the model attached to the request by the
     * ValidateModule middleware, falling back to null when unavailable.
     */
    protected function resolveModelDtoClass(): ?string
    {
        $modelClass = request()->attributes->get('modelClass');

        if ($modelClass === null || ! is_subclass_of($modelClass, BaseModel::class)) {
            return null;
        }

        $dtoClass = $modelClass::fieldSource();

        return is_string($dtoClass) && is_subclass_of($dtoClass, BaseDTO::class)
            ? $dtoClass
            : null;
    }

    /**
     * Build the pagination/query context passed down to the service layer.
     *
     * Filtering, sorting, includes and sparse fieldsets are read directly from
     * the request by spatie/laravel-query-builder in the repository layer, so
     * only pagination-relevant context is forwarded here.
     */
    protected function buildQueryContext(BaseRequest $request): array
    {
        return array_filter(
            [
                'limit' => $request->validated('limit'),
            ],
            static fn (mixed $value): bool => $value !== null,
        );
    }

    /**
     * Includes for single-resource reads are resolved by spatie's
     * allowedIncludes() against the ?include= query parameter, so no explicit
     * include list needs to be threaded through the controller.
     */
    protected function parseIncludes(BaseRequest $request): array
    {
        return [];
    }
}
