<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Publisher extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'country', 'description', 'website'];

    public function mangas(): HasMany
    {
        return $this->hasMany(Manga::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($publisher) {
            if (empty($publisher->slug)) {
                $publisher->slug = Str::slug($publisher->name);
            }
        });
    }
}
