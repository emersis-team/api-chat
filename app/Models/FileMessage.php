<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileMessage extends Model
{

    protected $fillable = [
        'file',
        'original_file',
        'description',
    ];
}
