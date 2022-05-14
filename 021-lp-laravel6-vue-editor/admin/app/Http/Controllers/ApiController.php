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

    public function news() {
        
        $news = file_get_contents(storage_path() . "/app/public/news.json");

        if(!empty($news)){
            $obj = json_decode($news);
            $dispOrders = array_column($obj->news, 'displayOrder');
            // displayOrder の昇順（SORT_ASC）に並び替える.
            array_multisort($dispOrders, SORT_ASC, $obj->news);
            $news = json_encode($obj);
        }

        //echo "<pre>";
        print_r($news);
    }

    
    public function update(Request $request) {

        $targetSeq = $request['seq'];

        $news = file_get_contents(storage_path() . "/app/public/news.json");
        $news = json_decode($news, true);

        //dd($targetSeq);

        //return $news;
        $updatedNews = [];

        foreach($news['news'] as $row){

            $seq = $row['seq'];
            if($targetSeq == $seq){
                //
                $row['message'] = $request['message'];
            }

            $updatedNews['news'][] = $row;

        }

        //dd($updatedNews['news']);

        //$bytes = file_put_contents(storage_path() . "/app/public/news.json", json_encode($updatedNews, JSON_PRETTY_PRINT)); 
        $bytes = file_put_contents(storage_path() . "/app/public/news.json", json_encode($updatedNews)); 
        
        $news = file_get_contents(storage_path() . "/app/public/news.json");
        //echo "<pre>";
        print_r($news);
        
    }


    public function addNews(Request $request) {

        

    }


}
