import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Student } from '../student';
import { News } from "../News";
import { map } from 'rxjs/operators';

@Component({
  selector: 'app-news',
  templateUrl: './news.component.html',
  styleUrls: ['./news.component.css']
})


export class NewsComponent implements OnInit {
  
  
  msg: string = '';
  news: News[] = [];

  editSeq: number = 0;
  editMessage: string = '';

  constructor(private http: HttpClient) { }

  ngOnInit(): void {

    console.log('ccc');
    this.http
      .get<any>(`http://127.0.0.1:8000/api/news`)
      // .pipe( map(res => ({
      //   seq: res.seq,
      //   title: res.title,
      //   message: res.message,
      //   image_name: res.image_name,
      // })))
      .subscribe(res => {
        //angular.forEach(element => console.log(element))
        console.log(this.news);
        let sortedNews = res.news.sort(function(a:News, b:News) {
          return (a.seq < b.seq) ? -1 : 1;  //オブジェクトの昇順ソート
        });
        this.news = sortedNews;
        console.log(res.news);
        console.log(this.news.length);
      }
    );

    //http://127.0.0.1:8000/news_img/

  }

  onFinished() {
    //this.newsClicked.emit();
    console.log('ccc');
    this.http
      .get<Student[]>(`http://127.0.0.1:8000/api/students`)
      //.get<Student[]>(`https://rehop.fun/angular/back/public/api/students/`)
      .subscribe(res => {
        console.log(res);
      }
    );

    // fetch('http://127.0.0.1:8000/api/students')
    // //fetch('https://rehop.fun/angular/back/public/api/students/')
    // .then(response => response.json())
    // .then(data => console.log(data));

    this.msg = new Date().toLocaleString();
  }

  selectTarget(targetId:number){
    this.editSeq = targetId;
    console.log(this.news[targetId-1].message);
    this.editMessage = this.news[targetId-1].message;
  }

  show(){
   // this.editNews = null;
  }

  onUpdate(){

    console.log('update');
    this.http
      .post<any>(`http://127.0.0.1:8000/api/news/update`,
        {
          "seq": this.editSeq,
          "message": this.editMessage,
          "image":this.news[this.editSeq-1].message
        },)
      .subscribe(res => {
        console.log(res);

        console.log(this.news);
        let sortedNews = res.news.sort(function(a:News, b:News) {
          return (a.seq < b.seq) ? -1 : 1;  //オブジェクトの昇順ソート
        });
        this.news = sortedNews;
        console.log(res.news);
        console.log(this.news.length);        
      }
    );


  }


}
