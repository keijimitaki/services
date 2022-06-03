<table class="table">
  <thead>
    <tr>
      <th scope="col" style="width:120px;">日付</th>
      <th scope="col" style="width:280px;">業務名</th>
      <th scope="col" style="width:600px">内容</th>
      <th scope="col" style="width:60px;">工数</th>
    </tr>
  </thead>
  
    <tbody>
    @foreach ($reports_last_week as $report)
      <tr>
        <input type="hidden" name="last_week_year[]" value="{{$report->year}}" ></input>
        <input type="hidden" name="last_week_month[]" value="{{$report->month}}" ></input>
        <input type="hidden" name="last_week_day[]" value="{{$report->day}}" ></input>
        <td>{{$report->year}}年{{$report->month}}月{{$report->day}}日 {{$report->dayofweek}}</td>
        <td><div class="col">
          <input type="text" name="last_week_task_name[]" 
            class="form-control-plaintext mt-0 pt-0 border border-secondary" value="{{$report->task_name}}" placeholder="入力してください"></input>
          </div>
        </td>
        <td>
          <div class="col">
          <textarea class="form-control border border-secondary" name="last_week_task_detail[]"  placeholder="入力してください">{{$report->task_detail}}</textarea >
          </div>
        </td>
        <td>          
          <div class="d-flex">
            <div class="col">
                <input type="text" name="last_week_m[]" 
                class="form-control-plaintext mt-0 pt-0 border border-secondary" value="{{$report->m}}" placeholder="0"></input>
            </div>
            <div class="col">
              <span>M</span>
            </div>
          </div>
        </td>
      </tr>
    @endforeach
    </tbody>

</table>

<div class="row">
  <div class="col-5"><textarea cols="65" rows="6" placeholder="状況報告"></textarea></div>
  <div class="col-5 d-flex d-flex justify-content-between">
    <div class="col m-3">
      <div class="row">先週の予定工数(M)</div>
      <div class="row ">
        <input name="plan_m" class="border border-secondary" 
          style="padding:4px 0px 12px 0px; width:120px; line-height:68px;font-size:32px;text-align: center;"></input>
      </div>
    </div>
    <div class="col m-3">
      <div class="row">先週の実績工数(M)</div>
      <div class="row">
        <span class="card" style="padding:4px 0px 12px 0px; width:120px; line-height:68px;font-size:32px;text-align: center;">
          240</span>
      </div>
    </div>
    <div class="col m-3">
      <div class="row">先週の負荷率(%)</div>
      <div class="row">
        <span class="card" style="padding:4px 0px 12px 0px; width:120px; line-height:68px;font-size:32px;text-align: center;">
          100</span>
      </div>
    </div>
  </div>
  <div class="col">
    <div class="row py-1"><button type="submit" class="btn btn-outline-secondary">保存</button></div>
    <div class="row py-1"><button type="button" class="btn btn-outline-secondary disabled">PDF出力</button></div>
    <div class="row py-1"><button type="submit" formaction="{{ url('/report_daily/csvimport')}}" name="btn_import_last_week_csvfile" class="btn btn-outline-secondary disabled">読込（日報データ）</button></div>
    <div class="row py-1"><input type="file" name="file_last_week_csv" accept=".csv, .CSV"></input></div>
  </div>
</div>

