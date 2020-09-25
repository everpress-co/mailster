<div class="wrap">
<h1><?php esc_html_e( 'Templates', 'mailster' ); ?> <a class="page-title-action upload-template"> <?php esc_html_e( 'Upload Template', 'mailster' ); ?> </a></h1>

<h2 class="screen-reader-text hide-if-no-js"><?php esc_html_e( 'Filter template list', 'mailster' ); ?></h2>
<div class="wp-filter hide-if-no-js">
	<div class="filter-count">
		<span class="count theme-count"></span>
	</div>

	<ul class="filter-links">
		<li><a href="#" data-sort="installed"><?php _ex( 'Installed', 'templates', 'mailster' ); ?></a></li>
		<li><a href="#" data-sort="featured"><?php _ex( 'Featured', 'templates', 'mailster' ); ?></a></li>
		<li><a href="#" data-sort="free"><?php _ex( 'Free', 'templates', 'mailster' ); ?></a></li>
		<li><a href="#" data-sort="popular"><?php _ex( 'Popular', 'templates', 'mailster' ); ?></a></li>
		<li><a href="#" data-sort="new"><?php _ex( 'Latest', 'templates', 'mailster' ); ?></a></li>
		<li><a href="#" data-sort="updated"><?php _ex( 'Recently Updated', 'templates', 'mailster' ); ?></a></li>
	</ul>

	<form class="search-form" method="get">
		<input type="hidden" name="tab" value="search">
		<label class="screen-reader-text" for="typeselector"><?php esc_html_e( 'Search Templates by', 'mailster' ); ?>:</label>
		<select name="type" id="typeselector">
			<option value="term" selected="selected"><?php esc_html_e( 'Keyword', 'mailster' ); ?></option>
			<option value="author"><?php esc_html_e( 'Author', 'mailster' ); ?></option>
			<option value="tag"><?php esc_html_e( 'Tag', 'mailster' ); ?></option>
		</select>
		<label class="screen-reader-text" for="search-plugins"><?php esc_html_e( 'Search Templates', 'mailster' ); ?></label>
		<input type="search" name="s" id="search-plugins" value="" class="wp-filter-search" placeholder="<?php esc_attr_e( 'Search templates', 'mailster' ); ?>..." aria-describedby="live-search-desc">
		<input type="submit" id="search-submit" class="button hide-if-js" value="<?php esc_attr_e( 'Search Templates', 'mailster' ); ?>">
	</form>


</div>

<div class="notice notice-alt notice-large inline theme-notice-free">
	<h3 class="notice-title">Update Available</h3>
	<p><strong>There is a new version of Chaplin available. <a href="https://wordpress.org/themes/chaplin/?TB_iframe=true&amp;width=1024&amp;height=800" class="thickbox open-plugin-details-modal" aria-label="View Chaplin version 2.5.16 details">View version 2.5.16 details</a> or <a href="https://fresh.local/wp-admin/update.php?action=upgrade-theme&amp;theme=chaplin&amp;_wpnonce=4d874535f3" aria-label="Update Chaplin now" id="update-theme" data-slug="chaplin">update now</a>.</strong></p>
</div>
<div class="notice notice-alt notice-large inline theme-notice-popular">
	<p>Browser the most popular templates for Mailster</p>
</div>
<div class="notice notice-alt notice-large inline theme-notice-updated">
	<p>This page displayed the most recent updated templates</p>
</div>

<h2 class="screen-reader-text hide-if-no-js"><?php esc_html_e( 'Template list', 'mailster' ); ?></h2>
<div class="theme-browser content-filterable _single-theme"></div>

<div class="theme-overlay hidden" tabindex="0" role="dialog" aria-label="Theme Details">
	<div class="theme-overlay">
		<div class="theme-backdrop"></div>
		<div class="theme-wrap wp-clearfix" role="document">
			<div class="theme-header">
				<button class="left dashicons dashicons-no"><span class="screen-reader-text">Show previous theme</span></button>
				<button class="right dashicons dashicons-no"><span class="screen-reader-text">Show next theme</span></button>
				<button class="close dashicons dashicons-no"><span class="screen-reader-text">Close details dialog</span></button>
			</div>
			<div class="theme-about wp-clearfix">
				<div class="theme-screenshots">
					<div class="screenshot">
						<img src="" alt="">
						<iframe src="" allowTransparency="true" frameBorder="0" sandbox="allow-presentation"></iframe>
					</div>
				</div>
				<div class="theme-info">
					<h2 class="theme-name"></h2>
					<p class="theme-author">
						<div class="notice notice-warning notice-alt notice-large inline">
							<h3 class="notice-title">Update Available</h3>
							<p><strong>There is a new version of Chaplin available. <a href="https://wordpress.org/themes/chaplin/?TB_iframe=true&amp;width=1024&amp;height=800" class="thickbox open-plugin-details-modal" aria-label="View Chaplin version 2.5.16 details">View version 2.5.16 details</a> or <a href="https://fresh.local/wp-admin/update.php?action=upgrade-theme&amp;theme=chaplin&amp;_wpnonce=4d874535f3" aria-label="Update Chaplin now" id="update-theme" data-slug="chaplin">update now</a>.</strong></p>
						</div>
					</p>
					<div class="theme-autoupdate">
						<div class="notice notice-error notice-alt inline hidden"><p></p></div>
					</div>
					<p class="theme-description"></p>
					<p class="theme-tags"></p>
				</div>
			</div>

			<div class="theme-actions">
				<div class="active-theme">
					<a href="https://fresh.local/wp-admin/customize.php?theme=Cayse&amp;return=%2Fwp-admin%2Fthemes.php" class="button button-primary customize load-customize hide-if-no-customize">Customize</a>
					<a class="button" href="widgets.php">Widgets</a> <a class="button" href="nav-menus.php">Menus</a>
					<a class="button duplicate-campaign" href="" aria-label="<?php esc_attr_e( 'Duplicate Campaign', 'mailster' ); ?>"><?php esc_html_e( 'Duplicate', 'mailster' ); ?></a>
				</div>
				<div class="inactive-theme">
					<a href="https://fresh.local/wp-admin/themes.php?action=activate&amp;stylesheet=Cayse&amp;_wpnonce=89d90450a2" class="button activate" aria-label="Activate Cayse">Activate</a>
					<a href="https://fresh.local/wp-admin/customize.php?theme=Cayse&amp;return=%2Fwp-admin%2Fthemes.php" class="button button-primary load-customize hide-if-no-customize">Live Preview</a>
				</div>
				<a href="https://fresh.local/wp-admin/themes.php?action=delete&amp;stylesheet=Cayse&amp;_wpnonce=a1ced50138" class="button delete-theme">Delete</a>
			</div>
		</div>
	</div>
</div>



<p class="no-themes"><?php esc_html_e( 'No templates found. Try a different search.', 'mailster' ); ?></p>
<span class="spinner"></span>

</div>
