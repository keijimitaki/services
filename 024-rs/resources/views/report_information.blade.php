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

    <!-- Custom styles for this template -->
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
          </ul>

        </div>

      </div>

      <div class="col">


        <div class="row">
          <div class="col-9"> 
            <!-- お知らせ -->
          </div>
          <div class="col-3">
            <div class="d-flex py-1 justify-content-end">
              <div class="col">{{ $user_name }}さん </div>
            </div>
            <div class="d-flex">
              <div class="col">お知らせ
                <span class="infobadge badge rounded-pill bg-danger px-2">3</span>
              </div>
              <div class="col">ログオフ</div>
            </div>
          </div>
        </div>

<!-- Button trigger modal -->
<button type="button" style="display:none;" class="infobutton btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
  Launch demo modal
</button>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">お知らせ内容</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <span class="text-danger">週報が未提出です。<br/>本日〇時までに提出してください！</span>
      </div>
      <div class="modal-body">
        入力していない箇所や誤字脱字があります。<br/>
        差戻しからコメントを確認して<br/>
        再提出をお願いします！　
      </div>
      <!-- <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div> -->
    </div>
  </div>
</div>


      </div>
      
    </div>

  </main>


</div>

    <script src="{{ asset('/assets/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>

    <script type="text/javascript">

      $(function(){

        $('.infobadge').on('click',function() {

          //window.alert('aa');
          $('.infobutton').click();
        });

        $('.infobutton').click();

      });
    </script>

  
    </body>

</html>
