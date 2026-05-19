<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename', 'disk_path', 'column_mapping', 'search_criteria',
        'total_rows', 'imported_count', 'failed_count', 'skipped_count',
        'failed_rows', 'status', 'uploaded_by',
    ];

    protected $casts = [
        'column_mapping'  => 'array',
        'search_criteria' => 'array',
        'failed_rows'     => 'array',
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
