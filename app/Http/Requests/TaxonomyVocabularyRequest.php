<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class TaxonomyVocabularyRequest extends FormRequest
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
        return [
            'tnid' => [
                'nullable',
                'integer',
                'min:1',
                Rule::exists('taxonomy_term_data', 'tnid')->where('vid', $this->vid),
            ],
            'weight' => [
                'nullable',
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
     * Require a matching weight only for languages with a filled name.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $names = $this->input('name', []);
            $weights = $this->input('weight', []);

            if (! is_array($names)) {
                return;
            }

            foreach ($names as $language => $name) {
                if (! is_string($name) || trim($name) === '') {
                    continue;
                }

                if (! is_array($weights) || ! array_key_exists($language, $weights) || ! filled($weights[$language])) {
                    $validator->errors()->add("weight.$language", __('The weight field is required when the name is filled.'));
                }
            }
        });
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
