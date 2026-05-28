<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResourceFileRequest;
use App\Models\ResourceFile;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;

class ResourceFileController extends Controller
{
    public function uploadImage(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'image' => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:3072', File::image()->dimensions(Rule::dimensions()->ratio(1.0))],
                'image_name' => 'nullable|string|max:255',
                'taxonomy_term_data_id' => 'nullable|exists:taxonomy_term_data,id',
            ],
            [
                'image.dimensions' => __('Upload an image that is the same width and height (a square).'),
            ],
        );

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'errors' => $validator->errors(),
                ],
                422,
            );
        }
        $file = $request->file('image');
        $filelabel = auth()->user()->id.'_'.time().'.'.$file->getClientOriginalExtension();
        $path = 'files/'.$filelabel;

        $diskType = diskType();
        Storage::disk($diskType)->put($path, file_get_contents($file));

        $imagine = new Imagine;
        $image = $imagine->open($file->getRealPath());

        // Get dimensions
        $width = $image->getSize()->getWidth();
        $height = $image->getSize()->getHeight();

        // Get file size
        $size = round($file->getSize() / 1024); // Get with KB

        $thumbnailPath = 'files/thumbnails/'.$filelabel;

        $tempDirectory = sys_get_temp_dir().'/thumbnails';

        if (! file_exists($tempDirectory)) {
            mkdir($tempDirectory, 0755, true);
        }

        $image->resize(new Box(250, 250))->save($tempDirectory.'/'.$filelabel);

        Storage::disk($diskType)->put($thumbnailPath, file_get_contents($tempDirectory.'/'.$filelabel));

        $resourceFile = ResourceFile::create([
            'label' => $request->image_name ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'language' => $request->language ?: config('app.locale'),
            'taxonomy_term_data_id' => $request->taxonomy_term_data_id,
            'name' => $filelabel,
            'height' => $height,
            'width' => $width,
            'size' => $size,
        ]);

        // Cleanup temporary thumbnail file
        $tempThumbFile = $tempDirectory.'/'.$filelabel;
        if (file_exists($tempThumbFile)) {
            @unlink($tempThumbFile);
        }

        return response()->json([
            'success' => true,
            'resource_file_id' => $resourceFile->id,
            'imageUrl' => Storage::disk($diskType)->temporaryUrl($thumbnailPath, now()->addMinutes(5)),
            'imageName' => $resourceFile->label,
            'message' => __('Image uploaded successfully'),
        ]);
    }

    public function searchImages(ResourceFileRequest $request)
    {
        $query = ResourceFile::query()
            ->select('id', 'label', 'name')
            ->where(function ($query) use ($request) {
                $subjectAreaId = $request->input('subject_area_id');
                $search = $request->input('search');
                if ($subjectAreaId) {
                    $resourceFileIds = DB::table('resource_subject_areas')
                        ->join('resources', 'resource_subject_areas.resource_id', '=', 'resources.id')
                        ->where('tid', $subjectAreaId)
                        ->pluck('resource_file_id');
                    $query->whereIn('id', $resourceFileIds);
                }
                if (! empty($search)) {
                    $query->where('label', 'like', "%{$search}%");
                }
            })
            ->where('language', $request->language);

        $count = $query->count();
        $files = $query->orderByDesc('created_at')->paginate(16)->appends(Arr::except($request->all(), ['page']));

        return view('resources.partial.file-list', compact('count', 'files'));
    }
}
