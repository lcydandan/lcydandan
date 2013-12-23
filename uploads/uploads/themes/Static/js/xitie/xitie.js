
if (!Array.prototype.indexOf)
{
    Array.prototype.indexOf = function(elt /*, from*/)
    {
        var len = this.length;

        var from = Number(arguments[1]) || 0;
        from = (from < 0)
            ? Math.ceil(from)
            : Math.floor(from);
        if (from < 0)
            from += len;

        for (; from < len; from++)
        {
            if (from in this && this[from] === elt)
                return from;
        }
        return -1;
    };
}


if (!Date.now)
{
    Date.now = function()
    {
        return new Date();
    };
}

//临时解决wxid的问题,拨号问题
$(window).bind('rendercomplete',function(){
    $('a').each(function(index,item){
        var url = $(item).attr('href');
        if(url){
            if(url.indexOf('vipcard')!=-1 || url.indexOf('marketing_scratch')!=-1 || url.indexOf('marketing_fruit')!=-1 || url.indexOf('marketing_rotate')!=-1){
                if($(item).attr('href').indexOf(window.localStorage.getItem('WXID'))==-1){//不包含微信ID则添加
                    $(item).attr('href',url + window.localStorage.getItem('WXID'));
                }
            }else if(url.indexOf('tel:')==0){
                var b = navigator.userAgent.match(/i(Pod|Pad|Phone)\;.*\sOS\s([\_0-9]+)/);
                if (!b) {//非iphone，电话链接
                    $(item).on('click',function(e){
                        if(confirm('你确定拨打' + url.replace('tel:','') + '吗?')){
                            e.preventDefault();
                            window.setTimeout(function(){
                                location.href= url;
                            },100);
                            return false;
                        }
                    }).addClass('autotel');
                }

            }
        }
    });
    //alert('系统正在升级，请稍后：' + window.localStorage.getItem('WXID'));
});


(function(window,$){
    $.translateTel= function(tel) {
        tel += '';
        var numbers = '-0123456789', telNumber = '', spStrings = [' ', ':', '：'];
        spStrings.forEach(function (ele) {
            var spIndex = tel.indexOf(ele);
            if (spIndex > -1) {
                tel = tel.substring(spIndex + 1);
            }
        });

        if (typeof tel === 'string') {
            for (var i = 0, length = tel.length; i < length; i++) {
                var t = tel.charAt(i);
                if (numbers.indexOf(t) > -1) {
                    telNumber += t;
                }
            }
        }
        return telNumber;
    };

    function getRad(d) {
        var PI = Math.PI;
        return d * PI / 180.0;
    }

    $.getFriendDistance = function(lat1, lng1, lat2, lng2) {
        var dis = 0;
        if (arguments.length == 1) {
            dis = lat1;
        } else {
            dis = $.getDistance(lat1, lng1, lat2, lng2);
        }
        if (dis < 1000) {
            return (dis >> 0) + 'm';
        } else {
            return ((dis / 1000) >> 0) + 'km';
        }
    };

    $.getDistance = function(lat1, lng1, lat2, lng2) {
        var EARTH_RADIUS = 6378137.0;
        lat1 = lat1 * 1;
        lng1 = lng1 * 1;
        lat2 = lat2 * 1;
        lng2 = lng2 * 1;
        var f = getRad((lat1 + lat2) / 2);
        var g = getRad((lat1 - lat2) / 2);
        var l = getRad((lng1 - lng2) / 2);

        var sg = Math.sin(g);
        var sl = Math.sin(l);
        var sf = Math.sin(f);

        var s, c, w, r, d, h1, h2;
        var a = EARTH_RADIUS;
        var fl = 1 / 298.257;

        sg = sg * sg;
        sl = sl * sl;
        sf = sf * sf;

        s = sg * (1 - sl) + (1 - sf) * sl;
        c = (1 - sg) * (1 - sl) + sf * sl;

        w = Math.atan(Math.sqrt(s / c));
        r = Math.sqrt(s * c) / w;
        d = 2 * w * a;
        h1 = (3 * r - 1) / 2 / c;
        h2 = (3 * r + 1) / 2 / s;

        return d * (1 + fl * (h1 * sf * (1 - sg) - h2 * (1 - sf) * sg));
    };

})(window,jQuery);

(function(window,$){

    $.getUrlParam = function (name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
        var r = window.location.search.substr(1).match(reg);
        if (r != null) return unescape(r[2]); return null;
    }
    /**
     * 加载多个json文件
     * @param reqs 请求的多个文件,格式类似：
     *          {
     *              key : {
     *                      url : '',
     *                      data : '',
     *                    }
     *          }
     * @param success
     * @param fail
     */
    $.getMultiJSON = function(reqs,success,fail){
        var count = 0;
        var result = {};

        if($.isPlainObject(reqs)){
            $.each(reqs,function(key,item){
                count++;
                if(reqs.hasOwnProperty(key)){
                    $.getJSON(item.url + '?&callback=?',item.data,function(r){
                        count--;
                        if(r['ret']===0){
                            result[key] = r['data'];
                            if(typeof item.success==='function'){
                                item.success.call(this,r['data']);
                            }
                        }
                        fn_ok();
                    }).fail(fn_err);
                }
            });

            if(count==0){
                fn_ok();
            }
        }
        else{
            fn_err();
        }

        function fn_ok(e){
            if(count<=0){
                if(typeof success === 'function'){
                    success.call(this,result);
                }
            }
        }

        function fn_err(e){
            count--;
            if(typeof fail === 'function'){
                fail.call(this,e);
            }
        }
    };


})(window,jQuery);

(function (window,$) {
    var onBridgeReady = function () {

        $(document).trigger('bridgeready');

        var $body = $('body'), appId = '',
            title = $body.attr('weiba-title'),
            imgUrl = $body.attr('weiba-icon'),
            link = $body.attr('weiba-link'),
            desc = $body.attr('weiba-desc') || link;
        if (!setForward()) {
            $(document).bind('weibachanged', function () {
                setForward();
            });
        }
    };
    if (document.addEventListener) {
        document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
    } else if (document.attachEvent) {
        document.attachEvent('WeixinJSBridgeReady', onBridgeReady);
        document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
    }

    function setForward() {
        var $body = $('body'), appId = '',
            title = $body.attr('weiba-title'),
            imgUrl = $body.attr('weiba-icon'),
            link = $body.attr('weiba-link'),
            desc = $body.attr('weiba-desc') || link;
        if (title && link) {
            WeixinJSBridge.on('menu:share:appmessage', function (argv) {
                WeixinJSBridge.invoke('sendAppMessage', {
                    //'appid': 'kczxs88',
                    'img_url': imgUrl?imgUrl:undefined,
                    'link': link,
                    'desc': desc?desc:undefined,
                    'title': title
                }, function (res) {
                    if (res && res['err_msg'] && res['err_msg'].indexOf('confirm') > -1) {
                        $(document).trigger('wx_sendmessage_confirm');
                    }
                });
            });
            WeixinJSBridge.on('menu:share:timeline', function (argv) {
                $(document).trigger('wx_timeline_before');

                WeixinJSBridge.invoke('shareTimeline', {
                    'img_url': imgUrl?imgUrl:undefined,
                    'link': link,
                    'desc': desc?desc:undefined,
                    'title': title
                }, function (res) {
                    //貌似目前没有简报
                });
            });
            /*
             WeixinJSBridge.on('menu:share:weibo', function (argv) {
             WeixinJSBridge.invoke('shareWeibo', {
             'content': title + desc,
             'url': link
             }, function (res) {

             });
             });
             */
            return true;
        }
        else {
            return false;
        }
    }

})(window,jQuery);
