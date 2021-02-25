jtourdata = {
	'tourdata': [
			{
				// element: '#mailster-mb-quick-links',
				html: "<h1>Welcome</h1><p>My name is Xaver and I am a product designer the tour.<br>To demonstrate how this can bring value to your customers we prepared this short demo tour.</p>",
				width: '400px',
				live:100000,
				onShow: function(){
					jQuery( '#dashboard_activity, #dashboard_primary' ).css(
						{
							_margin: '200px 0 200px 0'
						}
					)
					jQuery( '#dashboard_quick_press' ).css(
						{
							_padding: '200px'
						}
					)

				},
				buttons: [{
					className: '',
					label: 'Not now',
					click: function(){
						this.stop();
					}
				},{
					label: 'Start Tour!',
					className: 'button button-primary button-hero',
					click: function(){
						this.next();
					}
				}],
				//autoplay: false,
			},{
				text: "Hello EDIT!",
				offset: 20,
				position: 'sw'
			},
			{
				text: "Click here to go to the site",
				offset: 20,
				position: 'sw'
			}
		]
	};
