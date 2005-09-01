{* $Header: /cvsroot/bitweaver/_bit_articles/templates/edit_topic.tpl,v 1.2 2005/09/01 20:09:51 squareing Exp $ *}

{strip}
<div class="floaticon">{bithelp}</div>

<div class="admin articles">
	<div class="header">
		<h1>{tr}Admin Topics{/tr}</h1>
	</div>

	<div class="body">
		{form legend="Edit a Topic" enctype="multipart/form-data"}
      		<input type="hidden" name="topic_id" value="{$gContent->mTopicId}" />

			{formfeedback success=$gContent->mSuccess error=$gContent->mErrors}

			<div class="row">
				{formlabel label="Topic Name" for="topic_name"}
				{forminput}
					<input type="text" id="topic_name" name="topic_name" value="{$gContent->mInfo.topic_name}" />
					{formhelp note=""}
				{/forminput}
			</div>

			{* SQL in BitArticle.php needs sorting out first
			<div class="row">
				{formlabel label="Topic Enabled" for="topic_enabled"}
				{forminput}
					<input type="checkbox" id="topic_enabled" name="active" {if $gContent->mInfo.active == 'y'}checked="checked"{/if} />
					{formhelp note=""}
				{/forminput}
			</div>
			*}

			<div class="row">
				{formlabel label="Upload Image" for="t-image"}
				{forminput}
					<input name="upload" id="t-image" type="file" />
					{formhelp note=""}
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="Current Image"}
				{forminput}
					{if $gContent->mInfo.has_topic_image eq 'y'}
						<img src="{$gContent->mInfo.topic_image_url}" /> <br/>
						<a href="{$PHP_SELF}?topic_id={$gContent->mTopicId}&amp;fRemoveTopicImage=1">Remove Topic Image</a>
					{else}
						{tr}No Image found{/tr}
					{/if}
					{formhelp note=""}
				{/forminput}
			</div>

			<div class="row submit">
				<input type="submit" name="fSubmitSaveTopic" value="{tr}Update Topic{/tr}" />
			</div>
		{/form}
	</div><!-- end .body -->
</div><!-- end .admin -->
{/strip}
