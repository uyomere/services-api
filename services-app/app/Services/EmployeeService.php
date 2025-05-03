<?php

namespace App\Services;

use App\Services\EmployeeService;

class EmployeeService{

    public function getAllServices(){
        return EmployeeService::with(['department', 'country'])->get();
    }
}
