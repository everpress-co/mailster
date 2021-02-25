jtourdata = {
		'tourdata': [
			{
				html: "<h1>🎉 Explore new templates for your email marketing.</h1><p>Mailster offers a variety of free and premium email template you can.</p>",
				position: 'sw',
				live: false,
				buttons: [{
					label: 'Installed Templates',
					className: 'button button-primary',
					click: function(){
						this.stop();
						mailster.templates.filter('installed');
					}
				},{
					label: 'Popular Templates',
					className: 'button button-primary',
					click: function(){
						this.stop();
						mailster.templates.filter('popular');
					}
				},{
					label: 'Latest Templates',
					className: 'button button-primary',
					click: function(){
						this.stop();
						mailster.templates.filter('new');
					}
				}]
			},
			{
				element: 'a.mailster-icon.precheck',
				text: "Click here to go to the site",
				position: 'nw',
				live: false,
				expose: true,
			}
		],
		'options':
		{
			'axis': 'yx',
		}
	};