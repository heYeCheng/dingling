/**
 * Created by victor on 15/7/14.
 */
var myScroll,
    pullDownOffset,
    pullUpEl, pullUpOffset;

tempPage = 2;
tempPage_inte = 2;
/**
 * 滚动翻页 （自定义实现此方法）
 * myScroll.refresh();		// 数据加载完成后，调用界面更新方法
 */
function pullUpAction () {
    setTimeout(function () {
        var tBody = document.getElementById('tBody');
        var tr, td1,td2,td3,td4,td5;

        if(tBody.title=="order") {
            if (tempPage > 0) {
                $.getJSON("/person/getHis", {'page':tempPage}, function(data){
                    if (data.length < 1) {
                        tempPage = -1
                    };
                    for (var i = 0; i < data.length; i++) {
                        tr = document.createElement('tr');
                        td1 = document.createElement('td');
                        td2 = document.createElement('td');
                        td3 = document.createElement('td');
                        td4 = document.createElement('td');
                        td5 = document.createElement('td');

                        tr.className = "table-body-back";

                        td1.width = "30%";
                        td1.innerHTML = data[i]['created'];

                        td2.width = "25%";
                        td2.innerHTML = data[i]['g_name'];

                        td3.width = "15%";
                        td3.innerHTML = data[i]['num'];
                        if (data[i]['pay_type'] == 0) {
                            td4.innerHTML = "预付提水 -" + data[i]['total_fee'];
                        }else if (data[i]['pay_type'] == 1) {
                            td4.innerHTML = "提水";
                        }else if (data[i]['pay_type'] == 2) {
                            td4.innerHTML = "现金消费 -" + data[i]['total_fee'];
                        }else if (data[i]['pay_type'] == 3) {
                            td4.innerHTML = "积分消费 -" + data[i]['consume_points'];
                        }

                        if (data[i]['status'] <= 1) {  // 只要是下拉加载订单，就表示此订单不可取消
                            td5.innerHTML = '正在</br>配送';
                            td5.className = 'order-cancel-btn order-dealing';
                        }else if (data[i]['status'] == 2) {
                            td5.innerHTML = '配送</br>完成';
                            td5.className = 'order-cancel-btn order-done';
                        }
                        td5.title = 'btn';

                        tr.appendChild(td1);
                        tr.appendChild(td2);
                        tr.appendChild(td3);
                        tr.appendChild(td4);
                        tr.appendChild(td5);

                        tr.addEventListener('touchstart',touchStartPos,false);
                        tBody.appendChild(tr);
                    };
                });
                tempPage ++;
            }
        }else{
            if (tempPage_inte > 0) {
                $.getJSON("/person/getInte", {'page':tempPage_inte}, function(data){
                    if (data.length < 1) {
                        tempPage_inte = -1
                    };
                    for (var i = 0; i < data.length; i++) {
                        tr = document.createElement('tr');
                        td1 = document.createElement('td');
                        td2 = document.createElement('td');
                        td3 = document.createElement('td');

                        tr.className = "table-body-back";

                        td1.width = "30%";
                        td1.innerHTML = data[i]['time'];

                        td2.width = "45%";
                        td2.innerHTML = data[i]['status'];

                        td3.innerHTML = data[i]['cont'];


                        tr.appendChild(td1);
                        tr.appendChild(td2);
                        tr.appendChild(td3);

                        tBody.appendChild(tr);
                    };
                });
                tempPage_inte ++;
            }
        }

        myScroll.refresh();		// 数据加载完成后，调用界面更新方法 Remember to refresh when contents are loaded (ie: on ajax completion)
    }, 1000);	// <-- Simulate network congestion, remove setTimeout from production!
}

/**
 * 初始化iScroll控件
 */
function loaded() {
    pullUpEl = document.getElementById('pullUp');
    pullUpOffset = pullUpEl.offsetHeight;

    myScroll = new iScroll('wrapper', {
        scrollbarClass: 'myScrollbar', /* 重要样式 */
        useTransition: false, /* 此属性不知用意，本人从true改为false */
        topOffset: pullDownOffset,
        onRefresh: function () {
            if (pullUpEl.className.match('loading')) {
                pullUpEl.className = '';
                pullUpEl.querySelector('.pullUpLabel').innerHTML = '上拉加载更多...';
            }
        },
        onScrollMove: function () {
            if(this.y>-5){
                return;
            }else{
                if (this.y < (this.maxScrollY - 5) && !pullUpEl.className.match('flip')) {
                    pullUpEl.className = 'flip';
                    pullUpEl.querySelector('.pullUpLabel').innerHTML = '松手开始更新...';
                    this.maxScrollY = this.maxScrollY;
                } else if (this.y > (this.maxScrollY + 5) && pullUpEl.className.match('flip')) {
                    pullUpEl.className = '';
                    pullUpEl.querySelector('.pullUpLabel').innerHTML = '上拉加载更多...';
                    this.maxScrollY = pullUpOffset;
                }
            }
        },
        onScrollEnd: function () {
            if (pullUpEl.className.match('flip')) {
                pullUpEl.className = 'loading';
                pullUpEl.querySelector('.pullUpLabel').innerHTML = '加载中...';
                pullUpAction();	//调用加载新元素的方法
            }
        }
    });

    setTimeout(function () { document.getElementById('wrapper').style.left = '0'; }, 800);
}

//初始化绑定iScroll控件
document.addEventListener('touchmove', function (e) { e.preventDefault(); }, false);
document.addEventListener('DOMContentLoaded', loaded, false);

var startX = 0;
var startY = 0;
var movingX = 0;
var movingY = 0;
var movingElement;
//实现左右横拉事件--订单取消功能
function touchStartPos(){
    if(event.target.title == "btn"&&event.target.className == "order-cancel-btn"){
        tempId = event.target.id; //触发按钮对应功能函数
        var link = document.getElementById('wx_link').value
        link = link.replace(/state=/gm,'state='+tempId);
        location.href = link
    }else{
        var touch = event.targetTouches[0];
        startX = Number(touch.pageX);
        startY = Number(touch.pageY);

        //if(event.target.parentNode.title != "title") event.target.parentNode.title != "touching";
        //判断是否有打开的元素，且该元素不是当前点击元素
        var trList = document.getElementsByClassName('table-body-back');
        for(var i=0;i<trList.length;i++){
            if(trList[i]!=event.target.parentNode && trList[i].title == "moved"){
                otherTrRightMove(trList[i]);
            }
        }
        event.target.parentNode.addEventListener('touchmove',touchMoveEvent,false);
    }
}
function touchMoveEvent(e){
    if(event.target.title == "btn"){
    }else {
        var touchingEl = event.target;
        e.preventDefault();
        if (e.touches.length > 1 || e.scale && e.scale !== 1) return;
        var touch = e.touches[0];
        movingX = Number(touch.pageX);
        movingY = Number(touch.pageY);
        var changeX = (movingX - startX)/10;//获取本次滑动X轴方向上移动的数值；向左为负数，向右为正数

        var movingElement = touchingEl.parentNode;
        var leftPos = movingElement.style.left;
        var arry = leftPos.split('p',1);
        leftPos = Number(arry[0]);//获取元素当前样式的left数值；
        if (changeX < -5) {//判断为向左滑动
            var mL = leftPos+changeX-20;//获得新的left值
            if(mL<-70)mL = -70;//判断新的left值是否已经超出70px（按钮宽度），如果超出直接设成70
            if(leftPos>-70) movingElement.style.left = mL + "px";
            if(leftPos==-70){
                movingElement.title = "moved";
            }
        }else if(changeX >0){//判断为向右滑动（同理
            otherTrRightMove(movingElement);
        }
    }
}
function otherTrRightMove(e) {
    var leftPos = e.style.left;
    var arry = leftPos.split('p', 1);
    leftPos = Number(arry[0]);//获取元素当前样式的left数值；
    var mR = leftPos + 10;
    while(mR<0) {
        if (mR > 0) {
            mR = 0;
        } else {
            mR = mR + 1
        }
        e.style.left = mR + 'px';
    }
}

var orderTr = document.getElementsByClassName("table-body-back");
for(var i=0;i<orderTr.length;i++){
    orderTr[i].addEventListener('touchstart',touchStartPos,false);
}