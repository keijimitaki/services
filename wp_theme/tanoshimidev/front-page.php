<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package TsunaguMiyagi
 */

get_header();
?>
<body>

  <div id="splash">
    <div id="splash_text"></div>
    <div id="splash-logo">
        <div class="loader"></div><br>Loading
    </div>
    <!--/splash--></div>
    <div class="splashbg"></div><!---画面遷移用-->
    
<!-- -->
<canvas id="webgl-canvas">
</canvas>


<div id="wrapper">


    
<header id="header">
    <h1><span>ビートグランプリ2021にエントリーしました！</span></h1>
        
    <div class="sns_links">
      <nav>
        <ul>
          <li><a href="https://twitter.com/kuroobicode" target="_blank" rel="noopener"><ion-icon name="logo-twitter"></ion-icon></a></li>
          <li><a href="https://www.instagram.com/bitkidss/" target="_blank" rel="noopener"><ion-icon name="logo-instagram"></ion-icon></a></li>
          <li><a href="https://www.youtube.com/channel/UC5bpDi-V_e3S3VN5I2tIYCA" target="_blank" rel="noopener"><ion-icon  name="logo-youtube"></ion-icon></a></li>
        </ul>

      </nav>
    </div>

</header>

<article class="article_main">

    <h2>1.はじめに</h2>
    <p>
        ビートグランプリ 2021 バーチャル映像部門にエントリーしました。<br>
        今年のビートグランプリは"音楽と映像の融合"をテーマとした映像作品を対象としたオンラインコンテストです。<br>
        音と映像を約1ヵ月の時間をかけて、制作編集して、3分ジャストに詰め込みました。<br>
        普段は思いつきのまま気まぐれに作っているのですが、<br>
        今回は表現したいコンセプト、構成をしっかり作って、それに沿うように作る事を心掛けました。<br>
        もっと質の高いものを作りたくなったので、今後の活動の参考としてこのページに残しておこうと思います。<br>
        といっても、技術的な事は書きません。
        KodeLifeのプログラムコードや、AbletonLiveのTouchDesignerのファイルを見れば思い出せるし、<br>
        技術解説は文量が多くなり時間もかかるためです。<br>
        今回のような作品においては、技術よりも、作品のコンセプト作りが重要だと気付いたのでこれらを書きます。<br>
        本当のこというと、作品のコンセプト、ノートにメモってたけど、将来絶対忘れる自信があるので、忘れないためにです。
      </p>
      <p>
        作品概要
        <ul>
          <li>
            曲名：Bismuth（ビスマス）
          </li>
          <li>
            長さ：3:00（BPM150、曲の展開、約1分毎に3回）
          </li>
          <li>
            制作：Bitkids
          </li>
          <li>
            使用したソフトウェア：AbletonLive、KodeLife、TouchDesigner
          </li>
          <li>
            使用したハードウェア：無し
          </li>

        </ul>

    </p>
    
    <br>
    <h2>2.コンセプト</h2>
    <p>コンセプトは次の通りです。
      <ul>
        <li>非現実的、人工加工、デジタル質感</li>
        <li>有機的</li>
        <li>ダーク、シルキー、ポジティブ</li>
      </ul>

    </p>
    <p>
      映像は、無機質になりすぎないよう、動きやデジタルならではのオブジェクト、空間、造形物で有機的な印象を入れようと思いました。<br>
      無意識でしたが今思うと、少し前に公開されたGusGusのSimple Tuesdayの映像に強く影響を受けていると思います。大好きなアーティストで、よくMV見たので。
    </p>

    <p>
      曲名のBismuth（ビスマス）は、元素の一種です。<br>
      作品の世界観は、ビスマス結晶からインスピレーションを受けています。ビスマス結晶は、人工的に加工された美しさが凄く魅力的なものです。<br>
      声やベース、ドラムなど多くは生音素材ですが、DAWで加工、編集して、意識的にデジタルの雰囲気を強調したサウンドを狙いました。
    </p>

    <br>
    <h2>3.構成</h2>
    <p>

    サウンドについては、なるべく音楽的に展開をさせようと思いました。自分が飽き性なので。<br>
    展開といってもいろいろあります。コード進行や、リズムや音圧、音量、音色。<br>
    僕の技量では意識的にそれらを組み立てるとぎこちないものになるので、聴いていて気持ちよく切り替わる感覚だけに集中して音を組み立てました。
  </p>
    
  <p>
    個人的なこだわりで、切りのいい時間配分とBPMにすることに決めていました。<br>
    曲の長さは：3:00 <br>
    BPM：150 <br>
    展開：1分 × 3回 <br>
  </p>

  <p>
    映像については、サウンドのイメージに合わせるだけ。<br>
    ただ、単に無秩序にシーン切り替えを繰り返すだけだと統一感が無くなるため、3分間の中で、ストーリーに沿って展開するよう考えました。<br>
    考えたストーリーは、次のようなものです。<br>
    <ul class="story">
      <li>前半、主人公が現実世界から落下して美しいバーチャル空間に迷い込む。</li>
      <li>中盤、落下した空間から浮上し、別の空間に入り込む。新たな世界観を楽しむ。</li>
      <li>後半、更に別のバーチャル空間の出現を予感させる。</li>
    </ul>    
  </p>
  <p>
  なんだか、ショボいストーリー（笑）。いいんです。あくまでも映像の流れを作るために付けただけなので（言い訳）
  </p>
    
  <p>
    その他、タイトルやロゴは入れないことも決めていました。世界観が薄れると思ったので。<br>
    いや、単にコンセプトに合うロゴやタイポグラフィーが作れなかったためです。。
  </p>
  

  <br>
  <h2>4.まとめ</h2>
  <p>
    今回のエントリーしたのはバーチャル映像部門でした。
    未熟ながら日頃から音や絵をプログラミングしたり編集したりしてるので、<br>
    その素材を使ってまとまった作品を作ってみようと考えたのがきっかけです。<br>
    裏テーマとして、トラックメイキングに挑戦してみようという思いがありました。 <br>
    また、自分が作り出せる音や映像、扱えるツールに制限があると感じてますが、<br>制限の中で、自分なりに質の高いものを作る事を目標にしていました。<br>
    
    
    結果、評価はどうあれ、楽しむ事ができました。また違った観点での制作ができたので満足です。
  </p>


  </article>

    

<!--=============JS ===============--> 
<!--jQuery-->
<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
<script src="https://rawgit.com/kimmobrunfeldt/progressbar.js/master/dist/progressbar.min.js"></script>
<!--IE11用-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/babel-standalone/6.26.0/babel.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/babel-polyfill/6.26.0/polyfill.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/scrollgress/2.0.0/scrollgress.min.js"></script>

<!--
<script src="js/script.js"></script>

<script src="./webgl_minMatrix.js"></script>
<script src="./webgl_script.js"></script>
-->

<script>

    $(function(){

      let news = ['#info .one','#info .two'];
      news.forEach(targetName => {
        $(targetName).fadeOut('slow');
      });
      $('#info .one').fadeIn('slow',fadeInOutLoop);

    });

    let newsIndex = 0;
    function fadeInOutLoop() {

      newsIndex += 1;
      newsIndex = (newsIndex % 4)
      let news = ['#info .one','#info .two','#info .three','#info .four'];

      sleep(4, function () {
          news.forEach(targetName => {
            $(targetName).fadeOut('slow');
          });
          $(news[newsIndex]).delay(1000).fadeIn(2000,fadeInOutLoop);

        }

      );
    }
    
    function sleep(waitSec, callbackFunc) {
    
      var spanedSec = 0;
      // 1秒間隔で無名関数を実行
      var id = setInterval(function () {
          spanedSec++;
          if (spanedSec >= waitSec) {
              clearInterval(id);
              if (callbackFunc) callbackFunc();
          }
      }, 1000);

    }

  </script>

</body>

<?php
get_sidebar();
get_footer();
