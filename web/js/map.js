
var Map = {
		
	id_map: null,
	mapkey: null,
	rasterWidth: null,
	rasterHeight: null,
	
	svg: null,
	mainGroup: null,
	scale: 1,
	offsetX: 0,
	offsetY: 0,
	iconSize: 20,
	markerSize: 24,
	infoWindowOffset: 40,

	clickAction: null,
	mouseMoveAction: null,
	afterDrag: false,
	
	view: {
		linkPoints:		false,
		beacons:		false,
		routePoints:	false,
		locations:		false,
		locationRegions:false,
		heatMap:		false
	},
	
	periodicLoadDataTimer: null,
	
	/** инициализация карты */
	init: function() {
		// предустановка размеров иконок / маркеров
		Map.markerSize = Math.round(Math.max(Map.rasterWidth, Map.rasterHeight) / 100);
		Map.iconSize = Math.round(Math.max(Map.rasterWidth, Map.rasterHeight) / 75);
		// создаём основную группу элементов, именно эту группу мы будем транслировать и зумить
		Map.mainGroup = Map.svg.group({ 'class': 'main-group' });		
		// инициализация панораминга ... 
		$(Map.svg.root()).mousedown(function (event) {
			Map.afterDrag = false;
			event.preventDefault()
			if (event.toElement != null && typeof event.toElement != 'undefined') {
				var element = $(event.toElement);
			} else if (event.relatedTarget != null && typeof event.relatedTarget != 'undefined') {
				var element = $(event.relatedTarget);
			} else {
				var element = $(event.target);
			}
			if (element.attr('class') == HeatMap.cls.heatZone
				|| element.attr('class') == LocationRegion.cls.path) {
				element = $('.raster', Map.svg.root());
			}			
			if (element.attr('class') == 'raster' && event.button == 0) {
				element.attr('data-drag-mode', 'on'); 
				var offset = Map.getEventOffset(event);
				element.attr('data-drag-start-x', offset.x);
				element.attr('data-drag-start-y', offset.y);
			}
			if (event.button == 2 && Map.clickAction != null) {
				var menuFunc = function(e) {
					$(document).unbind("contextmenu", menuFunc);
					return false;
				};
				$(document).bind("contextmenu", menuFunc);
				Map.clickAction = null;
				Map.mouseMoveAction = null;
				Map.clearEditTools();
				$('.btn-edit-mode').removeClass('active');
				return true; 
			}
		});
		var checkDragEnd = function (event) {
			var element = $('.raster', Map.svg.root());
			if (element.attr('data-drag-mode') == 'on' && event.button == 0) {
				element.removeAttr('data-drag-mode');
				element.removeAttr('data-drag-start-x');
				element.removeAttr('data-drag-start-y');
			}
		};
		$(Map.svg.root()).mouseup(checkDragEnd);
		$(Map.svg.root()).mouseout(checkDragEnd);
		// ... и зуминга
		$(Map.svg.root()).mousewheel(function(event) {
			var scaleOld = Map.scale;
			Map.scale = Map.scale * (1 +  event.deltaY  / 15);
			if (Map.scale > 2) { Map.scale = 2; }
			var offset = Map.getEventOffset(event);
			var coord = Map.coordViewToImage(offset.x, offset.y);
			Map.offsetX = Map.offsetX + (scaleOld  - Map.scale) * coord.x;
			Map.offsetY = Map.offsetY + (scaleOld  - Map.scale) * coord.y;
			
			Map.redraw();
			return false;
		});
		// рисуем растр
		Map.svg.rect(Map.mainGroup, 0, 0, Map.rasterWidth, Map.rasterHeight, {fill: 'white', strokeWidth: 0}); 
		Map.svg.image(Map.mainGroup, 0, 0, Map.rasterWidth, Map.rasterHeight, '/map-backend/get-raster/?id_map=' + Map.id_map + '&mapkey=' + Map.mapkey, { 'class': 'raster' });
		// определяем оптимальный м асштаб
		Map.scale = Math.min($('.map').width() / Map.rasterWidth, $('.map').height() / Map.rasterHeight);
		// навешиваем дополнительные обработчики мыши
		$(Map.svg.root()).click(Map.doProcessClick);
		$(Map.svg.root()).mousemove(Map.doProcessMouseMove);
		// обработчик ESC
		$(document).keyup(function(event) {
    		if (event.which == 27) {
    			if (Map.clickAction != null) {
    				Map.clickAction = null;
					Map.mouseMoveAction = null;
					Map.clearEditTools();
					$('.btn-edit-mode').removeClass('active');
				}
    		}
    	});
		// перерисовываем карту и загружаем данные
		var view = $(Map.svg.root());
		var viewWidth = view.width();
		var viewHeight = view.height();
		Map.scale = Math.min(viewWidth / Map.rasterWidth, viewHeight / Map.rasterHeight);
		Map.redraw();
		Map.loadData();
		Map.periodicLoadData();
	},
	
	periodicLoadData: function() {
		if (Map.view.heatMap) {
			HeatMap.load();
			$('#view-heatmap').addClass('active');
		}
		Map.periodicLoadDataTimer = window.setTimeout(Map.periodicLoadData, 5000);
	},
	
	clearEditTools: function() {
		$('.' + RouteLine.cls.lineNew, Map.svg.root()).remove();
		$('.' + CalculatedRoute.cls.line, Map.svg.root()).remove();
		$('.' + CalculatedRoute.cls.point, Map.svg.root()).remove();
		$('.' + LocationRegion.cls.newPoint, Map.svg.root()).remove();
		$('.' + LocationRegion.cls.newPath, Map.svg.root()).remove();
	},
	
	doProcessClick: function(event) {
		if (Map.afterDrag) return null;
		// если мы в режиме добавления биконов
		if (Map.clickAction == 'add-beacon') {
			Beacon.doProcessClick(event);
		// если мы в режиме добавления маршрута
		} else if (Map.clickAction == 'add-route') {
			RoutePoint.doProcessClick(event);
		} else if (Map.clickAction == 'get-route' && $(event.toElement).attr('class') == 'route-point') {
			CalculatedRoute.doProcessClick(event);
		} else if (Map.clickAction == 'add-location') {
			Location.doProcessClick(event);
		} else if (Map.clickAction == 'add-region') {
			LocationRegion.doProcessClick(event);
		}
	},
	
	doProcessMouseMove: function(event) {
		var element = $('.raster', Map.svg.root());
		var offset = Map.getEventOffset(event);
		if (element.attr('data-drag-mode') == 'on') {
			Map.afterDrag = true;
			var deltaX = Number(element.attr('data-drag-start-x'));
			deltaX = deltaX ? deltaX : 0;
			var deltaY = Number(element.attr('data-drag-start-y'));
			deltaY = deltaY ? deltaY : 0;
			if (offset.x != null && offset.y != null) {
				Map.hideInfoPanel();
				Map.offsetX+= offset.x - deltaX;
				Map.offsetY+= offset.y - deltaY;
			}
			element.attr('data-drag-start-x', offset.x);
			element.attr('data-drag-start-y', offset.y);
			Map.redraw();
		// Если мы рисуем новую маршрутную линию
		} else if (Map.mouseMoveAction == RouteLine.cls.lineNew) {
			var line = $('.' + RouteLine.cls.lineNew, Map.svg.root());
			var coord = Map.coordViewToImage(offset.x, offset.y);
			// вычисляем знаки для отсутпов
			var x1 = Number(line.attr('x1'));
			var y1 = Number(line.attr('y1'));
			var xp = coord.x - x1 == 0 ? 0 : (coord.x - x1) / Math.abs(coord.x - x1);
			var yp = coord.y - y1 == 0 ? 0 : (coord.y - y1) / Math.abs(coord.y - y1);
			// вычисляем конечную точку (с отступами)
			line.attr('x2', coord.x - 5 * xp);
			line.attr('y2', coord.y - 5 * yp);
		} else if (Map.mouseMoveAction == LocationRegion.cls.newPath) {
			var path = $('.' + LocationRegion.cls.newPath, Map.svg.root());
			var coord = Map.coordViewToImage(offset.x, offset.y);
			// вычисляем знаки для отсутпов
			var x1 = Number(path.attr('data-last-x'));
			var y1 = Number(path.attr('data-last-y'));
			var xp = coord.x - x1 == 0 ? 0 : (coord.x - x1) / Math.abs(coord.x - x1);
			var yp = coord.y - y1 == 0 ? 0 : (coord.y - y1) / Math.abs(coord.y - y1);
			// вычисляем конечную точку (с отступами)
			path.attr('d', path.attr('data-d') + ' L' + (coord.x - 5 * xp) + ' ' + (coord.y - 5 * yp));
		}
	},
	
	coordViewToImage: function coordViewToImage(x, y) {
		var x = (x - Map.offsetX) / Map.scale;
		var y = (y - Map.offsetY) / Map.scale;
		return {x: x, y: y};
	},
	
	/** Загрузка данных */
	loadData: function() {
		if (Map.view.linkPoints) {
			LinkPoint.load();
			$('#view-link-point').addClass('active');
		}
		if (Map.view.beacons) {
			Beacon.load();
			$('#view-beacon').addClass('active');
		}
		if (Map.view.routePoints) {Map.clickAction = null;
					Map.mouseMoveAction = null;
			RoutePoint.load();
			$('#view-route').addClass('active');
		}
		if (Map.view.locations) {
			LocationRegion.load();
			$('#view-location').addClass('active');
		}
/*		if (Map.view.locationRegions) {
			//LocationRegion.load();
			//$('#view-location-regions').addClass('active');
		}*/
		
	},
			
	hideInfoPanel: function() {
		$('.info-panel').hide();
	},
	
	/** метод для выявления того, куда тыкнул пользователь. */
	getEventOffset: function(event) {
		var elementRaster = $('.raster', Map.svg.root());
		var offset = elementRaster.parent().parent().offset();
		return {
			x: (event.offsetX || event.clientX - offset.left),
			y: (event.offsetY || event.clientY - offset.top)
		};
	},
	
	/** Перерисовка карты */
	redraw: function() {
		// Ограничитель зуминга
		if (Map.scale > 3) { Map.scale = 3; }
		// собираем исходные данные
		var imageLeft = Map.offsetX;
		var imageTop = Map.offsetY;
		var imageWidth = Map.rasterWidth * Map.scale;
		var imageHeight = Map.rasterHeight * Map.scale;
		var view = $(Map.svg.root());
		var viewWidth = view.width();
		var viewHeight = view.height();
		// выбираем ограничения панораминга 
		if (imageWidth < viewWidth && imageHeight < viewHeight) {
			Map.scale = Math.min(viewWidth / Map.rasterWidth, viewHeight / Map.rasterHeight);
		}
		// отрабатываем ограничение по ширине
		if (imageWidth <= viewWidth) {
			imageLeft = (viewWidth - imageWidth) / 2;
		} else {
			if (imageLeft > 0) {
				imageLeft = 0;
			}
			if (imageLeft + imageWidth < viewWidth) {
				imageLeft = viewWidth - imageWidth;
			}
		}
		// отрабатываем ограничение по высоте
		if (imageHeight <= viewHeight) {
			imageTop = (viewHeight - imageHeight) / 2;
		} else {
			if (imageTop > 0) { imageTop = 0; }
			if (imageTop + imageHeight < viewHeight) {
				imageTop = viewHeight - imageHeight;
			}
		}
		// Запоминаем результаты
		Map.offsetX = imageLeft;
		Map.offsetY = imageTop;
		$(Map.mainGroup).attr('transform', 'translate(' + imageLeft + ', ' + imageTop + ') scale(' + Map.scale + ')');
	},

};

var LinkPoint = {

	apiBase: '/map-backend/link-point',
	
	cls: {
		point:			'link-point',
	},
	
	icon: '/img/icons/pin_link_point.png',
	
	mapClear: function () {
		$('.' + LinkPoint.cls.point, Map.svg.root()).remove();
	},
	
	load: function() {
		$.getJSON(LinkPoint.apiBase + '/list/', { id_map: Map.id_map, mapkey: Map.mapkey }, function(linkPoints) {
			// удаляем старые точки привязки
			LinkPoint.mapClear();
			// добавляем точки привязки
			for (i in linkPoints) { LinkPoint.add(linkPoints[i]); }
		});
	},

	add: function(point) {
		var point = $(Map.svg.image(Map.mainGroup, point.x - Map.iconSize / 2, point.y - Map.iconSize / 2, Map.iconSize, Map.iconSize, LinkPoint.icon, {
			'class':			LinkPoint.cls.point,
			'data-id':			point.id,
			'data-longitude':	point.longitude,
			'data-latitude':	point.latitude,
		}));
		point.mouseover(LinkPoint.onMouseOver);
		point.mouseout(Map.hideInfoPanel);
		interact('.' + LinkPoint.cls.point + '[data-id="' + point.attr('data-id') + '"]').draggable(LinkPoint.draggable);
	},
	
	onMouseOver: function(event) {
		var point = $(this);
		var infoPanel = $('.info-panel');
		infoPanel.html('<b>Точка привязки</b><br>');
		infoPanel.append('<b>lat:</b> ' + point.attr('data-latitude') + '<br>');
		infoPanel.append('<b>lng:</b> ' + point.attr('data-longitude') + '<br>');
		infoPanel.append('<b>x:</b> '	+ point.attr('x') + '<br>');
		infoPanel.append('<b>y:</b> '	+ point.attr('y') + '<br>');
		var pointOffset = point.offset();
		var x = pointOffset.left + Map.infoWindowOffset;
		var y = pointOffset.top - Map.infoWindowOffset - infoPanel.height();
		infoPanel.attr('style', 'left: ' + x + 'px; top: ' + y +'px;');
		infoPanel.show();
	},
	
	onMove: function (event) {
		var point = event.target;
		// убираем лишние элементы
		Map.hideInfoPanel();
		// репозиционируем точку
		var x = Number(point.getAttribute('x')) + event.dx / Map.scale;
		var y = Number(point.getAttribute('y')) + event.dy / Map.scale;
		if (x && y) {
			point.setAttribute('x', x);
			point.setAttribute('y', y);
		}
	},	
	
	savePosition: function (event) {
		var point = $(event.target);
		$.getJSON(LinkPoint.apiBase + '/save-position/', {
			id: point.attr('data-id'),
			x: Number(point.attr('x')) + Map.iconSize / 2,
			y: Number(point.attr('y')) + Map.iconSize / 2,
			id_map: Map.id_map, mapkey: Map.mapkey}, function() {
				Map.loadData();
			});
	},
	
	draggable: {
		inertia: true,
		restrict: {
			restriction: "parent",
			endOnly: true,
			elementRect: { top: 0, left: 0, bottom: 1, right: 1 }
		},
		onmove:	function(event)	{ LinkPoint.onMove(event); },
		onend:	function(event)	{ LinkPoint.savePosition(event); }
	},

};


var Beacon = {

	apiBase: '/map-backend/beacon',
	
	cls: {
		point:			'beacon',
	},
	
	icon: '/img/icons/pin_round_beacon_blue.png',
	iconInactive: '/img/icons/pin_round_beacon_grey.png',
	
	mapClear: function () {
		$('.' + Beacon.cls.point, Map.svg.root()).remove();
	},
	
	load: function() {
		$.getJSON(Beacon.apiBase + '/list/', { id_map: Map.id_map, mapkey: Map.mapkey }, function(beacons) {
			// удаляем старые биконы
			Beacon.mapClear();
			// добавляем биконы
			for (i in beacons) { Beacon.add(beacons[i]); }
		});
	},
	
	add: function(beacon) {
		var x = beacon.x - Map.iconSize / 2;
		var y = beacon.y - Map.iconSize / 2;
		var icon = Beacon.icon;
		if (beacon.signal_power == 0) {
			icon = Beacon.iconInactive;
		}
		var point = $(Map.svg.image(Map.mainGroup, x, y, Map.iconSize, Map.iconSize, icon, {
			'class': Beacon.cls.point, 'data-id': beacon.id, 'data-major': beacon.major, 'data-minor': beacon.minor, 'data-name': beacon.beacon_name }));
		point.mouseover(Beacon.onMouseOver);
		point.mouseout(Map.hideInfoPanel);
		point.click(Beacon.onClick);
		point.dblclick(Beacon.onDblClick);
		interact('.' + Beacon.cls.point + '[data-id="' + beacon.id + '"]').draggable(Beacon.draggable);
	},
	
	remove: function(beacon) {
		$.getJSON(Beacon.apiBase + '/remove/', { id: beacon.attr('data-id'), id_map: Map.id_map, mapkey: Map.mapkey }, function(answer) {
			$('.' + Beacon.cls.selectBorder + ', '
				+ '.' + Beacon.cls.line + '[data-point-from=' + beacon.attr('data-id') + '], '
				+ '.' + Beacon.cls.line + '[data-point-to=' + beacon.attr('data-id') + ']', Map.svg.root()).remove();
			beacon.remove();
		});
	},
	
	onMouseOver: function(event) {
		var beacon = $(this);
		var infoPanel = $('.info-panel');
		infoPanel.html('<b>Beacon</b><br>');
		infoPanel.append('<b>name:</b> '	+ beacon.attr('data-name') + '<br>');
		infoPanel.append('<b>id:</b> '	+ beacon.attr('data-major') + '.' + beacon.attr('data-minor') + '<br>');
		infoPanel.append('<b>x:</b> '	+ beacon.attr('x') + '<br>');
		infoPanel.append('<b>y:</b> '	+ beacon.attr('y') + '<br>');
		var beaconOffset = beacon.offset();
		var x = beaconOffset.left + Map.infoWindowOffset;
		var y = beaconOffset.top - Map.infoWindowOffset - infoPanel.height();
		infoPanel.attr('style', 'left: ' + x + 'px; top: ' + y +'px;');
		infoPanel.show();
	},
		
	onDblClick: function () {
		TableFormActions.getForm('beacon', { id_map: Map.id_map, id_beacon: $(this).attr('data-id') }, function() { $('#mainModal').modal('hide'); Beacon.load(); } );
	},
	
	onMove: function (event) {
		var point = event.target;
		// убираем лишние элементы
		Map.hideInfoPanel();
		// репозиционируем точку
		var x = Number(point.getAttribute('x')) + event.dx / Map.scale;
		var y = Number(point.getAttribute('y')) + event.dy / Map.scale;
		if (x && y) {
			point.setAttribute('x', x);
			point.setAttribute('y', y);
		}
	},	
	
	savePosition: function (event) {
		var point = $(event.target);
		$.getJSON(Beacon.apiBase + '/save-position/', {
			id: point.attr('data-id'),
			x: Number(point.attr('x')) + Map.iconSize / 2,
			y: Number(point.attr('y')) + Map.iconSize / 2,
			id_map: Map.id_map, mapkey: Map.mapkey});
	},
	
	draggable: {
		inertia: true,
		restrict: {
			restriction: "parent",
			endOnly: true,
			elementRect: { top: 0, left: 0, bottom: 1, right: 1 }
		},
		onmove:	function(event)	{ Beacon.onMove(event); },
		onend:	function(event)	{ Beacon.savePosition(event); }
	},
	
	doProcessClick: function (event) {
		var offset = Map.getEventOffset(event);
		var point = Map.coordViewToImage(offset.x, offset.y);
		TableFormActions.getForm('beacon', { id_map: Map.id_map, mapkey: Map.mapkey, x: point.x, y: point.y }, function() {
			$('#mainModal').modal('hide');
			Beacon.load();
		});
	},
	
	getById: function(id) {
		return $('.' + Beacon.cls.point + '[data-id="' + id + '"]', Map.svg.root());
	}
	
}


var Beacon4Select = {

	apiBase: '/map-backend/beacon',
	
	cls: {
		point:			'beacon',
	},
	
	widgetId: null,
	
	icon: '/img/icons/pin_round_beacon_blue.png',
	iconInactive: '/img/icons/pin_round_beacon_grey.png',
	iconSelected: '/img/icons/pin_round_beacon_red.png',
	
	mapClear: function () {
		$('.' + Beacon4Select.cls.point, Map.svg.root()).remove();
	},
	
	load: function() {
		$.getJSON(Beacon4Select.apiBase + '/list/', { id_map: Map.id_map, mapkey: Map.mapkey }, function(beacons) {
			// удаляем старые биконы
			Beacon4Select.mapClear();
			// добавляем биконы
			for (i in beacons) { Beacon4Select.add(beacons[i]); }
		});
	},
	
	add: function(beacon) {
		var x = beacon.x - Map.iconSize / 2;
		var y = beacon.y - Map.iconSize / 2;
		var icon = Beacon4Select.icon;
		
		var selected = 0;		
		if (beacon.signal_power == 0) {
			icon = Beacon4Select.iconInactive;
		}
		
		if (($('#' + Beacon4Select.widgetId).val() + '').split(',').indexOf(beacon.id) !== -1) {
			icon = Beacon4Select.iconSelected;
			selected = 1;
		}
		
		var point = $(Map.svg.image(Map.mainGroup, x, y, Map.iconSize, Map.iconSize, icon, {
			'class': Beacon4Select.cls.point, 'data-id': beacon.id, 'data-major': beacon.major, 'data-minor': beacon.minor,
			'data-name': beacon.beacon_name, 'data-selected': selected, 'data-power': beacon.signal_power}));
		point.mouseover(Beacon4Select.onMouseOver);
		point.mouseout(Map.hideInfoPanel);
		point.click(Beacon4Select.onClick);
		point.dblclick(Beacon4Select.onDblClick);
	},
	
	onMouseOver: function(event) {
		var beacon = $(this);
		var infoPanel = $('.info-panel');
		infoPanel.html('<b>Beacon</b><br>');
		infoPanel.append('<b>name:</b> '	+ beacon.attr('data-name') + '<br>');
		infoPanel.append('<b>id:</b> '	+ beacon.attr('data-major') + '.' + beacon.attr('data-minor') + '<br>');
		infoPanel.append('<b>x:</b> '	+ beacon.attr('x') + '<br>');
		infoPanel.append('<b>y:</b> '	+ beacon.attr('y') + '<br>');
		var beaconOffset = beacon.offset();
		var x = beaconOffset.left + Map.infoWindowOffset;
		var y = beaconOffset.top - Map.infoWindowOffset - infoPanel.height();
		infoPanel.attr('style', 'left: ' + x + 'px; top: ' + y +'px;');
		infoPanel.show();
	},
	
	onClick: function (event) {
		if ($(this).attr('data-selected') == '0') {
			$(this).attr('data-selected', 1);
			$(this).attr('href', Beacon4Select.iconSelected);
		} else {
			$(this).attr('data-selected', 0);
			if ($(this).attr('data-power') == '0') {
		
				$(this).attr('href', Beacon4Select.iconInactive);
			} else {
				$(this).attr('href', Beacon4Select.icon);
			}
		}
		var beacons = [];
		$('image.' + Beacon4Select.cls.point + '[data-selected="1"]', Map.svg.root()).each(function () {
			beacons.push($(this).attr('data-id'));
		});
		$('#' + Beacon4Select.widgetId).val(beacons.join(','));
	},
	
	getById: function(id) {
		return $('.' + Beacon4Select.cls.point + '[data-id="' + id + '"]', Map.svg.root());
	}
}

var RoutePoint = {

	apiBase: '/map-backend/route-point',
	
	cls: {
		locationLink:	'route-location-link',
		point:			'route-point',
		selectBorder:	'route-point-border'
	},
	
	icon: '/img/icons/pin_round_point_grey.png',
	iconEnd: '/img/icons/pin_round_point_green.png',
	
	activeRoutePoint: null,
	
	mapClear: function () {
		$('.' + RoutePoint.cls.point, Map.svg.root()).remove();
		$('.' + RouteLine.cls.line, Map.svg.root()).remove();
	},
	
	load: function() {
		$.getJSON(RoutePoint.apiBase + '/list/', { id_map: Map.id_map, mapkey: Map.mapkey }, function(routePoints) {
			// удаляем старые точки и пути
			RoutePoint.mapClear();
			// рисуем пути
			for (from in routePoints) {
				var pointFrom = routePoints[from];
				for (j in pointFrom.routes) {
					var to = pointFrom.routes[j];
					var pointTo = routePoints[to];
					if ($.inArray(from, pointTo.routes) >= 0) {
						RouteLine.addBoth(pointFrom, pointTo);
					} else {
						RouteLine.addOne(pointFrom, pointTo);
					}
				}
			}
			// Добавляем точки
			for (i in routePoints) { RoutePoint.add(routePoints[i]); }
			RoutePoint.doDrawLocationLink();
		});

	},
	
	add: function(routePoint) {
		var x = routePoint.x - Map.iconSize / 2;
		var y = routePoint.y - Map.iconSize / 2;
		var point = $(Map.svg.image(Map.mainGroup, x, y, Map.iconSize, Map.iconSize,
			routePoint.id_location ? RoutePoint.iconEnd : RoutePoint.icon,
			{'class': RoutePoint.cls.point, 'data-id': routePoint.id, 'data-id_location': routePoint.id_location }));
		point.mouseover(RoutePoint.onMouseOver);
		point.mouseout(RoutePoint.onMouseOut);
		point.click(RoutePoint.onClick);
		point.dblclick(RoutePoint.onDblClick);
		interact('.' + RoutePoint.cls.point + '[data-id="' + routePoint.id + '"]').draggable(RoutePoint.draggable);
	},
	
	create: function(coord, line) {
		$.getJSON(RoutePoint.apiBase + '/create/', { x: coord.x, y: coord.y, id_map: Map.id_map, mapkey: Map.mapkey }, function(answer) {
			RoutePoint.add(answer.route_point);
			var point = RoutePoint.getById(answer.route_point.id);
			if (line.attr('data-point-from')) {
				RouteLine.addLineToPoint(line, point);
			}
			// Добавляем саму линию
			RouteLine.newLine(answer.route_point);
		});
	},
	
	remove: function(point) {
		$.getJSON(RoutePoint.apiBase + '/remove/', { id: point.attr('data-id'), id_map: Map.id_map, mapkey: Map.mapkey }, function(answer) {
			$('.' + RoutePoint.cls.selectBorder + ', '
				+ '.' + RouteLine.cls.line + '[data-point-from=' + point.attr('data-id') + '], '
				+ '.' + RouteLine.cls.line + '[data-point-to=' + point.attr('data-id') + ']', Map.svg.root()).remove();
			point.remove();
		});
	},
	
	getById: function(id) {
		return $('.' + RoutePoint.cls.point + '[data-id="' + id + '"]', Map.svg.root());
	},

	onMouseOver: function() {
		var point = $(this);
		RoutePoint.activeRoutePoint = Number(point.attr('data-id'));
		var x = Number(point.attr('x')) + Map.iconSize / 2;
		var y = Number(point.attr('y')) + Map.iconSize / 2;
		var R = Map.iconSize / 3 * 2;
		Map.svg.circle(Map.mainGroup, x, y, R, { 'class': RoutePoint.cls.selectBorder }); 
	},
	
	onMouseOut: function() {
		RoutePoint.activeRoutePoint = null;
		$('.' + RoutePoint.cls.selectBorder, Map.svg.root()).remove();
	},
	
	onClick: function () {
		if (Map.clickAction == 'remove-route') {
			RoutePoint.remove($(this));
		}
	},
	
	onDblClick: function () {
		TableFormActions.getForm('route-point', { id_map: Map.id_map, id_route_point: $(this).attr('data-id') }, function() { $('#mainModal').modal('hide'); });
	},
	
	onMove: function (event) {
		var point = event.target;
		// убираем лишние элементы
		Map.hideInfoPanel();
		$('.' + RoutePoint.cls.selectBorder, Map.svg.root()).remove();
		// репозиционируем точку
		var x = Number(point.getAttribute('x')) + event.dx / Map.scale;
		var y = Number(point.getAttribute('y')) + event.dy / Map.scale;
		if (x && y) {
			point.setAttribute('x', x);
			point.setAttribute('y', y);
			// ищем линии, которые начинаются из этой точки
			$('.' + RouteLine.cls.line + '[data-point-from=' + point.getAttribute('data-id') + ']', Map.svg.root()).each(function() {
				var routeLine = $(this);
				routeLine.attr('x1', x + Map.iconSize / 2);
				routeLine.attr('y1', y + Map.iconSize / 2);
			});
			// ищем линии, которые заканчиваются в этой точке
			$('.' + RouteLine.cls.line + '[data-point-to=' + point.getAttribute('data-id') + ']', Map.svg.root()).each(function() {
				var routeLine = $(this);
				routeLine.attr('x2', x + Map.iconSize / 2);
				routeLine.attr('y2', y + Map.iconSize / 2);
			});
		}
	},
	
	draggable: {
		inertia: true,
		restrict: {
			restriction: "parent",
			endOnly: true,
			elementRect: { top: 0, left: 0, bottom: 1, right: 1 }
		},
		onmove:	function(event)	{ RoutePoint.onMove(event) },
		onend:	function(event)	{ RoutePoint.savePosition(event) }
	},
	
	savePosition: function (event) {
		var point = $(event.target);
		$.getJSON(RoutePoint.apiBase + '/save-position/', {
			id: point.attr('data-id'),
			x: Number(point.attr('x')) + Map.iconSize / 2,
			y: Number(point.attr('y')) + Map.iconSize / 2,
			id_map: Map.id_map, mapkey: Map.mapkey});
	},
	
	doProcessClick: function (event) {
		var line = $('.' + RouteLine.cls.lineNew, Map.svg.root());
		if (RoutePoint.activeRoutePoint != null) {	// если пользователь тыкнул в точку
			var pointObj = RoutePoint.getById(RoutePoint.activeRoutePoint);
			var point = {
				id:	pointObj.attr('data-id'),
				x:	Number(pointObj.attr('x')) + Map.iconSize / 2,
				y:	Number(pointObj.attr('y')) + Map.iconSize / 2
			};
			if (line.attr('data-point-from')) {	// если уже рисуется линия - добавляем связь между точками
				RouteLine.addLineToPoint(line, pointObj);
			} else {	// Добавляем новую линию
				RouteLine.newLine(point);
			}
		} else if (Location.ativeLocation != null) {	// если пользователь тыкнул в локацию
			var line = $('.' + RouteLine.cls.lineNew, Map.svg.root());
			if (line.attr('data-point-from')) {
				$.getJSON(RoutePoint.apiBase + '/save-id-location/', {
					id: line.attr('data-point-from'),
					id_location: Location.ativeLocation,
					id_map: Map.id_map, mapkey: Map.mapkey});
				var routePoint = RoutePoint.getById(line.attr('data-point-from'));
				routePoint.attr('data-id_location', Location.ativeLocation);
				RoutePoint.doDrawLocationLink();
			}
		} else if ($(event.toElement).attr('class') == RouteLine.cls.line) {	// если пользователь тыкнул в линию

		} else {	// если пользователь тыкнул в пустоту
			var offset = Map.getEventOffset(event);
			var coord = Map.coordViewToImage(offset.x, offset.y);
			RoutePoint.create(coord, line);
		}
	},
	
	doDrawLocationLink: function() {
		$('.' + RoutePoint.locationLink, Map.svg.root()).remove();
		$('.' + Location.cls.point, Map.svg.root()).each(function() {
			var obj = $(this);
			var to = { x: Number(obj.attr('x')), y: Number(obj.attr('y')), id: obj.attr('data-id') };
			$('.' + RoutePoint.cls.point + '[data-id_location="' + to.id + '"]', Map.svg.root()).each(function() {
				var obj = $(this);
				var from = { x: Number(obj.attr('x')), y: Number(obj.attr('y')), id: obj.attr('data-id') };
				Map.svg.line(Map.mainGroup, from.x + Map.iconSize / 2, from.y + Map.iconSize / 2,
					to.x + Map.iconSize / 2, to.y + Map.iconSize / 2, {
					'class': RouteLine.cls.line + ' ' + RoutePoint.cls.locationLink,
					'data-point-from': from.id, 'data-point-to': to.id});
			});
		});
	}
};

var RouteLine = {

	cls: {
		line:			'route-line',
		lineBoth:		'route-line-both',
		lineOne:		'route-line-one',
		lineNew:		'route-new-line',
		iconDirection:	'route-direction'
	},
		
	iconDirecion: '/img/icons/direction-arrow.png',
	
	addBoth: function(from, to) {
		var id = Math.min(from.id, to.id) + '-' + Math.max(from.id, to.id);
		if ($('.' + RouteLine.line + '[data-route-id="' + id + '"]', Map.svg.root()).attr('data-route-id') == id) return null;
		Map.svg.line(Map.mainGroup, from.x, from.y, to.x, to.y, { 'data-route-id': id,
			'class': RouteLine.cls.line +  ' ' + RouteLine.cls.lineBoth,
			'data-point-from': from.id, 'data-point-to': to.id});
	},
	
	addOne: function(from, to) {
		// рисуем линию
		var id = from.id + '-' + to.id;
		if ($('.' + RouteLine.line + '[data-route-id="' + id + '"]', Map.svg.root()).attr('data-route-id') == id) return null;
		Map.svg.line(Map.mainGroup, from.x, from.y, to.x, to.y, { 'data-route-id': id,
			'class': RouteLine.cls.line +  ' ' + RouteLine.cls.lineOne,
			'data-point-from': from.id, 'data-point-to': to.id});
		// определение угла поворота иконки
		var r = 0;
		var x = (from.x + to.x) / 2;
		var y = (from.y + to.y) / 2;
		if (from.y - to.y != 0) {
			r = - Math.atan((to.x - from.x) / (to.y - from.y)) * 180 / Math.PI;
		}
		if (from.y - to.y < 0) r+= 180;
		Map.svg.image(Map.mainGroup, x - Map.iconSize / 2, y - Map.iconSize / 2, Map.iconSize, Map.iconSize, RouteLine.iconDirecion, {
			'data-route-id': id, 'class': RouteLine.cls.line + ' ' + RouteLine.cls.iconDirection,
			'transform': 'rotate(' + r + ',' + x + ',' + y + ')'});
	},

	addLineToPoint: function(line, point) {
		// стучимся в API ради добавления линии
		var pointFrom = line.attr('data-point-from');
		var pointTo = point.attr('data-id');
		var from	= { id: line.attr('data-point-from'),	x: Number(line.attr('x1')),	y: Number(line.attr('y1')) };
		var to		= { id: point.attr('data-id'),
			x: Number(point.attr('x')) + Map.iconSize / 2,
			y: Number(point.attr('y')) + Map.iconSize / 2
		};
		$.getJSON(RoutePoint.apiBase + '/add-link/', { id_map: Map.id_map, mapkey: Map.mapkey,
			id_route_point_from: from.id, id_route_point_to: to.id, is_both: true}, function () {
			RouteLine.addBoth(from, to);
			line.remove();
			RouteLine.newLine(to);
		});
	},
	
	newLine: function (from) {
		Map.svg.line(Map.mainGroup, from.x, from.y, from.x + 1, from.y + 1, {
			'class': RouteLine.cls.lineNew, 'data-point-from': from.id});
		Map.mouseMoveAction = RouteLine.cls.lineNew;
	}
	
};

var CalculatedRoute = {
	
	cls: {
		point: 'route-point-target',
		line: 'calculated-route'
	},

	routeFrom: null,
	routeTo: null,
	
	mapClear: function () {
		$('.' + CalculatedRoute.cls.point, Map.svg.root()).remove();
		$('.' + CalculatedRoute.cls.line, Map.svg.root()).remove();
	},

	load: function() {
		$.getJSON(RoutePoint.apiBase + '/calculate-route/', { id_map: Map.id_map, mapkey: Map.mapkey, from: CalculatedRoute.routeFrom, to: CalculatedRoute.routeTo }, function(routePoints) {
			CalculatedRoute.mapClear();
			var points = [];
			for (i in routePoints) {
				var point = RoutePoint.getById(routePoints[i]);
				points.push([Number(point.attr('x')) + Map.iconSize / 2, Number(point.attr('y')) + Map.iconSize / 2]);
			}
			Map.svg.polyline(Map.mainGroup, points, { 'class': CalculatedRoute.cls.line, 'data-route': routePoints });
		});
	},
	
	doProcessClick: function(event) {
		var point = $(event.toElement);
		if (CalculatedRoute.routeFrom == null || CalculatedRoute.routeTo != null) {
			CalculatedRoute.mapClear();
			CalculatedRoute.routeFrom = point.attr('data-id');
			CalculatedRoute.routeTo = null;
		} else {
			CalculatedRoute.routeTo = point.attr('data-id');
			if (CalculatedRoute.routeFrom != CalculatedRoute.routeTo) {
				CalculatedRoute.load();
			}
		}
		var x = Number(point.attr('x')) + Map.iconSize / 2;
		var y = Number(point.attr('y')) + Map.iconSize / 2;
		var R = Map.iconSize / 3 * 2;
		Map.svg.circle(Map.mainGroup, x, y, R, { 'class': CalculatedRoute.cls.point });
	}

};

var HeatMap = {

	apiBase: '/map-backend/heatmap-raster/',
	
	cls: {
		heatZone: 'heatmap-zone',
		gradientsId: 'heatmap-gradient'
	},

	mapClear: function () {
		$('.' + HeatMap.cls.heatZone, Map.svg.root()).remove();
		$('#' + HeatMap.cls.gradientsId, Map.svg.root()).remove();
	},

	load: function() {
		var requestData = { id_map: Map.id_map, mapkey: Map.mapkey };
		var filters = $('#heatmap-filter');
		if (filters) {
			requestData['id_mobile_os'] = filters.find('#filters_id_mobile_os').val();
			requestData['id_sex'] = filters.find('#filters_id_sex').val();
			requestData['id_age_range'] = filters.find('#filters_id_age_range').val();
			requestData['period'] = filters.find('#filters_period').val();
		}
		$.get(HeatMap.apiBase, requestData, function(heatmap) {
			HeatMap.mapClear();
			Map.svg.image(Map.mainGroup, - Map.rasterWidth * 2,  - Map.rasterHeight * 2, Map.rasterWidth * 5, Map.rasterHeight * 5, heatmap, { 'class': HeatMap.cls.heatZone,  opacity: '0.6' });
		});
	},
}

var Location = {

	apiBase: '/map-backend/location',
	
	cls: {
		point:			'location-point',
		selectBorder:	'location-point-border'
	},
	
	icon: '/img/icons/pin_round_location_red.png',
	
	ativeLocation: null,
	
	mapClear: function () {
		$('.' + Location.cls.point, Map.svg.root()).remove();
	},
	
	load: function() {
		$.getJSON(Location.apiBase + '/list/', { id_map: Map.id_map, mapkey: Map.mapkey }, function(locations) {
			Location.mapClear();
			for (i in locations) {
				Location.add(locations[i]);
			}
			RoutePoint.doDrawLocationLink();
		});
	},
	
	add: function(location) {
		var x = location.x - Map.iconSize / 2;
		var y = location.y - Map.iconSize / 2;
		var point = $(Map.svg.image(Map.mainGroup, x, y, Map.iconSize, Map.iconSize, Location.icon, {
			'class': Location.cls.point, 'data-id': location.id, 'data-name': location.name, }));
		point.mouseover(Location.onMouseOver);
		point.mouseout(function () {
			Location.ativeLocation = null;
			Map.hideInfoPanel();
		});
		// point.click(Location.onClick);
		point.dblclick(Location.onDblClick);
		interact('.' + Location.cls.point + '[data-id="' + location.id + '"]').draggable(Location.draggable);
	},
	
	remove: function(location) {
		$.getJSON(Location.apiBase + '/remove/', { id: location.attr('data-id'), id_map: Map.id_map, mapkey: Map.mapkey }, function(answer) {
			$('.' + Location.cls.selectBorder, Map.svg.root()).remove();
			location.remove();
		});
	},
	
	onMouseOver: function(event) {
		var location = $(this);
		var infoPanel = $('.info-panel');
		infoPanel.html('<b>Location</b><br>');
		infoPanel.append('<b>id:</b> '	+ location.attr('data-name') + '<br>');
		infoPanel.append('<b>x:</b> '	+ location.attr('x') + '<br>');
		infoPanel.append('<b>y:</b> '	+ location.attr('y') + '<br>');
		var locationOffset = location.offset();
		var x = locationOffset.left + Map.infoWindowOffset;
		var y = locationOffset.top - Map.infoWindowOffset - infoPanel.height();
		infoPanel.attr('style', 'left: ' + x + 'px; top: ' + y +'px;');
		infoPanel.show();
		Location.ativeLocation = location.attr('data-id');
	},
		
	onDblClick: function () {
		TableFormActions.getForm('location', { id_map: Map.id_map, id_location: $(this).attr('data-id') }, function() { $('#mainModal').modal('hide'); });
	},
	
	onMove: function (event) {
		var point = event.target;
		// убираем лишние элементы
		Map.hideInfoPanel();
		// репозиционируем точку
		var x = Number(point.getAttribute('x')) + event.dx / Map.scale;
		var y = Number(point.getAttribute('y')) + event.dy / Map.scale;
		if (x && y) {
			point.setAttribute('x', x);
			point.setAttribute('y', y);
			// ищем линии, которые заканчиваются в этой точке
			$('.' + RouteLine.cls.line + '[data-point-to=' + point.getAttribute('data-id') + ']', Map.svg.root()).each(function() {
				var routeLine = $(this);
				routeLine.attr('x2', x + Map.iconSize / 2);
				routeLine.attr('y2', y + Map.iconSize / 2);
			});
		}
	},	
	
	savePosition: function (event) {
		var point = $(event.target);
		$.getJSON(Location.apiBase + '/save-position/', {
			id: point.attr('data-id'),
			x: Number(point.attr('x')) + Map.iconSize / 2,
			y: Number(point.attr('y')) + Map.iconSize / 2,
			id_map: Map.id_map, mapkey: Map.mapkey});
	},
	
	draggable: {
		inertia: true,
		restrict: {
			restriction: "parent",
			endOnly: true,
			elementRect: { top: 0, left: 0, bottom: 1, right: 1 }
		},
		onmove:	function(event)	{ Location.onMove(event); },
		onend:	function(event)	{ Location.savePosition(event); }
	},
	
	doProcessClick: function (event) {
		var offset = Map.getEventOffset(event);
		var point = Map.coordViewToImage(offset.x, offset.y);
		TableFormActions.getForm('location', { id_map: Map.id_map, mapkey: Map.mapkey, x: point.x, y: point.y  }, function() {
			$('#mainModal').modal('hide');
			Location.load();
		});
	},
	
	getById: function(id) {
		return $('.' + Location.cls.point + '[data-id="' + id + '"]', Map.svg.root());
	}
	
};


var LocationRegion = {

	apiBase: '/map-backend/location-region',
	
	cls: {
		point:	'location-region-point',
		newPoint:'location-region-new-point',
		path:	'location-region-path',
		newPath:'location-region-new-path'
	},
	
	activeMarker: null,
	regionForLocation: null,
	
	mapClear: function () {
		$('.' + LocationRegion.cls.point, Map.svg.root()).remove();
		$('.' + LocationRegion.cls.path, Map.svg.root()).remove();
		$('.' + LocationRegion.cls.newPath, Map.svg.root()).remove();
	},
	
	load: function() {
		$.getJSON(LocationRegion.apiBase + '/list/', { id_map: Map.id_map, mapkey: Map.mapkey }, function(data) {
			LocationRegion.mapClear();
			for (i in data['points']) {
				LocationRegion.addPoint(data['points'][i]);
			}
			for (i in data['regions']) {
				LocationRegion.addRegion(data['regions'][i]);
			}
			$('.' + LocationRegion.cls.point, Map.svg.root()).remove();
			for (i in data['points']) {
				LocationRegion.addPoint(data['points'][i]);
			}
			Location.load();
		});
	},
	
	addPoint: function(point) {
		var objPoint = $(Map.svg.rect(Map.mainGroup, point.x - Map.markerSize / 2, point.y - Map.markerSize / 2, Map.markerSize, Map.markerSize,
			{'class': LocationRegion.cls.point, 'data-id': point.id, 'data-x': point.x, 'data-y': point.y })); 
		interact('.' + LocationRegion.cls.point + '[data-id="' + point.id + '"]').draggable(LocationRegion.draggablePoint);
		objPoint.mouseover(LocationRegion.onMouseOverPoint);
		objPoint.mouseout(LocationRegion.onMouseOutPoint);
	},
	
	addRegion: function(region) {
		var path = '';
		var keys= '|';
		for (i in region['path']) {
			var ppoint = region['path'][i];
			var mpoint = $('.' + LocationRegion.cls.point + '[data-id="' + ppoint.id + '"]', Map.svg.root());
			path+= ' ' + ppoint['cmd'] + mpoint.attr('data-x') + ' ' + mpoint.attr('data-y');
			keys+= ppoint.id + '|';
		}
		path = $(Map.svg.path(Map.mainGroup, path, { 'class': LocationRegion.cls.path, 'fill': region['color'], 'data-id': region['id_location_region'], 'data-keys': keys, 'data-path': JSON.stringify(region['path']) }));
		path.dblclick(LocationRegion.onDblClick);
	},
		
	onDblClick: function () {
		TableFormActions.getForm('location-region', { id_map: Map.id_map, id_location_region: $(this).attr('data-id') }, function() { $('#mainModal').modal('hide'); });
	},
	
	onMovePoint: function (event) {
		var point = event.target;
		// убираем лишние элементы
		Map.hideInfoPanel();
		// репозиционируем точку
		var x = Number(point.getAttribute('data-x')) + event.dx / Map.scale;
		var y = Number(point.getAttribute('data-y')) + event.dy / Map.scale;
		if (x && y) {
			point.setAttribute('x', x - Map.markerSize / 2);
			point.setAttribute('y', y - Map.markerSize / 2);
			point.setAttribute('data-x', x);
			point.setAttribute('data-y', y);
			// перестраиваем ассоциированные пути
			$('.' + LocationRegion.cls.path + '[data-keys*="|' + point.getAttribute('data-id') + '|"]', Map.svg.root()).each(function () {
				var region = $(this);
				var regionPath = JSON.parse(region.attr('data-path'));
				var path = '';
				for (i in regionPath) {
					var ppoint = regionPath[i];
					var mpoint = $('.' + LocationRegion.cls.point + '[data-id="' + ppoint.id + '"]', Map.svg.root());
					path+= ' ' + ppoint['cmd'] + mpoint.attr('data-x') + ' ' + mpoint.attr('data-y');
				}
				region.attr('d', path);
			})
		}
	},
	
	draggablePoint: {
		inertia: true,
		restrict: {
			restriction: "parent",
			endOnly: true,
			elementRect: { top: 0, left: 0, bottom: 1, right: 1 }
		},
		onmove:	function(event)	{ LocationRegion.onMovePoint(event); },
		onend:	function(event)	{ LocationRegion.savePointPosition(event); }
	},
	
	savePointPosition: function() {
		var point = $(event.target);
		$.getJSON(LocationRegion.apiBase + '/save-point-position/', {
			id: point.attr('data-id'),
			x: Number(point.attr('data-x')),
			y: Number(point.attr('data-y')),
			id_map: Map.id_map, mapkey: Map.mapkey});
	},
	
	onMouseOverPoint: function() {	
		var point = $(this);
		LocationRegion.activeMarker = Number(point.attr('data-id'));
	},
	
	onMouseOutPoint: function() {
		LocationRegion.activeMarker = null;
	},
	
	doProcessClick: function (event) {
		var path = $('.' + LocationRegion.cls.newPath, Map.svg.root());
		if (LocationRegion.activeMarker != null) {	// если пользователь тыкнул в точку
			var pointObj = LocationRegion.getPointById(LocationRegion.activeMarker);
			var point = {
				id:	pointObj.attr('data-id'),
				x:	Number(pointObj.attr('data-x')),
				y:	Number(pointObj.attr('data-y'))
			};
			console.log(point);
			if (path.attr('class')) {	// если уже рисуется линия - добавляем связь между точками
				LocationRegion.addPathToPoint(path, point);
			} else {	// Добавляем новую линию
				LocationRegion.newPath(point);
			}
		} else {	// если пользователь тыкнул в пустоту
			var offset = Map.getEventOffset(event);
			var coord = Map.coordViewToImage(offset.x, offset.y);
			coord['id'] = 0;
			if (path.attr('class')) {	// если уже рисуется линия - добавляем связь между точками
				LocationRegion.addPathToPoint(path, coord);
			} else {	// Добавляем новую линию
				LocationRegion.newPath(coord);
			}
		}
	},
	
	addPathToPoint: function (pathObj, point) {
		var path = pathObj.attr('data-d') + ' L' + point.x + ' ' + point.y;
		pathObj.attr('d', path);
		pathObj.attr('data-d', path);
		pathObj.attr('data-last-x', point.x);
		pathObj.attr('data-last-y', point.y);
		pathObj.attr('data-ids', pathObj.attr('data-ids') + ',' + point.id);
		if (point.x == pathObj.attr('data-start-x') && point.y == pathObj.attr('data-start-y')) {
			$.getJSON(LocationRegion.apiBase + '/save-region/', { id_map: Map.id_map, mapkey: Map.mapkey, id_location: LocationRegion.regionForLocation, path: pathObj.attr('data-d'), ids: pathObj.attr('data-ids') }, function(locations) {
				Map.clickAction = null;
				Map.mouseMoveAction = null;
				LocationRegion.load();
			});
		}
	},
	
	newPath: function(point) {
		path = 'M' + point.x + ' ' + point.y;
		Map.svg.path(Map.mainGroup, path, { 'class': LocationRegion.cls.newPath, 'data-d': path, 'data-last-x': point.x, 'data-last-y': point.y,
			'data-start-x': point.x, 'data-start-y': point.y, 'data-ids': point.id });
		Map.mouseMoveAction = LocationRegion.cls.newPath;
		
		var objPoint = $(Map.svg.circle(Map.mainGroup, point.x, point.y, Map.markerSize / 2,
			{'class': LocationRegion.cls.point + ' ' + LocationRegion.cls.newPoint, 'data-id': 0, 'data-x': point.x, 'data-y': point.y })); 
		objPoint.mouseover(LocationRegion.onMouseOverPoint);
		objPoint.mouseout(LocationRegion.onMouseOutPoint);
		
	},
	
	remove: function(region) {
		$.getJSON(LocationRegion.apiBase + '/remove/', { id: region.attr('data-id'), id_map: Map.id_map, mapkey: Map.mapkey }, function(answer) {
			region.remove();
		});
	},
	
	getPointById: function(id) {
		return $('.' + LocationRegion.cls.point + '[data-id="' + id + '"]', Map.svg.root());
	},
	
	getPathById: function(id) {
		return $('.' + LocationRegion.cls.path + '[data-id="' + id + '"]', Map.svg.root());
	}
	
};
