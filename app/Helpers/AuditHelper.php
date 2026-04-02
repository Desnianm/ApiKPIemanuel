<?php

namespace App\Helpers;

use App\Models\AuditLog;

class AuditHelper
{
    public static function log(
        int $userId,
        string $aksi,
        string $tabelTarget,
        int $recordId,
        ?array $dataLama = null,
        ?array $dataBaru = null
    ): void {
        AuditLog::create([
            'user_id'      => $userId,
            'aksi'         => $aksi,
            'tabel_target' => $tabelTarget,
            'record_id'    => $recordId,
            'data_lama'    => $dataLama,
            'data_baru'    => $dataBaru,
            'created_at'   => now(),
        ]);
    }
}