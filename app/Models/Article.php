<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;
    public $table = 'articles';
    protected $fillable = [
        'title',
        'content',
        'author',
        'source',
        'unique_identifier',
        'published_at',
        'category_id'
    ];
    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
