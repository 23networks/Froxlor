<tr class="" onmouseover="this.className='RowOverSelected';" onmouseout="this.className='';">
	<td class="field_name_border_left"><font size="-1">{$row['domain']}<if (isset($row['standardsubdomain']) && $row['standardsubdomain'] == $row['id'])> ({$lng['admin']['stdsubdomain']})</if></font></td>
	<td class="field_name"><font size="-1">{$row['ipandport']}</font></td>
	<td class="field_name"><font size="-1">{$row['customername']} (<a href="admin_customers.php?s=$s&amp;page=customers&amp;action=su&amp;id={$row['customerid']}" target="_blank">{$row['loginname']}</a>)</font></td>
	<td class="field_name"><a href="$filename?s=$s&amp;page=$page&amp;action=edit&amp;id={$row['id']}">{$lng['panel']['edit']}</a></td>
	<td class="field_name">
		<if !(isset($row['domainaliasid']) && $row['domainaliasid'] != 0)>
			<if !(isset($row['standardsubdomain']) && $row['standardsubdomain'] == $row['id'])>
				<a href="$filename?s=$s&amp;page=$page&amp;action=delete&amp;id={$row['id']}">{$lng['panel']['delete']}</a>
			</if>
		</if>
		<if isset($row['domainaliasid']) && $row['domainaliasid'] != 0>
			<a href="$filename?s=$s&amp;page=$page&amp;searchfield=d.aliasdomain&amp;searchtext={$row['id']}">{$lng['domains']['hasaliasdomains']}</a>
		</if>
	</td>
</tr>
