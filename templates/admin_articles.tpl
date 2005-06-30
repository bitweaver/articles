{* $Header: /cvsroot/bitweaver/_bit_articles/templates/admin_articles.tpl,v 1.1 2005/06/30 01:10:46 bitweaver Exp $ *}
{strip}
{form}
	{jstabs}
		{jstab title="Article Settings"}
			<input type="hidden" name="page" value="{$page}" />

			{legend legend="CMS Settings"}
				{foreach from=$formCmsSettings key=feature item=output}
					<div class="row">
						{formlabel label=`$output.label` for=$feature}
						{forminput}
							{html_checkboxes name="$feature" values="y" checked=`$gBitSystemPrefs.$feature` labels=false id=$feature}
							{formhelp note=`$output.note` page=`$output.page`}
						{/forminput}
					</div>
				{/foreach}
				
				<div class="row">
					{formlabel label="Maximum Articles" for="articles-maxhome"}
					{forminput}
						<input size="5" type="text" name="maxArticles" id="articles-maxhome" value="{$gBitSystemPrefs.maxArticles|escape}" />
						{formhelp note="Number of articles shown on the main articles page."}
					{/forminput}
				</div>
				
				<div class="row">
					{formlabel label="Article Description Length" for="articles-descrlength"}
					{forminput}
						<input size="5" type="text" name="article_description_length" id="articles-descrlength" value="{$gBitSystemPrefs.article_description_length|escape}" />
						{formhelp note="The length article bodies are truncated for their abbreviated listings"}
					{/forminput}
				</div>
				
				<div class="row submit">
					<input type="submit" name="settingsTabSubmit" value="{tr}Change preferences{/tr}" />
				</div>
			{/legend}
		{/jstab}

		{jstab title="Article Comments"}
			{legend legend="Article Comment Settings"}
				<div class="row">
					{formlabel label="Default number of comments per page" for="articles-commentsnumber"}
					{forminput}
						<input size="5" type="text" name="article_comments_per_page" id="articles-commentsnumber" value="{$gBitSystemPrefs.article_comments_per_page|escape}" />
						{formhelp note="Number of comments visible below a given article."}
					{/forminput}
				</div>
				
				<div class="row">
					{formlabel label="Comments default ordering" for="articles-commentsorder"}
					{forminput}
						<select name="article_comments_default_ordering" id="articles-commentsorder">
							<option value="comment_date_desc" {if $gBitSystemPrefs.article_comments_default_ordering eq 'comment_date_desc'}selected="selected"{/if}>{tr}Newest first{/tr}</option>
							<option value="comment_date_asc" {if $gBitSystemPrefs.article_comments_default_ordering eq 'comment_date_asc'}selected="selected"{/if}>{tr}Oldest first{/tr}</option>
							<option value="points_desc" {if $gBitSystemPrefs.article_comments_default_ordering eq 'points_desc'}selected="selected"{/if}>{tr}Points{/tr}</option>
						</select>
						{formhelp note="Set the default order of comments for articles."}
					{/forminput}
				</div>
				
				<div class="row submit">
					<input type="submit" name="commentsTabSubmit" value="{tr}Change preferences{/tr}" />
				</div>
			{/legend}
		{/jstab}

		{jstab title="Article Listings"}
			{legend legend="CMS Settings"}
				{foreach from=$formArticleListing key=feature item=output}
					<div class="row">
						{formlabel label=`$output.label` for=$feature}
						{forminput}
							{html_checkboxes name="$feature" values="y" checked=`$gBitSystemPrefs.$feature` labels=false id=$feature}
							{formhelp note=`$output.note` page=`$output.page`}
						{/forminput}
					</div>
				{/foreach}
				
				<div class="row submit">
					<input type="submit" name="listTabSubmit" value="{tr}Change preferences{/tr}" />
				</div>
			{/legend}
		{/jstab}
	{/jstabs}
{/form}
{/strip}
