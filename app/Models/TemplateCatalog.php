<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateCatalog extends Model
{
    use HasFactory;

    protected $table = 'template_catalogs';
    protected $fillable = ['name'];

    public function company()
    {
        return $this->hasMany('App\Models\Company');
    }
}
