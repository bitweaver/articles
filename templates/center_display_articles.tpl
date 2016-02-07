<div class="display articles reverse">
	{foreach from=$articles item=article}
		{if $article@index == 0}
			<div class="col-md-6 col-sm-8 col-xs-12">
				<h1>{$article.title|escape}</h1>
				{$article.parsed_description}
				{if $article.has_more}
					{if $spacer}&nbsp; &bull; &nbsp;{/if}
					{assign var=spacer value=TRUE}
					<br /><a class="more" href="{$article.display_url}">{tr}Read More&hellip;{/tr}</a>
				{/if}
			</div>
		{/if}
		{if $article@index == 1}
			<div class="col-md-3 col-sm-4 hidden-xs news-border">
				{$article.parsed_description}
				{if $article.has_more}
					{if $spacer}&nbsp; &bull; &nbsp;{/if}
					{assign var=spacer value=TRUE}
					<br /><a class="more" href="{$article.display_url}">{tr}Read More&hellip;{/tr}</a>
				{/if}
				<h1>{$article.title|escape}</h1>
			</div>
			<div class="visible-xs col-xs-12">
				<h1>{$article.title|escape}</h1>
				{$article.parsed_description}
				{if $article.has_more}
					{if $spacer}&nbsp; &bull; &nbsp;{/if}
					{assign var=spacer value=TRUE}
					<br /><a class="more" href="{$article.display_url}">{tr}Read More&hellip;{/tr}</a>
				{/if}
			</div>
			<div class="visible-xs clear"></div>
		{/if}
		{if $article@index == 2}
			<div class="col-md-3 hidden-sm hidden-xs reverse news-border">
				<h1>{$article.title|escape}</h1>
				{$article.parsed_description}
				{if $article.has_more}
					{if $spacer}&nbsp; &bull; &nbsp;{/if}
					{assign var=spacer value=TRUE}
					<br /><a class="more" href="{$article.display_url}">{tr}Read More&hellip;{/tr}</a>
				{/if}
			</div>
			<div class="col-sm-12 visible-sm visible-xs reverse">
				<h1>{$article.title|escape}</h1>
				{$article.parsed_description}
				{if $article.has_more}
					{if $spacer}&nbsp; &bull; &nbsp;{/if}
					{assign var=spacer value=TRUE}
					<br /><a class="more" href="{$article.display_url}">{tr}Read More&hellip;{/tr}</a>
				{/if}
			</div>
			<div class="clear"></div>
		{/if}
		{if $article@index == 3}
			</div>
			<div class="display articles">
		{/if}
		{if $article@index > 2}
			<h1>{$article.title|escape}</h1>
			{$article.parsed_description}
			{if $article.has_more}
				{if $spacer}&nbsp; &bull; &nbsp;{/if}
				{assign var=spacer value=TRUE}
				<br /><a class="more" href="{$article.display_url}">{tr}Read More&hellip;{/tr}</a>
			{/if}
		{/if}
	{/foreach}
</div>
