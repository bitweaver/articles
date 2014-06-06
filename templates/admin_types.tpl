{* $Header$ *}
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
					<table class="table data">
						<caption>{tr}Article Types{/tr}</caption>
						<tr>
							<th style="width:30%;" title="{tr}Name{/tr}">{tr}Name{/tr}</th>
							{foreach from=$artTypes item=artType}
								<th style="width:5%;" title="{$artType.name}">{counter name=th}</th>
							{/foreach}
							<th style="width:5%" title="{tr}Remove Type{/tr}">{counter name=th}</th>
						</tr>
						{section name=user loop=$types}
							<tr class="{cycle values="odd,even"}">
								<td>
									<input type="hidden" name="type_array[{$types[user].article_type_id}]" />
									<input type="text" name="type_name[{$types[user].article_type_id}]" value="{$types[user].type_name}" />
									<a href="{$smarty.const.ARTICLES_PKG_URL}index.php?type_id={$types[user].article_type_id}">{booticon iname="icon-search"   iexplain="List Articles"}</a>
								</td>
								{foreach from=$artTypes item=artType key=key}
									<td style="text-align:center;"><input title="{$artType.name}" type="checkbox" name="{$key}[{$types[user].article_type_id}]" {if $types[user].$key eq 'y'}checked="checked"{/if} /></td>
								{/foreach}
								<td style="text-align:center;">
									{if $types[user].article_cnt eq 0}
										{smartlink ititle="remove" booticon="icon-trash" remove_type=$types[user].article_type_id}
									{else}
										{$types[user].article_cnt}
									{/if}
								</td>
							</tr>
						{/section}
					</table>
					{formhelp note='hover over the number to see the column header'}

					<div class="form-group submit">
						<input type="submit" class="btn btn-default" name="update_type" value="{tr}Apply changes{/tr}" /><br />
					</div>
				{/form}

				<dl>
					<dt>{counter start=0}</dt></dt>
					<dd>
						{tr}Name{/tr}<br />
						<small>{tr}Shows up in the drop down list of article types{/tr}</small>
					</dd>
					{foreach from=$artTypes item=artType}
						<dt>{counter}</dt>
						<dd>
							{$artType.name}<br />
							<small>{$artType.desc}</small>
						</dd>
					{/foreach}
					<dt>{counter}</dt>
					<dd>
						{tr}Remove Type{/tr}<br />
						<small>{tr}Delete this type (only possible if all articles of this type have been deleted previously){/tr}</small>
					</dd>
				</dl>
			{/jstab}

			{jstab title="Article Types - extended view"}
				{form legend="Modify Article Types"}
					{section name=user loop=$types}
						<input type="hidden" name="type_array[{$types[user].article_type_id}]" />
						<h2><a href="{$smarty.const.ARTICLES_PKG_URL}index.php?type_id={$types[user].article_type_id}">{tr}{$types[user].type_name}{/tr}</a></h2>

						<ul>
							{foreach from=$artTypes item=artType key=key}
								<li><label><input type="checkbox" name="{$key}[{$types[user].article_type_id}]" {if $types[user].$key eq 'y'}checked="checked"{/if} /> {$artType.name}</label></li>
							{/foreach}
							<li>
								{if $types[user].article_cnt eq 0}
									<a title="{tr}Remove{/tr}" href="{$smarty.const.ARTICLES_PKG_URL}admin/admin_types.php?remove_type={$types[user].article_type_id}">{booticon iname="icon-trash" ipackage="icons" iexplain=remove}</a> {tr}Remove Type{/tr}
								{else}
									{tr}Number of Entries{/tr}: {$types[user].article_cnt}
								{/if}
							</li>
						</ul>
					{/section}

					<div class="form-group submit">
						<input type="submit" class="btn btn-default" name="update_type" value="{tr}Apply changes{/tr}" /><br />
					</div>
				{/form}
			{/jstab}

			{jstab title="Add Article Type"}
				{form legend="Create New Article Type"}
					<div class="form-group">
						{formlabel label="Create new Type" for="add_type"}
						{forminput}
							<input type="text" id="add_type" name="type_name" />
							{formhelp note="Enter the name of a new article type."}
						{/forminput}
					</div>

					<div class="form-group submit">
						<input type="submit" class="btn btn-default" name="add_type" value="{tr}Create new article type{/tr}" />
					</div>
				{/form}
			{/jstab}
		{/jstabs}
	</div><!-- end .body -->
</div><!-- end .admin -->
{/strip}
