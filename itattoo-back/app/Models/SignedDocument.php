<?php

namespace App\Models;

use Illuminate\Support\Arr;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SignedDocument extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'signed_documents';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    public static function generateFromRequest()
    {
        $form = ConsentForm::find(request()->get('consent_form_id'));
        $client = Customer::find(request()->get('customer_id'));
        $project = Project::find(request()->get('project_id'));

        $data = [
            'form' => $form,
            'client' => $client,
            'signature' => request()->get('signature'),
        ];

        $pdf = PDF::loadView('consent-forms.default', $data);
        $pdfName = Arr::join([$project->name, $client->first_name, $client->last_name], '-');
        $pdfName = 'documents/122' . '/' . $pdfName . '.pdf';

        $pdf->save($pdfName, 'public');
        $path = asset('storage/' . $pdfName);

        $document = self::updateOrCreate([
            'documentable_id' => $project->id,
            'documentable_type' => 'App\Models\Project',
        ], [
            'path' => $path,
        ]);

        return $document;
    }
}
