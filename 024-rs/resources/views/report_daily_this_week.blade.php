<table class="table">
  <thead>
    <tr>
      <th scope="col" style="width:130px;">日付</th>
      <th scope="col" style="width:200px;">業務名</th>
      <th scope="col" style="width:400px">内容</th>
      <th scope="col" style="width:60px;">工数</th>
    </tr>
  </thead>
  
    <tbody>
    @foreach ($reports_this_week as $report)
      <tr>
        <input type="hidden" name="this_week_year[]" value="{{$report->year}}" ></input>
        <input type="hidden" name="this_week_month[]" value="{{$report->month}}" ></input>
        <input type="hidden" name="this_week_day[]" value="{{$report->day}}" ></input>
        <td>{{$report->year}}年{{$report->month}}月{{$report->day}}日 {{$report->dayofweek}}</td>
        <td><div class="col-sm-10">
          <input type="text" name="this_week_task_name[]" class="form-control-plaintext mt-0 pt-0" value="{{$report->task_name}}" placeholder="入力してください"></input>
          </div>
        </td>
        <td>
          <div class="col-sm-10">
          <textarea class="form-control" name="this_week_task_detail[]" placeholder="入力してください">{{$report->task_detail}}</textarea >
          </div>
        </td>
        <td  style="">
          
        <div class="row">
          <div class="col">
            <input type="text" name="this_week_m[]" class="form-control-plaintext mt-0 pt-0" value="{{$report->m}}" placeholder="0"></input>
          </div>
          <div class="col">
            <span>M</span>
          </div>
        </td>
      </tr>
    @endforeach
    </tbody>

</table>

<div class="row">
  <div class="col"><textarea cols="60" rows="6" placeholder="状況報告"></textarea></div>
  <div class="col d-flex">
    <div class="col m-3">
      <div class="row">予定工数(M)</div>
      <div class="row"><span class="card" style="width:120px;height:90px;">240</span></div>
    </div>
    <div class="col m-3">
      <div class="row">実績工数(M)</div>
      <div class="row"><span class="card" style="width:120px;height:90px;">240</span></div>
    </div>
    <div class="col m-3">
      <div class="row">負荷率(%)</div>
      <div class="row"><span class="card" style="width:120px;height:90px;">100</span></div>
    </div>
  </div>
  <div class="col">
    <div class="row py-1"><button type="submit" class="btn btn-outline-secondary">保存</button></div>
    <div class="row py-1"><button type="button" class="btn btn-outline-secondary disabled">PDF出力</button></div>
    <div class="row py-1"><button type="button" class="btn btn-outline-secondary disabled">読込（日報データ）</button></div>
  </div>
</div>

