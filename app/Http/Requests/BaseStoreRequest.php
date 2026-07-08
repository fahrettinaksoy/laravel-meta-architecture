<?php

declare(strict_types=1);

namespace App\Http\Requests;

class BaseStoreRequest extends BaseRequest
{
    public function rules(): array
    {
        return array_merge($this->commonRules(), $this->modelDtoRules('store'));
    }

    public function messages(): array
    {
        return [];
    }
}
