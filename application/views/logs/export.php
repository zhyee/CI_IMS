<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?>
		<table class="table" width="1500"  border="1">
			<thead>
			    <tr>
				    <th colspan="6" align="center"><h3>操作日志</h3></th>
				</tr>
				
				<tr>
				    <th width="60" align="center">序号</th>
					<th width="180" >日期</th>
					<th width="180" align="center">用户名</th>
					<th width="150" align="center">姓名</th>
					<th width="100" align="center">日志</th>	
					 
					<th width="100" align="center"></th>	
				</tr>
			</thead>
			<tbody>
			  <?php 
			  $i = 1;
			  foreach($list as $arr=>$row) {
			  ?>
				<tr target="id">
				    <td ><?=$i?></td>
					 
					<td ><?=$row['modifytime']?></td>
					<td ><?=$row['username']?></td>
					<td ><?=$row['name']?></td>
					<td ><?=$row['log']?></td>
					<td ></td>
				</tr>
				<?php $i++;}?>
 
 </tbody>
</table>	
