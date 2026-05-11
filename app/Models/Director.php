<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Director extends Model
{
    protected $fillable = ['name', 'surname', 'birthdate'];

   
    protected $hidden = ['updated_at', 'created_at'];


    public function peliculas(): HasMany
    {
        
        return $this->hasMany(Pelicula::class); 
    }
}
