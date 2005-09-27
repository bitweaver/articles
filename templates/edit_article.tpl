<script type="text/javascript">//<![CDATA[
	function charCounter( textareaId, counterId, maxLimit ) {ldelim}
		document.getElementById( counterId ).value = maxLimit - document.getElementById( textareaId ).value.length;
	{rdelim}
//]]></script>
<div class="floaticon">{bithelp}</div>
<div class="admin articles">
	<div class="header">
		{if $gContent->mArticleId}
			<h1>{tr}Edit Article {$article.title}{/tr}</h1>
		{elseif $gBitUser->hasPermission('bit_p_approve_submission') || $gBitUser->hasPermission('bit_p_admin_received_articles') || $gBitUser->hasPermission('bit_p_autoapprove_submission')}
			<h1>{tr}Create Article{/tr}</h1>
		{else}
			<h1>{tr}Submit Article{/tr}</h1>
		{/if}
	</div>

	{formfeedback hash=$feedback}

	{if $preview}
		<h2>Preview</h2>
		<div class="preview">{include file="bitpackage:articles/article_display.tpl" outer_div='display article'}</div>
	{/if}

	<div class="body">
		{form enctype="multipart/form-data" id="writearticle"}
			<input type="hidden" name="article_id" value="{$gContent->mArticleId}" />
			<input type="hidden" name="preview_image_url" value="{$article.preview_image_url}" />
			<input type="hidden" name="preview_image_path" value="{$article.preview_image_path}" />
			
			{jstabs}
				{jstab title="Article Body"}
					{legend legend="Article Body"}
						<div class="row">
							{formlabel label="Title" for="title"}
							{forminput}
								<input type="text" name="title" id="title" value="{$article.title|escape}" size="50" />
								{formhelp note=""}
							{/forminput}
						</div>

						<div class="row">
							{formlabel label="Author Name" for="author_name"}
							{forminput}
								<input type="text" name="author_name" id="author_name" value="{$article.author_name|escape}" />
								{formhelp note=""}
							{/forminput}
						</div>

						{if $topics or $gBitUser->hasPermission( 'bit_p_admin_articles' )}
							<div class="row">
								{formlabel label="Topic" for="topic_id"}
								{forminput}
									{if $topics}
										<select name="topic_id" id="topic_id">
											<option value="">{tr}None{/tr}</option>
											{section name=t loop=$topics}
												<option value="{$topics[t].topic_id}" {if $article.topic_id eq $topics[t].topic_id}selected="selected"{/if}>{$topics[t].topic_name|escape}</option>
											{/section}
										</select>
									{else}
										{tr}No Topics set{/tr}. {smartlink ititle="Article Topics" ifile="admin/admin_topics.php"}
									{/if}
									{formhelp note=""}
								{/forminput}
							</div>
						{/if}

						<div class="row">
							{formlabel label="Type" for="article_type_id"}
							{forminput}
								<select id="article_type_id" name="article_type_id">
									{section name=t loop=$types}
										<option value="{$types[t].article_type_id}" {if $article.article_type_id eq $types[t].article_type_id}selected="selected"{/if}>{tr}{$types[t].type_name}{/tr} {if $types[t].use_ratings eq 'y'}{assign var=rat value=TRUE} &nbsp; [ {tr}uses rating{/tr} ]{/if}</option>
									{/section}
								</select>
								{formhelp note=""}
							{/forminput}
						</div>

						{if $rat}
							<div id="ratingedit" class="row">
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

						{include file="bitpackage:liberty/edit_format.tpl"}

						{if $gBitSystemPrefs.feature_cms_templates eq 'y' and $gBitUser->hasPermission( 'bit_p_use_content_templates' )}
							<div class="row">
								{formlabel label="Apply template" for="template"}
								{forminput}
										<select name="template_id" onchange="javascript:document.getElementById('writearticle').submit();">
											<option value="0">{tr}none{/tr}</option>
											{section name=ix loop=$templates}
												<option value="{$templates[ix].template_id|escape}">{tr}{$templates[ix].name}{/tr}</option>
											{/section}
										</select>
										<noscript>
											<input type="submit" value="get template" />
										</noscript>
									{formhelp note=""}
								{/forminput}
							</div>
						{/if}

						{*<div class="row">
							{formlabel label="Introduction" for="heading"}
							{forminput}
								<textarea class="wikiedit" name="description" id="heading" rows="7" cols="80">{$article.description|escape}</textarea>
								{formhelp note="The introduction is shown on your articles home page. If you provide text in the body part or this article, a link to the full text will be provided."}
							{/forminput}
						</div>*}

						{if $gBitSystem->isPackageActive( 'quicktags' )}
							{include file="bitpackage:quicktags/quicktags_full.tpl"}
						{/if}

						<div class="row">
							{forminput}
								<textarea id="{$textarea_id}" name="edit" rows="{$rows|default:20}" cols="{$cols|default:80}"
									onkeydown="charCounter('{$textarea_id}','artCounter',{$gBitSystemPrefs.article_description_length})"
									onkeyup  ="charCounter('{$textarea_id}','artCounter',{$gBitSystemPrefs.article_description_length})"
								>{$article.data|escape:html}</textarea>
								{capture name=artCount}
									{$article.data|count_characters:true}
								{/capture}
								<input style="float:right" readonly="readonly" type="text" id="artCounter" size="5" value="{$gBitSystemPrefs.article_description_length-$smarty.capture.artCount}" />
								{formhelp note="If the article body exceeds the specified maximum body length, a seperate page will be provided with the full body of the article. You can override this by using <strong>...split...</strong> on a seperate line in your text."}
							{/forminput}
						</div>

						{if $cms_spellcheck eq 'y'}
							<div class="row">
								{formlabel label="Spellcheck" for="spellcheck"}
								{forminput}
									<input type="checkbox" name="spellcheck" id="spellcheck" {if $spellcheck eq 'y'}checked="checked"{/if} />
									{formhelp note=""}
								{/forminput}
							</div>
						{/if}

						{include file="bitpackage:liberty/edit_services_inc.tpl serviceFile=content_edit_mini_tpl}

						<div class="row submit">
							<input type="submit" name="preview" value="{tr}Preview{/tr}" />&nbsp;
							<input type="submit" name="save" value="{tr}Save{/tr}" />
						</div>
					{/legend}
				{/jstab}

				{jstab title="Article Dates"}
					{legend legend="Publication and Expiration Dates"}
						<div class="row">
							<input type="hidden" name="publishDateInput" value="1" />
							{formlabel label="Publish Date" for=""}
							{forminput}
								{html_select_date prefix="publish_" time=$article.publish_date start_year="-5" end_year="+10"} {tr}at{/tr}&nbsp;
								<span dir="ltr">{html_select_time prefix="publish_" time=$article.publish_date display_seconds=false}&nbsp;{$siteTimeZone}</span>
								{formhelp note="If the article type allows it, this article will not be displayed <strong>before</strong> this date."}
							{/forminput}
						</div>

						<div class="row">
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

				{include file="bitpackage:liberty/edit_services_inc.tpl serviceFile=content_edit_tab_tpl}

				{jstab title="Advanced"}
					{legend legend="Custom article image"}
						{*if $gBitSystem->isFeatureActive( 'feature_article__attachments' ) }
							{include file="bitpackage:liberty/edit_storage_list.tpl"}
						{/if*}

						{if $article.image_url}
							<div class="row">
								{formlabel label="Custom Image"}
								{forminput}
									<img alt="{tr}Article image{/tr}" title="{$article.title}" src="{$article.image_url}"/>
									<br />
									<input type="submit" name="remove_image" value="{tr}Remove Image{/tr}" />
									{formhelp note="You can replace this image by uploading a new one."}
								{/forminput}
							</div>
						{/if}

						<div class="row">
							{formlabel label="Use Existing Image" for="existing_attachment_id_input"}
							{forminput}
								<input type="text" name="image_attachment_id" id="existing_attachment_id_input" value="{$article.image_attachment_id}" size="6"/><br />
								{jspopup href="`$smarty.const.LIBERTY_PKG_URL`attachment_browser.php" title="Attachment browser"}
							{/forminput}
						</div>

						<div class="row">
							{formlabel label="Custom Image" for="upload"}
							{forminput}
								<input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
								<input type="file" id="upload" name="article_image"/>
								{formhelp note="Use a custom image file for the article."}
							{/forminput}
						</div>
					{/legend}
				{/jstab}
			{/jstabs}
		{/form}

		<br /><br />
		{include file="bitpackage:liberty/edit_help_inc.tpl"}

	</div><!-- end .body -->
</div><!-- end .article -->
