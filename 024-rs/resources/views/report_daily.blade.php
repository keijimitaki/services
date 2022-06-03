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
    

    </head>
  <body class="bg-light">

  <form action="{{ url('/report_daily/save')}}" method="POST" enctype="multipart/form-data">
    {{ csrf_field() }}  
    
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
          <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-dark text-decoration-none">
            <svg class="bi me-2" width="40" height="32"><use xlink:href="#bootstrap"/></svg>
            <span class="fs-4">週報</span>
          </a>
          <hr>
          <ul class="nav flex-column mb-auto">
            <li>
              <a href="./report_weekly" class="nav-link link-dark" aria-current="page">
                <svg class="bi me-2" width="16" height="16"><use xlink:href="#home"/></svg>
                週報一覧
              </a>
            </li>
            <li>
              <a href="#" class="nav-link active ">
                <svg class="bi me-2" width="16" height="16"><use xlink:href="#speedometer2"/></svg>
                新規作成
              </a>
            </li>
          </ul>

        </div>

      </div>
      
      <div class="col">


        <div class="row">
          <div class="col-9"> 
            基準週を選択してください
          </div>
          <div class="col-3">
            <!-- ログイン者の名前　さん     -->
          </div>
        </div>

        <div class="row">
          <div class="col-3">
            <div class="d-flex">

              <ul class="pagination">
                <li class="page-item">
                  <a class="page-link" href="./report_daily?week_no={{$current_week_no-1}}" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                  </a>
                </li>
                <li class="page-item"><a class="page-link" href="./report_daily?week_no={{$last_week_no}}">前週</a></li>
                <li class="page-item"><a class="page-link" href="./report_daily?week_no={{$this_week_no}}">当週</a></li>
                <li class="page-item"><a class="page-link" href="./report_daily?week_no={{$next_week_no}}">次週</a></li>
                <li class="page-item">
                  <a class="page-link" href="./report_daily?week_no={{$current_week_no+1}}" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                  </a>
                </li>
              </ul>
              <input type="hidden" name="week_no" value="{{$current_week_no}}"></input>

            </div>

          </div>

          <div class="col">
            {{$week_start_day}} ～ {{$week_end_day}} 所属：○○　名前：{{$user_name}}     
          </div>

          <div class="col-3">
            <div class="row">
              <div class="col">
                お知らせ   
              </div>         
              <div class="col">
                <a href="./report_daily/signout" class="nav-link link-dark" aria-current="page">
                  ログアウト
                </a>
              </div>          
            </div>          
          </div>          

        </div>

        <ul class="nav nav-tabs">
          <li class="nav-item">
            <a class="nav-link active" data-target="last_week" href="#">実績</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-target="this_week" href="#">予定</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-target="summary" href="#">所感</a>
          </li>
        </ul>      
        
        <div id="report_last_week" style="display:block;">
          @include('report_daily_last_week')
        </div>

        <div id="report_this_week" style="display:none;">
          @include('report_daily_this_week')
        </div>

        <div id="report_summary" style="display:none;">
          @include('report_daily_summary')
        </div>


      </div>


      

    </div>


  </main>

</div>

</form>

      <!-- <script src="../assets/dist/js/bootstrap.bundle.min.js"></script> -->
      <!-- <script src="form-validation.js"></script> -->
      <script src="{{ asset('/assets/dist/js/bootstrap.bundle.min.js') }}"></script>

      <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>

      <script type="text/javascript">
        $(function(){

          //Mの計算
          //実績のM
          $('input[name="last_week_m\\[\\]"]').each(function(index,element){
            console.log(index + ':' + $(element).val());
          });

          $('input[name="this_week_m\\[\\]"]').each(function(index,element){
            console.log(index + ':' + $(element).val());
          });

          $('.nav-item .nav-link').click(function () {

            $('.nav-item .nav-link').each(function(index, element){
              $(element).removeClass();
              $(element).addClass('nav-link');

              const target_hide_area = $(element).data('target');
              $('#report_' + target_hide_area).hide();
            })

            $(this).addClass('nav-link active');
            const target_show_area = $(this).data('target');
            $('#report_' + target_show_area).show();

          });


          $('input[name="file_last_week_csv"]').change(function () {

            $('button[name="btn_import_last_week_csvfile"]').removeClass();
            let btn_state = 'disabled';
            if($(this).val()) {
              btn_state = 'active';
            }
            $('button[name="btn_import_last_week_csvfile"]').addClass('btn btn-outline-secondary ' + btn_state);

            $('button[name="btn_import_this_week_csvfile"]').removeClass();
            $('button[name="btn_import_this_week_csvfile"]').addClass('btn btn-outline-secondary disabled');
            $('input[name="file_this_week_csv"]').val('');

          });

          $('input[name="file_this_week_csv"]').change(function () {

            $('button[name="btn_import_this_week_csvfile"]').removeClass();
            let btn_state = 'disabled';
            if($(this).val()) {
              btn_state = 'active';
            }
            $('button[name="btn_import_this_week_csvfile"]').addClass('btn btn-outline-secondary ' + btn_state);


            $('button[name="btn_import_last_week_csvfile"]').removeClass();
            $('button[name="btn_import_last_week_csvfile"]').addClass('btn btn-outline-secondary disabled');
            $('input[name="file_last_week_csv"]').val('');

          });


        });
      </script>

    </body>

</html>
