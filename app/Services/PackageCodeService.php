<?php

namespace App\Services;

use App\Models\Package;
use Illuminate\Support\Facades\DB;

class PackageCodeService
{
    public function generate()
    {
        return DB::transaction(function () {
            $year = date('Y');
            $lastPackage = Package::whereYear('created_at', $year)
                ->lockForUpdate()
                ->orderBy('id', 'desc')
                ->first();
            if ($lastPackage && $lastPackage->package_code) {
                $lastNumber = intval(substr($lastPackage->package_code, -4));
                $nextNumber = $lastNumber + 1;
            } else {
                $nextNumber = 1;
            }
            return 'PKG-' . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        });
    }
}