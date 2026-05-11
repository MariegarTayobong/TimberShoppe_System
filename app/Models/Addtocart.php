<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Addtocart extends Model
{
    public function product() {

        return $this->belongsTo(Product::class, 'product_id');
    }
    public function seller() {

        return $this->belongsTo(User::class, 'seller_id');
    }
    public function buyer() {

        return $this->belongsTo(User::class, 'buyer_id');
    }
}
