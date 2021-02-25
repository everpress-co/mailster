jtourdata = {
		'tourdata': [
			{
				html: "<h1>🎉 New Precheck Feature</h1><p>My name is Xaver and I am a product designer the tour.<br>To demonstrate how this can bring value to your customers we prepared this short demo tour.</p>",
				offset: 20,
				position: 'sw',
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