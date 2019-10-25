$(function () {
    $('#menus').load('/res/html/menus.html?v=' + ver);
    if (keyword != '') {
        search(1);
    } else {
        list(1);
    }
});

function search(page) {
    var key = $('input[name=key]').val();
    flag = true;
    issearch = true;
    $.ajax({
        url: '/tags/getList',
        data: {
            key: key,
            page: page
        },
        success: function (res) {
            var tpl = $('#all-template').html();
            var html = juicer(tpl, {list: res.result});
            if (page == 1) {
                $('#all').html(html);
            } else {
                $('#all').append(html);
            }
            if (!res.result.length) {
                end = true;
                $('.load-more').html('----我们是有底线的----');
            }
            flag = false;
        }
    });
}

function list(page) {
    flag = true;
    issearch = false;
    $.ajax({
        url: '/tags/getlist',
        data: {
            page: page
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
var issearch = false;
$(window).scroll(function () {
    if (!flag && !end && $('.load-more').offset().top < $(window).height() + $(document).scrollTop()) {
        page++;
        issearch ? search(page) : list(page);
    }
});