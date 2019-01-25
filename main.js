// // 基本　ボタンを押すと出て消える
// $(function () {
		
// 	// $("button").click(function(){
// 	$(".pic2").fadeIn();
// 	$(".pic2").fadeOut();
// 	// });
// });



// var form = $('form');
// form.submit(function(e) {
 
// 	// //ここで何か処理を行う
//   	$(".pic2").fadeIn();
//  	$(".pic2").fadeOut();

//  // 	// alert('aaa');

// 	//3秒遅れで発生したsubmitイベントを消してからsubmit
// 	setTimeout( function() {
// 		form.off('submit');
// 		form.submit();

// 	}, 1000);
 
// 	//自動でsubmitされないように処理を止める
//     return false;
    

// } );


//攻撃の時、稲妻を表示する
$(function () {

    $('input[type=submit]').on('click', function () {
        //クリックされたSubmitのNameを取得
        var name = $(this).attr('name');

        // attackだったら画像
        if(name == 'attack'){
            var form = $('form');

            $(".pic2").fadeIn();
            $(".pic2").fadeOut();

            setTimeout(function() {
            form.off('submit');
            form.submit();

            }, 1000);

            var postData = {"attack":"true"};
            $.post("index.php", postData);

            return false;
        };
    });
});














// $(function () {

// 	$("button").click(function(){
// $('body').append('<div class="loading"><img src="img/inazuma.png"></div>');
// });
// });


// $('input[name="attack"]').on('click',function(){
// 	setTimeout(function(){
//         $(".pic2").fadeIn()},100)
// });

// $('input[name="attack"]').on('click',function(){

// setTimeout(function(){
// 	// $('.pic2').append('<div class="loading"><img src="img/inazuma.png"></div>');},100);

// 	// setTimeout(function(){
//  //        $('body').append('<div class="loading"><img src="img/inazuma.png" /></div>');},5000);

// });





 







