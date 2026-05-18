<?php

namespace App\Helpers;

use App\Models\AuditLog;

class AuditHelper
{
    public static function log($action, $module, $description, $details = null)
    {
        return AuditLog::log($action, $module, $description, $details);
    }
}