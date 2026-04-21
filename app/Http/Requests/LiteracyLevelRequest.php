<?php

namespace App\Http\Requests;

use App\Enums\TaxonomyVocabularyEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LiteracyLevelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $vid = TaxonomyVocabularyEnum::ResourceLevels->value;

        return [
            'tnid' => [
                'nullable',
                'integer',
                'min:1',
                Rule::exists('taxonomy_term_data', 'tnid')->where('vid', $vid),
            ],
            'weight' => [
                'required',
                'array',
            ],
            'weight.*' => [
                'nullable',
                'integer',
            ],
            'name' => ['required', 'array', $this->atLeastOneNameFilled()],
            'name.*' => [
                'nullable',
                'string',
                'max:255',
            ],
            'parent' => [
                'nullable',
                'array',
            ],
            'parent.*' => [
                'nullable',
                'integer',
                'min:0',
            ],
            'id' => [
                'nullable',
                'array',
            ],
            'id.*' => [
                'nullable',
                'integer',
                Rule::exists('taxonomy_term_data', 'id'),
            ],
        ];
    }

    /**
     * At least one term name (across languages) must be filled.
     */
    protected function atLeastOneNameFilled(): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail): void {
            if (! is_array($value)) {
                $fail(__('At least one term name is required.'));

                return;
            }
            $filled = array_filter($value, fn ($v) => is_string($v) && trim($v) !== '');
            if (empty($filled)) {
                $fail(__('At least one term name is required.'));
            }
        };
    }
}
