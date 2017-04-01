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

var inventory_lists= "<?=site_url('inventory/lists')?>";
var inventory_query= "<?=site_url('inventory/query')?>";
var inventory_export= "<?=site_url('inventory/export')?>";
var inventory_generator = "<?=site_url('inventory/generator')?>";
</script>
<style>
.mod-search{ position:relative; }
#custom{ position:absolute; top:0; right:0; }
.ui-jqgrid-bdiv .ui-state-highlight { background: none; }
</style>
</head>

<body class="min-w">
<div class="wrapper">
  <div class="mod-search cf">
    <div class="fl">
      <ul class="ul-inline cf">
        <li>
          <span id="storage"></span>
        </li>
        <li>
          <span id="category"></span>
        </li>
        <li>
          <label>商品:</label>
          <input type="text" id="goods" class="ui-input w200">
        </li>
        <li id="showZero">
          <label class="chk" style="margin-top:6px; " title="显示零库存"><input type="checkbox" name="box">零库存</label>
        </li>
        <li><a class="ui-btn ui-btn-sp mrb" id="search">查询</a></li>
      </ul>
    </div>
    <div class="fr dn">
        <a class="ui-btn mrb" id="export">导出系统库存</a>
		<!--<a class="ui-btn mrb" id="import">导入盘点库存</a>-->
		<a class="ui-btn" id="save">生成盘点单据</a>
    </div>
  </div>
  <div class="grid-wrap no-query">
    <table id="grid">
    </table>
    <!--<div id="page"></div>-->
  </div>
  <div style="margin:10px 18px 0 0; " class="dn"  id="handleDom">
    <div class="fl">
      <label>备注:</label>
      <input type="text" id="note" class="ui-input" style="width:560px;">
    </div>
  </div>
</div>
<script src="<?=skin_url()?>/js/dist/inventory.js?2"></script>
</body>
</html>