<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'project_title', 'description', 'user_id', 'parent_id'
    ];

    public function parent()
    {
    	return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
    	return $this->hasMany(self::class, 'parent_id');
    }
}
