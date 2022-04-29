<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function getAllStudents() {
        
        return response()->json([
               'students' => [
                   ['name'=>'山田'],
                   ['name'=>'斎藤'],
                   ['name'=>'市川']
                ]
            ]
        );
        
    }
    
}
