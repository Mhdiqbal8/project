<?php

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

if (! function_exists('activity_log')) {
    /**
     * Tulis activity log.
     *
     * @param  string              $action        e.g. 'bap.acc_mutu'
     * @param  Model|array|null    $subject       model yang terkait (optional)
     * @param  string|null         $description   teks
     * @param  array               $properties    meta tambahan
     */
    function activity_log(string $action, $subject = null, ?string $description = null, array $properties = []): ActivityLog
    {
        $req = request();

        $subjectType = null;
        $subjectId   = null;

        if ($subject instanceof Model) {
            $subjectType = get_class($subject);
            $subjectId   = $subject->getKey();
        } elseif (is_array($subject)) {
            $properties = array_merge($properties, ['subject' => $subject]);
        }

        return ActivityLog::create([
            'user_id'      => optional(auth()->user())->id,
            'action'       => $action,
            'subject_type' => $subjectType,
            'subject_id'   => $subjectId,
            'description'  => $description,
            'properties'   => $properties,
            'ip_address'   => $req?->ip(),
            'user_agent'   => substr($req?->userAgent() ?? '', 0, 255),
            'method'       => $req?->method(),
            'url'          => substr($req?->fullUrl() ?? '', 0, 2048),
        ]);
    }
}
