var dataBasePath = 'http://wt.bama555.com';

//1.WeiBaUI
(function(window,$){
    window.WBPage = {};

    $(function(){
        /*
        //如果发现没有历史浏览记录，则把当前页面url作为历史记录保存，以便返回按钮一直有效
        alert(window.history.length);
        if(window.history.length<=1){
            var ls = window.localStorage;
            if(ls){
                ls.setItem('default_history',window.location.href);
            }
        }
        */
    });

    $.extend(window.WBPage,{
        /**
         * 返回
         */
        'goBack' : function(){
            if(window.history.length<=1){
                window.location.href = (window.WBPage.Info.home);
            }else{
                window.history.back();
            }
        },

        'getWBData' : function(name){
            return $('body').getWBData(name);
        },
        'show' : function(){
            $('.weiba-page').show();
        },
        'hide' : function(){
            $('.weiba-page').hide();
        },

        'info_init' : function(info){
            var status = -1;

            if(info && info['status']!==undefined){
                status = info.status;
            }
            $.extend({
                home : '',name : '微信网站'
            },info);

            if(status==0){
                window.WBPage.Info = {
                    'home' : info['url'],
                    'name' : info['company']
                };
            }else{
                var msg = '';
                switch (status){
                    case 1 :
                        msg = '微网站已经被禁用，请联系代理商。';
                        break;
                    case 2 :
                        msg = '微网站已经被删除，请联系代理商。';
                        break;
                    case 3 :
                        msg = '微网站已经被过期，请联系代理商。';
                        break;
                    default :
                        msg = '微网站状态不正常，请联系代理商。';
                }
                alert(msg);
                document.write(msg);
            }
            return status;
        },

        /**
         * 初始化插件
         * @param name 插件名称
         */
        'widget_init' : function(name){
            switch(name){
                case 'banner':
                    $('.weiba-banner').wb_ui_banner();
                    break;
                case 'navbar':
                    $('.weiba-navbar').wb_ui_navbar();
                    break;
                case 'quickpanel':
                    $('.weiba-quickpanel').wb_ui_quickpanel();
                    break;
                case 'easycall':
                    $('.weiba-easycall').wb_ui_easycall();
                    break;
            }
        },
        /**
         * 渲染模板数据
         */
        'tpl_render' : function(data,directive){
            $.each(directive,function(key,dir){
                $(key).render(data,dir);
            });
        },
        'PATH_TYPE_PROCESSOR' : '/assets/public/js/type_processor/',
        'PATH_DATA_INFO' : dataBasePath + '/data/info/',/*站点基本信息*/
        'PATH_DATA_BANNER' : dataBasePath + '/data/cate_banner/',/*首页banner*/
        'PATH_DATA_DETAIL_LIST' : dataBasePath + '/data/detail_list/',/*栏目下文章列表*/
        'PATH_DATA_FOOTER' : dataBasePath + '/data/footer/',/*底部技术支持*/
        'PATH_DATA_EASYCALL' : dataBasePath + '/data/easycall/',/*辅助按钮*/
        'PATH_DATA_CATELIST' : dataBasePath + '/data/cate_list/',/*栏目列表*/
        'PATH_DATA_DETAIL' : dataBasePath + '/data/detail',/*详情*/
        'PATH_DATA_LINKUS' : dataBasePath + '/data/linkus'/*详情*/
    });

    //扩展$对象
    $.fn.getWBData = function(name){
        var dataname = 'weiba-' + name;
        return this.attr(dataname);
    };
})(window,jQuery);

//Loader
(function(window,$){
    var $loader,loader_ids={};

    window.WBPage.Loader = {
        /**
         * 添加显示一个loader,并返回loaderid
         */
        'append' : function(){
            if(!$loader){
                var zindex = WBPage.MaskLayer.getZIndex()+1;
                if(zindex<999){
                    zindex = 999999;
                }
                $loader = $('<div class="weiba-loader" style="z-index: ' + zindex + '"></div>').appendTo('body');
            }
            $loader.show();
            return getNewLoaderID();
        },
        /**
         * 完成删除loader
         */
        'remove' : function(id){
            if(loader_ids.hasOwnProperty(id)){
                delete loader_ids[id];
            }
            if(WBPage.Loader.getAllIds().length==0){
                $loader.css('display','none');
            }
        },
        /**
         * 删除所有loader
         */
        'removeAll' : function(){
            loader_ids = {};
            $loader.hide();
        },
        /**
         * 返回正在执行的所有loaderid
         */
        'getAllIds' : function(){
            var ids = [];
            $.each(loader_ids,function(key,value){
                ids.push(key);
            });
            return ids;
        }
    };



    function getNewLoaderID(){
        var loaderid = 'weiba_loaders_' + Math.round(Math.random() * 8000000 + 1000000);
        if(!loader_ids.hasOwnProperty(loaderid)){
            loader_ids[loaderid]=true;
            return loaderid;
        }
        else{
            return getNewLoaderID();
        }
    }


})(window,jQuery);


//MaskLayer
(function(window,$){
    var $masklayer;
    window.WBPage.MaskLayer = {
        'show' : function(color){
            if(!$masklayer){
                $masklayer = $('<div class="weiba-masklayer"></div>').addClass(color?color:'');
            }
            return $masklayer.hide().appendTo('body').fadeIn();
        },
        'close' : function(){
            if($masklayer){
                $masklayer.fadeOut(function(){
                    $masklayer.off();
                    $masklayer.unbind();
                    $masklayer.remove();
                    $masklayer = null;
                });
            }
        },
        'getZIndex' : function(){
            if($masklayer){
                return $masklayer.css('z-index');
            }
            else{
                return 0;
            }
        }
    };


})(window,jQuery);


//2.navbar
(function(window,$){
    $.fn.wb_ui_navbar = function(){


        var $navBar = this.each(function(){
            $(this).on('tap','.weiba-navbar-item',function(e){
                if($(this).hasClass('quick')){
                    if(WBPage.QuickPanel){
                        if(!WBPage.QuickPanel.isOpened){
                            WBPage.QuickPanel.open();
                        }
                        else{
                            WBPage.QuickPanel.close();
                        }
                    }
                }
                else if($(this).hasClass('easycall')){
                    if($(this).hasClass('easycall-one')){//只有一个easycall按钮，则直接执行链接
                        return;
                    }else{
                        if(WBPage.EasyCall){
                            if(!WBPage.EasyCall.isOpened){
                                WBPage.EasyCall.open();
                            }
                            else{
                                WBPage.EasyCall.close();
                            }
                        }
                    }
                }
                else if($(this).hasClass('home')){
                    return;
                    //if(WBPage.Info){
                    //    window.location.href = WBPage.Info.home;
                    //}
                }
                else if($(this).hasClass('back')){
                    WBPage.goBack();
                    //window.history.back();
                }
                e.preventDefault();
                return false;
            });
        });

        window.WBPage.NavBar = {
            'Dom' : $navBar
        };

        return $navBar;
    };

    function quickpanelclose(e) {
        console.log('close');
        $('body').removeClass('weiba-quickpanel-animate-push');
        $('.weiba-quickpanel').hide();
        $('.weiba-page').unbind('tap.quickpanel',quickpanelclose);
        e.preventDefault();
        return false;
    }
})(window,jQuery);

//3.quickpanel
(function(window,$){
    $.fn.wb_ui_quickpanel = function(action){
        var _transitionEndEvents = 'webkitTransitionEnd oTransitionEnd otransitionend transitionend msTransitionEnd';
        var $quickpanel =  this;
        var $pannel_box;

        if($quickpanel.length>0){
            $pannel_box = $('<div class="weiba-quickpanel-box"><div class="weiba-quickpanel-toolbar"><div class="weiba-quickpanel-toolbar-title">快捷导航</div><div class="weiba-quickpanel-toolbar-close icon-delete"></div></div></div>')
                .append($quickpanel.show()).appendTo('body')
                .on('tap','.weiba-quickpanel-toolbar',function(){
                    window.WBPage.QuickPanel.close();
                });
        }

        window.WBPage.QuickPanel = {
            'isOpened': false,
            'open' : function(){
                window.WBPage.MaskLayer.show('black');
                $pannel_box.css({
                    'z-index' : window.WBPage.MaskLayer.getZIndex()+1,
                    'width': $quickpanel.width(),
                    'top' : 0,
                    'right' : -$quickpanel.width() + 'px'
                    //'height' : $(window).height()
                }).show().animate({
                    'right' : 0
                },function(){
                    window.WBPage.QuickPanel.isOpened = true;
                });
            },
            'close' : function(){
                $pannel_box.css({
                    'top' : 0,
                    'right' : 0
                }).show().animate({
                        'right' : -$quickpanel.width() + 'px'
                    },function(){
                        window.WBPage.QuickPanel.isOpened = false;
                        $pannel_box.hide();
                        window.WBPage.MaskLayer.close();
                    });
            }
        };
        return $quickpanel;
    };
})(window,jQuery);

//4.weiba-easycall
(function(window,$){
    $.fn.wb_ui_easycall = function(){
        this.addClass('child' + this.children('.weiba-easycall-item').each(function(index,item){
            $(item).addClass('no' + index);
        }).length);
        var $easycall =  this.on('tap','.weiba-easycall-item',function(e){
            e.stopPropagation();
            //return false;
        }).on('tap',function(){
            if(!$(this).hasClass('weiba-easycall-item')){
                window.WBPage.EasyCall.close();
            }
        });

        if(this.children('.weiba-easycall-item').length<=0){//隐藏navBar的图标
            $('.weiba-navbar').addClass('easycall-no');
        }
        else if(this.children('.weiba-easycall-item').length==1){//一个的时候navbar直接操作
            $('.weiba-navbar-item.easycall').addClass('easycall-one').attr('href',this.children('.weiba-easycall-item').attr('href'));
        }

        window.WBPage.EasyCall = {
            'isOpened': false,
            'open' : function(){
                window.WBPage.MaskLayer.show('black').on('tap',function(){
                    window.WBPage.EasyCall.close();
                });
                $easycall.css({
                    'z-index' : window.WBPage.MaskLayer.getZIndex()+1
                }).show(function(){
                    window.WBPage.EasyCall.isOpened = true;
                });
            },
            'close' : function(){
                $easycall.hide(function(){
                        window.WBPage.EasyCall.isOpened = false;
                        window.WBPage.MaskLayer.close();
                    });
            }
        };
        return $easycall;
    };
})(window,jQuery);

//4.banner
(function(window,$){
    $.fn.wb_ui_banner = function(){
        return this.each(function(){
            var $this = $(this),
                flganimate = false,
                count = $this.children('.weiba-banner-item').length;

            if (count>1) {
                //1.插入工具条
                var html = '<div class="weiba-banner-toolbar">';
                for (var i=0;i<count;i++) {
                    html+='<span class="weiba-banner-toolbar-item l'+ i +'"></span>';
                }
                html +='</div>';
                
                //2.绑定事件
                var $toolbar =  $(html).appendTo($this);
                $this.on({
                    'swipeleft': function (e) {
                        if (!flganimate && $this.data('__currIndex') < (count - 1)) {
                            e.preventDefault();
                            selectedIndex($this.data('__currIndex')+1);
                        }
                    },
                    'swiperight': function (e) {
                        if (!flganimate && $this.data('__currIndex') > 0) {
                            e.preventDefault();
                            selectedIndex($this.data('__currIndex') - 1);
                        }
                    }
                });
                
                //3.选中第一个
                selectedIndex(0);

                //4.自动播放
                autoPlay();
            }
            else if(count<=0){
                $this.remove();
            }
            
            function selectedIndex(index) {
        		$this.data('__currIndex', index);
        		changeMarginLeft(-$this.data('__currIndex') * 100 + '%');// $this.width());
                $($toolbar.children().removeClass('selected')[index]).addClass('selected');
            }   
            function changeMarginLeft(toLeft) {
                flganimate = true;
                $($this.children().get(0)).animate({
                    'margin-left' : toLeft
                }, 220, 'linear', function () {
                    flganimate = false;
                });
                        
            }
            function autoPlay(){
                var cindex = $this.data('__currIndex');
                cindex++;
                if(cindex>=count){
                    cindex = 0;
                }
                if(!flganimate){
                    selectedIndex(cindex);
                }
                window.setTimeout(autoPlay,3500);
            }
        });
    }; 
})(window,jQuery);


