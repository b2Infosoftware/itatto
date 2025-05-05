<?php

namespace App\Models;

use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Model;

class Sms extends Model
{
    protected $guarded = ['id'];

    public static function send(string $type, Appointment $appointment, string $message)
    {
        $user = $type == 'customer' ? $appointment->customer : $appointment->staff;
        if (! $user->phone_number) {
            logger('no phone number');

            return;
        }
        if ($user->organisation->cannotSendSms()) {
            logger('no more SMS');

            return;
        }

        $phone = urlencode($user->phone_number);

        $static_message = str_replace(config('customVariables.mail'),
            [
                $appointment->staff?->fullName,
                $appointment->organisation?->name,
                $appointment->service?->name,
                $appointment->date,
                $appointment->start_time,
                $appointment->end_time,
                $appointment->customer?->fullName,
                $appointment->duration,
            ],
            $message);
        $encodedMessage = urlencode($static_message);

        try {
            $link = 'https://www.services.europsms.com/smpp-gateway.php?op=sendSMS2&smpp_id=newsletter@mineralartgallery.com&utenti_password=u89010gHTR12we&tipologie_sms_id=2&destinatari_destination_addr=' . $phone . '&trasmissioni_messaggio=' . $encodedMessage . '&trasmissioni_mittente=iTattoo';
            $response = Http::get($link);
            if ((bool) $response->body()) {
                self::create([
                    'number' => $user->phone_number,
                    'customer_id' => $type == 'customer' ? $user->id : null,
                    'staff_id' => $type == 'staff' ? $user->id : null,
                    'message' => $static_message,
                    'status' => null,
                    'uid' => $response->body(),
                ]);
                $user->organisation->decrementSms();
            }
        } catch (\Throwable $th) {
            logger($th);
        }
    }

    public static function sendNewsletter(Customer $user, string $message)
    {
        if ($user->organisation->cannotSendSms()) {
            logger('no more SMS');

            return;
        }
        if (! $user->phone_number) {
            logger('no phone number');

            return;
        }
        $phone = urlencode($user->phone_number);

        $static_message = str_replace(config('customVariables.newsletter'),
            [
                $user->organisation?->name,
                $user->fullName,
            ],
            $message);
        $encodedMessage = urlencode($static_message);

        try {
            $link = 'https://www.services.europsms.com/smpp-gateway.php?op=sendSMS2&smpp_id=newsletter@mineralartgallery.com&utenti_password=u89010gHTR12we&tipologie_sms_id=2&destinatari_destination_addr=' . $phone . '&trasmissioni_messaggio=' . $encodedMessage . '&trasmissioni_mittente=iTattoo';
            $response = Http::get($link);

            if ((bool) $response->body()) {
                self::create([
                    'number' => $user->phone_number,
                    'customer_id' => $user->id,
                    'staff_id' => null,
                    'message' => $static_message,
                    'status' => null,
                    'uid' => $response->body(),
                ]);
                $user->organisation->decrementSms();
            }
        } catch (\Throwable $th) {
            logger($th);
        }
    }

    public function checkSmsStatus()
    {
        try {
            $link = 'http://www.services.europsms.com/smpp-gateway.php?op=txStatus&email=newsletter@mineralartgallery.com&password=u89010gHTR12we&trasmissioni_id=' . $this->uid;
            $response = Http::get($link);
            $json = $response->json();

            if ($json['trasmissione'] && $json['trasmissione']['trasmissioni_stati_descrizione']) {
                $this->update(['status' => $json['trasmissione']['trasmissioni_stati_descrizione']]);
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
