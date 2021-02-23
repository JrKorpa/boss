$import(['public/js/highcharts/js/highcharts.js','public/js/highcharts/js/highcharts-3d.js','public/js/highcharts/js/modules/exporting.js','public/js/highcharts/js/highcharts-zh_CN.js'],function(){
	$.post('index.php?mod=management&con=main&act=getChart1',function(data){
		$('#high_chart_01').highcharts({
			title: {
				text: '销售比较图'
			},
			xAxis: {
				categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun','Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
			},
			yAxis: {
				title: {
					text: '单位 (：万元)'
				},
				min:0,
				plotLines: [{
					value: 0,
					width: 1,
					color: '#808080'
				}]
			},
//			plotOptions: {
//				line: {
//					dataLabels: {
//						enabled: true
//					},
//					enableMouseTracking: true
//				}
//			},
			tooltip: {
				valueSuffix: '万'
			},
			legend: {
				layout: 'horizontal',//vertical
				align: 'center',
				verticalAlign: 'bottom',//middle
				borderWidth: 0
			},
//			credits:{
//				enabled:false
//			},	
			series: data
		});	
	});

	$.post('index.php?mod=management&con=main&act=getChart2',function(data){
		$('#high_chart_02').highcharts({
			chart: {
				backgroundColor: {
					linearGradient: [0, 0, 500, 500],
					stops: [
						[0, 'rgb(255, 255, 255)'],
						[1, 'rgb(255, 225, 255)']
					]
				},
				type: 'column'
			},

			title: {
				text: '月销售额柱状比较图'
			},
			xAxis: {
				categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun','Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
			},
			yAxis: {
				min: 0,
				title: {
					text: '单位 (万元)'
				}
			},
			tooltip: {
				headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
				pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
					'<td style="padding:0"><b>{point.y:.1f} mm</b></td></tr>',
				footerFormat: '</table>',
				shared: true,
				useHTML: true
			},
			plotOptions: {
				column: {
					pointPadding: 0.2,
					borderWidth: 0
				}
			},
			series: data
		});
	});

	$.post('index.php?mod=management&con=main&act=getChart3',function(data){
		$('#high_chart_03').highcharts({
			chart: {
				type: 'column'
			},
			title: {
				text: '2015年1月各体验店销售额柱状图'
			},
			xAxis: {
				type: 'category',
				labels: {
					rotation: -45,
					style: {
						fontSize: '13px',
						fontFamily: 'Verdana, sans-serif'
					}
				}
			},
			yAxis: {
				min: 0,
				title: {
					text: '单位 (万元)'
				}
			},
			legend: {
				enabled: false
			},
			tooltip: {
				pointFormat: '{point.y:.1f} 万'
			},
			series: [{
				name: '销售额',
				data: data,
				dataLabels: {
					enabled: true,
					rotation: -90,
					color: '#FFFFFF',
					align: 'right',
					x: 4,
					y: 10,
					style: {
						fontSize: '13px',
						fontFamily: 'Verdana, sans-serif',
						textShadow: '0 0 3px black'
					}
				}
			}]
		});
	});

	$.post('index.php?mod=management&con=main&act=getChart4',function(data){

		$('#high_chart_04').highcharts({
			chart: {
				plotBackgroundColor: null,
				plotBorderWidth: null,//null,
				plotShadow: false
			},
			title: {
				text: '各模块控制器的数量'
			},
			tooltip: {
				pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
			},
			plotOptions: {
				pie: {
					allowPointSelect: true,
					cursor: 'pointer',
					dataLabels: {
						enabled: true,
						format: '<b>{point.name}</b>: {point.percentage:.1f} %',
						style: {
							color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
						}
					}
				}
			},
			series: [{
				type: 'pie',
				name: '模块比重',
				data:data
			}]
		});
    });

	$.post('index.php?mod=management&con=main&act=getChart5',function(data){
		$('#high_chart_05').highcharts({
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
            },
            title: {
                text: '各模块控制器文件大小比重'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: false
                    },
                    showInLegend: true
                }
            },
            series: [{
                type: 'pie',
                name: '文件比重',
                data: data
            }]
        });
    });

	$.post('index.php?mod=management&con=main&act=getChart6',function(data){
		$('#high_chart_06').highcharts({
			chart: {
				plotBackgroundColor: null,
				plotBorderWidth: 0,
				plotShadow: false
			},
			title: {
				text: '<b>学历构成</b>',
				align: 'center',
				verticalAlign: 'middle',
				y: 70
			},
			tooltip: {
				pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
			},
			plotOptions: {
				pie: {
					dataLabels: {
						enabled: true,
						style: {
							color: 'black',
							textShadow: '0px 1px 2px gray'
						}
					},
					startAngle: -90,
					endAngle: 90,
					center: ['50%', '75%']
				}
			},
			series: [{
				type: 'pie',
				name: '学历比重',
				innerSize: '50%',
				data: data
			}]
		});
	});

	$.post('index.php?mod=management&con=main&act=getChart7',function(data){
		$('#high_chart_07').highcharts({
			chart: {
				type: 'pie',
				options3d: {
					enabled: true,
					alpha: 45,
					beta: 0
				}
			},
			title: {
				text: '<b>研发组人员比重</b>'
			},
			tooltip: {
				pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
			},
			plotOptions: {
				pie: {
					allowPointSelect: true,
					cursor: 'pointer',
					depth: 35,
					dataLabels: {
						enabled: true,
						format: '{point.name}'
					}
				}
			},
			series: [{
				type: 'pie',
				name: '比重',
				data: data
			}]
		});
	});

	$.post('index.php?mod=management&con=main&act=getChart8',function(data){
		var d = new Date();
		var m = d.getFullYear()+'年'+(d.getMonth()+1)+'月';
		$('#high_chart_08').highcharts({
			chart: {
				type: 'spline'
			},
			title: {
				text: '系统用户'+m+'访问数量统计图'
			},
			subtitle: {
				text: '来源: system_access_log'
			},
			xAxis: {
				
				categories: [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31],
				labels: {
					formatter: function () {
						return this.value + '日';
					}
				},
				lineColor: '#000',
				tickColor: '#000'
			},
			yAxis: {
				title: {
					text: '单位（次）'
				},
				min:0,
				labels: {
					formatter: function () {
						return this.value + '次';
					}
				}
			},
			tooltip: {
				crosshairs: true,
				shared: true
			},
			plotOptions: {
				spline: {
					marker: {
						radius: 4,
						lineColor: '#666666',
						lineWidth: 1
					}
				},
				candlestick: { lineColor: '#404048' }
			},
			series:data
		});
	
	});
});