{* $Header: /cvsroot/bitweaver/_bit_articles/templates/admin_articles.tpl,v 1.6 2005/12/07 12:17:41 squareing Exp $ *}
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
						<input size="5" type="text" name="max_articles" id="articles-maxhome" value="{$gBitSystemPrefs.max_articles|escape}" />
						{formhelp note="Number of articles shown on the main articles page."}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Article Description Length" for="articles-descrlength"}
					{forminput}
						<input size="5" type="text" name="article_description_length" id="articles-descrlength" value="{$gBitSystemPrefs.article_description_length|escape}" /> {tr}characters{/tr}
						{formhelp note="Number of characters displayed on the articles main page before splitting the article into a heading and body.<br />Changing this value might influence existing articles."}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Article Image Size" for="article_topic_thumbnail_size"}
					{forminput}
						<input size="5" type="text" id="article_topic_thumbnail_size" name="article_topic_thumbnail_size" value="{$gBitSystemPrefs.article_topic_thumbnail_size|default:"160"}" /> {tr}pixels{/tr}
						{formhelp note="Here you can set the maximum width and height an article image can have. If you don't want this feature active, set it very high, then your images will not be resized."}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Display Time since Publishing" for="articles-date-format"}
					{forminput}
						{html_options options=$articleDateFormat values=$articleDateFormat name=article_date_display_format id=article-date-format selected=$gBitSystem->mPrefs.article_date_display_format}
						{formhelp note="Display the time since the article has been published instead of the full date. Pick the timespan for which this is true."}
					{/forminput}
				</div>

				<div class="row submit">
					<input type="submit" name="settingsTabSubmit" value="{tr}Change preferences{/tr}" />
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
