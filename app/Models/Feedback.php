<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    // PENTING: Mendefinisikan nama tabel secara manual agar tidak error
    protected $table = 'feedbacks'; 

    // Ini sudah benar, biarkan saja
    protected $guarded = ['id'];
}