<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ReportWeeklyController
{
   
    public function index(Request $request){
        
        $uid = $request->session()->get('uid');


        if($uid = null) {
            return view('signin');
        }

        $reports = DB::table('REPORT_W AS report_w')
            ->select('user.name', 'user.user_no', 'week_no.day', 'status.status', 'report_w.week_no')
            ->leftjoin('USER AS user', 'report_w.id', 'user.id')
            ->leftjoin('STATUS AS status', 'status.status_id', 'report_w.status_id')
            ->leftjoin('WEEK_NO AS week_no', 'week_no.week_no', 'report_w.week_no')
            ->orderBy('user.user_no', 'asc')
            ->orderBy('week_no.day', 'desc')
            ->get();

// dd($reports);

        $view_data['reportlist'] = $reports;

        return view('./report_weekly', $view_data);

    }

}
