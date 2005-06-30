<script type="text/javascript">
	function charCounter(textareaId,counterId,maxLimit) {literal}{{/literal}
		document.getElementById(counterId).value = maxLimit - document.getElementById(textareaId).value.length;
	{literal}}{/literal}
</script>

{strip}
<div class="floaticon">{bithelp}</div>
{if $preview}
	{include file="bitpackage:articles/article_display.tpl"}
{/if}

<div class="admin articles">
	<div class="header">
		{if $gContent->mArticleId}
			<h1>{tr}Edit Article {$gContent->mInfo.title}{/tr}</h1>
		{elseif $gBitUser->hasPermission('bit_p_approve_submission') || $gBitUser->hasPermission('bit_p_admin_received_articles') || $gBitUser->hasPermission('bit_p_autoapprove_submission')}
			<h1>{tr}Create Article{/tr}</h1>
		{else}
			<h1>{tr}Submit Article{/tr}</h1>
		{/if}
		
	</div>

	<div class="body">
		{form enctype="multipart/form-data"}
			<input type="hidden" name="article_id" value="{$gContent->mArticleId}" />
			
			{jstabs}
				{jstab title="Article Body"}
					{legend legend="Article Body"}
						<div class="row">
							{formlabel label="Title" for="title"}
							{forminput}
								<input type="text" name="title" id="title" value="{$article.title|escape}" size="60" />
								{formhelp note=""}
							{/forminput}
						</div>

						<div class="row">
							{formlabel label="Author Name" for="author_name"}
							{forminput}
								<input type="text" name="author_name" id="author_name" value="{if $article.author_name|escape}{$article.author_name|escape}{else}{$author_name|escape}{/if}" />
								{formhelp note=""}
							{/forminput}
						</div>

						<div class="row">
							{formlabel label="Topic" for="topic_id"}
							{forminput}
								<select name="topic_id" id="topic_id">
									{section name=t loop=$topics}
										<option value="{$topics[t].topic_id|escape}" {if $article.topic_id eq $topics[t].topic_id}selected="selected"{/if}>{$topics[t].topic_name}</option>
									{/section}
									<option value="" {if $article.topic_id eq 0}selected="selected"{/if}>{tr}None{/tr}</option>
								</select>
								{formhelp note=""}
							{/forminput}
						</div>

						<div class="row">
							{formlabel label="Type" for="type"}
							{forminput}
								<select id="article_type_id" name="article_type_id">
									{section name=t loop=$types}
										<option value="{$types[t].article_type_id}" {if $article.article_type_id eq $types[t].article_type_id}selected="selected"{/if}>{tr}{$types[t].type_name}{/tr}</option>
									{/section}
								</select>
								{formhelp note=""}
							{/forminput}
						</div>

						<div class="row">
							{formlabel label="Rating" for="rating"}
							{forminput}
								<select name="rating" id="rating">
									{* rating options were xs-ive - xing
									<option value="9.5" {if $article.rating eq "9.5"}selected="selected"{/if}>{tr}9.5{/tr}</option>
									<option value="8.5" {if $article.rating eq "8.5"}selected="selected"{/if}>{tr}8.5{/tr}</option>
									<option value="7.5" {if $article.rating eq "7.5"}selected="selected"{/if}>{tr}7.5{/tr}</option>
									<option value="6.5" {if $article.rating eq "6.5"}selected="selected"{/if}>{tr}6.5{/tr}</option>
									<option value="5.5" {if $article.rating eq "5.5"}selected="selected"{/if}>{tr}5.5{/tr}</option>
									<option value="4.5" {if $article.rating eq "4.5"}selected="selected"{/if}>{tr}4.5{/tr}</option>
									<option value="3.5" {if $article.rating eq "3.5"}selected="selected"{/if}>{tr}3.5{/tr}</option>
									<option value="2.5" {if $article.rating eq "2.5"}selected="selected"{/if}>{tr}2.5{/tr}</option>
									<option value="1.5" {if $article.rating eq "1.5"}selected="selected"{/if}>{tr}1.5{/tr}</option>
									<option value="0.5" {if $article.rating eq "0.5"}selected="selected"{/if}>{tr}0.5{/tr}</option>
									<option value="10" {if $article.rating eq 10}selected="selected"{/if}>{tr}10{/tr}</option>
									<option value="9" {if $article.rating eq 9}selected="selected"{/if}>{tr}9{/tr}</option>
									<option value="8" {if $article.rating eq 8}selected="selected"{/if}>{tr}8{/tr}</option>
									<option value="7" {if $article.rating eq 7}selected="selected"{/if}>{tr}7{/tr}</option>
									<option value="6" {if $article.rating eq 6}selected="selected"{/if}>{tr}6{/tr}</option>
									*}
									{repeat count=5}
									<option value="5" {if $article.rating eq 5}selected="selected"{/if}>{$count}</option>
									{/repeat}
									<option value="5" {if $article.rating eq 5}selected="selected"{/if}>{tr}5{/tr}</option>
									<option value="4" {if $article.rating eq 4}selected="selected"{/if}>{tr}4{/tr}</option>
									<option value="3" {if $article.rating eq 3 or !$article.rating}selected="selected"{/if}>{tr}3{/tr}</option>
									<option value="2" {if $article.rating eq 2}selected="selected"{/if}>{tr}2{/tr}</option>
									<option value="1" {if $article.rating eq 1}selected="selected"{/if}>{tr}1{/tr}</option>
								</select>
								{formhelp note="Rating is only used when the article type allows it."}{*(we shouldn't use hide since you can't edit this page if you don't have js enabled)    (can't get js to enable/disable this as needed)*}
							{/forminput}
						</div>

						{if $gBitSystemPrefs.feature_cms_templates eq 'y' and $gBitUser->hasPermission( 'bit_p_use_content_templates' )}
							<div class="row">
								{formlabel label="Apply template" for="template"}
								{forminput}
										<select name="template_id" onchange="javascript:document.getElementById('editpageform').submit();">
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
									onKeyDown="charCounter('{$textarea_id}','artCounter',{$gBitSystemPrefs.article_description_length})"
									onKeyUp  ="charCounter('{$textarea_id}','artCounter',{$gBitSystemPrefs.article_description_length})"
								>{$article.data|escape:html}</textarea>
								{capture name=artCount}
									{$article.data|count_characters:true}
								{/capture}
								<input style="float:right" readonly="readonly" type="text" id="artCounter" size="5" value="{$gBitSystemPrefs.article_description_length-$smarty.capture.artCount}">
								{formhelp note="If the article body exceeds the specified maximum body length, a seperate page will be provided with the full body of the article."}
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

						{include file="bitpackage:liberty/edit_format.tpl"}

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
								{html_select_date prefix="publish_" time=$gContent->mInfo.publish_date start_year="-5" end_year="+10"} {tr}at{/tr}&nbsp;
								<span dir="ltr">{html_select_time prefix="publish_" time=$publish_dateSite display_seconds=false}&nbsp;{$siteTimeZone}</span>
								{formhelp note=""}
							{/forminput}
						</div>

						<div class="row">
							<input type="hidden" name="expireDateInput" value="1" />
							{formlabel label="Expiration Date" for=""}
							{forminput}
								{html_select_date prefix="expire_" time=$gContent->mInfo.expire_date start_year="-5" end_year="+10"} {tr}at{/tr}&nbsp;
								<span dir="ltr">{html_select_time prefix="expire_" time=$expire_dateSite display_seconds=false}&nbsp;{$siteTimeZone}</span>
								{formhelp note=""}
							{/forminput}
						</div>
					{/legend}
				{/jstab}

				{if $gBitSystem->isPackageActive( 'categories' )}
					{jstab title="Categorize"}
						{legend legend="Categorize"}
							{include file="bitpackage:categories/categorize.tpl"}
						{/legend}
					{/jstab}
				{/if}

				{jstab title="Article Image"}
					{legend legend="Upload custom article image"}
						{if $gBitSystem->isFeatureActive( 'feature_wiki_attachments' )}
							{include file="bitpackage:liberty/edit_storage_list.tpl"}
						{/if}
						{if $gContent->mInfo.image_attachment_id}
							<div class="row">
								{formlabel label="Own Image"}
								{forminput}
									<img alt="{tr}Article image{/tr}" border="0" src="{$gContent->mImage.image_url}"/><br />
									{formhelp note=""}
								{/forminput}
							</div>
						{/if}

						<div class="row">
							{formlabel label="Custom Image" for="upload"}
							{forminput}
								<input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
								<input type="file" name="article_image"/>
								{formhelp note="Use a custom image file for the article."}
							{/forminput}
						</div>
					{/legend}
				{/jstab}
			{/jstabs}
		{/form}

		<br /><br />
		{include file="bitpackage:liberty/edit_help.tpl"}

	</div><!-- end .body -->
</div><!-- end .article -->
{/strip}
