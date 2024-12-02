<?php

namespace App\Models\Report;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ReportPhoto extends Model
{
    protected $table = 'rapporti_foto';
    protected $primaryKey = 'id_foto';
    protected $fillable = [
        'id_rapporto',
        'filename',
    ];

    public function report() {
        return $this->belongsTo(Report::class, 'id_rapporto');
    }

    public function delete()
    {
        Storage::delete($this->filename);
        return parent::delete();
    }
}
