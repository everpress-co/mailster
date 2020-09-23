<div class="wrap">
<div id="mailster_templates">

<h1><?php esc_html_e( 'Templates', 'mailster' ); ?> <a class="page-title-action upload-template"> <?php esc_html_e( 'Upload Template', 'mailster' ); ?> </a></h1>

<h2 class="screen-reader-text hide-if-no-js"><?php esc_html_e( 'Filter template list', 'mailster' ); ?></h2>
<div class="wp-filter hide-if-no-js">
	<div class="filter-count">
		<span class="count theme-count">3</span>
	</div>

	<ul class="filter-links">
		<li><a href="#" data-sort="installed"><?php _ex( 'Installed', 'templates', 'mailster' ); ?></a></li>
		<li><a href="#" data-sort="featured"><?php _ex( 'Featured', 'templates', 'mailster' ); ?></a></li>
		<li><a href="#" data-sort="popular"><?php _ex( 'Popular', 'templates', 'mailster' ); ?></a></li>
		<li><a href="#" data-sort="new"><?php _ex( 'Latest', 'templates', 'mailster' ); ?></a></li>
	</ul>

	<?php get_search_form(); ?>

</div>

<h2 class="screen-reader-text hide-if-no-js"><?php esc_html_e( 'Template list', 'mailster' ); ?></h2>
<div class="template-browser content-filterable"></div>
<p class="no-templates"><?php esc_html_e( 'No templates found. Try a different search.', 'mailster' ); ?></p>
<span class="spinner"></span>

<div id="ajax-response"></div>
<br class="clear">
</div>
