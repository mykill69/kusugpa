<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class LoanAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id', 'filename', 'original_filename', 'file_path',
        'mime_type', 'file_size', 'document_type', 'description', 'uploaded_by'
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get temporary URL for viewing the file
     */
    public function getTemporaryUrl($minutes = 5)
    {
        return route('loans.attachment.view', [
            'attachment' => $this->id,
            'token' => $this->generateToken()
        ]);
    }

    /**
     * Generate a secure token for file access
     */
    public function generateToken()
    {
        $data = [
            'attachment_id' => $this->id,
            'loan_id' => $this->loan_id,
            'expires' => now()->addMinutes($minutes ?? 5)->timestamp,
        ];
        return Crypt::encrypt($data);
    }

    /**
     * Validate a token and return data or null
     */
    public static function validateToken($token)
    {
        try {
            $data = Crypt::decrypt($token);
            if (!isset($data['expires']) || $data['expires'] < now()->timestamp) {
                return null;
            }
            return $data;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get file size in human readable format
     */
    public function getFileSizeFormattedAttribute()
    {
        $bytes = $this->file_size;
        if ($bytes === 0) return '0 B';
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes, 1024));
        return round($bytes / pow(1024, $i), 1) . ' ' . $units[$i];
    }
}