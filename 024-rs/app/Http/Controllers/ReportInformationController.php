<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ReportInformationController
{
   
    public function index(Request $request){
        

        $user_id = $request->session()->get('uid');
        $user_name = DB::table('USER')->where('id',$user_id)->first()->name;
        if($user_id == null) {
            return view('signin');
        }

        $reports = DB::table('REPORT_W AS report_w')
            ->select('user.name', 'user.user_no', 'week_no.day', 'report_w.week_no')
            //->select('user.name', 'user.user_no', 'week_no.day', 'status.status', 'report_w.week_no')
            ->leftjoin('USER AS user', 'report_w.id', 'user.id')
            //->leftjoin('STATUS AS status', 'status.status_id', 'report_w.status_id')
            ->leftjoin('WEEK_NO AS week_no', 'week_no.week_no', 'report_w.week_no')
            ->where('report_w.id',$user_id)
            ->orderBy('week_no.day', 'asc')
            ->orderBy('user.user_no', 'asc')
            ->get();


        $view_data['user_name'] = $user_name;
        $view_data['reportlist'] = $reports;

        return view('./report_information', $view_data);

    }

}
