<?php

namespace App\Imports;

use App\Jobs\ProcessImportedContact;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;

class ContactImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        ProcessImportedContact::dispatch($rows->toArray());
    }
}
