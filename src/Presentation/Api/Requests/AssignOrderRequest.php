<?php

declare(strict_types=1);

namespace Src\Presentation\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Assignment is driven entirely by the order id in the route and the
        // domain's "nearest available" rule; no request body is accepted.
        return [];
    }
}
