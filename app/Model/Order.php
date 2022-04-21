<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['sale_amount', 'profit'];
    protected $hidden = ['sale_amount', 'profit'];

    public function items()
    {
        return $this->hasMany(OrderDetail::class, 'order_id')->where('status', true);
    }

    public function items_with_details(){
        return $this->hasMany(OrderDetail::class, 'order_id')->where('status', true)->with('product');
    }
}
