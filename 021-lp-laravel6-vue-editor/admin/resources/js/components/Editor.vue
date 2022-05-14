<template>

<div>

    <div class="d-flex mx-md-5 px-md-4">
        <div class="col pb-3 text-right">
            <button type="button" class="btn btn-sm btn-primary"
                v-on:click="newEntry">ニュース追加</button>
        </div>
        <div class="col-7 col-md-9">
            <input type="hidden" v-model="editingId" placeholder="current">
        </div> 

    </div>


        <section>

            <div class="content_m pb-4" style="display:flex; flex-wrap: wrap; justify-content: center; background-image:url('./news_img/news_bg.jpg')">

                <div id="con_m" class="masonry" style="position: relative; ">
                <!-- <div id="con_m" class="masonry" style="position: relative; height: 320px; width: 960px;"> -->
               
                    <div v-for="(news,index) in newsList" :key="index">
                        <span class="lov_block" :href="`${news.linkUrl}`" target="_blank">
                            <div class="m-box masonry-brick" v-on:click="openNewsDetail(`${news.linkUrl}`)" style="margin: 8px 15px; width: 290px; height: 320px; color: rgb(0, 0, 0);" 
                            v-bind:style="{'background-color': news.backgroundColor}" >
                            
                            <!-- <div class="m-box masonry-brick" style="width: 290px; height: 290px; background-color: rgb(255, 255, 204); color: rgb(0, 0, 0); "> -->
                                <div style="display:flex;justify-content: flex-end;">
                                <button type="button" class="btn btn-sm btn-primary" v-on:click.stop="editNews(`${news.id}`)" >編集</button>
                                <button type="button" class="btn btn-sm btn-danger" v-on:click.stop="deleteNews(`${news.id}`)">削除</button>
                                <span style="flex:1;"><span>表示順：{{news.displayOrder}}</span></span>
                                <span v-bind:style="{'color':news.tagColor, 'background-color':news.tagBackgroundColor, 'font-size':'12px' }">{{news.tag}}</span>
                                </div>
                                <!-- <p class="con_m3"><img :src="`/news_img/${news.image}`" width="50%"></p> -->
                                <p class="con_m3"><img :src="`${news.image_url}`" width="50%"></p>
                                
                                <p class="con_m1" v-bind:style="{'color':news.titleColor, 'font-size':'20px'}">{{news.title}}</p>
                                <p class="con_m2" v-for="message in news.messeges" :key="message"
                                    v-bind:style="{'color':news.messageColor, 'font-size':'16px', 'line-height':'1.2em', 'margin':'0' }">
                                    {{message}}
                                </p>
                            </div>
                        </span>
                        <!-- <button type="button" class="btn btn-sm btn-dark" style="position:absolute; top:9px; margin-left:-290px;">編集</button> -->
                    </div>
                
                </div>
                <br clear="all">

            </div> 

        </section>

    <div class="container">

        <div class="row justify-content-center py-3">
            <div v-if="editingId!=''" class="col mx-4 px-2 alert" role="alert">
                <span v-if="editingId=='0'">
                    <span class="badge rounded-pill bg-warning text-dark">ニュース追加</span>
                    <span class="mx-3">新しいニュースを入力してください</span>
                </span>
                <span v-if="editingId!=='0'">
                    <span class="badge rounded-pill bg-warning text-dark">ニュース編集</span>
                    <span class="mx-3">{{this.title}}&nbsp;の編集内容を入力してください</span>
                </span>
            </div>
            <div v-if="notification!=''" class="col mx-4 px-2 alert alert-success" role="alert">
                <span>
                    <span class="mx-3">{{this.notification}}</span>
                </span>
            </div>
        </div>
    </div>

<form @submit.prevent="submitForm" v-if="editingId!=''">
    <div class="container">

        <div class="row justify-content-center my-1">
            <div class="col text-right">
                お知らせ表示順
            </div>
            <div class="col">
                <div class="row">
                    <input type="number" style="width:42px" v-model="displayOrder" placeholder="" />
                </div>
            </div>
        </div>

        <div class="row justify-content-center my-1">
            <div class="col text-right">
                お知らせタイトル
            </div>
            <div class="col">
                <div class="row">
                    <input style="width:326px" v-model="title" placeholder="タイトルを入力してください" />
                    <span class="rounded ps-2" v-bind:style="{
                        'width':'200px',
                        'margin':'0px 12px ',
                        'color': this.titleColor, 
                        'background-color':this.backgroundColor,
                        }">
                        {{ this.title }}
                    </span>
                </div>
            </div>
        </div>

        <div class="row justify-content-center my-1">
            <div class="col text-right">
                タイトル文字色
            </div>
            <div class="col">
                <div class="row">
                    <input type="hidden" v-model="titleColor" />
                </div>
                <div class="row">
                    <span v-for="(pattern,index) in titleColorPattern" :key="index" class="m-1 badge" 
                        style="width:80px; hight:50px; background-color:#11ffee;" 
                        v-bind:style="{'background-color': pattern}" 
                        v-on:click="selectTitleColor(pattern)">
                        {{pattern}}
                    </span>
                </div>

            </div>
        </div>


        <div class="row justify-content-center my-1">
            <div class="col text-right">
                お知らせ本文
            </div>
            <div class="col">
                <div class="row">
                    <textarea v-model="message" cols="38" placeholder="お知らせ本文を入力してください"></textarea>                    
                    <span class="rounded ps-2" v-bind:style="{
                        'width':'200px',
                        'margin':'0px 12px ',
                        'color':this.messageColor,
                        'background-color':this.backgroundColor,
                        }">
                        {{ this.message }}
                    </span>
                </div>
            </div>            
        </div>

        <div class="row justify-content-center my-1">
            <div class="col text-right">
                本文文字色
            </div>
            <div class="col">
                <div class="row">
                    <input type="hidden" v-model="messageColor" />
                </div>
                <div class="row">
                    <span v-for="(pattern,index) in messageColorPattern" :key="index" class="m-1 badge" 
                        style="width:80px; hight:50px; background-color:#11ffee;" 
                        v-bind:style="{'background-color': pattern}" 
                        v-on:click="selectMessageColor(pattern)">
                        {{pattern}}
                    </span>
                </div>
            </div>
        </div>

        <div class="row justify-content-center my-1">
            <div class="col text-right">
                本文背景色
            </div>
            <div class="col">
                <div class="row">
                    <input type="hidden" v-model="backgroundColor" />
                </div>
                <div class="row">
                    <span v-for="(pattern,index) in colorPattern" :key="index" class="m-1 badge" 
                        style="width:80px; hight:50px; background-color:#11ffee;" 
                        v-bind:style="{'background-color': pattern}" 
                        v-on:click="selectBackgroundColor(pattern)">
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
                    <input v-model="linkUrl" style="width:326px" placeholder="リンクURL [例] https://www.google.co.jp">
                </div>
                <div class="row">
                    <a :href="`${this.linkUrl}`" target="_blank" rel="noopener noreferrer">
                        {{this.linkUrl}}
                    </a>
                </div>
            </div>
        </div>


        <div class="row justify-content-center my-1">
            <div class="col text-right">
                タグ
            </div>
            <div class="col">
                <div class="row">
                    <input style="width:326px" v-model="tag" placeholder="タグを入力してください">
                    <span class="rounded ps-2" v-bind:style="{
                        'width':'200px',
                        'margin':'0px 12px ',
                        'color':this.tagColor,
                        'background-color':this.tagBackgroundColor,
                        }">
                        {{ this.tag }}
                    </span>                    
                </div>
                <div class="row">
                    ※「2016/08/08」「セミナー」「おしらせ」等、自由に設定。
                </div>
            </div>
        </div>

        <div class="row justify-content-center my-1">
            <div class="col text-right">
                タグ文字色
            </div>
            <div class="col">
                <div class="row">
                    <input type="hidden" v-model="tagColor" placeholder="#ffffff">
                </div>
                <div class="row">
                    <span v-for="(pattern,index) in tagColorPattern" :key="index" class="m-1 badge" 
                        style="width:80px; hight:50px; background-color:#11ffee;" 
                        v-bind:style="{'background-color': pattern}" 
                        v-on:click="selectTagColor(pattern)">
                        {{pattern}}
                    </span>
                </div>                
            </div>
        </div>

        <div class="row justify-content-center my-1">
            <div class="col text-right">
                タグ背景色
            </div>
            <div class="col">
                <div class="row">
                    <input type="hidden" v-model="tagBackgroundColor" placeholder="#ff0000">
                </div>
                <div class="row">
                    <span v-for="(pattern,index) in tagBackgroundColorPattern" :key="index" class="m-1 badge" 
                        style="width:80px; hight:50px;" 
                        v-bind:style="{'background-color': pattern}" 
                        v-on:click="selectTagBackgroundColor(pattern)">
                        {{pattern}}
                    </span>
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
                </div>

                <div class="row">
                    <div class="col" v-if="previewImage!==''">
                        <img :src="previewImage" style="width:120px; hight:120px;" />
                    </div>
                    <div class="col" v-if="editingNewsImageExists!==false">
                        <img :src="`${editingNewsImage}`" style="width:120px; hight:120px;" />
                    </div>

                    <!-- 
                        <button v-on:click="uploadImage">アップロード</button>
                    <div class="col">
                        <select v-model="imageSize">
                            <option disabled value="">画像サイズ</option>
                            <option>A</option>
                            <option>B</option>
                            <option>C</option>
                        </select>
                    </div>
                     -->

                </div>

            </div>
        </div>


        <hr/>
        <div class="row justify-content-center my-1">
            <div class="col text-right">
                内容確認
            </div>

            <div class="col">
                <div class="row">
                    <input class="mx-2 mt-1" type="checkbox" id="checkbox" v-model="confirmCheck">
                    <label for="checkbox">
                        <span v-if="confirmCheck==false">未確認</span>
                        <span v-else>確認済み</span>
                    </label>
                </div>
                <div class="row">
                
                <button type="submit" class="btn btn-lg btn-primary mx-2" >登録</button>
                <button type="button" v-on:click="cancelEdit" class="btn btn-lg btn-secondary mx-2" >キャンセル</button>
                </div>
            </div>


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

                row['image_url'] = '';

                if(element.image){
                    row['image_url'] = `../public/news_img/${element.image}`;
                    if(process.env.MIX_APP_ENV == 'local'){
                        row['image_url'] = `../news_img/${element.image}`;
                    }

                }

                if(element.linkUrl === null){
                   row['linkUrl'] = ``;
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

            //表示順で並び替え
            let sortedNews = this.newsList.sort(function(a, b) {
                return (a.displayOrder < b.displayOrder) ? -1 : 1;
            });
            this.newsList = sortedNews;

        });
            

       console.log('newsList',this.newsList);

    },

    data() {
        return {
            newsList:[],
            id: '',
            displayOrder: 1,
            editingId: '',
            title: '',
            titleColor: '#000000',
            titleColorPattern: ['#000000','#ffffff','#ff0000','#003399','#009900'],
            message: '',
            messageColor: '#000000',
            messageColorPattern: ['#000000','#ffffff','#ff0000','#003399','#009900'],
            backgroundColor: '#ffffff',
            linkUrl: '',
            imageFileName: '',
            imageSize: '',
            fileInfo: '',
            confirmCheck: false,
            editingNewsImageExists: false,
            editingNewsImage: '',
            previewImage: '',
            colorPattern: ['#ff0000','#003399','#ffffff','#ffffcc','#D4EDF4'],
            tag: '',
            tagColor: '#000000',
            tagColorPattern: ['#000000','#ffffff','#ff0000','#003399','#009900'],
            tagBackgroundColor: '#ffffff',
            tagBackgroundColorPattern: ['#000000','#ffffff','#ff0000','#003399','#009900'],
            notification: '',

        }
    },

    methods: {
        async submitForm() {

            if(!this.editingId){
                return;
            }

            if(!this.confirmCheck){
                window.alert('入力内容を確認して、内容確認にチェックをつけてください');
                return;
            }

            const formData = new FormData();
            
            formData.append('editingId',this.editingId);
            formData.append('title',this.title);
            formData.append('displayOrder',this.displayOrder);
            formData.append('titleColor',this.titleColor);
            formData.append('message',this.message);
            formData.append('messageColor',this.messageColor);
            formData.append('backgroundColor',this.backgroundColor);
            formData.append('linkUrl',this.linkUrl);
            formData.append('imageFileName',this.imageFileName);
            formData.append('imageSize',this.imageSize);
            if(this.fileInfo){
                formData.append('file',this.fileInfo);
                // console.log('file exists');
            }
            
            formData.append('tag',this.tag);
            formData.append('tagColor',this.tagColor);
            formData.append('tagBackgroundColor',this.tagBackgroundColor);
            
            if(this.editingId === '0') {

                await axios.post('./addNews',formData).then(res => {
                    console.log(res.data.news);

                    this.newsList = [];

                    res.data.news.forEach(element =>{
                        let row = element;

                        row['image_url'] = '';
                        if(element.image){
                            row['image_url'] = `../public/news_img/${element.image}`;
                            if(process.env.MIX_APP_ENV == 'local'){
                                row['image_url'] = `../news_img/${element.image}`;
                            }
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

                    this.notification = 'ニュースを追加しました';
                    this.editingId = '';
                    

                });

            //更新
            } else {

                await axios.post('./updateNews',formData).then(res =>{
                    console.log(res);

                    this.newsList = [];

                    res.data.news.forEach(element =>{
                        let row = element;

                        row['image_url'] = '';
                        if(element.image){
                            row['image_url'] = `../public/news_img/${element.image}`;
                            if(process.env.MIX_APP_ENV == 'local'){
                                row['image_url'] = `../news_img/${element.image}`;
                            }
                        }

                        if(element.linkUrl === null){
                        row['linkUrl'] = ``;
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

                    this.notification = 'ニュースを更新しました';
                    this.editingId = '';
                    this.dataReset();


                });

            }

            //表示順で並び替え
            let sortedNews = this.newsList.sort(function(a, b) {
                return (a.displayOrder < b.displayOrder) ? -1 : 1;
            });
            this.newsList = sortedNews;

        },


        async deleteNews(id) {

            if(confirm('削除してもいいですか')){

                this.editingId = id;

                const formData = new FormData();
                formData.append('editingId',this.editingId);

                await axios.post('./deleteNews',formData).then(res =>{
                    
                    this.newsList = [];
                    res.data.news.forEach(element =>{
                        let row = element;

                        row['image_url'] = '';

                        if(element.image){
                            row['image_url'] = `../public/news_img/${element.image}`;
                            if(process.env.MIX_APP_ENV == 'local'){
                                row['image_url'] = `../news_img/${element.image}`;
                            }

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

                    this.notification = 'ニュースを削除しました';
                    this.editingId = '';
                    this.dataReset();
                                        
                });

            }
            
        },


        newEntry() {
            this.dataReset();
            this.editingId = '0';
            this.notification = '';
        },

        editNews(id) {
            this.dataReset();
            this.editingId = id;
            this.notification = '';

            const editingNews = this.newsList.filter( function( value, index, array ) {
 
                if( id ===  value['id'] ) {
                    return true;
                    
                } else {
                    return false;
                }
                
            })
            
            // console.log('editingNews:',editingNews);
            this.id = editingNews[0]['id'];
            this.displayOrder = editingNews[0]['displayOrder'];
            this.title = editingNews[0]['title'];
            this.titleColor = editingNews[0]['titleColor'];
            this.message = editingNews[0]['message'];
            this.messageColor = editingNews[0]['messageColor'];
            this.backgroundColor = editingNews[0]['backgroundColor'];
            this.linkUrl = editingNews[0]['linkUrl'];
            this.imageFileName = editingNews[0]['imageFileName'];

            this.editingNewsImage = `../public/news_img/${editingNews[0]['image']}`;
            if(process.env.MIX_APP_ENV == 'local'){
                this.editingNewsImage = `../news_img/${editingNews[0]['image']}`;
            }
            this.editingNewsImageExists = true;
            
            this.tag = editingNews[0]['tag'];
            this.tagColor = editingNews[0]['tagColor'];
            this.tagBackgroundColor = editingNews[0]['tagBackgroundColor'];


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
            this.previewImage = URL.createObjectURL(file);
            
            this.editingNewsImageExists = false;

        },


        cancelEdit(){

            const cancel = confirm('入力内容を破棄しますか');
            if(cancel) {
                this.editingId = '';
                
            }

        },

        selectTitleColor(color){
            this.titleColor = color;
        },

        selectMessageColor(color){
            this.messageColor = color;
        },

        selectBackgroundColor(color){
            this.backgroundColor = color;
        },

        selectTagColor(color){
            this.tagColor = color;
        },

        selectTagBackgroundColor(color){
            this.tagBackgroundColor = color;
        },

        openNewsDetail(url) {
            if(url){
                window.open(url);
            }
        },


        dataReset() {

            this.id = '';
            this.displayOrder = 1;
            this.editingId = '';
            this.title = '';
            this.titleColor = '#000000';
            this.title = '';
            this.message = '';
            this.messageColor = '#000000';
            this.backgroundColor = '#ffffff';
            this.linkUrl = '';
            this.tag = '';
            this.tagColor = '#000000';
            this.tagBackgroundColor = '#ffffff';
            this.imageFileName = '';
            this.fileInfo = '';
            this.previewImage = '';
            this.editingNewsImageExists = false;
            this.editingNewsImage = '';
            this.confirmCheck = false;


        },

    }
    
    
}

</script>

<style scoped>

</style>