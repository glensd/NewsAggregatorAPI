<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Preference extends Model
{
    use HasFactory;
    public $table = 'preferences';

    protected $fillable = [
        'content',
        'authors',
        'categories',
        'user_id',
        'sources',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //Get the categories as an array of ids.
    public function getCategoryIdsAttribute()
    {
        return json_decode($this->attributes['categories'], true);
    }

    //Get the category names from the ids stored in the json field.
    public function getCategoryNamesAttribute()
    {
        $categoryIds = $this->category_ids;

        return Category::whereIn('id', $categoryIds)->pluck('name')->toArray();
    }
}
