<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'price',
        'desc',
        'slug',
    ];

    public function productGalleries() {
        return $this->hasMany(ProductGallery::class, 'products_id', 'id');
    }

    public function carts() {
        return $this->hasMany(Cart::class, 'product_id', 'id');
    }

    public function transactionItems() {
        return $this->hasMany(TransactionsItem::class, 'product_id', 'id');
    }
}
