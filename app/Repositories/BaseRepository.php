<?php 

namespace App\Repositories;

use Illuminate\Support\Facades\Cache;

class BaseRepository
{
    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    protected function remember($key, $callback, $minutes=null)
    {
        
        if($minutes != null){
            return Cache::remember($key, now()->addMinutes($minutes), $callback);
        }
        
        return Cache::rememberForever($key, $callback);
    }

    // Find record by ID
    public function find($id)
    {
        return $this->model::find($id);
    }

    public function findOrFail($id)
    {
        return $this->model::findOrFail($id);
    }

    // Common create/update
    public function save($data, $id = null)
    {
        if ($id) {
            $model = $this->findOrFail($id);
            $model->fill($data);
        } else {
            $model = new $this->model($data);
        }

        $model->save();
        return $model;
    }

    // Common list with optional filters
    public function list($select = ['*'], $filters = [])
    {
        $query = $this->model::select($select);

        foreach ($filters as $field => $value) {
            $query->where($field, $value);
        }

        return $query->get();
    }
}
