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
        

        $news = file_get_contents(storage_path() . "/app/public/news.json");
        $news = json_decode($news, true);

        $updatedNews = [];

        if(array_key_exists('news',$news)){

            foreach($news['news'] as $row){

                $updatedNews['news'][] = $row;
    
            };
    
        }
        

        $newRow = [];
        $newRow['seq'] = $unixTime;
        $newRow['title'] = $request['title'];
        $newRow['titleColor'] = $request['titleColor'];
        $newRow['message'] = $request['message'];
        $newRow['backGroundColor'] = $request['backGroundColor'];
        $newRow['linkCheck'] = $request['linkCheck'];
        $newRow['imageFileName'] = $request['imageFileName'];
        $newRow['imageSize'] = $request['imageSize'];


        if($request->file){
            $file_name = request()->file->getClientOriginalName();
            //dd(request()->file);
            

            //request()->file('file')->storeAs('public/',$file_name);
            request()->file('file')->storeAs('public/news_img/',$file_name);
        
            $newRow['image'] = $file_name;
        }
             

        $updatedNews['news'][] = $newRow;

        $bytes = file_put_contents(storage_path() . "/app/public/news.json", json_encode($updatedNews));
        $news = file_get_contents(storage_path() . "/app/public/news.json");
        print_r($news);


    }


    public function updateNews(Request $request){
        
        $editingSeq = $request['editingSeq'];
        $news = file_get_contents(storage_path() . "/app/public/news.json");
        $news = json_decode($news, true);

        $updatedNews = [];
        
        if(!array_key_exists('news',$news)){
            return;
        }

        foreach($news['news'] as $row){

            if($editingSeq == $row['seq']){

                $row['title'] = $request['title'];
                $row['titleColor'] = $request['titleColor'];
                $row['message'] = $request['message'];
                $row['backGroundColor'] = $request['backGroundColor'];
                $row['linkCheck'] = $request['linkCheck'];
                
                $row['imageSize'] = $request['imageSize'];

                if($request->file){
                    $file_name = request()->file->getClientOriginalName();
                    //dd(request()->file);
                    
                    //request()->file('file')->storeAs('public/',$file_name);
                    request()->file('file')->storeAs('public/news_img/',$file_name);
                
                    $row['image'] = $file_name;
                }
             

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
