{* $Header$ *}
{strip}
<div class="floaticon">{bithelp}</div>

<div class="listing articles">
	<div class="header">
		<h1>{tr}Article Topics{/tr}</h1>
	</div>

	<div class="body">
		<ul class="data">
			{foreach from=$topics item=topic}
				<li class="item {cycle values='odd,even'}">
					<h2><a href="{$smarty.const.ARTICLES_PKG_URL}index.php?topic_id={$topic.topic_id}">{$topic.topic_name}</a></h2>

					{if $topic.has_topic_image == 'y'}
						<a href="{$smarty.const.ARTICLES_PKG_URL}index.php?topic_id={$topic.topic_id}">
							<img class="thumb" alt="{tr}topic image{/tr}" src="{$topic.topic_image_url}" />
						</a>
					{/if}

					<p>{tr}This Topic contains {$topic.num_articles} article(s){/tr}</p>

					<div class="clear"></div>
				</li>
			{foreachelse}
				<li class="norecords">
					{tr}No records found{/tr}
				</li>
			{/foreach}
		</ul>
	</div><!-- end .body -->
</div><!-- end .admin -->
{/strip}
