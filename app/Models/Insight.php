<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Insight extends Model
{
    protected $table = 'insights';
    protected $fillable = ['judul', 'slug', 'isi', 'penulis', 'image_url', 'TanggalTerbit','category_id'];
}
