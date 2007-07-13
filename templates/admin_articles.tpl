{* $Header: /cvsroot/bitweaver/_bit_articles/templates/admin_articles.tpl,v 1.13 2007/07/13 09:52:02 squareing Exp $ *}
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
							{html_checkboxes name="$feature" values="y" checked=$gBitSystem->getConfig($feature) labels=false id=$feature}
							{formhelp note=`$output.note` page=`$output.page`}
						{/forminput}
					</div>
				{/foreach}

				<div class="row">
					{formlabel label="Maximum Articles" for="articles-maxhome"}
					{forminput}
						<input size="5" type="text" name="articles_max_list" id="articles-maxhome" value="{$gBitSystem->getConfig('articles_max_list')|escape}" />
						{formhelp note="Number of articles shown on the main articles page."}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Article Description Length" for="articles-descrlength"}
					{forminput}
						<input size="5" type="text" name="articles_description_length" id="articles-descrlength" value="{$gBitSystem->getConfig('articles_description_length')|escape}" /> {tr}characters{/tr}
						{formhelp note="Number of characters displayed on the articles main page before splitting the article into a heading and body.<br />Changing this value might influence existing articles."}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Article Image Size" for="article_topic_thumbnail_size"}
					{forminput}
						{html_options values=$imageSizes options=$imageSizes name="articles_image_size" selected=$gBitSystem->getConfig('articles_image_size')|default:small}
						{formhelp note="Here you can select the size of the displayed article image."}
					{/forminput}
				</div>
			{/legend}
		{/jstab}

		{jstab title="Article Listings"}
			{legend legend="CMS Settings"}
				{foreach from=$formArticleListing key=feature item=output}
					<div class="row">
						{formlabel label=`$output.label` for=$feature}
						{forminput}
							{html_checkboxes name="$feature" values="y" checked=$gBitSystem->getConfig($feature) labels=false id=$feature}
							{formhelp note=`$output.note` page=`$output.page`}
						{/forminput}
					</div>
				{/foreach}
			{/legend}
		{/jstab}
	{/jstabs}

	<div class="row submit">
		<input type="submit" name="store_settings" value="{tr}Change preferences{/tr}" />
	</div>
{/form}
{/strip}
