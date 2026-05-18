<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;
    protected $fillable = [
        'meta_tag',
        'meta_description',
        'keywords',
        'heading',
        'category_id',
        'description',
        'procedure_heading',
        'procedure_description',
        'title',
        'is_discounted',
        'price',
        'discounted_price',
        'is_button_availalble',
        'status',
        'url',
        'slug'
    ];
    public function category(){
        return $this->belongsTo('App\Models\Category','category_id','id');
    }
}
