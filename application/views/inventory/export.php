<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?>
		<table class="table" width="1500"  border="1">
			<thead>
			    <tr>
				    <th colspan="7" align="center"><h3>盘点表</h3></th>
				</tr>
				
				<tr>
					<th width="100" >商品类别</th>
					<th width="100" align="center">商品编号</th>
					<th width="150" align="center">商品名称</th>
					<th width="80" align="center">规格型号</th>
					<th width="70" align="center">单位</th>	
					<th width="80" align="center">系统库存</th>	
					<th width="80" align="center">盘点库存</th>
				</tr>
			</thead>
			<tbody>
			  <?php foreach($list as $arr=>$row) {?>
				<tr target="id">
				    <td ><?=$row['categoryname']?></td>
					<td >No.<?=$row['number']?></td>
					<td ><?=$row['name']?></td>
					<td ><?=$row['spec']?></td>
					<td ><?=$row['unitname']?></td>
					<td ><?=$row['qty']?></td>
					<td ></td>
				</tr>
				<?php }?>
 
 </tbody>
</table>	
