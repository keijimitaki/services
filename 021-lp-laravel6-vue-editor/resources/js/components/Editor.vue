<template>

    <div class="container">

        <section>
            <div class="content_m" style="background-image:url('./news_img/news_bg.jpg')">
            <div id="con_m" class="masonry" style="position: relative; height: 320px; width: 960px;"><a class="lov_block" href="https://line.me/R/ti/p/%40378ntnxx" target="_blank"><div class="m-box masonry-brick" style="width: 290px; height: 290px; background-color: rgb(0, 51, 153); color: rgb(255, 255, 255); position: absolute; top: 0px; left: 0px;">
            <span style="color:#FFFFFF; background-color:#FF0000;">LINE公式</span>
            <p class="con_m3"><img src="/news_img/image1.png" width="60%"></p><p class="con_m1">【LINE公式やってます】<br>
            </p>
            <p class="con_m2">ダイエットアカデミー代表・上野が直接あなたのメッセージにお答えします(^^♪</p>
            </div>
            </a><a class="lov_block" href="http://dietacademy.co.jp/wp/voice/" target="_blank"><div class="m-box masonry-brick" style="width: 290px; height: 290px; background-color: rgb(212, 237, 244); color: rgb(0, 0, 0); position: absolute; top: 0px; left: 320px;">
            <span style="color:#FFFFFF; background-color:#FF0000;">お客様の声</span>
            <p class="con_m3"><img src="/news_img/image2.jpg" width="60%"></p><p class="con_m1">体験者の方からの声はこちら</p>
            <p class="con_m2">　看護師、ヨガ講師、女医、、、<br>
            健康の仕事に携わる方が増えています</p>
            </div>
            </a><a class="lov_block" href="https://dietacademy.co.jp/weblog/myfavoritewords20220419/" target="_blank"><div class="m-box masonry-brick" style="width: 290px; height: 290px; background-color: rgb(255, 255, 204); color: rgb(0, 0, 0); position: absolute; top: 0px; left: 640px;">
            <span style="color:#FFFFFF; background-color:#003399;">ブログ</span>
            <p class="con_m3"><img src="/news_img/image3.jpg" width="50%"></p><p class="con_m1">【ブログ】</p>
            <p class="con_m2">私を支える言葉たち</p>
            </div>
            </a></div>
            <br clear="all">
            </div>  
        </section>


        <div class="row justify-content-center my-1">
            <div class="col text-right">
                お知らせサイズ
            </div>
            <div class="col">
                <select v-model="selected">
                <option disabled value="">Please select one</option>
                <option>A</option>
                <option>B</option>
                <option>C</option>
                </select>
                <span>Selected: {{ selected }}</span>                    
            </div>
        </div>

        <div class="row justify-content-center my-1">
            <div class="col text-right">
                お知らせタイトル
            </div>
            <div class="col">
                <span>Multiline title is:</span>
                <p style="white-space: pre-line;">{{ title }}</p>
                <br>
                <textarea v-model="title" placeholder="add multiple lines"></textarea>                    
            </div>
        </div>

        <div class="row justify-content-center my-1">
            <div class="col text-right">
                お知らせ本文
            </div>
            <div class="col">
                <span>Multiline content is:</span>
                <p style="white-space: pre-line;">{{ content }}</p>
                <br>
                <textarea v-model="content" placeholder="add multiple lines"></textarea>                    
            </div>
        </div>

        <div class="row justify-content-center my-1">
            <div class="col text-right">
                画像
            </div>
            <div class="col">
                <h1>File Upload</h1>
                <p><input type="file" @change="fileSelected" /></p>
                <button v-on:click="uploadImage">アップロード</button>

            </div>
        </div>


    </div>

</template>

<script>
    

export default {

    mounted() {
        console.log('Component mounted.');
                axios.get('/api/news').then(res => {
            console.log(res.data);
        });
    },

    data() {
        return {
            selected: '',
            title: '',
            content: '',
            fileInfo: '',
        }
    },

    methods: {

        uploadImage(event) {

    //   const config = {
    //     headers: {
    //       'content-type': 'multipart/form-data'
    //     }
    //   };
            const formData = new FormData();

            formData.append('file',this.fileInfo);

            console.log(formData);

            axios.post('/fileupload',formData).then(response =>{
                console.log(response);
            });

        },
        
        fileSelected(event){
            this.fileInfo = event.target.files[0]
        },

        update() {

        },


    }
    
    
}

</script>

<style scoped>
@import '/css/editor.css';

</style>