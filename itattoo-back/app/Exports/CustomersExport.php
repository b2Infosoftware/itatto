<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomersExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'Id',
            'Name',
            'Email',
            'Phone Number',
            'Birth Date',
            'Gender',
            'Country',
            'City',
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $customers = Customer::get();

        $customersCollection = $customers->map(function ($customer) {
            return [
                'id' => $customer->id,
                'name' =>  $customer->fullName,
                'email' =>  $customer->email,
                'phone_number' =>  $customer->phone_number,
                'birth_date' => $customer->birth_date,
                'gender' => $customer->gender,
                'country' => $customer->country->name,
                'city' => $customer->city,
            ];
        });

        return $customersCollection;
    }
}
