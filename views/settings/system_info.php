<h4><?php esc_html_e( 'System Test', 'mailster' );?></h4>
<p><?php esc_html_e( 'Run a System Test to get more info about issues with Mailster on your server.', 'mailster' );?></p>
<p><a class="button button-primary button-hero" href="<?php echo admin_url( 'admin.php?page=mailster_tests' ); ?>"><?php esc_html_e( 'Start System Test', 'mailster' );?></a>
</p>
<p class="description"><?php printf( __( 'Please check out %s if you have problems with the plugin', 'mailster' ), '<a href="https://rxa.li/support?utm_source=Mailster+System+Info+Page" class="external">' . __( 'our Knowledge base', 'mailster' ) . '</a>' ); ?></p>
<div id="system_info_output">
</div>
<textarea id="system_info_content" readonly class="code">
</textarea>
<a class="clipboard" data-clipboard-target="#system_info_content"><?php esc_html_e( 'Copy Info to Clipboard', 'mailster' ) ?></a>
