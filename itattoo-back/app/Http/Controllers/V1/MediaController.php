<?php

namespace App\Http\Controllers\V1;

use App\Models\Media;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\MediaRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\V1\MediaResource;

class MediaController extends Controller
{
    /**
     * Uploads an image and returns the path
     *
     * Given the nature of the webapp we don't need to associate it
     * with anything in the database.
     *
     *
     * @param MediaRequest $request
     *
     * @return JsonResponse
     */
    public function upload(\Illuminate\Http\Request $request) : JsonResponse
    {
        $storageSize = Media::where('organisation_id', auth()->user()->default_organisation_id)->sum('size');
        if ($storageSize > 10000000) {
            abort(403, 'You have reached your 10GB storage limit!');
        }

        $file = $request->file('attachment');
        $mimeType = explode('/', $file->getMimeType());
        $fileType = $mimeType[0] == 'application' ? $mimeType[1] : $mimeType[0];

        $fileName = auth()->user()->default_organisation_id . '/' . Str::slug($file->getClientOriginalName()) . time() . '.' . $file->getClientOriginalExtension();
        try {
            $path = Storage::disk('r2')->putFileAs('media', $file, $fileName);
            $media = Media::updateOrCreate(
                [
                    'organisation_id' => auth()->user()->default_organisation_id,
                    'media_type' => in_array($request->media_type, [1, 2]) ? $request->media_type : null,
                ],
                [
                    'location_id' => auth()->user()->default_location_id,
                    'customer_id' => request()->get('customer_id') ?? null,
                    'staff_id' => request()->get('staff_id') ?? null,
                    'project_id' => request()->get('project_id') ?? null,
                    'setting_public_booking_id' => request()->get('setting_public_booking_id') ?? null,
                    'type' => $fileType,
                    'size' => ceil($file->getSize() / 1024),
                    'path' => $path,
                ]
            );
        } catch (\Throwable $th) {
            logger($th);
        }

        return response()->json(['data' => new MediaResource($media)], 201);
    }

    public function delete(Media $media): JsonResponse
    {
        $customer = $media->customer;

        // $this->authorize('managePhotos', $customer);

        if ($media->organisation_id !== auth()->user()->default_organisation_id) {
            return abort(403, trans('general.permission_denied'));
        }

        $media->delete();
        Storage::disk('r2')->delete($media->path);

        return response()->json(['message' => trans('general.delete', ['resource' => trans('resource.file')])]);
    }
}
