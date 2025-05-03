<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\EmployeeService;
use Illuminate\Support\Facades\Http;

class FakeStoreController extends Controller
{
    public function getAllUsers()
    {
        $response = Http::get("https://fakestoreapi.com/products");
        $users = $response->json();
        return view('getallusers.getallusers', compact('users'));
    }

    protected $employeeService;

    public function __construct(EmployeeService $employeeService)
    {
        $this->$employeeService = $employeeService;
    }

    public function allUsers(Request $request)
    {
        $employee = $this->employeeService->getAllUsers();
        return view();
    }
}
