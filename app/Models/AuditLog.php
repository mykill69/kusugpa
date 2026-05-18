<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'user_name', 'user_role', 'action', 'module',
        'description', 'details', 'ip_address', 'user_agent'
    ];

    protected $casts = [
        'details' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Create an audit log entry
     */
    public static function log($action, $module, $description, $details = null)
    {
        $user = auth()->user();
        
        return self::create([
            'user_id' => $user ? $user->id : null,
            'user_name' => $user ? $user->fname . ' ' . $user->lname : 'System',
            'user_role' => $user ? $user->role : 'System',
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'details' => $details,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}