<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreQuotationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('currency_id')) {
            $this->merge(['currency_id' => strtoupper((string) $this->input('currency_id'))]);
        }
    }

    public function rules(): array
    {
        return [
            'age' => ['required', 'string', 'regex:/^\d{1,3}(\s*,\s*\d{1,3})*$/'],
            'currency_id' => ['required', 'string', 'in:EUR,GBP,USD'],
            'start_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'end_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:start_date'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }
            $ages = array_map('intval', array_filter(array_map('trim', explode(',', (string) $this->input('age')))));
            foreach ($ages as $age) {
                if ($age < 18 || $age > 70) {
                    $validator->errors()->add('age', 'Each age must be between 18 and 70.');
                    break;
                }
            }
        });
    }
}
