<template>

<div>

<!--
        <section>
            <div class="content_m" style="background-image:url('./news_img/news_bg.jpg')">
            <div id="con_m" class="masonry" style="position: relative; height: 320px; width: 960px;">
                
                <a class="lov_block" href="https://line.me/R/ti/p/%40378ntnxx" target="_blank">
                    <div class="m-box masonry-brick" style="width: 290px; height: 290px; background-color: rgb(0, 51, 153); color: rgb(255, 255, 255); position: absolute; top: 0px; left: 0px;">
                        <span style="color:#FFFFFF; background-color:#FF0000;">LINE公式</span>
                        <p class="con_m3"><img src="/news_img/image1.png" width="60%"></p><p class="con_m1">【LINE公式やってます】<br></p>
                        <p class="con_m2">ダイエットアカデミー代表・上野が直接あなたのメッセージにお答えします(^^♪</p>
                    </div>
                </a>
                <a class="lov_block" href="http://dietacademy.co.jp/wp/voice/" target="_blank">
                    <div class="m-box masonry-brick" style="width: 290px; height: 290px; background-color: rgb(212, 237, 244); color: rgb(0, 0, 0); position: absolute; top: 0px; left: 320px;">
                        <span style="color:#FFFFFF; background-color:#FF0000;">お客様の声</span>
                        <p class="con_m3"><img src="/news_img/image2.jpg" width="60%"></p><p class="con_m1">体験者の方からの声はこちら</p>
                        <p class="con_m2">　看護師、ヨガ講師、女医、、、<br>
                        健康の仕事に携わる方が増えています</p>
                    </div>
                </a>
                <a class="lov_block" href="https://dietacademy.co.jp/weblog/myfavoritewords20220419/" target="_blank">
                    <div class="m-box masonry-brick" style="width: 290px; height: 290px; background-color: rgb(255, 255, 204); color: rgb(0, 0, 0); position: absolute; top: 0px; left: 640px;">
                        <span style="color:#FFFFFF; background-color:#003399;">ブログ</span>
                        <p class="con_m3"><img src="/news_img/image3.jpg" width="50%"></p><p class="con_m1">【ブログ】</p>
                        <p class="con_m2">私を支える言葉たち</p>
                    </div>
                </a>

                <a class="lov_block" href="https://dietacademy.co.jp/weblog/myfavoritewords20220419/" target="_blank">
                    <div class="m-box masonry-brick" style="width: 290px; height: 290px; background-color: rgb(255, 255, 204); color: rgb(0, 0, 0); position: absolute; top: 0px; left: 640px;">
                        <span style="color:#FFFFFF; background-color:#003399;">ブログ</span>
                        <p class="con_m3"><img src="/news_img/image3.jpg" width="50%"></p><p class="con_m1">【ブログ】</p>
                        <p class="con_m2">私を支える言葉たち</p>
                    </div>
                </a>

            </div>
            <br clear="all">
            </div>  
        </section>
        <hr>
-->        
        
        <section>

            <div class="content_m" style="background-image:url('./news_img/news_bg.jpg')">

                <div id="con_m" class="masonry" style="position: relative; height: 320px; width: 960px;">
               
                    <div v-for="(news,index) in newsList" :key="index">
                        <span class="lov_block" href="https://dietacademy.co.jp/weblog/myfavoritewords20220419/" target="_blank">
                            <div class="m-box masonry-brick" v-on:click="openNewsDetail(`${news.image_url}`)" style="margin: 3px 12px; width: 290px; height: 320px; color: rgb(0, 0, 0);" 
                            v-bind:style="{'background-color': news.backGroundColor}" >
                            
                            <!-- <div class="m-box masonry-brick" style="width: 290px; height: 290px; background-color: rgb(255, 255, 204); color: rgb(0, 0, 0); "> -->
                                <button type="button" class="btn btn-sm btn-dark" v-on:click.stop="editNews(`${news.seq}`)" >編集</button>
                                <button type="button" class="btn btn-sm btn-danger" v-on:click.stop="deleteNews(`${news.seq}`)">削除</button>
                                <span style="color:#FFFFFF; background-color:#003399;">{{news.title}}</span>
                                <!-- <p class="con_m3"><img :src="`/news_img/${news.image}`" width="50%"></p> -->
                                <p class="con_m3"><img :src="`${news.image_url}`" width="50%"></p>
                                
                                <p class="con_m1">{{news.title}}</p>
                                <p class="con_m2" v-for="message in news.messeges" :key="message">
                                    {{message}}
                                </p>
                            </div>
                        </span>
                        <!-- <button type="button" class="btn btn-sm btn-dark" style="position:absolute; top:9px; margin-left:-290px;">編集</button> -->
                    </div>
                
                </div>
                <br clear="all">



                <input v-model="editingSeq" placeholder="current">

            </div> 

                <div class="d-flex" >
                    <div style="width:300px; padding:0 0 10px 0">
                        <button type="button" class="btn btn-sm btn-primary">新規追加</button>
                    </div>
                </div>



        </section>

<form @submit.prevent="submitForm">
    <div class="container">

        <div class="row justify-content-center my-1">
            <div class="col text-right">
                お知らせタイトル
            </div>
            <div class="col">
                <div class="row">
                    <textarea v-model="title" placeholder="add multiple lines"></textarea>
                </div>
                <div class="row">
                    <input v-model="titleColor" placeholder="edit me">
                </div>
            </div>
        </div>

        <div class="row justify-content-center my-1">
            <div class="col text-right">
                お知らせ本文
            </div>
            <div class="col">
                <div class="row">
                    <textarea v-model="message" placeholder="add multiple lines"></textarea>                    
                </div>
            </div>
        </div>

        <div class="row justify-content-center my-1">
            <div class="col text-right">
                背景色
            </div>
            <div class="col">
                <div class="row">
                    <input v-model="backGroundColor" placeholder="背景色を選択してください">
                </div>
                <div class="row">
                    <span v-for="(pattern,index) in colorPattern" :key="index" class="m-1" 
                        style="width:80px; hight:50px; background-color:#11ffee;" 
                        v-bind:style="{'background-color': pattern}" 
                        v-on:click="selectBackGroundColor(pattern)">
                        {{pattern}}
                    </span>
                </div>
            </div>
        </div>

        <div class="row justify-content-center my-1">
            <div class="col text-right">
                リンク
            </div>
            <div class="col">
                <div class="row">
                    <input type="checkbox" id="checkbox" v-model="linkCheck">
                    <label for="checkbox">{{ linkCheck }}</label>
                </div>
                <div class="row">
                    <input v-model="linkUrl" placeholder="リンクURL [例] https://www.google.co.jp">
                    <p>Message is: {{ linkUrl }}</p>
                </div>
            </div>
        </div>


        <div class="row justify-content-center my-1">
            <div class="col text-right">
                画像指定
            </div>
            <div class="col">
                <div class="row">
                    <div class="col">
                        <p><input type="file" ref="preview" @change="fileSelected" /></p>
                    </div>
                    <div class="col" v-if="previewImage">
                        <img :src="previewImage" style="width:100px; hight:100px;" />
                    </div>

                    <button v-on:click="uploadImage">アップロード</button>
                    <div class="col">
                        <select v-model="imageSize">
                            <option disabled value="">画像サイズ</option>
                            <option>A</option>
                            <option>B</option>
                            <option>C</option>
                        </select>
                    </div>
                </div>

            </div>
        </div>

        <div class="row justify-content-center my-1">
            <div class="row">
                <img v-show="editingNewsImageExists" :src="`${editingNewsImage}`" width="50%"/>
            </div>

        </div>

        <div class="row justify-content-center my-1">
            <input type="checkbox" id="checkbox" v-model="confirmCheck">
            <label for="checkbox">{{ confirmCheck }}</label>
            
            <!-- <button type="button" v-on:click="updateNews" class="btn btn-lg btn-primary mx-2" >登録</button> -->
            <button type="submit" class="btn btn-lg btn-primary mx-2" >登録</button>
            <button type="button" class="btn btn-lg btn-primary mx-2" >キャンセル</button>



        </div>
    </div>

</form>


</div>


</template>

<script>
    

export default {

    async mounted() {
        console.log('Component mounted. env',process.env.MIX_APP_ENV);

        await axios.get('./api/news').then(res => {
            

            if(!res.data.news){
                return;
            }
            res.data.news.forEach(element =>{
                //const news = { seq: element.seq, image: element.image}
                let row = element;

                row['image_url'] = `../public/news_img/${element.image}`;
                if(process.env.MIX_APP_ENV == 'local'){
                    row['image_url'] = `../news_img/${element.image}`;
                }

                let messeges = '';
                if( element.message && element.message.length>0) {
                    messeges = element.message.split('\n');

                } else {
                    messeges = element.message;
                }
                row['messeges'] = messeges;

                this.newsList.push(row);
            })

        });
            

       console.log('newsList',this.newsList);

    },

    data() {
        return {
            newsList:[],
            editingSeq: '',
            selected: '',
            title: '',
            titleColor: '',
            message: '',
            backGroundColor: '',
            linkCheck: false,
            linkUrl: '',
            imageFileName: '',
            imageSize: '',
            fileInfo: '',
            confirmCheck: false,
            editingNewsImageExists: false,
            editingNewsImage: '',
            previewImage: '',
            colorPattern: ['#c5e1a5','#f8ffd7','#ffe082','#eeeeee','#b39ddb'],


        }
    },

    methods: {
        async submitForm() {

            const formData = new FormData();
            
            formData.append('editingSeq',this.editingSeq);
            formData.append('title',this.title);
            formData.append('titleColor',this.titleColor);
            formData.append('message',this.message);
            formData.append('backGroundColor',this.backGroundColor);
            formData.append('linkUrl',this.linkUrl);
            formData.append('imageFileName',this.imageFileName);
            formData.append('imageSize',this.imageSize);
            if(this.fileInfo){
                formData.append('file',this.fileInfo);
                console.log('file exists');
            }

            //更新
            if(this.editingSeq){
                
                await axios.post('/updateNews',formData).then(res =>{
                    console.log(res);

                    this.newsList = [];

                    res.data.news.forEach(element =>{
                        let row = element;

                        row['image_url'] = `../public/news_img/${element.image}`;
                        if(process.env.MIX_APP_ENV == 'local'){
                            row['image_url'] = `../news_img/${element.image}`;
                        }

                        let messeges = '';
                        if( element.message && element.message.length>0) {
                            messeges = element.message.split('\n');

                        } else {
                            messeges = element.message;
                        }
                        row['messeges'] = messeges;

                        this.newsList.push(row);


                    });

                    this.formReset();

                });


            } else {

                await axios.post('./addNews',formData).then(res => {
                    console.log(res.data.news);

                    this.newsList = [];

                    res.data.news.forEach(element =>{
                        let row = element;

                        row['image_url'] = `../public/news_img/${element.image}`;
                        if(process.env.MIX_APP_ENV == 'local'){
                            row['image_url'] = `../news_img/${element.image}`;
                        }

                        let messeges = '';
                        if( element.message && element.message.length>0) {
                            messeges = element.message.split('\n');

                        } else {
                            messeges = element.message;
                        }
                        row['messeges'] = messeges;

                        this.newsList.push(row);

                    });

                    this.editingSeq = '';


                });

            }


        },


        openNewsDetail(url) {
            window.alert(url);

        },

        async deleteNews(seq) {

            if(confirm('削除してもいいですか')){

                this.editingSeq = seq;

                const formData = new FormData();
                formData.append('editingSeq',this.editingSeq);

                await axios.post('/deleteNews',formData).then(res =>{
                    console.log(res);
                    
                    this.newsList = [];
                    res.data.news.forEach(element =>{
                        let row = element;

                        row['image_url'] = `../public/news_img/${element.image}`;
                        if(process.env.MIX_APP_ENV == 'local'){
                            row['image_url'] = `../news_img/${element.image}`;
                        }

                        let messeges = '';
                        if( element.message && element.message.length>0) {
                            messeges = element.message.split('\n');

                        } else {
                            messeges = element.message;
                        }
                        row['messeges'] = messeges;

                        this.newsList.push(row);
                    })
                                        
                });

            }
            
        },


        editNews(seq) {
            //window.alert(seq);
            this.editingSeq = seq;

            // let editingNews = {};
            console.log('seq == ',seq );

            const editingNews = this.newsList.filter( function( value, index, array ) {
 
                if( seq ==  value['seq'] ) {
                    return true;
                } else {
                    return false;
                }
                
            })
            
            console.log('editingSeq:',editingNews);
            this.title = editingNews[0]['title'];
            this.titleColor = editingNews[0]['titleColor'];
            this.message = editingNews[0]['message'];
            this.backGroundColor = editingNews[0]['backGroundColor'];
            this.linkUrl = editingNews[0]['linkUrl'];
            this.imageFileName = editingNews[0]['imageFileName'];

            this.editingNewsImage = `../public/news_img/${editingNews[0]['image']}`;
            if(process.env.MIX_APP_ENV == 'local'){
                this.editingNewsImage = `../news_img/${editingNews[0]['image']}`;
            }
            this.editingNewsImageExists = true;
            
        },


        async addNews(){
            await axios.post('./add',{message:this.message}).then(res => {
                console.log(res.data.news);

                res.data.news.forEach(element =>{
                    //const news = { seq: element.seq, image: element.image}
                    this.newsList.push(element);

                })
            });

        },

        uploadImage(event) {

            const formData = new FormData();

            formData.append('file',this.fileInfo);

            console.log(formData);

            axios.post('/fileupload',formData).then(response =>{
                console.log(response);
            });

        },
        
        fileSelected(event){
            this.fileInfo = event.target.files[0];
           
            //this.previewImage = event.target.files[0];
            const file = this.$refs.preview.files[0];

console.log(file);

            this.previewImage = URL.createObjectURL(file);
            
            //this.$refs.preview.value = "";


        },


        async formReset() {
            this.editingSeq = '';

        },

        selectBackGroundColor(color){
            this.backGroundColor = color;

        }

    }
    
    
}

</script>

<style scoped>
@import '/css/editor.css';

</style>