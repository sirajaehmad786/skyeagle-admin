<?php

namespace App\Jobs;

use App\Constants\ContactStatus;
use App\Models\Contact;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessImportedContact implements ShouldQueue
{
    use Queueable;

    protected $data;
    protected $contactRepository;

    /**
     * Create a new job instance.
     */
    public function __construct(array $data, ContactRepository $contactRepository)
    {
        
        $this->data = $data;
        $this->contactRepository = $contactRepository;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        if(!empty($this->data)){
            $insert=[];
            foreach($this->data as $key=>$row){
                
                if($key >0){
                    $insert[] = [
                        'initial' => $row['0'] ?? null,
                        'first_name' => $row['1'] ?? null,
                        'last_name' => $row['2'] ?? null,
                        'email' => $row['3'] ?? null,
                        'mobile_no' => $row['4'] ?? null,
                        'lead_source' => $row['5'] ?? null,
                        'status' => ContactStatus::ACTIVE,
                    ];
                    $request = new Request($data);
                    $this->contactRepository->create($request);
                }
            }
        }
    }
}
