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
    var orderMethod = document.getElementById('order-method')
    var brandList = document.getElementsByClassName('light');
    for(var i=0; i<brandList.length; i++){
        brandList[i].addEventListener("touchstart", selectBrand, false);
    }

    var ctrlL = document.getElementById('control-left');
    var ctrlR = document.getElementById('control-right');
    ctrlR.addEventListener("touchstart", numPlus, false);
    ctrlL.addEventListener("touchstart", numMinus, false);

    if (orderMethod.title == 'batch') {
        var ctrlLEX = document.getElementById('control-left-extract');
        var ctrlREX = document.getElementById('control-right-extract');
        ctrlREX.addEventListener("touchstart", numPlusEx, false);
        ctrlLEX.addEventListener("touchstart", numMinusEx, false);
    }

    var check = document.getElementById('integration-check');
    var selected = document.getElementById('check-selected');
    if (orderMethod.title == 'once') {
        check.addEventListener("touchstart", integrationCheck, false);
    }
    selected.addEventListener("touchstart", integrationCheck, false);

    //调整品牌位置
    var liNum = document.getElementsByClassName("brand-List-item");
    //alert(liNum.length);
    if(liNum.length == 1){
        for(var i=0;i<liNum.length;i++){
            liNum[i].style.left = 36 +"%";
        }
    }else if(liNum.length == 2){
        for(var i=0;i<liNum.length;i++){
            liNum[i].style.left = 20 +"%";
        }
    }else if(liNum.length == 3){
        for(var i=0;i<liNum.length;i++){
            liNum[i].style.left = 3.8 +"%";
        }
    }
});

function upForm(btype){
    var num = parseInt(document.getElementById('num-value').value)
    var choose_id = document.getElementById('brand-id').value
    var choose_id = choose_id.replace(/brand/, '')


    if (btype == 1) {
        var val = document.getElementById('pay-method').value
        if (val == 'price') {
            str = '{"p_type":2,"order":['
        }else{
            var tempPoint = parseInt(document.getElementById('point').innerHTML)
            var wholePoint = parseInt(document.getElementById('brand-value').value) * num;
            if (tempPoint < wholePoint) {
                alert('对不起，您的积分不足')
                return false;
            }else if(wholePoint==0){
                alert('对不起，积分兑换暂未开启，经期期待');
            }
            var str = '{"p_type":3,"order":['
        }
        str += '{"num":' + num + ',"gid":' + choose_id + '}]}'
    }else if (btype == 2) {
        exnum = parseInt(document.getElementById('extract-num-value').value)
        if (num > 0) {
            str = '{"p_type":0,"order":['
            str += '{"num":' + exnum + ',"gid":' + choose_id + ',"pnum":' + num + '}]}'
        }else{
            str = '{"p_type":1,"order":['
            str += '{"num":' + exnum + ',"gid":' + choose_id + '}]}'
        }
    }else if (btype == 3) {
        var exnum = parseInt(document.getElementById('num-value').value)
        var leftnum = document.getElementById('left_num').innerHTML
        if (exnum > leftnum) {
            alert('对不起，您的存水量不足')
            return false;
        }else{
            str = '{"p_type":1,"order":['
            str += '{"num":' + exnum + ',"gid":' + choose_id + '}]}'
        }
    };

    var link = document.getElementById('wx_link').value
    var data = BASE64.encoder(str);
    link = link.replace(/state=/gm,'state='+data);
    location.href = link
}

//叮呤专属判断弹框
function dlConfirm(){
    var choose_id = document.getElementById('brand-id').value;
    var choose_id = choose_id.replace(/brand/, '');
    if (choose_id == '0') {
        alert('请您选择要订的水')
        return false;
    }else {
        var conFirmIS = document.getElementsByClassName('confirm-info-source');
        var conFirmBox = document.getElementsByClassName('confirm-info');
        var conFirmPoPBox = document.getElementById('confirm-whole-box');

        conFirmBox[0].innerHTML = conFirmIS[0].innerHTML;
        conFirmBox[1].innerHTML = conFirmIS[1].title;
        conFirmBox[2].innerHTML = conFirmIS[2].value;

        if (conFirmIS[3].value == "price") {
            conFirmBox[3].innerHTML = "水到付现金";
        } else {
            conFirmBox[3].innerHTML = "积分免费换";
        }

        conFirmPoPBox.style.display = "block";
    }
}
//取消弹框提醒
function confirmCancel(){
    var conFirmPoPBox = document.getElementById('confirm-whole-box');
    conFirmPoPBox.style.display = "none";
}
