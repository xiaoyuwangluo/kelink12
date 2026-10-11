function SplitMessages() {
	var FlfMuTmAD46 = {
		set: [{
			name: "波色",
			set: [{
				name: "红",
				set: [1, 2, 7, 8, 12, 13, 18, 19, 23, 24, 29, 30, 34, 35, 40, 45, 46]
			}, {
				name: "绿",
				set: [5, 6, 11, 16, 17, 21, 22, 27, 28, 32, 33, 38, 39, 43, 44, 49]
			}, {
				name: "蓝",
				set: [3, 4, 9, 10, 14, 15, 20, 25, 26, 31, 36, 37, 41, 42, 47, 48]
			}]
		},{
			name: "五行",
			set: [{
				name: "金",
				set: [1,14,15,22,23,30,31,44,45]
			}, {
				name: "木",
				set: [4,5,12,13,26,27,34,35,42,43]
			}, {
				name: "水",
				set: [2,3,10,11,18,19,32,33,40,41,48,49]
			}, {
				name: "火",
				set: [6,7,20,21,28,29,36,37]
			}, {
				name: "土",
				set: [8,9,16,17,24,25,38,39,46,47]
			}]
		},{
			name: "生肖",
			set: [{
				name: "鸡",
				set: [1, 13, 25, 37, 49]
			}, {
				name: "猴",
				set: [2, 14, 26, 38]
			}, {
				name: "羊",
				set: [3, 15, 27, 39]
			}, {
				name: "马",
				set: [4, 16, 28, 40]
			}, {
				name: "蛇",
				set: [5, 17, 29, 41]
			}, {
				name: "龙",
				set: [6, 18, 30, 42]
			}, {
				name: "兔",
				set: [7, 19, 31, 43]
			}, {
				name: "虎",
				set: [8, 20, 32, 44]
			}, {
				name: "牛",
				set: [9, 21, 33, 45]
			}, {
				name: "鼠",
				set: [10, 22, 34, 46]
			}, {
				name: "猪",
				set: [11, 23, 35, 47]
			}, {
				name: "狗",
				set: [12, 24, 36, 48]
			}]
		}]
	};
	this["BoSe"] = function(PMMvLohp47) {
		return (this["each"](0, PMMvLohp47));
	};
	this["WuXing"] = function(PMMvLohp47) {
		return (this["each"](1, PMMvLohp47));
	};
	this["ShengXiao"] = function(WQ49) {
		return (this["each"](2, WQ49));
	};
	this["each"] = function(QdJqzIlQC52, PIdlRu53) {
		var w54 = null;
		var hVgK$U55 = (!isNaN(PIdlRu53)) ? window["Math"]["floor"](PIdlRu53) : PIdlRu53;
		var Oam56 = FlfMuTmAD46["set"][QdJqzIlQC52]["set"];
		for (var YkWB57 = 0; YkWB57 < Oam56["length"]; YkWB57++) {
			var p58 = Oam56[YkWB57]["set"];
			for (var MFtDG59 = 0; MFtDG59 < p58["length"]; MFtDG59++) {
				if (p58[MFtDG59] == hVgK$U55) {
					w54 = Oam56[YkWB57]["name"];
					break;
				};
			};
			if (w54) break;
		};
		return w54;
	};
}