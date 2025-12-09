<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = ['name', 'price', 'storage', 'websites', 'type', 'description'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
