<div class="wrap">
<div id="mailster_templates">

<h1><?php esc_html_e( 'Templates', 'mailster' ); ?> <a class="page-title-action upload-template"> <?php esc_html_e( 'Upload Template', 'mailster' ); ?> </a></h1>

<h2 class="screen-reader-text hide-if-no-js"><?php esc_html_e( 'Filter template list', 'mailster' ); ?></h2>
<div class="wp-filter hide-if-no-js">
	<div class="filter-count">
		<span class="count template-count">3</span>
	</div>

	<ul class="filter-links">
		<li><a href="#" data-sort="installed"><?php _ex( 'Installed', 'templates', 'mailster' ); ?></a></li>
		<li><a href="#" data-sort="featured"><?php _ex( 'Featured', 'templates', 'mailster' ); ?></a></li>
		<li><a href="#" data-sort="free"><?php _ex( 'Free', 'templates', 'mailster' ); ?></a></li>
		<li><a href="#" data-sort="popular"><?php _ex( 'Popular', 'templates', 'mailster' ); ?></a></li>
		<li><a href="#" data-sort="new"><?php _ex( 'Latest', 'templates', 'mailster' ); ?></a></li>
		<li><a href="#" data-sort="updated"><?php _ex( 'Recently Updated', 'templates', 'mailster' ); ?></a></li>
	</ul>

	<?php get_search_form(); ?>

</div>

<h2 class="screen-reader-text hide-if-no-js"><?php esc_html_e( 'Template list', 'mailster' ); ?></h2>
<div class="template-browser content-filterable"></div>



<div class="template-overlay" tabindex="0" role="dialog" aria-label="Theme Details">
	<div class="template-overlay">
		<div class="template-backdrop"></div>
		<div class="template-wrap wp-clearfix" role="document">
			<div class="template-header">
				<button class="left dashicons dashicons-no"><span class="screen-reader-text">Show previous template</span></button>
				<button class="right dashicons dashicons-no"><span class="screen-reader-text">Show next template</span></button>
				<button class="close dashicons dashicons-no"><span class="screen-reader-text">Close details dialog</span></button>
			</div>
			<div class="template-about wp-clearfix">
				<div class="template-screenshots">
					<div class="screenshot"><img src="" alt=""></div>
				</div>
				<div class="template-info">
					<h2 class="template-name"></h2>
					<p class="template-author">
						By <span class="template-author-name"><a href="http://shop.playnetemplates.com">Playne Themes</a></span>
					</p>
					<div class="template-autoupdate">
						<div class="notice notice-error notice-alt inline hidden"><p></p></div>
					</div>
					<p class="template-description"></p>
					<p class="template-tags"></p>
				</div>
			</div>

			<div class="template-actions">
				<div class="active-template">
					<a href="https://fresh.local/wp-admin/customize.php?template=Cayse&amp;return=%2Fwp-admin%2Ftemplates.php" class="button button-primary customize load-customize hide-if-no-customize">Customize</a>
					<a class="button" href="widgets.php">Widgets</a> <a class="button" href="nav-menus.php">Menus</a>
				</div>
				<div class="inactive-template">
					<a href="https://fresh.local/wp-admin/templates.php?action=activate&amp;stylesheet=Cayse&amp;_wpnonce=89d90450a2" class="button activate" aria-label="Activate Cayse">Activate</a>
					<a href="https://fresh.local/wp-admin/customize.php?template=Cayse&amp;return=%2Fwp-admin%2Ftemplates.php" class="button button-primary load-customize hide-if-no-customize">Live Preview</a>
				</div>
				<a href="https://fresh.local/wp-admin/templates.php?action=delete&amp;stylesheet=Cayse&amp;_wpnonce=a1ced50138" class="button delete-template">Delete</a>
			</div>
		</div>
	</div>
</div>



<p class="no-templates"><?php esc_html_e( 'No templates found. Try a different search.', 'mailster' ); ?></p>
<span class="spinner"></span>

<div id="ajax-response"></div>
<br class="clear">
</div>
