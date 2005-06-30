{* $Header: /cvsroot/bitweaver/_bit_articles/templates/admin_topics.tpl,v 1.1 2005/06/30 01:10:46 bitweaver Exp $ *}
{strip}
<div class="floaticon">{bithelp}</div>

<div class="admin articles">
	<div class="header">
		<h1>{tr}Admin Topics{/tr}</h1>
	</div>

	<div class="body">
		{form legend="Create a new Topic" enctype="multipart/form-data"}
			<input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
			
			<div class="row">
				{formlabel label="Topic Name" for="topic_name"}
				{forminput}
					<input type="text" id="topic_name" name="topic_name" />
					{formhelp note=""}
				{/forminput}
			</div> 
			
			<div class="row">
				{formlabel label="Upload Image" for="t-image"}
				{forminput}
					<input name="upload" id="t-image" type="file" />
					{formhelp note=""}
				{/forminput}
			</div>
			
			<div class="row submit">
				<input type="submit" name="fSubmitAddTopic" value="{tr}Add Topic{/tr}" />
			</div>
		{/form}

		<table class="data">
			<caption>{tr}List of Topics{/tr}</caption>
			<tr>
				<th>{tr}Name{/tr}</th>
				<th>{tr}Image{/tr}</th>
				<th>{tr}Active{/tr}</th>
				<th>{tr}Articles (sub){/tr}</th>
				<th>{tr}Actions{/tr}</th>
			</tr>
			{section name=user loop=$topics}
				<tr class="{cycle values="even,odd"}">
					<td>{$topics[user].topic_name}</td>
					<td>
						{if $topics[user].has_topic_image == 'y'}
							<img class="icon" alt="{tr}topic image{/tr}" src="{$topics[user].topic_image_url}" />
						{/if}
					</td>
					<td style="text-align:center;">
						{if $topics[user].active eq 'n'}
							{smartlink ititle='activate' ibiticon='liberty/inactive' fActivateTopic=1 topic_id=`$topics[user].topic_id`}
						{else}
							{smartlink ititle='deactivate' ibiticon='liberty/active' fDeactivateTopic=1 topic_id=`$topics[user].topic_id`}
						{/if}
					</td>
					<td>{$topics[user].arts} ({$topics[user].subs})</td>
					<td align="right" nowrap="nowrap">
						<a href="{$gBitLoc.ARTICLES_PKG_URL}admin/admin_topics.php?fRemoveTopic=1&amp;topic_id={$topics[user].topic_id}">{tr}remove topic{/tr}</a>
						<br />
						<a href="{$gBitLoc.ARTICLES_PKG_URL}admin/admin_topics.php?fRemoveTopicAll=1&amp;topic_id={$topics[user].topic_id}">{tr}remove topic &amp; articles{/tr}</a>
						<br />
						{smartlink ititle='edit' ibiticon='liberty/edit' ifile='edit_topic.php' topic_id=`$topics[user].topic_id`}
						{smartlink ititle='permissions' ibiticon='liberty/permissions' ipackage='kernel' ifile='object_permissions.php' objectName="Topic `$topics[user].name`" object_type=topic permType=topics object_id=`$topics[user].topic_id`}
					</td>
				</tr>
			{sectionelse}
				<tr class="norecords">
					<td colspan="5">{tr}No records found{/tr}</td>
				</tr>
			{/section}
		</table>
	</div><!-- end .body -->
</div><!-- end .admin -->
{/strip}
