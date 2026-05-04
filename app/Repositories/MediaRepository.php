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
        if (!$request->hasFile('images')) {
            return;
        }
        $files = $request->file('images');
        if (!is_array($files)) {
            $files = [$files];
        }
        if (count($files) > 10) {
            throw new \Exception('Maximum 10 images allowed');
        }
        $insertData = [];
        foreach ($files as $index => $file) {
            if (!$file->isValid()) {
                continue;
            }
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('media', $fileName, 'public');
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
