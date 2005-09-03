{if ( $gBitUser->isAdmin() || $gBitUser->hasPermission( 'bit_p_admin_cms' ) ) and $gBitSystem->isFeatureActive( 'display_article_filter_bar' ) and $filter}
	{form}
		<table class="optionbar">
			<caption>{tr}Article Filter{/tr}</caption>
			<tr>
				<td>{tr}Status{/tr}</td>
				<td>{html_options options=$filter.status values=$filter.status name=status_id selected=$smarty.request.status_id}</td>
				<td>{tr}Type{/tr}</td>
				<td>{html_options options=$filter.type values=$filter.type name=type_id selected=$smarty.request.type_id}</td>
				<td>{tr}Topic{/tr}</td>
				<td>{html_options options=$filter.topic values=$filter.topic name=topic_id selected=$smarty.request.topic_id}</td>
				<td><input type="submit" value="Apply filter"></td>
			</tr>
		</table>
	{/form}
{/if}
