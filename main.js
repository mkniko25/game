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




 







