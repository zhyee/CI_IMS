function initGrid() {
	var e = Public.setGrid(),
		t = ["操作", "客户类别", "客户编号", "客户名称", "联系人", "手机", "座机", "QQ/MSN", "期初往来余额", "送货地址"],
		i = !(parent.SYSTEM.isAdmin || parent.SYSTEM.rights.AMOUNT_OUTAMOUNT),
		a = [{
			name: "operate",
			width: 60,
			fixed: !0,
			formatter: Public.operFmatter,
			title: !1
		}, {
			name: "customerType",
			index: "customerType",
			width: 100,
			fixed: !0,
			title: !1
		}, {
			name: "number",
			index: "number",
			width: 100,
			title: !1
		}, {
			name: "name",
			index: "name",
			width: 220,
			classes: "ui-ellipsis"
		}, {
			name: "contacter",
			index: "contacter",
			width: 80,
			align: "center",
			fixed: !0
		}, {
			name: "mobile",
			index: "mobile",
			width: 100,
			align: "center",
			title: !1
		}, {
			name: "telephone",
			index: "telephone",
			width: 100,
			title: !1
		}, {
			name: "linkIm",
			index: "linkIm",
			width: 80,
			title: !1
		}, {
			name: "difMoney",
			index: "difMoney",
			width: 100,
			align: "right",
			title: !1,
			formatter: "currency",
			hidden: i
		}, {
			name: "deliveryAddress",
			index: "deliveryAddress",
			width: 200,
			classes: "ui-ellipsis"
		}];
	$("#grid").jqGrid({
		//url: "../basedata/contact.do?action=list&isDelete=2",
		url: basedata_contact+"?type=1",
		datatype: "json",
		autowidth: !0,
		height: e.h,
		altRows: !0,
		gridview: !0,
		onselectrow: !1,
		multiselect: !0,
		colNames: t,
		colModel: a,
		pager: "#page",
		viewrecords: !0,
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
			} else {
				var r = 250 === e.status ? searchFlag ? "没有满足条件的结果哦！" : "没有客户数据哦！" : e.msg;
				parent.Public.tips({
					type: 2,
					content: r
				})
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
		var t = "输入客户编号/ 名称/ 联系人/ 电话查询" === $_matchCon.val() ? "" : $.trim($_matchCon.val());
		$("#grid").jqGrid("setGridParam", {
			page: 1,
			postData: {
				skey: t
			}
		}).trigger("reloadGrid")
	});
	$("#btn-add").on("click", function(e) {
		e.preventDefault();
		Business.verifyRight("BU_ADD") && handle.operate("add")
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
		if (Business.verifyRight("BU_EXPORT")) {
			var e = "输入客户编号/ 名称/ 联系人/ 电话查询" === $_matchCon.val() ? "" : $.trim($_matchCon.val());
			 
			//$(this).attr("href", "/basedata/customer.do?action=exporter&isDelete=2&skey=" + e)
			$(this).attr("href", customer_export+"?skey=" + e)
		}
	});
	$("#grid").on("click", ".operating .ui-icon-pencil", function(e) {
		e.preventDefault();
		if (Business.verifyRight("BU_UPDATE")) {
			var t = $(this).parent().data("id");
			handle.operate("edit", t)
		}
	});
	$("#grid").on("click", ".operating .ui-icon-trash", function(e) {
		e.preventDefault();
		if (Business.verifyRight("BU_DELETE")) {
			var t = $(this).parent().data("id");
			handle.del(t + "")
		}
	});
	$("#btn-batchDel").click(function(e) {
		e.preventDefault();
		if (Business.verifyRight("BU_DELETE")) {
			var t = $("#grid").jqGrid("getGridParam", "selarrrow");
			t.length ? handle.del(t.join()) : parent.Public.tips({
				type: 2,
				content: "请选择需要删除的项"
			})
		}
	});
	$(window).resize(function() {
		Public.resizeGrid()
	})
}
var searchFlag = !1,
	filterClassCombo, handle = {
		operate: function(e, t) {
			if ("add" == e) var i = "新增客户",
				a = {
					oper: e,
					callback: this.callback
				};
			else var i = "修改客户",
				a = {
					oper: e,
					rowId: t,
					callback: this.callback
				};
			$.dialog({
				title: i,
				//content: "url:customer-manage.jsp",
				content: "url:"+settings_customer_manage,
				data: a,
				width: 640,
				height: 422,
				max: !1,
				min: !1,
				cache: !1,
				lock: !0
			})
		},
		del: function(e) {
			$.dialog.confirm("删除的客户将不能恢复，请确认是否删除？", function() {
				//Public.ajaxPost("../basedata/contact.do?action=delete", {
				Public.ajaxPost(customer_del, {	
					id: e
				}, function(t) {
					if (t && 200 == t.status) {
						var i = t.data.id || [];
						parent.Public.tips(e.split(",").length === i.length ? {
							content: "成功删除" + i.length + "个客户！"
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
						content: "删除客户失败！" + t.msg
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
//			var s = ""; 
//  for (var property in e) { 
//   s = s + "\n "+property +": " + e[property] ; 
//  } 
//  alert(s); 
			
			e.difMoney = e.amount - e.periodMoney;
			a[e.id] = e;
			if ("edit" == t) {
				$("#grid").jqGrid("setRowData", e.id, e);
				i && i.api.close()
			} else {
				$("#grid").jqGrid("addRowData", e.id, e, "first");
				i && i.resetForm(e)
			}
		}
	};
initGrid();
initEvent();