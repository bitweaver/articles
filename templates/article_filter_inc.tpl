{if ( $gBitUser->isAdmin() || $gBitUser->hasPermission( 'p_articles_admin' ) ) and $gBitSystem->isFeatureActive( 'articles_display_filter_bar' ) and $filter}
	{form}
		<table class="optionbar">
			<caption>{tr}Article Filter{/tr}</caption>
			<tr>
				<td class="odd">
					{tr}Status{/tr}
					{html_options options=$filter.status values=$filter.status name=status_id selected=$smarty.request.status_id}
				</td>
				<td class="even">
					{tr}Type{/tr}
					{html_options options=$filter.type values=$filter.type name=type_id selected=$smarty.request.type_id}
				</td>
				<td class="odd">
					{tr}Topic{/tr}
					{html_options options=$filter.topic values=$filter.topic name=topic_id selected=$smarty.request.topic_id}
				</td>
				<td>
					<input type="submit" class="ink-button" value="Apply filter">
				</td>
			</tr>
		</table>
	{/form}
{/if}
