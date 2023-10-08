<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductDetail extends Model
{
    protected $table = 'product_detail';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'unique_key',
        'product_file_id',
        'product_title',
        'product_description',
        'style',
        'sanmar_mainframe_color',
        'size',
        'color_name',
        'piece_price'
    ];
    
    
    /**
     * Get the parent - banner
     */
    public function file()
    {
        return $this->hasMany('App\Models\ProductFile', 'product_file_id', 'id');
    }
    
}