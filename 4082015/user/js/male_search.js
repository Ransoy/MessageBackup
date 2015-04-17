var value_opacity; // 透明度を保持する変数
var elem_target;
elem_target = null;
var elem_txt_target;
var innertxt=new Array(
	'会員様がログインした時間で検索可能。まだサイトで遊んでいるかも知れない会員様へメール送信', //最終ログイン
	'誕生日は特別な日。常連様の誕生日は必ずCHECKしておきましょう！', // 誕生日
	'寝る時間の３時間前くらいを逆算してお仕事帰りやまったりタイムを狙ってメールを送ってみよう！', // 寝る時間
	'マイキーワードを参考にメール内容を考えよう！同じ趣味の会員様とは話が盛り上がる可能性大！', // マイキーワード
	'過去に入室して頂いた会員様もチャットネームで検索できます。', //チャットネーム
	'',
	'',
	'マシェリにご登録頂いているお客様です。まずは自身を知ってもらう為に自己紹介やお誘いのメールを送信しましょう。', //全会員
	'ご登録してまだお気に入りのパフォーマーがいらっしゃらない会員様ですので狙い目の会員様です！是非お誘いしてみてください！', //無料会員
	'芸能イベント参加頂いていて定期的にマシェリに来て頂いておりますので是非お誘いください！芸能イベントについて触れてみるのが良いかもしれません' //芸能イベント
);

function onevent(i,e) {
	if(document.all){
		elem_target.style.left = e.x - 40 + "px";
		elem_target.style.top = e.y+ 20 + "px";
	}else{
		elem_target.style.left = e.pageX-40 + "px";
		elem_target.style.top = e.pageY+20 + "px";
	}
	elem_txt_target.innerHTML = innertxt[i];
	elem_target.style.display="block";
	value_opacity = 0;
	setOpacityZero();
	return false;
}
function Balreset(e){
	elem_target.style.display = "none";
	elem_target.style.opacity=0;
	elem_txt_target.innerHTML = "";
}

// 透明度が10になるまで+1加算する（タイマー）
function setOpacityZero() {
	if(value_opacity < 10){
		value_opacity += 3.66;
		setTimeout('setOpacityZero()',80);
		setOpacity();
	}
}
// 透明度をセットする処理
function setOpacity () {
	elem_target.style.filter = 'alpha(opacity=' + (value_opacity * 10) + ')';
	elem_target.style.MozOpacity = value_opacity / 10;
	elem_target.style.opacity = value_opacity / 10;
}

// イベントの初期化処理
function initalizeEvents() {
	elem_target = document.getElementById("balloon");
	elem_txt_target = document.getElementById("inBalloontxt");
}