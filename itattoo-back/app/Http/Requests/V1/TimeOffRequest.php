<?php

namespace App\Http\Requests\V1;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class TimeOffRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->request->get('organisation_id') == auth()->user()->default_organisation_id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'staff_id' => 'required',
            'start_date' => 'required|after:today',
            'end_date' => 'required|after_or_equal:start_date', // Allows same-day selection
            'reason' => 'required',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'start_date' => Carbon::parse($this->start_date),
            'end_date' => Carbon::parse($this->end_date),
            'organisation_id' => auth()->user()->default_organisation_id,
            'location_id' => auth()->user()->default_location_id,
        ]);
    }
}
