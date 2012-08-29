<div class="wrap">
	<div id="icon-options-general" class="icon32"><br /></div>
	<h2>Post Type Archives</h2>
	
	<form action="options.php" method="post">
		<?php settings_fields('pta-setting-group'); ?>
		
		<table class="form-table">
			<tr valign="middle">
				<th scope="row">
					<label for="pta-url-base">URL base</label>
				</th>
				<td>
					<input
						type="text"
						name="pta-url-base"
						id="pta-url-base"
						size="50"
						value="<?php echo $url_base; ?>"
					/>
				</td>
			</tr>
			
			<tr valign="middle">
				<th scope="row">
					<label for="pta-use-rewrite-slug">Use Rewrite Slug</label>
				</th>
				<td>
					<input
						type="checkbox"
						name="pta-use-rewrite-slug"
						id="pta-use-rewrite-slug"
						<?php if($use_rewrite_slug) echo 'checked="checked"'; ?>
						value="1"
					/>
				</td>
			</tr>
			
			<tr valign="middle">
				<th scope="row">
					<label for="pta-title">Title</label>
				</th>
				<td>
					<input
						type="text"
						name="pta-title"
						id="pta-title"
						size="50"
						value="<?php echo $title; ?>"
					/>
				</td>
			</tr>
			
			<tr valign="middle">
				<th scope="row">
					<label for="pta-template-pattern">Template Pattern</label>
				</th>
				<td>
					<input
						type="text"
						name="pta-template-pattern"
						id="pta-template-pattern"
						size="50"
						value="<?php echo $template_pattern; ?>"
					/>
				</td>
			</tr>
			
			<tr valign="middle">
				<th scope="row">
					<label for="pta-fallback-template">Fallback Template</label>
				</th>
				<td>
					<input
						type="text"
						name="pta-fallback-template"
						id="pta-fallback-template"
						size="50"
						value="<?php echo $fallback_template; ?>"
					/>
				</td>
			</tr>
			
			<?php if(!current_theme_supports('automatic-feed-links')) : ?>
				<tr valign="middle">
					<th scope="row">
						<label for="pta-enable-feed-links">Enable feed links</label>
					</th>
					<td>
						<input
							type="checkbox"
							name="pta-enable-feed-links"
							id="pta-enable-feed-links"
							<?php if($enable_feed_links) echo 'checked="checked"'; ?>
							value="1"
						/>
					</td>
				</tr>
			<?php endif; ?>
			
			<tr valign="top">
				<th scope="row">Enabled Custom Post Type Archives</th>
				<td>
					<?php foreach(get_post_types(array(), 'object') as $post_type_slug => $post_type) : ?>
						<input
							type="checkbox"
							name="pta-enabled-post-type-archives[]"
							<?php if(in_array($post_type_slug, $enabled_post_type_archives)) echo 'checked="checked"'; ?>
							value="<?php echo $post_type_slug; ?>"
						/>
						<?php echo $post_type->labels->singular_name; ?><br />
						<?php if(in_array($post_type_slug, $enabled_post_type_archives)) : ?>
							<em style="margin-left:40px">
								<strong>URL</strong> &raquo; <?php the_post_type_permalink($post_type_slug); ?>
							</em><br />
							<em style="margin-left:40px">
								<strong>Template</strong> &raquo; <?php echo pta_register_post_type_redirects('None', $post_type_slug); ?>
							</em><br />
							<em style="margin-left:40px">
								<label for="pta-enabled-post-type-customisations-<?php echo $post_type_slug; ?>-title" style="font-weight:bold">Title</label> &raquo;
								<input
									type="text"
									name="pta-enabled-post-type-customisations[<?php echo $post_type_slug ?>][title]"
									id="pta-enabled-post-type-customisations-<?php echo $post_type_slug; ?>-title"
									size="50"
									value="<?php echo @ $enabled_post_type_customisations[$post_type_slug]['title']; ?>"
								/>
							</em><br />
							<?php if($use_rewrite_slug) : ?>
							<em style="margin-left:40px">
								<label for="pta-enabled-post-type-customisations-<?php echo $post_type_slug; ?>-rewrite-slug" style="font-weight:bold">Rewrite Slug</label> &raquo;
								<input
									type="text"
									name="pta-enabled-post-type-customisations[<?php echo $post_type_slug ?>][rewrite_slug]"
									id="pta-enabled-post-type-customisations-<?php echo $post_type_slug; ?>-rewrite-slug"
									value="<?php echo @ $enabled_post_type_customisations[$post_type_slug]['rewrite_slug']; ?>"
									size="50"
								/>
							</em><br />
							<?php endif; ?>
						<?php endif; ?>
					<?php endforeach; ?>
				</td>
			</tr>
		</table>
		
		<p>
			<input type="submit" value="Save" class="button-primary" />
		</p>
	</form>
</div>