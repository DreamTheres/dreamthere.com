function setParam(param, value) {
    var query = location.search.substring(1);
    var p = new RegExp("(^|)" + param + "=([^&]*)(|$)");
    if (p.test(query)) {
        //query = query.replace(p,"$1="+value);
        var firstParam = query.split(param)[0];
        var secondParam = query.split(param)[1];
        if (secondParam.indexOf("&") > -1) {
            var lastPraam = secondParam.split("&")[1];
            return  '?' + firstParam + param + '=' + value + '&' + lastPraam;
        } else {
            if (firstParam) {
                return '?' + firstParam + param + '=' + value;
            } else {
                return '?' + param + '=' + value;
            }
        }
    } else {
        if (query == '') {
            return '?' + param + '=' + value;
        } else {
            return '?' + query + '&' + param + '=' + value;
        }
    }
}