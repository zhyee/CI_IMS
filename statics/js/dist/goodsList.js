function initTree() {
	Public.zTree.init($("#tree"), {
		defaultClass: "innerTree",
		showRoot: !0,
		rootTxt: "全部"
	}, {
		callback: {
			beforeClick: function(e, t) {
				$("#currentCategory").data("id", t.id).html(t.name);
				$("#search").trigger("click")
			}
		}
	})
}
function initGrid() {
	var e = Public.setGrid(ajustH, ajustW),
		t = ["操作", "商品类别", "商品编号", "商品名称", "规格型号", "单位", "期初数量", "单位成本", "期初总价", "预计采购价", "预计销售价"],
		i = parent.SYSTEM.rights,
		a = !(parent.SYSTEM.isAdmin || i.AMOUNT_COSTAMOUNT),
		r = !(parent.SYSTEM.isAdmin || i.AMOUNT_INAMOUNT),
		n = !(parent.SYSTEM.isAdmin || i.AMOUNT_OUTAMOUNT),
		o = [{
			name: "operate",
			width: 90,
			fixed: !0,
			formatter: function(e, t, i) {
				var a = '<div class="operating" data-id="' + i.id + '"><span class="ui-icon ui-icon-pencil" title="修改"></span><span class="ui-icon ui-icon-trash" title="删除"></span></div>';
				//var a = '<div class="operating" data-id="' + i.id + '"><span class="ui-icon ui-icon-pencil" title="修改"></span><span class="ui-icon ui-icon-trash" title="删除"></span><span class="ui-icon ui-icon-pic" title="商品图片"></span></div>';
				return a
			},
			title: !1
		}, {
			name: "categoryName",
			index: "categoryName",
			width: 100,
			title: !1
		}, {
			name: "number",
			index: "number",
			width: 100,
			title: !1
		}, {
			name: "name",
			index: "name",
			width: 250,
			classes: "ui-ellipsis"
		}, {
			name: "spec",
			index: "spec",
			width: 60,
			classes: "ui-ellipsis"
		}, {
			name: "unitName",
			index: "unitName",
			width: 40,
			align: "center",
			title: !1
		}, {
			name: "quantity",
			index: "quantity",
			width: 100,
			align: "right",
			title: !1,
			formatter: format.quantity
		}, {
			name: "unitCost",
			index: "unitCost",
			width: 100,
			align: "right",
			formatter: "currency",
			formatoptions: {
				showZero: !0,
				decimalPlaces: pricePlaces
			},
			title: !1,
			hidden: a
		}, {
			name: "amount",
			index: "amount",
			width: 100,
			align: "right",
			formatter: "currency",
			formatoptions: {
				showZero: !0,
				decimalPlaces: amountPlaces
			},
			title: !1,
			hidden: a
		}, {
			name: "purPrice",
			index: "purPrice",
			width: 100,
			align: "right",
			formatter: "currency",
			formatoptions: {
				showZero: !0,
				decimalPlaces: pricePlaces
			},
			title: !1,
			hidden: r
		}, {
			name: "salePrice",
			index: "salePrice",
			width: 100,
			align: "right",
			formatter: "currency",
			formatoptions: {
				showZero: !0,
				decimalPlaces: pricePlaces
			},
			title: !1,
			hidden: n
		}];
	$("#grid").jqGrid({
		//url: "/basedata/inventory.do?action=list&isDelete=2",
		url: basedata_goods,
		datatype: "json",
		width: e.w,
		height: e.h,
		altRows: !0,
		gridview: !0,
		onselectrow: !1,
		colNames: t,
		colModel: o,
		pager: "#page",
		viewrecords: !0,
		multiselect: !0,
		cmTemplate: {
			sortable: !1
		},
		rowNum: 100,
		rowList: [100, 200, 500],
		shrinkToFit: !0,
		jsonReader: {
			root: "data.rows",
			records: "data.records",
			total: "data.total",
			repeatitems: !1,
			id: "id"
		},
		loadComplete: function(e) {
			if (e && 200 == e.status) {
				var t = {};
				e = e.data;
				for (var i = 0; i < e.rows.length; i++) {
					var a = e.rows[i];
					t[a.id] = a
				}
				$("#grid").data("gridData", t)
			}
		},
		loadError: function() {
			parent.Public.tips({
				type: 1,
				content: "操作失败了哦，请检查您的网络链接！"
			})
		}
	})
}
function initEvent() {
	$_matchCon = $("#matchCon");
	$_matchCon.placeholder();
	$("#search").on("click", function(e) {
		e.preventDefault();
		var t = "按商品编号，商品名称，规格型号等查询" === $_matchCon.val() ? "" : $.trim($_matchCon.val()),
			i = $("#currentCategory").data("id");
		$("#grid").jqGrid("setGridParam", {
			postData: {
				skey: t,
				assistId: i
			}
		}).trigger("reloadGrid")
	});
	$("#btn-add").on("click", function(e) {
		e.preventDefault();
		Business.verifyRight("INVENTORY_ADD") && handle.operate("add")
	});
	$("#btn-print").on("click", function(e) {
		e.preventDefault()
	});
	$("#btn-import").on("click", function(e) {
		e.preventDefault();
		Business.verifyRight("BaseData_IMPORT") && parent.$.dialog({
			width: 560,
			height: 300,
			title: "批量导入",
			content: "url:/import.jsp",
			lock: !0
		})
	});
	$("#btn-export").on("click", function() {
		if (Business.verifyRight("INVENTORY_EXPORT")) {
			var e = "按商品编号，商品名称，规格型号等查询" === $_matchCon.val() ? "" : $.trim($_matchCon.val()),
				t = $("#currentCategory").data("id") || "";
			//$(this).attr("href", "/basedata/inventory.do?action=exporter&isDelete=2&skey=" + e + "&assistId=" + t)
			$(this).attr("href", goods_export+"?isDelete=2&skey=" + e + "&assistId=" + t)
		}
	});
	$("#grid").on("click", ".operating .ui-icon-pencil", function(e) {
		e.preventDefault();
		if (Business.verifyRight("INVENTORY_UPDATE")) {
			var t = $(this).parent().data("id");
			handle.operate("edit", t)
		}
	});
	$("#grid").on("click", ".operating .ui-icon-trash", function(e) {
		e.preventDefault();
		if (Business.verifyRight("INVENTORY_DELETE")) {
			var t = $(this).parent().data("id");
			handle.del(t + "")
		}
	});
	$("#grid").on("click", ".operating .ui-icon-pic", function(e) {
		e.preventDefault();
		var t = $(this).parent().data("id"),
			i = "商品图片";
		$.dialog({
			content: "url:../settings/fileUpload.jsp",
			data: {
				title: i,
				id: t,
				callback: function() {}
			},
			title: i,
			width: 775,
			height: 470,
			max: !1,
			min: !1,
			cache: !1,
			lock: !0
		})
	});
	$("#btn-batchDel").click(function(e) {
		e.preventDefault();
		if (Business.verifyRight("INVENTORY_DELETE")) {
			var t = $("#grid").jqGrid("getGridParam", "selarrrow");
			t.length ? handle.del(t.join()) : parent.Public.tips({
				type: 2,
				content: "请选择需要删除的项"
			})
		}
	});
	$("#hideTree").click(function(e) {
		e.preventDefault();
		var t = $(this),
			i = t.html();
		if ("&gt;&gt;" === i) {
			t.html("&lt;&lt;");
			ajustW = 0;
			$("#tree").hide();
			Public.resizeGrid(ajustH, ajustW)
		} else {
			t.html("&gt;&gt;");
			ajustW = 270;
			$("#tree").show();
			Public.resizeGrid(ajustH, ajustW)
		}
	});
	$(window).resize(function() {
		Public.resizeGrid(ajustH, ajustW);
		$(".innerTree").height($("#tree").height() - 95)
	});
	Public.setAutoHeight($("#tree"));
	$(".innerTree").height($("#tree").height() - 95)
}
var qtyPlaces = Number(parent.SYSTEM.qtyPlaces),
	pricePlaces = Number(parent.SYSTEM.pricePlaces),
	amountPlaces = Number(parent.SYSTEM.amountPlaces),
	searchFlag = !1,
	filterClassCombo, ajustH = 95,
	ajustW = 270,
	thisTree, handle = {
		operate: function(e, t) {
			if ("add" == e) var i = "新增商品",
				a = {
					oper: e,
					callback: this.callback
				};
			else var i = "修改商品",
				a = {
					oper: e,
					rowId: t,
					callback: this.callback
				};
			var r = parent.SYSTEM.enableStorage ? 780 : 640;
			$.dialog({
				title: i,
				content: "url:"+settings_goods_manage,
				data: a,
				width: r,
				height: 420,
				max: !1,
				min: !1,
				cache: !1,
				lock: !0
			})
		},
		del: function(e) {
			$.dialog.confirm("删除的商品将不能恢复，请确认是否删除？", function() {
				Public.ajaxPost(goods_del, {
					id: e
				}, function(t) {
					if (t && 200 == t.status) {
						var i = t.data.id || [];
						parent.Public.tips(e.split(",").length === i.length ? {
							content: "成功删除" + i.length + "个商品！"
						} : {
							type: 2,
							content: t.data.msg
						});
						for (var a = 0, r = i.length; r > a; a++) {
							$("#grid").jqGrid("setSelection", i[a]);
							$("#grid").jqGrid("delRowData", i[a])
						}
					} else parent.Public.tips({
						type: 1,
						content: "删除商品失败！" + t.msg
					})
				})
			})
		},
		callback: function(e, t, i) {
			var a = $("#grid").data("gridData");
			if (!a) {
				a = {};
				$("#grid").data("gridData", a)
			}
			a[e.id] = e;
			if ("edit" == t) {
				$("#grid").jqGrid("setRowData", e.id, e);
				i && i.api.close()
			} else {
				$("#grid").jqGrid("addRowData", e.id, e, "last");
				i && i.resetForm(e)
			}
		}
	},
	format = {
		money: function(e) {
			var e = Public.numToCurrency(e);
			return e || "&#160;"
		},
		quantity: function(e) {
			return e || "&#160;"
		}
	};
initGrid();
initTree();
initEvent();