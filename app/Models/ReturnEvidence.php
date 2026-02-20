<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnEvidence extends Model
{
    use HasFactory;

    protected $table = 'return_evidences';

    protected $fillable = [
        'order_return_id',
        'file_path',
        'file_type',
    ];

    public function returnRequest()
    {
        return $this->belongsTo(OrderReturn::class, 'order_return_id');
    }
}
