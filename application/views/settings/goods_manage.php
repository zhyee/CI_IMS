<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=1280, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="renderer" content="webkit|ie-comp|ie-stand">
<title>在线进销存</title>
<link href="<?=skin_url()?>/css/common.css?ver=20140815" rel="stylesheet" type="text/css">
<link href="<?=skin_url()?>/css/<?=skin()?>/ui.min.css?ver=20140815" rel="stylesheet">
<script src="<?=skin_url()?>/js/common/libs/jquery/jquery-1.10.2.min.js"></script>
<script src="<?=skin_url()?>/js/common/libs/json2.js"></script>
<script src="<?=skin_url()?>/js/common/common.js?ver=20140815"></script>
<script src="<?=skin_url()?>/js/common/grid.js?ver=20140815"></script>
<script src="<?=skin_url()?>/js/common/plugins.js?ver=20140815"></script>
<script src="<?=skin_url()?>/js/common/plugins/jquery.dialog.js?self=true&ver=20140815"></script>
<script type="text/javascript">
try{
	document.domain = '<?=base_url()?>';
}catch(e){
	//console.log(e);
}
</script>

<script type="text/javascript">
var WDURL = "";
var SCHEME= "<?=skin()?>";
var icon_url = "<?=skin_url()?>/css/base/dialog/icons/";                      
var settings_customer_manage = "<?=site_url('settings/customer_manage')?>";   
var settings_vendor_manage = "<?=site_url('settings/vendor_manage')?>";       
var settings_vendor_batch = "<?=site_url('settings/vendor_batch')?>";         
var settings_customer_batch = "<?=site_url('settings/customer_batch')?>";   
var basedata_settlement = "<?=site_url('basedata/settlement')?>";            
var settings_settlement_manage = "<?=site_url('settings/settlement_manage')?>";       
var basedata_category = "<?=site_url('basedata/category')?>";                     
var basedata_category_type= "<?=site_url('basedata/category_type')?>";         
var settings_goods_manage = "<?=site_url('settings/goods_manage')?>";     
var settings_goods_batch  = "<?=site_url('settings/goods_batch')?>";       
var basedata_goods = "<?=site_url('basedata/goods')?>";                     
var basedata_unit  = "<?=site_url('basedata/unit')?>";                      
var settings_unit_manage = "<?=site_url('settings/unit_manage')?>";    
var basedata_contact  = "<?=site_url('basedata/contact')?>";             
var settings_inventory =  "<?=site_url('settings/inventory')?>";          
var settings_skins =  "<?=site_url('settings/skins')?>"; 
var category_save = "<?=site_url('category/save')?>";
var basedata_category = "<?=site_url('basedata/category')?>";  //分类接口
var basedata_goods_query = "<?=site_url('basedata/goods_query')?>";
var basedata_goods_checkname = "<?=site_url('basedata/goods_checkname')?>";
var basedata_goods_getnextno = "<?=site_url('basedata/goods_getnextno')?>";
var goods_save = "<?=site_url('goods/save')?>";
var aidArr = <?=$aidArr?>;
var aidArr_en = <?=$aidArr_en ?>;

</script>
<link rel="stylesheet" href="<?=skin_url()?>/js/common/plugins/validator/jquery.validator.css">
<script type="text/javascript" src="<?=skin_url()?>/js/common/plugins/validator/jquery.validator.js"></script>
<script type="text/javascript" src="<?=skin_url()?>/js/common/plugins/validator/local/zh_CN.js"></script>
<style>
body{background: #fff;}
.ui-combo-wrap{position:static;}
.mod-form-rows .label-wrap{font-size:12px;}
.manage-wrapper{margin:20px auto 0;width:600px;}
.manage-wrap .ui-input{width: 198px;}
.base-form{*zoom: 1;margin:0 -10px;}
.base-form:after{content: '.';display: block;clear: both;height: 0;overflow: hidden;}
.base-form .row-item{float: left;width: 290px;height: 31px;margin: 0 10px;overflow: visible;padding-bottom:15px;}
.manage-wrap textarea.ui-input{width: 588px;height: 60px;*vertical-align:auto;overflow: hidden;}
#purchasePrice,#salePrice{text-align: right;}

.contacters{margin-bottom: 10px;}
.contacters h3{margin-bottom: 10px;font-weight: normal;}
.ui-jqgrid-bdiv .ui-state-highlight { background: none; }
.operating .ui-icon{ margin:0; }
.ui-icon-plus { background-position:-80px 0; }
.ui-icon-trash { background-position:-64px 0; }
.mod-form-rows .ctn-wrap{overflow: visible;;}
.mod-form-rows .pb0{margin-bottom:0;}
.jdInfo{display:none;margin-top: 5px;}
.jdInfo h3{position: absolute;left: 50%;margin-left: -62px;top: -11px;background-color: #fff;padding: 0 10px;color: #ccc;}
.jdInfo a{cursor: help;border-bottom:dotted #555 1px;}
.hasJDStorage .manage-wrapper{margin:20px auto 0;width:723px;}
.hasJDStorage .jdInfo{display:block;position:relative;z-index:1;padding-top: 15px;margin: 10px 0;border-top: solid 1px #ccc;}
.hasJDStorage .manage-wrap textarea.ui-input{height: 30px;width:708px;}
.hasJDStorage .base-form .row-item{width: 227px;padding-bottom:9px;}
.hasJDStorage .manage-wrap .ui-input{width: 135px;}
</style>
</head>
<body>
<div class="manage-wrapper">
    <div id="manage-wrap" class="manage-wrap">
    	<form id="manage-form" action="">
    		<ul class="mod-form-rows base-form cf" id="base-form">
    			<li class="row-item">
    				<div class="label-wrap"><label for="number">商品编号</label></div>
    				<div class="ctn-wrap"><input type="text" value="" class="ui-input" name="number" id="number"></div>
    			</li>
    			<li class="row-item">
    				<div class="label-wrap"><label for="name">商品名称</label></div>
    				<div class="ctn-wrap"><input type="text" value="" class="ui-input" name="name" id="name"></div>
    			</li>
    			<li class="row-item dn">
    				<div class="label-wrap"><label for="barCode">商品条码</label></div>
    				<div class="ctn-wrap"><input type="text" value="" class="ui-input" name="barCode" id="barCode"></div>
    			</li>
    			<li class="row-item">
    				<div class="label-wrap"><label for="category">商品类别</label></div>
    				<div class="ctn-wrap"><span id="category"></span></div>
    			</li>
    			<li class="row-item">
    				<div class="label-wrap"><label for="specs">规格型号</label></div>
    				<div class="ctn-wrap"><input type="text" value="" class="ui-input" name="specs" id="specs" /></div>
    			</li>
    			<li class="row-item" style="display:none">
    				<div class="label-wrap"><label for="storage">首选仓库</label></div>
    				<div class="ctn-wrap"><span id="storage"></span></div>
    			</li>
    			<li class="row-item row-category">
    				<div class="label-wrap"><label for="unit">计量单位</label></div>
    				<div class="ctn-wrap"><span id="unit"></span></div>
    			</li>
    			<li class="row-item">
    				<div class="label-wrap"><label for="purchasePrice">预计采购价</label></div>
    				<div class="ctn-wrap"><input type="text" value="" class="ui-input" name="purchasePrice" id="purchasePrice" /></div>
    			</li>
    			<li class="row-item">
    				<div class="label-wrap"><label for="salePrice">预计销售价</label></div>
    				<div class="ctn-wrap"><input type="text" value="" class="ui-input" name="salePrice" id="salePrice"></div>
    			</li>
				<li class="row-item">
    				<div class="label-wrap"><label for="quantity">期初数量</label></div>
    				<div class="ctn-wrap"><input type="text" value="" class="ui-input" name="quantity" id="quantity" /></div>
    			</li>
    			<li class="row-item">
    				<div class="label-wrap"><label for="unitCost">单位成本</label></div>
    				<div class="ctn-wrap"><input type="text" value="" class="ui-input" name="unitCost" id="unitCost"></div>
    			</li>
				<li class="row-item">
    				<div class="label-wrap"><label for="amount">期初总价</label></div>
    				<div class="ctn-wrap"><input type="text" value="" class="ui-input" name="amount" id="amount"></div>
    			</li>

				<li class="row-item">
					<div class="label-wrap"><label for="relate-goods" style="font-size: 6px">关联产品(中文站)</label></div>
					<div class="ctn-wrap">
						<select style="height: 30px;" name="aid" id="relate-goods" class="ui-input">
							<option value="0">-- 请选择关联的门户产品 --</option>
						</select>
					</div>
				</li>

				<li class="row-item">
					<div class="label-wrap"><label for="relate-goods-en" style="font-size: 6px">关联产品(英文站)</label></div>
					<div class="ctn-wrap">
						<select style="height: 30px;" name="aid_en" id="relate-goods-en" class="ui-input">
							<option value="0">-- 请选择关联的门户产品 --</option>
						</select>
					</div>
				</li>

    		</ul>
    		<div id="jdInfo" class="jdInfo cf dn">
    			<h3>维护京东仓储信息</h3>
    			<ul class="mod-form-rows base-form cf">
    				<li class="row-item">
	    				<div class="label-wrap"><label for="jianxing">商品件型</label></div>
	    				<div class="ctn-wrap"><span id="jianxing"></span></div>
    				</li>   
    				<li class="row-item">
    					<div class="label-wrap"><label for="length">长(mm)</label></div>
    					<div class="ctn-wrap"><input type="text" value="" class="ui-input" name="length" id="length"></div>
    				</li> 
    				<li class="row-item">
    					<div class="label-wrap"><label for="width">宽(mm)</label></div>
    					<div class="ctn-wrap"><input type="text" value="" class="ui-input" name="width" id="width"></div>
    				</li> 
    				<li class="row-item">
    					<div class="label-wrap"><label for="height">高(mm)</label></div>
    					<div class="ctn-wrap"><input type="text" value="" class="ui-input" name="height" id="height"></div>
    				</li> 
    				<li class="row-item">
    					<div class="label-wrap"><label for="weight">重量(kg)</label></div>
    					<div class="ctn-wrap"><input type="text" value="" class="ui-input" name="weight" id="weight"></div>
    				</li> 				
    			</ul>
    		</div>
    		<div class="contacters">
    			
    			<!--<div class="grid-wrap">
				  <table id="grid">
				  </table>
				  <div id="page"></div>
				</div>-->
    		</div>
    		<ul class="mod-form-rows">
    			<li class="row-item pb0">
    				<div class="ctn-wrap" style="line-height: normal;"><textarea name="" id="note" class="ui-input ui-input-ph">添加备注信息</textarea></div>
    			</li>
    		</ul>
    		
    	</form>
    </div>
    
    <div id="initCombo" class="dn">
      <input type="text" class="textbox storageAuto" name="storage" autocomplete="off">
    </div>
</div>
<script src="<?=skin_url()?>/js/dist/goodsManage.js?3"></script>

<script>
	function relate_goods(obj) {
		if (typeof obj == 'object' && obj.length > 0)
		{

			var rowId = frameElement.api.data.rowId;
			if (rowId > 0)
			{
				$.ajax({
					url : basedata_goods_query,
					data : {id: rowId},
					type : 'POST',
					dataType : 'JSON',
					success : function (rt) {
						if (rt.status = 200)
						{
							var aid = rt.data.aid;
							var html = '';
							for (var i in obj)
							{
								if ($.inArray(parseInt(obj[i].id), aidArr) < 0 || aid == obj[i].id)
								{
									html += '<option value="'+ obj[i].id +'"';
									if (aid == obj[i].id)
									{
										html += ' selected';
									}
									html += '>'+ obj[i].title +'</option>\n';
								}
							}

							$('#relate-goods').append(html);
						}
					}
				});

			}
			else
			{
				var html = '';
				for (var i in obj) {
					if ($.inArray(parseInt(obj[i].id), aidArr) < 0) {
						html += '<option value="' + obj[i].id + '"';
						html += '>' + obj[i].title + '</option>\n';
					}
				}

				$('#relate-goods').append(html);
			}
		}
	}

	function relate_goods_en(obj) {
		if (typeof obj == 'object' && obj.length > 0)
		{

			var rowId = frameElement.api.data.rowId;
			if (rowId > 0)
			{
				$.ajax({
					url : basedata_goods_query,
					data : {id: rowId},
					type : 'POST',
					dataType : 'JSON',
					success : function (rt) {
						if (rt.status = 200)
						{
							var aid_en = rt.data.aid_en;
							var html = '';
							for (var i in obj)
							{
								if ($.inArray(parseInt(obj[i].id), aidArr) < 0 || aid_en == obj[i].id)
								{
									html += '<option value="'+ obj[i].id +'"';
									if (aid_en == obj[i].id)
									{
										html += ' selected';
									}
									html += '>'+ obj[i].title +'</option>\n';
								}
							}

							$('#relate-goods-en').append(html);
						}
					}
				});

			}
			else
			{
				var html = '';
				for (var i in obj) {
					if ($.inArray(parseInt(obj[i].id), aidArr) < 0) {
						html += '<option value="' + obj[i].id + '"';
						html += '>' + obj[i].title + '</option>\n';
					}
				}

				$('#relate-goods-en').append(html);
			}
		}
	}


	$(function () {
		var scriptNode = document.createElement('script');
		scriptNode.src = 'http://nfkycn.eonfox.cc/plus/ajax_goods.php?typeid=3&callback=relate_goods';
		scriptNode.type = 'text/javascript';
		document.body.appendChild(scriptNode);

		var node2 = document.createElement('script');
		node2.src = 'http://nfkyen.eonfox.cc/plus/ajax_goods.php?typeid=3&callback=relate_goods_en';
		node2.type = 'text/javascript';
		document.body.appendChild(node2);
	});
</script>
</body>
</html>