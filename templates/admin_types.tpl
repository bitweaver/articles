{* $Header: /cvsroot/bitweaver/_bit_articles/templates/admin_types.tpl,v 1.2 2005/08/13 09:34:09 squareing Exp $ *}
{strip}
<div class="floaticon">{bithelp}</div>

<div class="admin articles">
	<div class="header">
		<h1>Article Types</h1>
	</div>

	<div class="body">
		{formfeedback error=$gContent->mErrors}
		
		{jstabs}
			{jstab title="Article Types"}

				{form legend="Modify Article Types"}
					<table class="data">
						<caption>{tr}Article Types{/tr}</caption>
						<tr>
							<th style="width:30%" title="{tr}Name{/tr}">{tr}Name{/tr}</th>
							<th style="width:5%" title="{tr}Rate{/tr}">1</th>
							<th style="width:5%" title="{tr}Before publication date{/tr}">2</th>
							<th style="width:5%" title="{tr}Show after expiration date{/tr}">3</th>
							<th style="width:5%" title="{tr}Heading only{/tr}">4</th>
							<th style="width:5%" title="{tr}Comments{/tr}">5</th>
							<th style="width:5%" title="{tr}Comment can Rate Article{/tr}">6</th>
							<th style="width:5%" title="{tr}Show image{/tr}">7</th>
							<th style="width:5%" title="{tr}Show avatar{/tr}">8</th>
							<th style="width:5%" title="{tr}Show author{/tr}">9</th>
							<th style="width:5%" title="{tr}Show publication date{/tr}">10</th>
							<th style="width:5%" title="{tr}Show expiration date{/tr}">11</th>
							<th style="width:5%" title="{tr}Show reads{/tr}">12</th>
							<th style="width:5%" title="{tr}Show size{/tr}">13</th>
							<th style="width:5%" title="{tr}Author can edit{/tr}">14</th>
							<th style="width:5%" title="{tr}Remove Type{/tr}">15</th>
						</tr>
						{section name=user loop=$types}
							<tr class="{cycle values="odd,even}">
								<td>
									<input type="hidden" name="type_array[{$types[user].article_type_id}]" />
									<a href="{$smarty.const.ARTICLES_PKG_URL}index.php?type={$types[user].article_type_id}">{tr}{$types[user].type_name}{/tr}</a>
								</td>
								<td style="text-align:center;"><input title="{tr}Rate{/tr}" type="checkbox" name="use_ratings[{$types[user].article_type_id}]" {if $types[user].use_ratings eq 'y'}checked="checked"{/if} /></td>
								<td style="text-align:center;"><input title="{tr}Before publication date{/tr}" type="checkbox" name="show_pre_publ[{$types[user].article_type_id}]" {if $types[user].show_pre_publ eq 'y'}checked="checked"{/if} /></td>
								<td style="text-align:center;"><input title="{tr}Show after expiration date{/tr}" type="checkbox" name="show_post_expire[{$types[user].article_type_id}]" {if $types[user].show_post_expire eq 'y'}checked="checked"{/if} /></td>
								<td style="text-align:center;"><input title="{tr}Heading only{/tr}" type="checkbox" name="heading_only[{$types[user].article_type_id}]" {if $types[user].heading_only eq 'y'}checked="checked"{/if} /></td>
								<td style="text-align:center;"><input title="{tr}Comments{/tr}" type="checkbox" name="allow_comments[{$types[user].article_type_id}]" {if $types[user].allow_comments eq 'y'}checked="checked"{/if} /></td>
								<td style="text-align:center;"><input title="{tr}Comment can Rate Article{/tr}" type="checkbox" name="comment_can_rate_article[{$types[user].article_type_id}]" {if $types[user].comment_can_rate_article eq 'y'}checked="checked"{/if} /></td>
								<td style="text-align:center;"><input title="{tr}Show image{/tr}" type="checkbox" name="show_image[{$types[user].article_type_id}]" {if $types[user].show_image eq 'y'}checked="checked"{/if} /></td>
								<td style="text-align:center;"><input title="{tr}Show avatar{/tr}" type="checkbox" name="show_avatar[{$types[user].article_type_id}]" {if $types[user].show_avatar eq 'y'}checked="checked"{/if} /></td>
								<td style="text-align:center;"><input title="{tr}Show author{/tr}" type="checkbox" name="show_author[{$types[user].article_type_id}]" {if $types[user].show_author eq 'y'}checked="checked"{/if} /></td>
								<td style="text-align:center;"><input title="{tr}Show publication date{/tr}" type="checkbox" name="show_pubdate[{$types[user].article_type_id}]" {if $types[user].show_pubdate eq 'y'}checked="checked"{/if} /></td>
								<td style="text-align:center;"><input title="{tr}Show expiration date{/tr}" type="checkbox" name="show_expdate[{$types[user].article_type_id}]" {if $types[user].show_expdate eq 'y'}checked="checked"{/if} /></td>
								<td style="text-align:center;"><input title="{tr}Show reads{/tr}" type="checkbox" name="show_reads[{$types[user].article_type_id}]" {if $types[user].show_reads eq 'y'}checked="checked"{/if} /></td>
								<td style="text-align:center;"><input title="{tr}Show size{/tr}" type="checkbox" name="show_size[{$types[user].article_type_id}]" {if $types[user].show_size eq 'y'}checked="checked"{/if} /></td>
								<td style="text-align:center;"><input title="{tr}Author can edit{/tr}" type="checkbox" name="creator_edit[{$types[user].article_type_id}]" {if $types[user].creator_edit eq 'y'}checked="checked"{/if} /></td>
								<td>
									{if $types[user].num_articles eq 0}
										{smartlink ititle="remove" ibiticon="liberty/delete" remove_type=`$types[user].article_type_id`}
									{else}
										{$types[user].num_articles}
									{/if}
								</td>
							</tr>
						{/section}
					</table>
					{formhelp note='hover over the number to see the column header'}

					<div class="row submit">
						<input type="submit" name="update_type" value="{tr}Apply changes{/tr}" /><br />
					</div>
				{/form}

				<dl>
					<dt>0 {tr}Name{/tr}</dt>
					<dd>{tr}Shows up in the drop down list of article types{/tr}</dd>
					<dt>1 {tr}Rate{/tr}</dt>
					<dd>{tr}Allow ratings by the author{/tr}</dd>
					<dt>2 {tr}Show before publish date{/tr}</dt>
					<dd>{tr}non-admins can view before the publish date{/tr}</dd>
					<dt>3 {tr}Show after expire date{/tr}</dt>
					<dd>{tr}non-admins can view after the expire date{/tr}</dd>
					<dt>4 {tr}Heading only{/tr}</dt>
					<dd>{tr}No article body, heading only{/tr}</dd>
					<dt>5 {tr}Comments{/tr}</dt>
					<dd>{tr}Allow comments for this type{/tr}</dd>
					<dt>6 {tr}Comment can Rate Article{/tr}</dt>
					<dd>{tr}Allow comments to include a rating value{/tr}</dd>
					<dt>7 {tr}Show image{/tr}</dt>
					<dd>{tr}Show topic or image{/tr}</dd>
					<dt>8 {tr}Show avatar{/tr}</dt>
					<dd>{tr}Show author's avatar{/tr}</dd>
					<dt>9 {tr}Show author{/tr}</dt>
					<dd>{tr}Show author's name{/tr}</dd>
					<dt>10 {tr}Show publish date{/tr}</dt>
					<dd>{tr}Show publication date{/tr}</dd>
					<dt>11 {tr}Show expiratio date{/tr}</dt>
					<dd>{tr}Show expiration date{/tr}</dd>
					<dt>12 {tr}Show reads{/tr}</dt>
					<dd>{tr}Show the number of times an article has been read{/tr}</dd>
					<dt>13 {tr}Show size{/tr}</dt>
					<dd>{tr}Show the size of the article{/tr}</dd>
					<dt>14 {tr}Creator can edit{/tr}</dt>
					<dd>{tr}The person who submits an article of this type can edit it{/tr}</dd>
					<dt>15 {tr}Remove Type{/tr}</dt>
					<dd>{tr}Delete this type (only possible if all articles of this type have been delted previously){/tr}</dd>
				</dl>
			{/jstab}

			{jstab title="Article Types - extended view"}
				{form legend="Modify Article Types"}
					{section name=user loop=$types}
						<input type="hidden" name="type_array[{$types[user].article_type_id}]" />
						<h2><a href="{$smarty.const.ARTICLES_PKG_URL}index.php?type={$types[user].article_type_id}">{tr}{$types[user].type_name}{/tr}</a></h2>

						<ul>
							<li><label><input type="checkbox" name="use_ratings[{$types[user].article_type_id}]" {if $types[user].use_ratings eq 'y'}checked="checked"{/if} /> {tr}Rate{/tr}</label></li>
							<li><label><input type="checkbox" name="show_pre_publ[{$types[user].article_type_id}]" {if $types[user].show_pre_publ eq 'y'}checked="checked"{/if} /> {tr}Show before publication date{/tr}</label></li>
							<li><label><input type="checkbox" name="show_post_expire[{$types[user].article_type_id}]" {if $types[user].show_post_expire eq 'y'}checked="checked"{/if} /> {tr}Show after expiration date{/tr}</label></li>
							<li><label><input type="checkbox" name="heading_only[{$types[user].article_type_id}]" {if $types[user].heading_only eq 'y'}checked="checked"{/if} /> {tr}Heading only{/tr}</label></li>
							<li><label><input type="checkbox" name="allow_comments[{$types[user].article_type_id}]" {if $types[user].allow_comments eq 'y'}checked="checked"{/if} /> {tr}Comments{/tr}</label></li>
							<li><label><input type="checkbox" name="comment_can_rate_article[{$types[user].article_type_id}]" {if $types[user].comment_can_rate_article eq 'y'}checked="checked"{/if} /> {tr}Comment can Rate Article{/tr}</label></li>
							<li><label><input type="checkbox" name="show_image[{$types[user].article_type_id}]" {if $types[user].show_image eq 'y'}checked="checked"{/if} /> {tr}Show image{/tr}</label></li>
							<li><label><input type="checkbox" name="show_avatar[{$types[user].article_type_id}]" {if $types[user].show_avatar eq 'y'}checked="checked"{/if} /> {tr}Show avatar{/tr}</label></li>
							<li><label><input type="checkbox" name="show_author[{$types[user].article_type_id}]" {if $types[user].show_author eq 'y'}checked="checked"{/if} /> {tr}Show author{/tr}</label></li>
							<li><label><input type="checkbox" name="show_pubdate[{$types[user].article_type_id}]" {if $types[user].show_pubdate eq 'y'}checked="checked"{/if} /> {tr}Show publication date{/tr}</label></li>
							<li><label><input type="checkbox" name="show_expdate[{$types[user].article_type_id}]" {if $types[user].show_expdate eq 'y'}checked="checked"{/if} /> {tr}Show expiration date{/tr}</label></li>
							<li><label><input type="checkbox" name="show_reads[{$types[user].article_type_id}]" {if $types[user].show_reads eq 'y'}checked="checked"{/if} /> {tr}Show reads{/tr}</label></li>
							<li><label><input type="checkbox" name="show_size[{$types[user].article_type_id}]" {if $types[user].show_size eq 'y'}checked="checked"{/if} /> {tr}Show size{/tr}</label></li>
							<li><label><input type="checkbox" name="creator_edit[{$types[user].article_type_id}]" {if $types[user].creator_edit eq 'y'}checked="checked"{/if} />{tr}Author can edit{/tr}</label></li>
							<li>
								{if $types[user].num_articles eq 0}
									<a title="{tr}Remove{/tr}" href="{$smarty.const.ARTICLES_PKG_URL}admin/admin_types.php?remove_type={$types[user].article_type_id}">{biticon ipackage=liberty iname=delete iexplain=remove}</a>
								{else}
									{$types[user].num_articles}
								{/if}
							</li>
						</ul>
					{/section}
					
					<div class="row submit">
						<input type="submit" name="update_type" value="{tr}Apply changes{/tr}" /><br />
					</div>
				{/form}
			{/jstab}

			{jstab title="Add Article Type"}
				{form legend="Create New Article Type"}
					<div class="row">
						{formlabel label="Create new Type" for="add_type"}
						{forminput}
							<input type="text" id="add_type" name="type_name" />
							{formhelp note="Enter the name of a new article type."}
						{/forminput}
					</div>

					<div class="row submit">
						<input type="submit" name="add_type" value="{tr}Create new article type{/tr}" />
					</div>
				{/form}
			{/jstab}
		{/jstabs}
	</div><!-- end .body -->
</div><!-- end .admin -->
{/strip}
