{* $Header$ *}
{strip}

<div class="floaticon">{bithelp}</div>

<div class="admin articles">
	<div class="header">
		<h1>{tr}Admin Topics{/tr}</h1>
	</div>

	<div class="body">
		{formfeedback error=$gContent->mErrors}

		{form legend="Create a new Topic" enctype="multipart/form-data"}
			<input type="hidden" name="MAX_FILE_SIZE" value="1000000" />

			<div class="control-group">
				{formlabel label="Topic Title" for="topic_name"}
				{forminput}
					<input type="text" id="topic_name" name="topic_name" />
					{formhelp note=""}
				{/forminput}
			</div> 

			<div class="control-group">
				{formlabel label="Upload Image" for="t-image"}
				{forminput}
					<input name="upload" id="t-image" type="file" />
					{formhelp note=""}
				{/forminput}
			</div>

			<div class="control-group submit">
				<input type="submit" class="btn" name="fSubmitAddTopic" value="{tr}Add Topic{/tr}" />
			</div>
		{/form}

		<table class="data">
			<caption>{tr}List of Topics{/tr}</caption>
			<tr>
				<th>{tr}Image{/tr}</th>
				<th>{tr}Title{/tr} [ {tr}Number of Articles{/tr} ]</th>
				<th>{tr}Active{/tr}</th>
				<th>{tr}Actions{/tr}</th>
			</tr>

			{section name=user loop=$topics}
				<tr class="{cycle values="even,odd"}">
					<td>
						{if $topics[user].has_topic_image == 'y'}
							<a href="{$smarty.const.ARTICLES_PKG_URL}index.php?topic_id={$topics[user].topic_id}">
								<img class="icon" alt="{tr}topic image{/tr}" src="{$topics[user].topic_image_url}" />
							</a>
						{/if}
					</td>

					<td>
						<h2>
							<a href="{$smarty.const.ARTICLES_PKG_URL}index.php?topic_id={$topics[user].topic_id}">{$topics[user].topic_name}</a>
							&nbsp; <small>[ {$topics[user].num_articles} ]</small>
						</h2>

					</td>

					<td style="text-align:center;">
						{if $topics[user].active_topic eq 'n'}
							{smartlink ititle='activate' booticon="icon-remove-circle" fActivateTopic=1 topic_id=$topics[user].topic_id}
						{else}
							{smartlink ititle='deactivate' booticon="icon-ok-circle" fDeactivateTopic=1 topic_id=$topics[user].topic_id}
						{/if}
					</td>

					<td align="right">
						{smartlink ititle='edit' booticon="icon-edit" ifile='edit_topic.php' topic_id=$topics[user].topic_id}
						{* smartlink ititle='permissions' booticon="icon-key" ipackage='kernel' ifile='object_permissions.php' objectName="Topic `$topics[user].name`" object_type=topic permType=topics object_id=$topics[user].topic_id *}
						<br />
						<a href="{$smarty.const.ARTICLES_PKG_URL}admin/admin_topics.php?fRemoveTopic=1&amp;topic_id={$topics[user].topic_id}">{booticon iname="icon-trash" ipackage="icons" iforce=icon_text iexplain="Remove Topic"}</a>
						<br />
						<a href="{$smarty.const.ARTICLES_PKG_URL}admin/admin_topics.php?fRemoveTopicAll=1&amp;topic_id={$topics[user].topic_id}">{booticon iname="icon-trash" ipackage="icons" iforce=icon_text iexplain="Remove Topic and its Articles"}</a>
					</td>
				</tr>
			{sectionelse}
				<tr class="norecords">
					<td colspan="4">{tr}No records found{/tr}</td>
				</tr>
			{/section}
		</table>
	</div><!-- end .body -->
</div><!-- end .admin -->
{/strip}
