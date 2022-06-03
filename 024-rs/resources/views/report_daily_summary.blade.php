<table class="table">
  <thead>
    <tr>
      <th scope="col" style="width:280px;">機種名/業務項目</th>
      <th scope="col" style="width:720px;">内容</th>
    </tr>
  </thead>
  
    <tbody>
      <tr>
        <td>
          <div class="col">
            <textarea class="form-control border border-secondary" placeholder="入力してください"
             cols="180" rows="18"></textarea >
          </div>
        </td>
        <td>
          <div class="col">
            <textarea class="form-control border border-secondary" placeholder="入力してください"
             cols="180" rows="18"></textarea >
          </div>
        </td>
      </tr>
    </tbody>

</table>

<div class="row">
  <div class="col-10"><textarea disabled cols="145" rows="6" placeholder="上長補足コメント"></textarea></div>
  <div class="col-2">
    <div class="row py-1"><button type="submit" formaction="{{ url('/report_daily/pdfexport')}}" class="btn btn-outline-secondary">PDF出力</button></div>
    <div class="row py-1"><button type="submit" class="btn btn-outline-secondary">提出</button></div>
  </div>
</div>

