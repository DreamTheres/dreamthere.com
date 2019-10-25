/**
 * 操作类工具方法
 * @type type
 */
var operateUtil = {
    /**
     * 添加收藏夹
     * @param {type} url
     * @param {type} title
     * @returns {undefined}
     */
    addFavorite: function (url, title) {
        try {
            window.external.addFavorite(url, title);
        } catch (e) {
            try {
                window.sidebar.addPanel(title, url, "");
            } catch (e) {
                alert("您的浏览器不支持,请按 Ctrl+D 手动收藏!");
            }
        }
    },
    /**
     * 设置主页
     * @param {type} obj
     * @param {type} url
     * @returns {undefined}
     */
    setHome: function (obj, url) {
        try {
            obj.style.behavior = 'url(#default#homepage)';
            obj.setHomePage(url);
        } catch (e) {
            if (window.netscape) {
                try {
                    netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
                } catch (e) {
                    alert("抱歉，此操作被浏览器拒绝！\n\n请在浏览器地址栏输入“about:config”并回车然后将[signed.applets.codebase_principal_support]设置为'true'");
                }
            } else {
                alert("抱歉，您所使用的浏览器无法完成此操作。\n\n您需要手动将【" + url + "】设置为首页。");
            }
        }
    }
};