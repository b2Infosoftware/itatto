<?php

namespace App\Http\Requests\V1;

use Carbon\Carbon;
use App\Models\TimeOff;
use App\Models\Appointment;
use App\Models\Availability;
use App\Models\Organisation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Auth\Access\AuthorizationException;

class AppointmentRequest extends FormRequest
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
        throw new AuthorizationException(trans('validation.custom.no_double_booking'));
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (auth()->guest()) {
            return false;
        }

        $id = $this->appointment ? $this->appointment->id : false;

        $overlaps = Appointment::withoutGlobalScopes()
                            ->when($id, function ($q) use ($id) {
                                $q->whereNot('id', $id);
                            })
                            ->whereOrganisationId($this->organisation_id)
                            ->whereStaffId($this->staff_id)
                            ->whereDate('date', $this->date)
                            ->whereTime('start_time', '<', $this->end_time)
                            ->whereTime('end_time', '>', $this->start_time)
                            ->exists();

        if ($overlaps) {
            $organisation = Organisation::find($this->organisation_id);

            return $organisation->calendarSettings->allow_double_booking;
        }
        // dealing with time off
        $organisation = Organisation::find($this->organisation_id);
        if (! $organisation->calendarSettings->allow_off_hours_booking) {
            $day = Carbon::parse($this->date)->weekDay();
            if ($day == 0) {
                $day = 7;
            }
            $break = Availability::whereStaffId($this->staff_id)
                                    ->whereLocationId($this->location_id)
                                    ->whereIsAvailable(0)
                                    ->where('day', $day)
                                    ->whereTime('start_time', '<', $this->end_time)
                                    ->whereTime('end_time', '>', $this->start_time)
                                    ->exists();
            if ($break) {
                return false;
            }

            $timeOff = TimeOff::whereStaffId($this->staff_id)
                                ->whereDate('start_date', '<', $this->end_time)
                                ->whereDate('end_date', '>', $this->start_time)
                                ->exists();

            if ($timeOff) {
                return false;
            }

            
            $unavailableNearStartEndOfProgram = Availability::whereStaffId($this->staff_id)
                                                    ->whereLocationId($this->location_id)
                                                    ->whereIsAvailable(1)
                                                    ->where('day', $day)
                                                    ->where(function($q){
                                                        $q->whereTime('start_time', '>', $this->start_time)
                                                        ->orWhereTime('end_time', '<', $this->end_time);
                                                    })->exists();
            if($unavailableNearStartEndOfProgram){                
                return false;
            }
        }

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
            'staff_id' => 'required',
            'customer_id' => 'required',
            'location_id' => 'required',
            'service_id' => 'required',
            'date' => 'required',
            'start_time' => 'required',
            'duration' => 'required',
            // 'note' => 'required',
            'price' => 'required|numeric|min:0',
            'deposit' => 'sometimes|numeric|min:0',
            // 'signed_document' => 'required',
        ];
    }

    /** Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation() : void
    {
        $status = $this->deposit ? 'deposit' : null;
        $this->merge([
            'end_time' => Carbon::parse($this->start_time)->addMinutes($this->duration)->format('H:i'),
            'status' => $this->status ?? $status,
            'organisation_id' => auth()->user()->default_organisation_id,
            'deposit' => $this->deposit ?? 0,
            'price' => $this->price ?? 0
        ]);
    }
}
