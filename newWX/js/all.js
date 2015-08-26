/**
 * Created by victor on 15/7/7.
 */
function change(option){
    var first = option.options[0];
    var content = option.options[option.selectedIndex].text;
    var currentSchool = document.getElementsByClassName("current-select")[0];
    var currentDistrict = document.getElementsByClassName("current-select")[1];

    first.disabled = true;
    if(option.title=="school"){
        currentSchool.innerHTML = content;
        currentSchool.className = "current-select selected-color";
    }else{
        currentDistrict.innerHTML = content;
        currentDistrict.className = "current-select selected-color";
    }
}

function selectBrand(){
    event.preventDefault();
    if (!event.touches.length) return;
    var dom = event.target;
    var imgList = document.getElementsByClassName('light');
    var brandId = document.getElementById('brand-id');

    for(var i=0; i<imgList.length; i++){
        imgList[i].src="/static/imgs/brand-light.png";
        imgList[i].name="";
    }

    dom.src = "/static/imgs/brand-selected.png";
    dom.name = "selected";
    brandId.value = dom.id;
    brandId.title = dom.title;

    findSelected();
}


//找到要显示价格的元素
function findSelected(){
    var imgList = document.getElementsByClassName('light');
    var priceList = document.getElementsByClassName('brand-price');
    var integrationList = document.getElementsByClassName('brand-integration');
    var method = document.getElementById('pay-method');


    for(var i=0; i<imgList.length; i++){
        priceList[i].style.display = "none";
        integrationList[i].style.display = "none";
        if(imgList[i].name == "selected"){
            if(method.value == 'price') {
                setTimeout(_showPrice(priceList[i],'价格'), 50);
            }else{
                setTimeout(_showPrice(integrationList[i],'积分'), 50);
            }
        }
    }
}
//显示价格
function showPrice(sl, method){
    var num = document.getElementById('num-value');
    var brandV = document.getElementById('brand-value');

    brandV.value = sl.title;

    sl.innerHTML = num.value*sl.title + method;
    sl.style.display = "block";
}
function _showPrice(sl, method){
    return function(){
        showPrice(sl, method);
    }
}



//下订数量增减
function numPlus(){
    var numValue = document.getElementById('num-value');
    var num = document.getElementById('num');
    var numNow = numValue.value;
    numNow = parseInt(numNow);

    var touch = event.target;

    if(touch.title=="once"){
        if(numNow < 4){
            numNow++;
            num.innerHTML = numNow;
            numValue.value = numNow;
            if(numNow == 4){
                var plusBtnR = document.getElementById('control-right');
                plusBtnR.className = "control-right untouched";
            }
            if(numNow == 2){
                var plusBtnL = document.getElementById('control-left');
                plusBtnL.className = "control-left";
            }
        }
    }else{
        if(numNow < 60){
            numNow = numNow + 5;
            num.innerHTML = numNow;
            numValue.value = numNow;
            if(numNow == 60){
                var plusBtnR = document.getElementById('control-right');
                plusBtnR.className = "control-right untouched";
            }
            if(numNow == 20){
                var plusBtnL = document.getElementById('control-left');
                plusBtnL.className = "control-left";
            }
        }
    }
    findSelected();

}

function numMinus(){
    var numValue = document.getElementById('num-value');
    var num = document.getElementById('num');
    var numNow = numValue.value;
    numNow = parseInt(numNow);

    var touch = event.target;

    if(touch.title=="once") {
        if (numNow > 1) {
            numNow--;
            num.innerHTML = numNow;
            numValue.value = numNow;
            if (numNow == 1) {
                var plusBtnL = document.getElementById('control-left');
                plusBtnL.className = "control-left untouched";
            }
            if (numNow == 3) {
                var plusBtnR = document.getElementById('control-right');
                plusBtnR.className = "control-right";
            }
        }
    }else{
        if (numNow > 15) {
            numNow = numNow-5;
            num.innerHTML = numNow;
            numValue.value = numNow;
            if (numNow == 15) {
                var plusBtnL = document.getElementById('control-left');
                plusBtnL.className = "control-left untouched";
            }
            if (numNow == 55) {
                var plusBtnR = document.getElementById('control-right');
                plusBtnR.className = "control-right";
            }
        }
    }
    findSelected();
}

//提取数量增减
function numPlusEx(){
    var numValue = document.getElementById('extract-num-value');
    var num = document.getElementById('num-extract');
    var numNow = numValue.value;
    numNow = parseInt(numNow);

    if(numNow < 4){
        numNow++;
        num.innerHTML = numNow;
        numValue.value = numNow;
        if(numNow == 4){
            var plusBtnR = document.getElementById('control-right-extract');
            plusBtnR.className = "control-right untouched";
        }
        if(numNow == 2){
            var plusBtnL = document.getElementById('control-left-extract');
            plusBtnL.className = "control-left";
        }
    }
    findSelected();
}
function numMinusEx(){
    var numValue = document.getElementById('extract-num-value');
    var num = document.getElementById('num-extract');
    var numNow = numValue.value;
    numNow = parseInt(numNow);


    if (numNow > 1) {
        numNow--;
        num.innerHTML = numNow;
        numValue.value = numNow;
        if (numNow == 1) {
            var plusBtnL = document.getElementById('control-left-extract');
            plusBtnL.className = "control-left untouched";
        }
        if (numNow == 3) {
            var plusBtnR = document.getElementById('control-right-extract');
            plusBtnR.className = "control-right";
        }
    }
    findSelected();
}


function integrationCheck(){
    var selected = document.getElementById('check-selected');
    var method = document.getElementById('pay-method');

    if(selected.style.display == 'none'){
        selected.style.display = 'block';
        method.value = 'integration';
        findSelected();
    }else{
        selected.style.display = 'none';
        method.value = 'price';
        findSelected();
    }

}
//跳转到订水下单页面
function atOnce(){
    //配置上服务器，修改未对应的路径
    setTimeout(realJump, 500);
}
function realJump(){
    window.location.href ="order.html";
}
