<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Pelicula extends Model
{
    use HasFactory;
    
    protected $table = 'peliculas'; 

    
    protected $fillable = [
        'title', 
        'release_date', 
        'sinopsis', 
        'duration', 
        'gendre', 
        'director_id'
    ];

    protected $hidden = ['updated_at', 'created_at'];

    public function director(): BelongsTo
    {
        return $this->belongsTo(Director::class);
    }
}