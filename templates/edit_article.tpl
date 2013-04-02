<script type="text/javascript">//<![CDATA[
	function charCounter( textareaId, counterId, maxLimit ) {ldelim}
		document.getElementById( counterId ).value = maxLimit - document.getElementById( textareaId ).value.length;
	{rdelim}
//]]></script>
{strip}
<div class="floaticon">{bithelp}</div>
<div class="edit articles">
	<div class="header">
		{if $gContent->mArticleId}
			<h1>{tr}Edit Article{/tr}: {$article.title|escape}</h1>
			{elseif $gContent->hasUserPermission('p_articles_approve_submission') || $gContent->hasUserPermission('p_articles_auto_approve')}
			<h1>{tr}Create Article{/tr}</h1>
		{else}
			<h1>{tr}Submit Article{/tr}</h1>
		{/if}
	</div>

	{if $preview}
		<h2>Preview</h2>
		<div class="preview">{include file="bitpackage:articles/article_display.tpl" outer_div='display articles'}</div>
	{/if}

	{formfeedback hash=$feedback}
	{formfeedback warning=`$errors.title`}

	<div class="body">
		{form enctype="multipart/form-data" id="writearticle"}
			<input type="hidden" name="article_id" value="{$gContent->mArticleId}" />
			<input type="hidden" name="content_id" value="{$gContent->getField('content_id')}" />
			<input type="hidden" name="preview_image_url" value="{$article.preview_image_url}" />
			<input type="hidden" name="preview_image_path" value="{$article.preview_image_path}" />

			{jstabs}
				{jstab title="Article Body"}
					{legend legend="Article Body"}
						<div class="control-group">
							{formlabel label="Title" for="title"}
							{forminput}
								<input type="text" name="title" id="title" value="{$article.title|escape}" size="50" />
								{formhelp note=""}
							{/forminput}
						</div>

						<div class="control-group">
							{formlabel label="Author Name" for="author_name"}
							{forminput}
								<input type="text" name="author_name" id="author_name" value="{$article.author_name|escape}" />
								{formhelp note=""}
							{/forminput}
						</div>

						{if $topics or $gContent->hasUserPermission( 'p_articles_admin' )}
							<div class="control-group">
								{formlabel label="Topic" for="topic_id"}
								{forminput}
									{if $topics}
										<select name="topic_id" id="topic_id">
											<option value="">{tr}None{/tr}</option>
											{section name=t loop=$topics}
												<option value="{$topics[t].topic_id}" {if $article.topic_id eq $topics[t].topic_id or $topic eq $topics[t].topic_name}selected="selected"{/if}>{$topics[t].topic_name|escape}</option>
											{/section}
										</select>
									{else}
										<span id="topic_id">{tr}No Topics set{/tr}</span>. {smartlink ititle="Article Topics" ifile="admin/admin_topics.php"}
									{/if}
									{formhelp note=""}
								{/forminput}
							</div>
						{/if}

						{if count($types) == 1}
							{section name=t loop=$types}
								<input type="hidden" name="article_type_id" value="{$types[t].article_type_id}" />
								{if $types[t].use_ratings eq 'y'}{assign var=ratings value=TRUE}{/if}
							{/section}
						{else}
							<div class="control-group">
								{formlabel label="Type" for="article_type_id"}
								{forminput}
									<select id="article_type_id" name="article_type_id">
										{section name=t loop=$types}
											<option value="{$types[t].article_type_id}" {if $article.article_type_id eq $types[t].article_type_id}selected="selected"{/if}>{tr}{$types[t].type_name}{/tr} {if $types[t].use_ratings eq 'y'}{assign var=ratings value=TRUE} &nbsp; [ {tr}uses rating{/tr} ]{/if}</option>
										{/section}
									</select>
									{formhelp note=""}
								{/forminput}
							</div>
						{/if}

						{if $ratings}
							<div id="ratingedit" class="control-group">
								{formlabel label="Rating" for="rating"}
								{forminput}
									<select name="rating" id="rating">
										<option value="5" {if $article.rating eq 5}selected="selected"{/if}>{tr}5{/tr}</option>
										<option value="4" {if $article.rating eq 4}selected="selected"{/if}>{tr}4{/tr}</option>
										<option value="3" {if $article.rating eq 3 or !$article.rating}selected="selected"{/if}>{tr}3{/tr}</option>
										<option value="2" {if $article.rating eq 2}selected="selected"{/if}>{tr}2{/tr}</option>
										<option value="1" {if $article.rating eq 1}selected="selected"{/if}>{tr}1{/tr}</option>
									</select>
									{formhelp note="Rating is only used when the article type allows it."}{*(we shouldn't use hide since you can't edit this page if you don't have js enabled)    (can't get js to enable/disable this as needed)*}
								{/forminput}
							</div>
						{/if}

						{capture assign=textarea_help}
							{tr}If the article body exceeds the specified maximum body length, a separate page will be provided with the full body of the article. You can override this by using <strong>...split...</strong> on a separate line in your text.{/tr}
						{/capture}
						{assign var=length value=$gBitSystem->getConfig('articles_description_length')}
						{assign var=textarea_id value=$smarty.const.LIBERTY_TEXT_AREA}
						{textarea name="edit" onkeydown="charCounter('$textarea_id','artCounter','$length');" onkeyup="charCounter('$textarea_id','artCounter','$length');"}{$article.raw}{/textarea}
						{assign var=artCount value=$article.data|count_characters:true}
						<input style="float:right" readonly="readonly" type="text" id="artCounter" size="5" value="{$gBitSystem->getConfig('articles_description_length')-$artCount}" />

						{include file="bitpackage:liberty/edit_services_inc.tpl" serviceFile="content_edit_mini_tpl"}

						{if $gBitSystem->isFeatureActive( 'articles_submissions_rnd_img' ) && !( $gContent->mArticleId || ( $gContent->hasUserPermission('p_articles_approve_submission') || $gContent->hasUserPermission('p_articles_auto_approve') ) )}
							<hr />
							{formfeedback error=$errors.captcha}
							{captcha force=true variant=row}
						{/if}

						<div class="control-group submit">
							<input type="submit" name="preview" value="{tr}Preview{/tr}" />&nbsp;
							<input type="submit" name="save" value="{tr}Save{/tr}" />
						</div>

						{if $gBitSystem->isFeatureActive( 'articles_attachments' ) }
							{include file="bitpackage:liberty/edit_storage_list.tpl" primary_label="Article Image"}
						{/if}
					{/legend}
				{/jstab}

				{include file="bitpackage:liberty/edit_services_inc.tpl" serviceFile="content_edit_tab_tpl"}

				{jstab title="Advanced"}
					{legend legend="Publication and Expiration Dates"}
						<div class="control-group">
							<input type="hidden" name="publishDateInput" value="1" />
							{formlabel label="Publish Date" for=""}
							{forminput}
								{html_select_date prefix="publish_" time=$article.publish_date start_year="-5" end_year="+10"} {tr}at{/tr}&nbsp;
								<span dir="ltr">{html_select_time prefix="publish_" time=$article.publish_date display_seconds=false}&nbsp;{$siteTimeZone}</span>
								{formhelp note="If the article type allows it, this article will not be displayed <strong>before</strong> this date."}
							{/forminput}
						</div>

						<div class="control-group">
							<input type="hidden" name="expireDateInput" value="1" />
							{formlabel label="Expiration Date" for=""}
							{forminput}
								{html_select_date prefix="expire_" time=$article.expire_date start_year="-5" end_year="+10"} {tr}at{/tr}&nbsp;
								<span dir="ltr">{html_select_time prefix="expire_" time=$article.expire_date display_seconds=false}&nbsp;{$siteTimeZone}</span>
								{formhelp note="If the article type allows it, this article will not be displayed <strong>after</strong> this date."}
							{/forminput}
						</div>
					{/legend}
				{/jstab}

				{if $gBitSystem->isFeatureActive( 'articles_attachments' ) && $gBitUser->hasPermission('p_liberty_attach_attachments') }
					{jstab title="Attachments"}
						{legend legend="Attachment Browser"}
							{include file="bitpackage:liberty/edit_storage.tpl" formid="writearticle"}
						{/legend}
					{/jstab}
				{/if}

			{/jstabs}
		{/form}
	</div><!-- end .body -->
</div><!-- end .article -->
{/strip}
