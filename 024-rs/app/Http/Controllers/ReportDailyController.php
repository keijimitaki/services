<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use DateTime;
use SplFileObject;
use TCPDF;

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

    public function csvImport(Request $request){

        $last_week_csv = $request->file('file_last_week_csv');
        $this_week_csv = $request->file('file_this_week_csv');
        if( is_null($last_week_csv) && is_null($this_week_csv) ){
            return redirect('/report_daily');
        }

        $week_no = $request->input('week_no');
        $user_id = $request->session()->get('uid');

        $target_week_no = $week_no;
        $target_file = null;
        $file_input_name = null;
        $file_path = null;
        $file_check_ok = true;

        if(is_null($last_week_csv)){
            $target_week_no = $week_no;
            $target_file = $this_week_csv;
            $file_input_name = 'file_this_week_csv';

        } else {
            $target_week_no = $week_no - 1;
            $target_file = $last_week_csv;
            $file_input_name = 'file_last_week_csv';

        }


        

        setlocale(LC_ALL, 'ja_JP.UTF-8');

        $file_path = $request->file($file_input_name)->path($target_file);
        $file = new SplFileObject($file_path);
        $file->setFlags(SplFileObject::READ_CSV);

        //エラーチェック
        //他人のcsvはng

        
        $target_week = DB::table('CALENDAR')
            ->select('year', 'month', 'day', 'week_no')
            ->where('week_no', $target_week_no)
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->orderBy('day', 'asc')
            ->limit(6)
            ->get();
        
        $target_days = [];
        foreach ($target_week as $key=>$row){
            $target_date = $row->year . '/' . $row->month . '/' .$row->day;
            $target_days[] = $target_date;
        }
        //dd($week_no,$target_week_no,$target_days);
        //dd($target_days);

        $row_count = 1;
        $reports_csv =[];

        foreach ($file as $row){
            if ($row === [null]) continue;
            if ($row_count > 1) {
                
                // CSVの文字コードがSJISなのでUTF-8に変更
                $date = mb_convert_encoding($row[0], 'UTF-8', 'SJIS');
                $user_id_csv = mb_convert_encoding($row[3], 'UTF-8', 'SJIS');
                $task_name = mb_convert_encoding($row[6], 'UTF-8', 'SJIS');
                $task_detail = mb_convert_encoding($row[8], 'UTF-8', 'SJIS');
                $m = mb_convert_encoding($row[10], 'UTF-8', 'SJIS');
                
                //該当週の日付データ以外は読み飛ばす
                //csvの日付はyyyy/m/d 例）2022/4/1  ※2022/04/01ではない
                if(!in_array($date,$target_days)){
                     continue;
                }

                //同じ日のデータは加算する
                $report = [];
                if(!empty($reports_csv[$date])){
                    $report = $reports_csv[$date];

                    $report['user_id'] = $user_id_csv;
                    $report['task_name'] = $report['task_name'] . "\n". $task_name;
                    $report['task_detail'] = $report['task_name'] . "\n". $task_detail;
                    $m_int = 0;
                    if(is_numeric($m)){
                        $m_int = intval($m);
                    }
                    $m_int_saved = 0;
                    if(is_numeric($report['m'])){
                        $m_int_saved = intval($report['m']);
                    }

                    $report['m'] = $m_int_saved + $m_int;
    
                } else {
                    $report['date'] = $date;
                    $report['user_id'] = $user_id_csv;
                    $report['task_name'] = $task_name;
                    $report['task_detail'] = $task_detail;
                    $m_int = 0;
                    if(is_numeric($m)){
                        $m_int = intval($m);
                    }
                    $report['m'] = $m_int;
    
                }


                $reports_csv[$date] = $report;

            }
            $row_count++;
        }

        if(!$file_check_ok){
            return redirect('/report_daily');
        }

        //既に登録されていれば更新しない
        foreach ($reports_csv as $key=>$row){
            
            $ymd = explode('/',$key);

            $report_saved = DB::table('REPORT_D')
                ->where('id', $user_id)
                ->where('year', $ymd[0])
                ->where('month', $ymd[1])
                ->where('day', $ymd[2])
                ->first();
            
            if( !empty($report_saved) && 
                (!empty($report_saved->task_name) ||
                !empty($report_saved->task_detail) ||
                (!empty($report_saved->task_m) && ($report_saved->task_m !== 0) ) ) ){
                //既にデータが登録されていたら対象外とする
                continue;

            } else {

                //delete insert
                DB::table('REPORT_D')
                    ->where('id', $user_id)
                    ->where('year', $ymd[0])
                    ->where('month', $ymd[1])
                    ->where('day', $ymd[2])
                    ->delete();

                $year = $ymd[0];
                $month = $ymd[1];
                $day = $ymd[2];
                $task_name = $row['task_name'];
                $task_detail = $row['task_detail'];
                $m = $row['m'];
    
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

        }

        //dd($reports_csv);
        
        return redirect('/report_daily');

    }

    private function getM(){
        
    }


    public function pdfExport(Request $request){
        
        $week_no = $request->input('week_no');
        $user_id = $request->session()->get('uid');

        $tcpdf = new TCPDF();

        // フォント、スタイル、サイズ をセット
        $tcpdf->setFont('kozminproregular','',10);
        // ページを追加
        $tcpdf->addPage();
        // HTMLを描画、viewの指定と変数代入
        $tcpdf->writeHTML(view("pdf.pdf", ['name' => 'PDFさん'])->render());
        // 出力の指定です、ファイル名、拡張子、Dはダウンロードを意味します。
        //$tcpdf->output('test' . '.pdf', 'D');
        $tcpdf->output('test' . '.pdf', 'I');
        return redirect('/report_daily');

    }


    private function isValidCsvRow($user_id, $user_id_csv){
        //user_idが他人の場合、NG
        return ($user_id == $user_id_csv);
    }

    public function signOut(Request $request){

        $request->session()->forget('uid');
        $request->session()->flush();

        return redirect('./');

    }


}
