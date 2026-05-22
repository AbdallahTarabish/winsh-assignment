<?php

declare(strict_types=1);

namespace Src\Presentation\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Src\Domain\Order\Enums\OrderStatus;

class IndexOrdersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['sometimes', 'nullable', Rule::enum(OrderStatus::class)],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function statusFilter(): ?OrderStatus
    {
        return $this->filled('status') ? OrderStatus::from($this->string('status')->value()) : null;
    }

    public function perPage(): int
    {
        return (int) $this->input('per_page', 15);
    }
}
