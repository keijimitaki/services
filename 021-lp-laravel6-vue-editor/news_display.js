
window.addEventListener('load', function() {

  //fetch(`/api/news`)
  fetch(`http://127.0.0.1:8000/api/news`)
  .then(response => {
    //console.log('response=>',response.json());
    return response.json();
    
  }).then(data => {
    //console.log(data.news);
    data.news.forEach((element, index) => {
      console.log(element);

      let messeges = [];
      if( element.message && element.message.length>0) {
          messeges = element.message.split('\n');

      } else {
          messeges[0] = element.message;
      }

      let a_start = `<a class="lov_block">`;
      if(element.linkUrl) {
        a_start = `<a class="lov_block" href="${element.linkUrl}" target="_blank">`;
      }
      
      const a_end = `</a>`;
      const div_m_box_start = `<div class="m-box masonry-brick" style="margin:8px 5px; width: 290px; height: 300px; 
      background-color:${element.backgroundColor}; color: rgb(0, 0, 0);" >`;
      const div_m_box_end = `</div>`;
      const div_tag = `<div style="display:flex;justify-content: flex-end; margin:-10px -10px 0px 0px;">
      <span style="color:${element.tagColor} ; background-color:${element.tagBackgroundColor}; font-size:12px; padding:0px 8px;">${element.tag}</span>
      </div>`;
      //const label = `<span style="color:${element.tagColor}; background-color:${element.tagBackgroundColor};">${element.title}</span>`;
      const title = `<p class="con_m3"><img src="./admin/public/news_img/${element.image}" width="50%"></p>`;
      const l1 = `<p class="con_m1" style="color:${element.titleColor}; font-size:20px; padding-bottom:10px;" >${element.title}</p>`;

      let l2 = '';
      messeges.forEach((msg, index) => {

        l2 = l2 + `<p class="con_m2" style="color:${element.messageColor}; font-size:16px; margin:0;" >${msg}</p>`;
        // console.log(`${index}: ${msg}`);

      });

      const html = a_start + div_m_box_start + div_tag + title + l1 + l2 + div_m_box_end + a_end;

      //console.log(html);
      $('#news_display .content_m .masonry').append(html);

      
    });

  }).catch(error => {
    console.error('通信に失敗しました', error);
  })






});
