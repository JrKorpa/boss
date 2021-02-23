$import(['public/js/echarts/echarts_src.js'],function(){
    require.config({
        paths: {
            echarts: 'public/js/echarts'
        }
    });

	require(
        [
            'echarts',
            'echarts/chart/bar',
            'echarts/chart/line',
            'echarts/chart/map'
        ],
        function (ec) {
            // --- 地图 ---
			$.post('index.php?mod=management&con=main&act=getEchart1',function(data){
				var myChart2 = ec.init(document.getElementById('ec_chart_01'));
				myChart2.setOption({
					title : {
						text: '逗逼分布图',
						subtext: '纯属虚构',
						x:'center'
					},
					tooltip : {
						trigger: 'item'
//						,
//						formatter:function(a){
//							var str = '';
//							str+=a[1]+'<br />';
//							if (a[3])
//							{
//								str+=a[3]+'人';
//								if(a[5].t)
//								{
//									str +='<br />'+a[5].t;
//								}
//							}
//							else 
//							{
//								str+='0人';
//							}
//							return str;								
//						} 
					},
					itemStyle: {
						normal: {
							// color: 各异,
							borderColor: '#fff',
							borderWidth: 1,
							areaStyle: {
								color: '#ccc'//rgba(135,206,250,0.8)
							},
							label: {
								show: false,
								textStyle: {
									color: 'rgba(139,69,19,1)'
								}
							}
						},
						emphasis: {                 // 也是选中样式
							// color: 各异,
							borderColor: 'rgba(0,0,0,0)',
							borderWidth: 1,
							areaStyle: {
								color: 'rgba(255,215,0,0.8)'
							},
							label: {
								show: false,
								textStyle: {
									color: 'rgba(139,69,19,1)'
								}
							}
						}
					},
					dataRange: {
						min: 0,
						max: 10,
						text:['多','少'],           // 文本，默认为数值文本
						calculable : false
					},
					series : [
						{
							name: '逗逼总数',
							type: 'map',
							mapType: 'china',
							data:[
								{name: '北京',value: 10},
								{name: '天津',value: 10},
								{name: '上海',value: 10},
								{name: '重庆',value: 10},
								{name: '河北',value: 8},
								{name: '河南',value: 7},
								{name: '云南',value: 6},
								{name: '辽宁',value: 5},
								{name: '黑龙江',value: 10},
								{name: '湖南',value: 3},
								{name: '安徽',value: 2},
								{name: '山东',value: 1},
								{name: '新疆',value: 10},
								{name: '江苏',value: 10},
								{name: '浙江',value: 9},
								{name: '江西',value: 8},
								{name: '湖北',value: 7},
								{name: '广西',value: 6},
								{name: '甘肃',value: 5},
								{name: '山西',value: 4},
								{name: '内蒙古',value: 3},
								{name: '陕西',value: 2},
								{name: '吉林',value: 1},
								{name: '福建',value: 0},
								{name: '贵州',value: 10},
								{name: '广东',value: 9},
								{name: '青海',value: 8},
								{name: '西藏',value: 7},
								{name: '四川',value: 6},
								{name: '宁夏',value: 5},
								{name: '海南',value: 4},
								{name: '台湾',value: 3},
								{name: '香港',value: 2},
								{name: '澳门',value: 1},
								{name: '南海诸岛',value: 0}
							]
						}
					],
					animation: true
				});

			});
		}
    );
});