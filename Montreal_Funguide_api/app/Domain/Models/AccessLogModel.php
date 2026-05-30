<?php

namespace App\Domain\Models;

class AccessLogModel extends BaseModel
{
    public function insertLog(array $log): mixed
    {
        return $this->insert('ws_log', [
            'user_id'     => $log['user_id'] ?? null,
            'email'       => $log['email'] ?? null,
            'method'      => $log['method'],
            'uri'         => $log['uri'],
            'ip_address'  => $log['ip_address'],
            'status_code' => $log['status_code'] ?? null,
            'user_action' => $log['user_action'],
        ]);
    }
}
