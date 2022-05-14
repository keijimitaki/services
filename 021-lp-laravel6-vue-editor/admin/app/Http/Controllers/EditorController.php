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
        $newRow['id'] = $unixTime;
        $newRow['displayOrder'] = $request['displayOrder'];
        $newRow['title'] = $request['title'];
        $newRow['titleColor'] = $request['titleColor'];
        $newRow['message'] = $request['message'];
        $newRow['messageColor'] = $request['messageColor'];
        $newRow['backgroundColor'] = $request['backgroundColor'];
        $newRow['linkCheck'] = $request['linkCheck'];
        $newRow['linkUrl'] = $request['linkUrl'];

        $newRow['imageFileName'] = $request['imageFileName'];
        $newRow['imageSize'] = $request['imageSize'];

        if($request->file){
            $file_name = request()->file->getClientOriginalName();
            //dd(request()->file);
            //request()->file('file')->storeAs('public/',$file_name);
            request()->file('file')->storeAs('public/news_img/',$file_name);
        
            $newRow['image'] = $file_name;
        }
        $newRow['tag'] = $request['tag'];
        $newRow['tagColor'] = $request['tagColor'];
        $newRow['tagBackgroundColor'] = $request['tagBackgroundColor'];

        $updatedNews['news'][] = $newRow;

        $bytes = file_put_contents(storage_path() . "/app/public/news.json", json_encode($updatedNews));
        $news = file_get_contents(storage_path() . "/app/public/news.json");
        print_r($news);


    }


    public function updateNews(Request $request){
        
        $editingId = $request['editingId'];
        $news = file_get_contents(storage_path() . "/app/public/news.json");
        $news = json_decode($news, true);

        $updatedNews = [];
        
        if(!array_key_exists('news',$news)){
            return;
        }

        foreach($news['news'] as $row){

            if($editingId == $row['id']){

                $row['displayOrder'] = $request['displayOrder'];
                $row['title'] = $request['title'];
                $row['titleColor'] = $request['titleColor'];
                $row['message'] = $request['message'];
                $row['messageColor'] = $request['messageColor'];
                $row['backgroundColor'] = $request['backgroundColor'];
                $row['linkCheck'] = $request['linkCheck'];
                $row['linkUrl'] = $request['linkUrl'];
                $row['imageSize'] = $request['imageSize'];

                if($request->file){

                    //変更前のファイルがあれば削除
                    if(array_key_exists('image',$row)){
                        $image = $row['image'];
                        \Storage::disk('public')->delete('/news_img/'.$image);
                    }

                    $file_name = request()->file->getClientOriginalName();
                    //dd(request()->file);
                    
                    //request()->file('file')->storeAs('public/',$file_name);
                    request()->file('file')->storeAs('public/news_img/',$file_name);
                
                    $row['image'] = $file_name;
                }
                $row['tag'] = $request['tag'];
                $row['tagColor'] = $request['tagColor'];
                $row['tagBackgroundColor'] = $request['tagBackgroundColor'];
        
            }

            //dd($row);

            $updatedNews['news'][] = $row;

        }

        $bytes = file_put_contents(storage_path() . "/app/public/news.json", json_encode($updatedNews));
        $news = file_get_contents(storage_path() . "/app/public/news.json");
        print_r($news);

    }

    public function deleteNews(Request $request){
        
        $editingId = $request['editingId'];
        $news = file_get_contents(storage_path() . "/app/public/news.json");
        $news = json_decode($news, true);

        $updatedNews = [];

        foreach($news['news'] as $row){

            if($editingId != $row['id']){
                $updatedNews['news'][] = $row;
            
            } else {

                //ファイルがあれば削除
                if(array_key_exists('image',$row)){
                    $image = $row['image'];
                    \Storage::disk('public')->delete('/news_img/'.$image);
                }
            
            }

        }

        $bytes = file_put_contents(storage_path() . "/app/public/news.json", json_encode($updatedNews));
        $news = file_get_contents(storage_path() . "/app/public/news.json");
        print_r($news);


    }

}
