<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DateTime;
use Carbon\Carbon;

class EditorController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('editor');
    }

    // public function fileupload(Request $request){

    //     $file_name = $request->file->getClientOriginalName();
    //     request()->file->storeAs('app/public/',$file_name);
    // } 

    public function addNews(Request $request){
        
        $now = new DateTime(); 
        $unixTime = $now->format('U');

        // 1651970210
        //dd($unixTime);
        

        $news = file_get_contents(storage_path() . "/app/public/news.json");
        $news = json_decode($news, true);

        $updatedNews = [];

        foreach($news['news'] as $row){

            $updatedNews['news'][] = $row;

        }

        $updatedNews['news'][] = [
            "seq" => $unixTime,
            "title" => "テスト登録",
            "message" => $request['message'],
            "image" => "image3.jpg",

        ];

        $bytes = file_put_contents(storage_path() . "/app/public/news.json", json_encode($updatedNews)); 


    }


    public function updateNews(Request $request){
        
        $editingSeq = $request['editingSeq'];
        $news = file_get_contents(storage_path() . "/app/public/news.json");
        $news = json_decode($news, true);

        $updatedNews = [];

        foreach($news['news'] as $row){

            if($editingSeq == $row['seq']){

                $row['title'] = $request['title'];
                $row['titleColor'] = $request['titleColor'];
                $row['message'] = $request['message'];
                $row['backGroundColor'] = $request['backGroundColor'];
                $row['linkCheck'] = $request['linkCheck'];
                $row['imageFileName'] = $request['imageFileName'];
                $row['imageSize'] = $request['imageSize'];

            }

            $updatedNews['news'][] = $row;

        }

        $bytes = file_put_contents(storage_path() . "/app/public/news.json", json_encode($updatedNews));
        $news = file_get_contents(storage_path() . "/app/public/news.json");
        print_r($news);

    }

    public function deleteNews(Request $request){
        
        $editingSeq = $request['editingSeq'];
        $news = file_get_contents(storage_path() . "/app/public/news.json");
        $news = json_decode($news, true);

        //dd($editingSeq);

        $updatedNews = [];

        foreach($news['news'] as $row){

            if($editingSeq != $row['seq']){
                $updatedNews['news'][] = $row;
            }

        }

        //dd($updatedNews);

        $bytes = file_put_contents(storage_path() . "/app/public/news.json", json_encode($updatedNews));
        $news = file_get_contents(storage_path() . "/app/public/news.json");
        print_r($news);


    }

}
