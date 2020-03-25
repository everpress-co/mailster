<style type="text/css">

.mailster-blank #poststuff, .mailster-blank #screen-meta-links { opacity: 0; display: none; }

h1.wp-heading-inline{
	 transition: all .5s ease-in-out;
}
.mailster-blank .wrap h1.wp-heading-inline{
	margin-left: 50%;
	transform: translateX(-50%);
}
.mailster-blank-state{
	transition: height .5s ease-in-out, opacity .5s ease-in-out;
	height: 1px;
	opacity: 0;
}
.mailster-blank .mailster-blank-state{
	max-width: 764px;
	text-align: center;
	margin: auto;
	height: 100vh;
	overflow: hidden;
	opacity: 1;
}
.mailster-blank-state-inner{
	display: flex;
	justify-content: space-around;
	flex-wrap: wrap;
	align-items: stretch;
	margin: 4em auto 1em;
}
.mailster-blank-state-inner li{
	cursor: pointer;
	position: relative;
	width: 30.6%;
	height: 300px;
	border: 1px solid #ddd;
	box-shadow: 0 1px 1px -1px rgba(0, 0, 0, 0.1);
	box-sizing: border-box;
}
.mailster-blank-state-inner li:focus, .mailster-blank-state-inner li:hover {
	border-color: #5b9dd9;
	box-shadow: 0 0 2px rgba(30, 140, 190, 0.8);
}
.mailster-blank-state-inner h4{
	bottom: 0;
	position: absolute;
	left: 0;
	right: 0;
}
</style>

<div class="mailster-blank-state">

	<h2><?php esc_html_e( 'Which type of campaign do you like to create?', 'mailster' ); ?></h2>

	<ul class="mailster-blank-state-inner">
		<li>
			<h4><?php esc_html_e( 'Regular Campaign', 'mailster' ); ?></h4>
		</li>
		<li>
			<h4><?php esc_html_e( 'Welcome New Subscribers', 'mailster' ); ?></h4>
		</li>
	</ul>
	<h2><?php esc_html_e( 'Send your latest posts', 'mailster' ); ?></h2>
	<ul class="mailster-blank-state-inner">
		<li>
			<h4><?php esc_html_e( 'When they are published', 'mailster' ); ?></h4>
		</li>
		<li>
			<h4><?php esc_html_e( 'In a given interval', 'mailster' ); ?></h4>
		</li>
	</ul>
	<h2><?php esc_html_e( 'Special Auto responders', 'mailster' ); ?></h2>
	<ul class="mailster-blank-state-inner">
		<li>
			<h4><?php esc_html_e( 'Follow Up Campaign', 'mailster' ); ?></h4>
		</li>
		<li>
			<h4><?php esc_html_e( 'User Time based Campaign', 'mailster' ); ?></h4>
		</li>
		<li>
			<h4><?php esc_html_e( 'Action Hook based Campaign', 'mailster' ); ?></h4>
		</li>
	</ul>

	<p><label><input type="checkbox"> <?php esc_html_e( 'Do not show again!', 'mailster' ); ?></label></p>

</div>
