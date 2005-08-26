{form method="get" ipackage="articles" ifile="list.php"}
	<input type="text" name="find" value="{$find|escape}" />
	<input type="submit" value="{tr}find{/tr}" name="search" />
	<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
	<select name="type">
		<option value="" {if $find_type eq ''}selected="selected"{/if}>{tr}all{/tr}</option>
		{section name=t loop=$types}
			<option value="{$types[t].type|escape}" {if $type eq $types[t].type}selected="selected"{/if}>{$types[t].type}</option>
		{/section}
	</select>
	<select name="topic">
		<option value="" {if $find_topic eq ''}selected="selected"{/if}>{tr}all{/tr}</option>
		{section name=ix loop=$topics}
			<option value="{$topics[ix].topic_id|escape}" {if $find_topic eq $topics[ix].topic_id}selected="selected"{/if}>{tr}{$topics[ix].name}{/tr}</option>
		{/section}
	</select>
{/form}
