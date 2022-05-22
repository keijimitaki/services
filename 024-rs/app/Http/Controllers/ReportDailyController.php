<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DateTime;

class ReportDailyController
{
   
    public function index(Request $request){

        $week_no = $request->input('week_no');
        $user_id = $request->session()->get('uid');
        $user_name = DB::table('USER')->where('id',$user_id)->first()->name;

        //本日日付から、今週の週番号を取得
        $today = new DateTime();
            
        $year = $today->format('Y');
        $month = $today->format('m');
        $day = $today->format('d');

        $this_week_no = DB::table('CALENDAR')
            ->select('week_no')
            ->where('year', '=', $year)            
            ->where('month', '=', $month)            
            ->where('day', '=', $day)            
            ->first()->week_no;

        $last_week_no = $this_week_no - 1;
        $next_week_no = $this_week_no + 1;
        
        if(empty($week_no)){
            $week_no = $this_week_no;
        }

        //レポート取得（今週予定）
        $reports_this_week = DB::table('CALENDAR AS c')
            ->select('c.year', 'c.month', 'c.day', 'c.dayofweek', 'rd.task_name', 'rd.task_detail', 'rd.m')
            ->leftjoin('REPORT_D AS rd', function($join) use ($user_id){
                $join->on('c.year', 'rd.year');
                $join->on('c.month', 'rd.month');
                $join->on('c.day', 'rd.day');
                $join->where('rd.id', $user_id);
            })
            ->where('c.week_no', $week_no)
            ->orderBy('c.year', 'asc')
            ->orderBy('c.month', 'asc')
            ->orderBy('c.day', 'asc')
            ->limit(6)
            ->get();

        //レポート取得（先週実績）
        $reports_last_week = DB::table('CALENDAR AS c')
            ->select('c.year', 'c.month', 'c.day', 'c.dayofweek', 'rd.task_name', 'rd.task_detail', 'rd.m')
            ->leftjoin('REPORT_D AS rd', function($join) use ($user_id){
                $join->on('c.year', 'rd.year');
                $join->on('c.month', 'rd.month');
                $join->on('c.day', 'rd.day');
                $join->where('rd.id', $user_id);
            })
            ->where('c.week_no', $week_no-1)
            ->orderBy('c.year', 'asc')
            ->orderBy('c.month', 'asc')
            ->orderBy('c.day', 'asc')
            ->limit(6)
            ->get();

        if(empty($reports_this_week)){
            //対象カレンダーが無い場合
            //エラー処理
        }


        $view_data['user_name'] = $user_name;
        $view_data['current_week_no'] = $week_no;
        $view_data['last_week_no'] = $last_week_no;
        $view_data['this_week_no'] = $this_week_no;
        $view_data['next_week_no'] = $next_week_no;

        $view_data['week_start_day'] = $reports_this_week[0]->year . '年' . $reports_this_week[0]->month . '月' . $reports_this_week[0]->day . '日';
        $view_data['week_end_day'] = $reports_this_week[5]->year . '年' . $reports_this_week[5]->month . '月' . $reports_this_week[5]->day . '日';


        $view_data['reports_last_week'] = $reports_last_week;
        $view_data['reports_this_week'] = $reports_this_week;

        return view('report_daily', $view_data);

    }


    public function saveReports(Request $request){

        //先週実績 上長が承認したら更新不可。非表示にする？。実績入力操作をしてクリアする？
        //今週予定の登録

        $week_no = $request->input('week_no');
        $user_id = $request->session()->get('uid');

        //先週データの削除（TODO 承認済みのデータの扱いの考慮が必要）
        DB::table('REPORT_D AS rd')
            ->leftjoin('CALENDAR AS c', function($join) {
                $join->on('c.year', 'rd.year');
                $join->on('c.month', 'rd.month');
                $join->on('c.day', 'rd.day');
            })
            ->where('rd.id', $user_id)
            ->where('c.week_no', $week_no-1)
            ->delete();


        $last_week_year = $request->input('last_week_year');
        $last_week_month = $request->input('last_week_month');
        $last_week_day = $request->input('last_week_day');
        $last_week_task_name = $request->input('last_week_task_name');
        $last_week_task_detail = $request->input('last_week_task_detail');
        $last_week_m = $request->input('last_week_m');


        foreach($last_week_year as $index=>$val){

            $year = $last_week_year[$index];
            $month = $last_week_month[$index];
            $day = $last_week_day[$index];
            $task_name = $last_week_task_name[$index];
            $task_detail = $last_week_task_detail[$index];
            $m = $last_week_m[$index];

            $data = [
                'id' => $user_id,
                'year' => $year,
                'month' => $month,
                'day' => $day,
                'task_name' => $task_name,
                'task_detail' => $task_detail,
                'm' => empty($m)? 0 : $m,
                'status_id' => 0, //暫定
            ];

            DB::table('REPORT_D')
                ->insert($data);

        }

        //TODO リファクタリング

        //今週データの削除（TODO 承認済みのデータの扱いの考慮が必要）
        DB::table('REPORT_D AS rd')
            ->leftjoin('CALENDAR AS c', function($join) {
                $join->on('c.year', 'rd.year');
                $join->on('c.month', 'rd.month');
                $join->on('c.day', 'rd.day');
            })
            ->where('rd.id', $user_id)
            ->where('c.week_no', $week_no)
            ->delete();


        $this_week_year = $request->input('this_week_year');
        $this_week_month = $request->input('this_week_month');
        $this_week_day = $request->input('this_week_day');
        $this_week_task_name = $request->input('this_week_task_name');
        $this_week_task_detail = $request->input('this_week_task_detail');
        $this_week_m = $request->input('this_week_m');

        foreach($this_week_year as $index=>$val){

            $year = $this_week_year[$index];
            $month = $this_week_month[$index];
            $day = $this_week_day[$index];
            $task_name = $this_week_task_name[$index];
            $task_detail = $this_week_task_detail[$index];
            $m = $this_week_m[$index];

            $data = [
                'id' => $user_id,
                'year' => $year,
                'month' => $month,
                'day' => $day,
                'task_name' => $task_name,
                'task_detail' => $task_detail,
                'm' => empty($m)? 0 : $m,
                'status_id' => 0, //暫定
            ];

            DB::table('REPORT_D')
                ->insert($data);

        }
    

        return redirect('/report_daily');

    }

    public function signOut(Request $request){

        $request->session()->forget('uid');
        $request->session()->flush();

        return redirect('./');

    }

}
