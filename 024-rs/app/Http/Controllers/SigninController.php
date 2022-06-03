<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SigninController
{
   
    public function signin(Request $request){


        $id = $request['id']; 
        $pw = $request['pw'];
        
        $user = DB::table('USER')
            ->where('id',$id)
            ->where('pw',$pw)
            ->first();

        //dd($request->session()->get('uid'));

        $request->session()->flush();

        if( $user != null ){
            $request->session()->put('uid',$id);
            // return redirect('./report_weekly');
            return redirect('./report_information');

        } else {
            return view('signin');
        }

        
    }

}
