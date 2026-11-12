<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['order_number','customer_name','guest_count','table_number','type','platform','status','total'];
    public function items() { return $this->hasMany(OrderItem::class); }
}
