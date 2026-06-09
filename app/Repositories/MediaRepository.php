<?php

namespace App\Repositories;

use App\Models\Media;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MediaRepository extends BaseRepository
{
    protected $mediaModel;
    public function __construct(Media $media)
    {
        parent::__construct($media);
        $this->mediaModel = $media;
    }

    public function createMedia($request)
    {
        $data = $request->only([
            'title',
            'sub_title',
            'module',
            'section',
            'button_text',
            'redirect_url',
            'start_date',
            'end_date'  
        ]);
        return $this->mediaModel->create($data);
    }

    public function uploadMediaImages($request, $mediaId)
    {
        $maxFiles = config('constant.media_upload.max_files', 10);
        $expectedCount = (int) $request->input('expected_images_count', 0);

        if (!$request->hasFile('images')) {
            if ($expectedCount > 0) {
                throw new \Exception('Image upload failed. Files were not received by the server. Please check server upload limits (post_max_size / upload_max_filesize) and try again.');
            }
            return;
        }

        $files = $request->file('images');
        if (!is_array($files)) {
            $files = [$files];
        }

        if (count($files) > $maxFiles) {
            throw new \Exception("Maximum {$maxFiles} images allowed");
        }

        if ($expectedCount > 0 && count($files) < $expectedCount) {
            throw new \Exception('Image upload failed. Some files were not received by the server. Please try again.');
        }

        $insertData = [];
        $errors = [];

        foreach ($files as $index => $file) {
            if (!$file->isValid()) {
                $errors[] = $file->getClientOriginalName() . ': ' . $file->getErrorMessage();
                continue;
            }

            $extension = $file->getClientOriginalExtension() ?: $file->extension();
            $fileName = time() . '_' . uniqid() . '.' . $extension;
            $filePath = $file->storeAs('media', $fileName, 'public');

            if (!$filePath) {
                $errors[] = $file->getClientOriginalName() . ': failed to save file';
                continue;
            }

            $insertData[] = [
                'media_id'   => $mediaId,
                'file_name'  => $fileName,
                'file_path'  => $filePath,
                'file_type'  => $file->getClientMimeType(),
                'sort_order' => $index + 1,
                'is_active'  => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($errors)) {
            throw new \Exception('Image upload failed: ' . implode(', ', $errors));
        }

        if ($expectedCount > 0 && empty($insertData)) {
            throw new \Exception('Image upload failed. No images were saved. Please check file size and format.');
        }

        if (!empty($insertData)) {
            DB::table('media_images')->insert($insertData);
        }
    }

    public function initData()
    {
        return Media::query()
            ->where('is_active', 1)
            ->latest();
    }

    public function deleteMediaRecord($id){
        $media = Media::findOrFail($id);
        $media->delete(); 
    }
    
    public function findWithImages($id)
    {
        return Media::with(['images' => function ($q) {
            $q->orderBy('sort_order');
        }])->find($id);
    }

    public function updateMedia($request, $id)
    {
        $media = $this->mediaModel->findOrFail($id);

        $data = $request->only([
            'title',
            'sub_title',
            'module',
            'section',
            'button_text',
            'redirect_url',
            'start_date',
            'end_date'
        ]);
        $media->update($data);
        return $media;
    }

    public function deleteRemovedImages($request)
    {
        if (!$request->removed_images) {
            return;
        }

        $images = json_decode($request->removed_images, true);

        if (!is_array($images)) {
            return;
        }

        foreach ($images as $img) {

            // storage se delete
            if (!empty($img['path']) && Storage::disk('public')->exists($img['path'])) {
                Storage::disk('public')->delete($img['path']);
            }

            // DB se delete
            DB::table('media_images')
                ->where('id', $img['id'])
                ->delete();
        }
    }
}
