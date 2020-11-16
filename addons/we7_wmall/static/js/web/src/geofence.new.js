define(["jquery", "clockpicker"], function($) {
	var geofence = {
		polygons: {
			normal: {},
			special: {}
		},
		colors: {
			1: {
				strokeColor: '#4589ef',
				fillColor: '#71a3ef',
			},
			2: {
				strokeColor: '#1ebd4f',
				fillColor: '#1ecb54',
			},
			3: {
				strokeColor: '#06954b',
				fillColor: '#41ad73',
			},
			4: {
				strokeColor: '#9a6a38',
				fillColor: '#b38f66',
			},
			5: {
				strokeColor: '#6b543c',
				fillColor: '#917e6a',
			},
			6: {
				strokeColor: '#4589ef',
				fillColor: '#71a3ef',
			},
			7: {
				strokeColor: '#1ebd4f',
				fillColor: '#1ecb54',
			},
			8: {
				strokeColor: '#06954b',
				fillColor: '#41ad73',
			},
			9: {
				strokeColor: '#9a6a38',
				fillColor: '#b38f66',
			},
			10: {
				strokeColor: '#6b543c',
				fillColor: '#917e6a',
			}
		},
		areas: {

		}
	};
	geofence.init = function(params){
		var map = new AMap.Map('allmap', {
			resizeEnable: true,
			zoom: 14,
			doubleClickZoom: false,
			center: [params.store.location_y, params.store.location_x]
		});
		map.addControl(new AMap.ToolBar());
		window.map = map;
		window.tmodtpl = params.tmodtpl;
		geofence.isChange = params.isChange;
		geofence.store = params.store;
		if(!geofence.length(params.areas)) {
			params.areas = {
				normal: {
					M1234567891001: {
						startHour: '00:00',
						endHour: '00:00',
						areas: {
						}
					}
				},
				special: {}
			};
		} else {
			if(!geofence.length(params.areas.normal)) {
				params.areas.normal = {
					M1234567891001: {
						startHour: '00:00',
						endHour: '00:00',
						areas: {
						}
					}
				}
			}
			if(!geofence.length(params.areas.special)) {
				params.areas.special = {};
			}
		}
		geofence.areas = params.areas;
		geofence.areasOriginal = params.areas;
		geofence.tplArea();
		geofence.tplEditor();
		geofence.initDom();
	};

	geofence.tplArea = function() {
		var html = tmodtpl("tpl-area", geofence);
		$(".geofence-container").html(html);
	};

	geofence.markerStore = function() {
		if(geofence.store.location_y && geofence.store.location_x) {
			var marker = new AMap.Marker({
				position: [geofence.store.location_y, geofence.store.location_x],
				offset: new AMap.Pixel(-10, -36),
				content: '<div class="marker-start-head-route"></div>'
			});
			marker.setMap(map);
		}
	};

	geofence.tplEditor = function() {
		$('.clockpicker :text').clockpicker({
			autoclose: true,
			afterDone: function() {
				$('.clockpicker :text').trigger('change')
				geofence.tplArea();
				geofence.tplEditor();
			}
		});
		map.clearMap();
		geofence.markerStore();
		$.each(geofence.areas, function(i, j){
			$.each(j, function(m, n){
				geofence.polygons[i][m] = {};
				$.each(n['areas'], function(k, v){
					var color = geofence.colors[v.colorType];
					var polygon = new AMap.Polygon({
						path: v.path,//设置多边形边界路径
						strokeColor: color.strokeColor, //线颜色
						strokeOpacity: 0.9, //线透明度
						strokeWeight: 3,    //线宽
						fillColor: color.fillColor, //填充色
						fillOpacity: 0.8//填充透明度
					});
					geofence.polygons[i][m][k] = polygon;
					polygon.setMap(map);
				});
			});
		});
		$(':hidden[name="areas"]').val((encodeURI(JSON.stringify((geofence.areas)))));
	};

	geofence.initDom = function() {
		$('[data-toggle="tooltip"]').tooltip()
		$(document).off('click', '.area-add');
		$(document).on('click', '.area-add', function() {
			var type = $(this).data('type');
			var parentid = $(this).data('parentid');
			if(geofence.isActive == 1) {
				return false;
			}
			var itemid = geofence.getId('M', 0);
			var num = geofence.length(geofence.areas[type][parentid]['areas']);
			if(num >= 10) {
				Notify.info("最多可添加10个！");
				return;
			}
			var index = geofence.getColor(type, parentid);
			var color = geofence.colors[index];
			geofence.isActive = 1;
			geofence.areas[type][parentid]['areas'][itemid] = {
				delivery_price: 0,
				delivery_free_price: 0,
				send_price: 0,
				description: '',
				strokeColor: color.strokeColor,
				fillColor: color.fillColor,
				isActive: 1,
				isAdd: 1,
				path: [],
				colorType: index,
			};
			if(!geofence.polygons[type][parentid]) {
				geofence.polygons[type][parentid] = {};
			}
			geofence.polygons[type][parentid][itemid] = {}
			var mouseTool = new AMap.MouseTool(map);
			var polygon = mouseTool.polygon();
			AMap.event.addListener(mouseTool, 'draw', function callback(e) {
				mouseTool.close();
				var eObject = e.obj;
				var polygonEditor= new AMap.PolyEditor(map, eObject);
				polygonEditor.open();
				geofence.areas[type][parentid]['areas'][itemid].path = eObject.getPath();
				geofence.tplArea();
				geofence.tplEditor();
			});
			geofence.tplArea();
			geofence.tplEditor();
		});

		$(document).off('click', '.area-item .editor-area-item');
		$(document).on('click', '.area-item .editor-area-item', function() {
			var type = $(this).data('type');
			var parendid = $(this).data('parentid');
			var id = $(this).data('id');
			if(!type || !parendid || !id || !geofence.polygons[type][parendid][id]) {
				return false;
			}
			geofence.isActive = 1;
			var area = geofence.areas[type][parendid]['areas'][id];
			area['isActive'] = 1;
			area['isAdd'] = 0;
			var polygonEditor= new AMap.PolyEditor(map, geofence.polygons[type][parendid][id]);
			polygonEditor.open();
			geofence.tplArea();
		});

		$(document).off('click', '.area-item .btn-reset');
		$(document).on('click', '.area-item .btn-reset', function() {
			var type = $(this).data('type');
			var parendid = $(this).data('parentid');
			var id = $(this).data('id');
			Notify.confirm("退出编辑后，此次修改将不会生效，是否确定退出？", function(){
				geofence.isActive = 0;
				var area = geofence.areas[type][parendid]['areas'][id];
				area.isActive = 0;
				if(area.isAdd == 1) {
					delete(geofence.areas[type][parendid]['areas'][id]);
				} else {
					geofence.areas[type][parendid]['areas'][id] = geofence.areasOriginal[type][parendid]['areas'][id];
				}
				geofence.tplArea();
				geofence.tplEditor();
			});
		});

		$(document).off('click', '.area-item .btn-delete');
		$(document).on('click', '.area-item .btn-delete', function() {
			var type = $(this).data('type');
			var parendid = $(this).data('parentid');
			var id = $(this).data('id');
			Notify.confirm("确定删除此区域吗？", function(){
				geofence.isActive = 0;
				delete(geofence.areas[type][parendid]['areas'][id]);
				geofence.tplArea();
				geofence.tplEditor();
			});
		});

		$(document).off('click', '.area-item .btn-save');
		$(document).on('click', '.area-item .btn-save', function() {
			var type = $(this).data('type');
			var parendid = $(this).data('parentid');
			var id = $(this).data('id');
			Notify.confirm("确定对该区域进行修改？", function(){
				var polygon = geofence.polygons[type][parendid][id];
				geofence.areas[type][parendid]['areas'][id].path = polygon.getPath();
				if(!geofence.areas[type][parendid]['areas'][id].path.length) {
					Notify.info("请设置配送范围！");
					return;
				}
				geofence.areas[type][parendid]['areas'][id].isActive = 0;
				geofence.isActive = 0;
				geofence.tplArea();
				geofence.tplEditor();
			});
		});
		$(document).off('click', '#add-hour');
		$(document).on('click', '#add-hour', function() {
			var type = 'special';
			var parentid = geofence.getId('M', 0);
			if(geofence.isActive == 1) {
				return false;
			}
			var itemid = geofence.getId('M', 0);
			var num = geofence.length(geofence.areas[type]);
			if(num >= 10) {
				Notify.info("最多可添加10个特殊时段！");
				return;
			}
			var index = geofence.getColor(type, parentid);
			var color = geofence.colors[index];
			geofence.isActive = 1;
			geofence.areas[type][parentid] = {
				startHour: '00:00',
				endHour: '00:00',
				areas: {}
			};
			geofence.areas[type][parentid]['areas'][itemid] = {
				delivery_price: 0,
				delivery_free_price: 0,
				send_price: 0,
				description: '',
				strokeColor: color.strokeColor,
				fillColor: color.fillColor,
				isActive: 1,
				isAdd: 1,
				path: [],
				colorType: index,
			};

			var temp = {};
			temp[itemid] = {};
			if(!geofence.polygons[type][parentid]) {
				geofence.polygons[type][parentid] = {};
			}
			geofence.polygons[type][parentid][itemid] = {};
			var mouseTool = new AMap.MouseTool(map);
			var polygon = mouseTool.polygon();

			AMap.event.addListener(mouseTool, 'draw', function callback(e) {
				mouseTool.close();
				var eObject = e.obj;
				var polygonEditor= new AMap.PolyEditor(map, eObject);
				polygonEditor.open();
				geofence.areas[type][parentid]['areas'][itemid].path = eObject.getPath();
				geofence.tplArea();
				geofence.tplEditor();
			});

			geofence.tplArea();
			geofence.tplEditor();
		});

		$(document).on('input propertychange change', '.diy-bind', function() {
			var _this = $(this);
			var bind = _this.data("bind");
			var bindtype = _this.data("bind-type");
			var bindancestor = _this.data("bind-ancestor");
			var bindchild = _this.data('bind-child');
			var bindparent = _this.data('bind-parent');
			var value = '';
			var tag = this.tagName;
			if (tag == 'INPUT') {
				var placeholder = _this.data('placeholder');
				value = _this.val();
				value = value == '' ? placeholder : value;
			} else if (tag == 'SELECT') {
				value = _this.find('option:selected').val();
			} else if (tag == 'TEXTAREA') {
				value = _this.val();
			}
			value = $.trim(value);
			if(bindtype) {
				geofence.areas[bindtype][bindancestor][bindchild][bindparent][bind] = value;
			} else {
				if(bindancestor) {
					geofence.areas[bindancestor][bindchild][bindparent][bind] = value;
				} else {
					if(bindchild) {
						geofence.areas[bindchild][bindparent][bind] = value;
					} else {
						if(bindparent) {
							geofence.areas[bindparent][bind] = value;
						} else {
							geofence.areas[bind] = value;
						}
					}
				}
			}
		});
	};

	geofence.getColor = function(type, parentid) {
		var arr = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
		if(geofence.areas[type][parentid] && geofence.areas[type][parentid]['areas']) {
			for(var i in geofence.areas[type][parentid]['areas']) {
				var index = $.inArray(geofence.areas[type][parentid]['areas'][i]['colorType'], arr);
				if(index != -1) {
					arr.splice(index, 1);
				}
			}
		}
		return arr.shift();
	};

	geofence.length = function(json) {
		if(typeof(json) === 'undefined') {
			return 0;
		}
		var len = 0;
		for(var i in json) {
			len++;
		}
		return len;
	};

	geofence.getId = function(S, N) {
		var date = +new Date();
		var id = S + (date + N);
		return id;
	};
	return geofence;
});