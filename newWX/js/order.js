/**
 * Created by victor on 15/7/7.
 */
//页面元素加载完成后，触发事件
(function () {
    var ie = !!(window.attachEvent && !window.opera);
    var wk = /webkit\/(\d+)/i.test(navigator.userAgent) && (RegExp.$1 < 525);
    var fn = [];
    var run = function () { for (var i = 0; i < fn.length; i++) fn[i](); };
    var d = document;
    d.ready = function (f) {
        if (!ie && !wk && d.addEventListener)
            return d.addEventListener('DOMContentLoaded', f, false);
        if (fn.push(f) > 1) return;
        if (ie)
            (function () {
                try { d.documentElement.doScroll('left'); run(); }
                catch (err) { setTimeout(arguments.callee, 0); }
            })();
        else if (wk)
            var t = setInterval(function () {
                if (/^(loaded|complete)$/.test(d.readyState))
                    clearInterval(t), run();
            }, 0);
    };
})();

document.ready(function(){
    var brandList = document.getElementsByClassName('light');
    for(var i=0; i<brandList.length; i++){
        brandList[i].addEventListener("touchstart", selectBrand, false);
    }

    var ctrlL = document.getElementById('control-left');
    var ctrlR = document.getElementById('control-right');
    ctrlR.addEventListener("touchstart", numPlus, false);
    ctrlL.addEventListener("touchstart", numMinus, false);

    var check = document.getElementById('integration-check');
    var selected = document.getElementById('check-selected');
    check.addEventListener("touchstart", integrationCheck, false);
    selected.addEventListener("touchstart", integrationCheck, false);
});
