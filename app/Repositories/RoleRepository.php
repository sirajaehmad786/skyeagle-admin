<?php 

namespace App\Repositories;

use App\Models\Role;
use Illuminate\Support\Facades\Auth;


class RoleRepository
{
    public function roles()
    {
        $userLevel = Auth::user()->roles[0]->level;
        if($userLevel==1){
            return Role::get();
        }else{
            return Role::where('level', '>', $userLevel)->get();
        }
        
    }
}
