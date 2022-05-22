<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.84.0">
    <title>Report System</title>

    <!-- <link rel="canonical" href="https://getbootstrap.com/docs/5.0/examples/checkout/"> -->

    

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="{{ asset('/assets/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/reportlist.css') }}">

    <!-- <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
    </style> -->

    
    <!-- Custom styles for this template -->
    <link href="reportlist.css" rel="stylesheet">
  </head>
  <body class="bg-light">
    
<div class="container-fluid px-5">
  
  <main>
    <div class="py-4 text-center">
      <!-- <img class="d-block mx-auto mb-4" src="../assets/brand/bootstrap-logo.svg" alt="" width="72" height="57"> -->
      <h2>Report System</h2>
      <!-- <p class="lead"></p> -->
    </div>

    <div class="row">
      <div class="col-3">

        <div class="d-flex flex-column flex-shrink-0 p-3 bg-light" >
          <a href="#" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-dark text-decoration-none">
            <svg class="bi me-2" width="40" height="32"><use xlink:href="#bootstrap"/></svg>
            <span class="fs-4">週報</span>
          </a>
          <hr>
          <ul class="nav flex-column mb-auto">
            <li class="nav-item">
              <a href="#" class="nav-link active" aria-current="page">
                <svg class="bi me-2" width="16" height="16"><use xlink:href="#home"/></svg>
                週報一覧
              </a>
            </li>
            <li>
              <a href="./report_daily" class="nav-link link-dark">
                <svg class="bi me-2" width="16" height="16"><use xlink:href="#speedometer2"/></svg>
                新規作成
              </a>
            </li>
            <li>
              <a href="#" class="nav-link link-dark disabled">
                <svg class="bi me-2" width="16" height="16"><use xlink:href="#table"/></svg>
                下書き
              </a>
            </li>
          </ul>

        </div>

      </div>
      <div class="col ">
        週報一覧
        <table class="table">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">日付</th>
              <th scope="col">申請者</th>
              <th scope="col">ステータス</th>
              <th scope="col">確認</th>
            </tr>
          </thead>
          <tbody>
          @foreach ($reportlist as $report)
            <tr>
              <th scope="row">{{ ($loop->index) + 1}}</th>
              <td>{{$report->day}}</td>
              <td>{{$report->name}}</td>
              <td>{{$report->status}}</td>
              <td>確認</td>
            </tr>
          @endforeach
          </tbody>
        </table>        
      </div>
      <div class="col-3 pt-4 px-5 ">
        <div class="row py-2">
          <label for="start">提出日（From）</label>
          <input type="date" class="col-6" id="start" name="report-from"
            value=""
            min="2018-01-01" max=""/>
        </div>
        <div class="row py-2">
          <label for="to">提出日（To）</label>
          <input type="date" class="col-6" id="to" name="report-to"
            value=""
            min="2018-01-01" max=""/>
        </div>
        <div class="row py-2">
          <label for="user">申請者</label>
          <input type="text" class="col-6" id="user" name="user" value=""/>
        </div>        
        <div class="row py-2">
          <label for="status">ステータス</label>
          <select class="col-6" id="status" name="status">
            <option>提出済み</option>
            <option>確認</option>
          </select>
        </div>
        <div class="row py-2">
          <label for="disp_count">表示件数</label>
          <select class="col-3" id="disp_count" name="disp_count">
            <option>50</option>
            <option>100</option>
          </select>
          <span class="col-3" >
            ずつ表示
          </span>
        </div>
        <div class="row py-2">
          <!-- <div class="d-flex col-6 justify-content-between"> -->
            <button type="button" class="col-3 btn btn-outline-secondary">クリア</button>
            <button type="button" class="col-3 btn btn-primary">検索</button>
          <!-- </div> -->
        </div>

      </div>

    </div>

  </main>


</div>


    <script src="../assets/dist/js/bootstrap.bundle.min.js"></script>

      <!-- <script src="form-validation.js"></script> -->
  
    </body>

</html>
