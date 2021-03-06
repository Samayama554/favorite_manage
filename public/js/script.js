$(window).on('load', function () {

    var adjust = 0; //スクロール時のトップ位置調整用（問題なければ0）
    var sidebar = $('#sidebar_right'); //サイドバーを指定
    var wrap = $('#sidebar_right__wrap'); //ラッパーを指定

    var adjustTop = 0;
    var sidebarTop = parseInt(sidebar.css('top'));
    var sidebarMax = wrap.height() + adjust - sidebar.height();

    $(window).on('scroll', function () {
        var h = sidebarTop + $(window).scrollTop();
        if (h < sidebarMax) {
            if($(window).scrollTop() < adjust) {
                adjustTop = 0;
            } else {
                adjustTop = adjust;
            }
            var offset = sidebarTop-adjustTop + $(window).scrollTop() + 'px';
            sidebar.animate({top: offset},{duration:500, queue: false});
        }
    });
});