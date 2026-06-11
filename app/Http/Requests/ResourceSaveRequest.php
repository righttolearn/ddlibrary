<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResourceSaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'title'                     => 'required|string|max:255',
            'author'                    => 'nullable|string|max:255',
            'publisher'                 => 'nullable|string|max:255',
            'language'                  => 'required|string|max:10',
            'has_translator'            => 'nullable|boolean',
            'translator'                => 'nullable|required_if:has_translator,1|string|max:255',
            'resource_file_id'          => 'required|integer|exists:resource_files,id',
            'abstract'                  => 'required|string',

            'attachments.*'             => 'nullable|file|mimes:xlsx,epub,xls,csv,jpg,jpeg,png,bmp,mpga,ppt,pptx,doc,docx,pdf,tif,tiff,mp3|max:131072',
            'subject_areas'             => 'required|array',
            'subject_areas.*'           => 'integer|exists:taxonomy_term_data,id',
            'keywords'                  => 'nullable|string',
            'learning_resources_types'  => 'required|array',
            'learning_resources_types.*'=> 'integer|exists:taxonomy_term_data,id',
            'educational_use'           => 'required|array',
            'educational_use.*'         => 'integer|exists:taxonomy_term_data,id',
            'level'                     => 'required|array',
            'level.*'                   => 'integer|exists:taxonomy_term_data,id',

            'resource_rights'           => 'nullable|in:translation,educational,author',
            'copyright_holder'          => 'nullable|string|max:255',
            'creative_commons'          => 'nullable|integer|exists:taxonomy_term_data,id',
            'creative_commons_other'    => 'nullable|integer|exists:taxonomy_term_data,id',
            'published'                 => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'                    => __('Title is required.'),
            'language.required'                 => __('Language is required.'),
            'resource_file_id.required'         => __('Please select an image.'),
            'abstract.required'                 => __('Abstract is required.'),
            'subject_areas.required'            => __('Please select at least one subject area.'),
            'learning_resources_types.required' => __('Please select at least one learning resource type.'),
            'educational_use.required'          => __('Please select at least one educational use.'),
            'level.required'                    => __('Please select at least one level.'),
            'translator.required_if'            => __('Translator is required when translation is selected.'),
        ];
    }
}
