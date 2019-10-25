$(function () {
    $('#menus').load('/res/html/menus.html?v=' + ver);
    list(1);
});

function addWeight(id) {
    $.ajax({
        url: '/urls/add_weight/' + id,
        data: {},
        success: function (res) {
            console.log(res);
        }
    });
}

function list(page) {
    flag = true;
    $.ajax({
        url: '/urls/getlistByTag',
        data: {
            page: page,
            tag: tag
        },
        success: function (res) {
            var tpl = $('#all-template').html();
            var html = juicer(tpl, {list: res.result});
            $('#all').append(html);
            if (!res.result.length) {
                end = true;
                $('.load-more').html('----我们是有底线的----');
            }
            flag = false;
        }
    });
}

var page = 1;
var end = false;
var flag = false;
$(window).scroll(function () {
    if (!flag && !end && $('.load-more').offset().top < $(window).height() + $(document).scrollTop()) {
        page++;
        list(page);
    }
});