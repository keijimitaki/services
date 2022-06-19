
// ページが読み込まれたらすぐに動かしたい場合の記述
$(window).on('load',function(){
	$("#splash-logo").delay(1200).fadeOut('slow');//ロゴを1.2秒でフェードアウトする記述

	//=====ここからローディングエリア（splashエリア）を1.5秒でフェードアウトした後に動かしたいJSをまとめる
	$("#splash").delay(1500).fadeOut('slow',function(){//ローディングエリア（splashエリア）を1.5秒でフェードアウトする記述
			$('body').addClass('appear');//フェードアウト後bodyにappearクラス付与
	});
	//=====ここまでローディングエリア（splashエリア）を1.5秒でフェードアウトした後に動かしたいJSをまとめる
	
 //=====ここから背景が伸びた後に動かしたいJSをまとめたい場合は
	$('.splashbg').on('animationend', function() {    
	});

});// ここまでページが読み込まれたらすぐに動かしたい場合の記述



function SmoothTextAnime() {
	$('.smoothTextTrigger').each(function(){ //smoothTextTriggerというクラス名が
		var elemPos = $(this).offset().top-50;//要素より、50px上の
		var scroll = $(window).scrollTop();
		var windowHeight = $(window).height();
		if (scroll >= elemPos - windowHeight){
		$(this).addClass('smoothTextAppear');// 画面内に入ったらsmoothTextAppearというクラス名を追記
		}else{
		$(this).removeClass('smoothTextAppear');// 画面外に出たらsmoothTextAppearというクラス名を外す
		}
		});	
}


// #page-topをクリックした際の設定
$('#page-top').click(function () {
    $('body,html').animate({
        scrollTop: 0//ページトップまでスクロール
    }, 500);//ページトップスクロールの速さ。数字が大きいほど遅くなる
    return false;//リンク自体の無効化
});

$('body').scrollgress({//バーの高さの基準となるエリア指定
	height: '5px',//バーの高さ
	color: '#eb6100',//バーの色
});





// 画面をスクロールをしたら動かしたい場合の記述
$(window).scroll(function (){
    //fadeAnime();//印象編 4 最低限おぼえておきたい動きの関数を呼ぶ
	//RandomAnimeControl();//印象編 8-8 テキストがランダムに出現、アニメーション用の関数を呼ぶ
});

// ページが読み込まれたらすぐに動かしたい場合の記述
$(window).on('load', function () {
    
//テキストのカウントアップ+バーの設定
var bar = new ProgressBar.Line(splash_text, {//id名を指定
	easing: 'easeInOut',//アニメーション効果linear、easeIn、easeOut、easeInOutが指定可能
	duration: 1000,//時間指定(1000＝1秒)
	strokeWidth: 0.2,//進捗ゲージの太さ
	color: '#eb6100',//進捗ゲージのカラー
	trailWidth: 0.2,//ゲージベースの線の太さ
	trailColor: '#ccc',//ゲージベースの線のカラー
	text: {//テキストの形状を直接指定				
		style: {//天地中央に配置
			position: 'absolute',
			left: '50%',
			top: '50%',
			padding: '0',
			margin: '-30px 0 0 0',//バーより上に配置
			transform:'translate(-50%,-50%)',
			'font-size':'1rem',
			color: '#eb6100',
		},
		autoStyleContainer: false //自動付与のスタイルを切る
	},
	step: function(state, bar) {
		bar.setText(Math.round(bar.value() * 100) + ' %'); //テキストの数値
	}
});

//アニメーションスタート
bar.animate(1.0, function () {//バーを描画する割合を指定します 1.0 なら100%まで描画します
	
    //=====ここからローディングエリア（splashエリア）を0.8秒でフェードアウトした後に動かしたいJSをまとめる
    // $("#splash").delay(500).fadeOut(800,function(){//#splashエリアをフェードアウトした後にアニメーションを実行
    // }); 
    
});   
    

});// ここまでページが読み込まれたらすぐに動かしたい場合の記述
    
