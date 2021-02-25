jtourdata = {
		'tourdata': [
			{
				html: "<h1>Welcome to Mailster!</h1><p>This tour will help you getting started.</p><p>Use your arrow keys to navigate from one step to the next one. You can pause the campaigns with your space bar and end it with Esc.</p>",
				live: false,
				buttons: [{
					className: '',
					label: 'No thanks!',
					click: function(){
						this.stop();
					}
				},{
					label: 'Start Tour!',
					className: 'button button-primary button-hero',
					click: function(){
						this.next();
					}
				}]
			},
			{
				html: "<h2>This is your Dashboard</h2><p>The start and overview.</p>",
				overlayOpacity: 0.1,
			},
			{
				element: '#mailster-mb-quick-links',
				html: "<h2>Quick Links</h2><p>The place where you have quick access to your campaigns, subscribers, lists and forms.</p>",
				position: 'e',
				expose: true,
			},
			{
				element: '#mailster-mb-campaigns',
				html: "<h2>Campaigns</h2><p>Find your most recent campaign here.</p>",
				position: 'e',
				expose: true,
			},
			{
				element: '#mailster-mb-mailster',
				html: "<h2><strike>My</strike> Your Mailster</h2><p>Your place to astart.</p>",
				position: 'w',
				expose: true,
			},
			{
				element: '#mailster-mb-lists',
				html: "<h2>Quick Links</h2><p>The place where you have quick access to your campaigns, subscribers, lists and forms.</p>",
				position: 'w',
				expose: true,
			},
			{
				element: '#menu-posts-newsletter',
				html: "<p>All Newsletter related things can be found in this menu.</p>",
				position: 'e',
				overlayOpacity: 0.7,
				expose: true,
			},
			{
				element: '#menu-posts-newsletter li',
				index: 2,
				html: "<p>Find all your Campaign here.</p>",
				overlayOpacity: 0.7,
				exposeOffset: 10,
				position: 'e',
				expose: true,
			},
			{
				element: '#menu-posts-newsletter li',
				index: 3,
				html: "<p>Create a new Campaign.</p>",
				overlayOpacity: 0.7,
				exposeOffset: 10,
				position: 'e',
				expose: true,
			},
			{
				element: '#menu-posts-newsletter li',
				index: 4,
				html: "<p>Create a new Autoresponder which is for your email marketing automation.</p>",
				overlayOpacity: 0.7,
				exposeOffset: 10,
				position: 'e',
				expose: true,
			},
			{
				element: '#menu-posts-newsletter li',
				index: 5,
				html: "<p>Find your subscribers here.</p>",
				overlayOpacity: 0.7,
				exposeOffset: 10,
				position: 'e',
				expose: true,
			},
			{
				element: '#menu-posts-newsletter li',
				index: 6,
				html: "<p>Find your Forms here.</p>",
				overlayOpacity: 0.7,
				exposeOffset: 10,
				position: 'e',
				expose: true,
			},
			{
				element: '#menu-posts-newsletter li',
				index: 7,
				html: "<p>Find your Lists here.</p>",
				overlayOpacity: 0.7,
				exposeOffset: 10,
				position: 'e',
				expose: true,
			},
			{
				html: "<h1>Now let's create a campaign.</h1>",
				live: 20000000,
				expose: true,
				buttons: [{
					className: '',
					label: 'No thanks!',
					click: function(){
						this.stop();
					}
				},{
					label: 'Create Campaign',
					className: 'button button-primary button-hero',
					click: function(){
						this.stop();
						window.location = 'post-new.php?post_type=newsletter';
					}
				}]

			}
		],
		'options':
		{
			'speed': 0.5,
			'startAt': 0
		}
	};