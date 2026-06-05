<?php

namespace App\Http\Requests;

use App\Enums\AssessmentType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreAssessmentResultRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'assessment_type' => ['required', new Enum(AssessmentType::class)],
            'child_name' => ['required', 'string', 'max:100'],
            'child_age' => ['required', 'string', 'max:20'],
            'scores_json' => ['required', 'array'],

            'scores_json.aspects' => ['required_if:assessment_type,kesiapan', 'array'],
            'scores_json.aspects.*.subtotal' => ['required_if:assessment_type,kesiapan', 'integer', 'min:0'],
            'scores_json.aspects.*.max' => ['required_if:assessment_type,kesiapan', 'integer'],
            'scores_json.total' => ['required_if:assessment_type,kesiapan', 'integer', 'min:0'],
            'scores_json.max_total' => ['required_if:assessment_type,kesiapan', 'integer'],
            'scores_json.kategori' => ['required_if:assessment_type,kesiapan', 'string'],

            'scores_json.child_profile' => ['required_if:assessment_type,pemilihan', 'array'],
            'scores_json.schools' => ['required_if:assessment_type,pemilihan', 'array'],
            'scores_json.schools.*.name' => ['required_if:assessment_type,pemilihan', 'string', 'max:100'],
            'scores_json.schools.*.aspects' => ['required_if:assessment_type,pemilihan', 'array'],
            'scores_json.schools.*.aspects.*.subtotal' => ['required_if:assessment_type,pemilihan', 'integer', 'min:0'],
            'scores_json.schools.*.aspects.*.max' => ['required_if:assessment_type,pemilihan', 'integer'],
            'scores_json.schools.*.total' => ['required_if:assessment_type,pemilihan', 'integer', 'min:0'],
            'scores_json.schools.*.kategori' => ['required_if:assessment_type,pemilihan', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'assessment_type.enum' => 'Tipe instrumen tidak valid.',
            'child_name.required' => 'Nama anak wajib diisi.',
            'child_age.required' => 'Usia anak wajib diisi.',
            'scores_json.required' => 'Data skor wajib disertakan.',
        ];
    }
}
