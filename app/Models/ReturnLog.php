<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_return_id',
        'old_status',
        'new_status',
        'changed_by',
        'note',
    ];

    public function returnRequest()
    {
        return $this->belongsTo(OrderReturn::class, 'order_return_id');
    }

    public function changer()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
