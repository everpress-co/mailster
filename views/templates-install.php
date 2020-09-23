<div class="wrap">
<div id="mailster_templates">

<h1><?php esc_html_e( 'Add Templates', 'mailster' ); ?> <a class="page-title-action upload-template"> <?php esc_html_e( 'Upload Template', 'mailster' ); ?> </a></h1>

<h2 class="screen-reader-text hide-if-no-js">Filter themes list</h2>
<div class="wp-filter hide-if-no-js">
	<div class="filter-count">
		<span class="count theme-count">3</span>
	</div>

	<ul class="filter-links">
		<li><a href="#" data-sort="featured"><?php _ex( 'Featured', 'themes' ); ?></a></li>
		<li><a href="#" data-sort="popular"><?php _ex( 'Popular', 'themes' ); ?></a></li>
		<li><a href="#" data-sort="new"><?php _ex( 'Latest', 'themes' ); ?></a></li>
		<li><a href="#" data-sort="favorites"><?php _ex( 'Favorites', 'themes' ); ?></a></li>
	</ul>

	<form class="search-form"></form>

</div>

<h2 class="screen-reader-text hide-if-no-js">Themes list</h2>
<div class="template-browser content-filterable"></div>

<div class="clear affiliate-note description">
	Disclosure: Some of the links on this page are affiliate links. This means if you click on the link and purchase the item, we may receive an affiliate commission.
</div>

<div id="thickboxbox">
	<ul class="thickbox-filelist"></ul>
	<iframe class="thickbox-iframe" src="" data-no-lazy=""></iframe>
</div>
<div id="ajax-response"></div>
<br class="clear">
</div>
