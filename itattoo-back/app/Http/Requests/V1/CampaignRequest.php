<?php

namespace App\Http\Requests\V1;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Auth\Access\AuthorizationException;

class CampaignRequest extends FormRequest
{
     /**
     * Handle a failed authorization attempt.
     *
     * @return void
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    protected function failedAuthorization()
    {
        throw new AuthorizationException(trans('validation.custom.campaign.in_the_past'));
    }
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $campaignDate = Carbon::parse($this->scheduled_date . ' ' . $this->scheduled_time, $this->timezone);
        if($campaignDate->isPast()){
            return false;
        }
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => 'required',
            'message' => 'required',
            'type' => 'required|in:sms,email',
            'email_subject' => 'required_if:campaign_type,email',
            'scheduled_date' => 'required_unless:is_birthday,true',
            'scheduled_time' => 'required',
        ];

        if($this->type == 'sms'){
            $rules['message'] = 'required|max:160';
        }        
        return $rules;
    }

    public function prepareForValidation() : void
    {
        $this->merge([
            'organisation_id' => auth()->user()->default_organisation_id,
        ]);
    }
}
