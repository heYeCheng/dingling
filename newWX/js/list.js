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
        var tr, td1,td2,td3,td4;

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

                        tr.className = "table-body-back";

                        td1.width = "30%";
                        td1.innerHTML = data[i]['created'];

                        td2.width = "25%";
                        td2.innerHTML = data[i]['g_name'];

                        td3.width = "15%";
                        td3.innerHTML = "1";
                        if (data[i]['pay_type'] == 0) {
                            td4.innerHTML = "预付提水 -" + data[i]['total_fee'];
                        }else if (data[i]['pay_type'] == 1) {
                            td4.innerHTML = "提水";
                        }else if (data[i]['pay_type'] == 2) {
                            td4.innerHTML = "现金消费 -" + data[i]['total_fee'];
                        }else if (data[i]['pay_type'] == 3) {
                            td4.innerHTML = "积分消费 -" + data[i]['consume_points'];
                        }
                        tr.appendChild(td1);
                        tr.appendChild(td2);
                        tr.appendChild(td3);
                        tr.appendChild(td4);

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