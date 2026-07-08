<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\BaseModel;
use App\Support\MetadataResolver;
use App\Support\ResponseReference;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class BaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    abstract public function rules(): array;

    protected function commonRules(): array
    {
        return [];
    }

    /**
     * Validation rules generated from the resolved model's DTO metadata for the
     * given action. The model class is attached to the request attributes by the
     * ValidateModule middleware. Returns [] when no model/DTO can be resolved.
     */
    protected function modelDtoRules(string $action): array
    {
        $modelClass = $this->attributes->get('modelClass');

        if ($modelClass === null || ! is_subclass_of($modelClass, BaseModel::class)) {
            return [];
        }

        $dtoClass = $modelClass::fieldSource();

        if ($dtoClass === null) {
            return [];
        }

        return MetadataResolver::rulesForAction($dtoClass, $action);
    }

    public function messages(): array
    {
        return [];
    }

    public function attributes(): array
    {
        return [];
    }

    protected function failedValidation(Validator $validator): void
    {
        $message = __('api.validation.error');

        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => $message,
                'error_code' => 'VALIDATION_ERROR',
                'errors' => $validator->errors()->toArray(),
                'reference' => app(ResponseReference::class)->build($message, 422),
            ], 422),
        );
    }
}
