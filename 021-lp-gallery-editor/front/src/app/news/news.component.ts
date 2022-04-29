import { HttpClient } from '@angular/common/http';
import { Component, OnInit } from '@angular/core';
import { Student } from '../student';

@Component({
  selector: 'app-news',
  templateUrl: './news.component.html',
  styleUrls: ['./news.component.css']
})


export class NewsComponent implements OnInit {
  
  msg: string = '';

  constructor(private http: HttpClient) { }

  ngOnInit(): void {
  }

  onFinished() {
    //this.newsClicked.emit();
    console.log('ccc');
    this.http
      //.get<Student[]>(`http://127.0.0.1:8000/api/students`)
      .get<Student[]>(`https://rehop.fun/angular/back/public/api/students/`)
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


}
