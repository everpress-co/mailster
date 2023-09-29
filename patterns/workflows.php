<?php


register_block_pattern(
	'mailster-workflow/scratch',
	array(
		'title'         => __( 'Start from scratch', 'mailster' ),
		'description'   => __( 'Create your own custom workflow on a blank canvas.', 'mailster' ),
		'viewportWidth' => 300,
		'postTypes'     => array( 'mailster-workflow' ),
		'categories'    => array( 'mailster-custom-category' ),
		'content'       => '<!-- wp:mailster-workflow/triggers -->
		<!-- wp:mailster-workflow/trigger /-->
		<!-- /wp:mailster-workflow/triggers -->',
	)
);

register_block_pattern(
	'mailster-workflow/simple-welcome',
	array(
		'title'         => __( 'Welcome new subscribers', 'mailster' ),
		'postTypes'     => array( 'mailster-workflow' ),
		'description'   => __(
			'This simple and efficient workflow is perfect for welcoming new subscribers. It is easy to set up and produces great outcomes. Once an individual subscribes to your form and is added to a list, the workflow will automatically send them a welcome email.',
			'mailster'
		),
		'viewportWidth' => 600,
		'categories'    => array( 'mailster-custom-category' ),
		'content'       => '<!-- wp:mailster-workflow/triggers -->
		<!-- wp:mailster-workflow/trigger {"trigger":"list_add","lists":[-1]} /-->
		<!-- /wp:mailster-workflow/triggers -->
		
		<!-- wp:mailster-workflow/comment {"comment":"Send a welcome email whenever a user subscribes to your lists."} /-->
		
		<!-- wp:mailster-workflow/email {"id":"6332d1","name":"Welcome Email"} /-->',
	)
);


register_block_pattern(
	'mailster-workflow/enhanced-welcome',
	array(
		'title'         => __( 'Enhanced Welcome Email Series', 'mailster' ),
		'postTypes'     => array( 'mailster-workflow' ),
		'description'   => __(
			'Employ various channels such as sign up forms or landing pages to activate the identical workflow for your new subscribers. All new subscribers will be sent the same welcome campaign. Additionally, you can modify a custom field for the engaged subscribers and shift the unresponsive ones to a designated group for future targeting.',
			'mailster'
		),
		'viewportWidth' => 600,
		'categories'    => array( 'mailster-custom-category' ),
		'content'       => '<!-- wp:mailster-workflow/triggers -->
		<!-- wp:mailster-workflow/trigger {"trigger":"list_add","lists":[-1]} /-->
		<!-- /wp:mailster-workflow/triggers -->
		
		<!-- wp:mailster-workflow/email {"id":"bf5811","name":"Welcome Email"} /-->
		
		<!-- wp:mailster-workflow/delay {"id":"272e1c","amount":1,"unit":"days"} /-->
		
		<!-- wp:mailster-workflow/email {"id":"71804e","name":"Preferences Email"} /-->
		
		<!-- wp:mailster-workflow/delay {"id":"3dac13","amount":3,"unit":"days"} /-->
		
		<!-- wp:mailster-workflow/conditions {"id":"e2447d","conditions":"conditions%5B0%5D%5B0%5D%5Bfield%5D=_click\u0026conditions%5B0%5D%5B0%5D%5Boperator%5D=is\u0026conditions%5B0%5D%5B0%5D%5Bvalue%5D%5B0%5D=bf5811\u0026conditions%5B0%5D%5B0%5D%5Bvalue%5D%5B1%5D=71804e"} -->
		<!-- wp:mailster-workflow/condition-yes -->
		<!-- wp:mailster-workflow/action {"id":"21ad89","action":"add_tag"} /-->
		
		<!-- wp:mailster-workflow/email {"id":"3431f0","name":"Offer #1"} /-->
		<!-- /wp:mailster-workflow/condition-yes -->
		
		<!-- wp:mailster-workflow/condition-no -->
		<!-- wp:mailster-workflow/email {"id":"cf181c","name":"Welcome"} /-->
		
		<!-- wp:mailster-workflow/delay {"id":"b7b1fa","amount":3,"unit":"hours"} /-->
		
		<!-- wp:mailster-workflow/conditions {"id":"e97c5f","conditions":"conditions%5B0%5D%5B0%5D%5Bfield%5D=_click\u0026conditions%5B0%5D%5B0%5D%5Boperator%5D=is\u0026conditions%5B0%5D%5B0%5D%5Bvalue%5D=cf181c"} -->
		<!-- wp:mailster-workflow/condition-yes -->
		<!-- wp:mailster-workflow/action {"id":"b426fd","action":"add_tag"} /-->
		<!-- /wp:mailster-workflow/condition-yes -->
		
		<!-- wp:mailster-workflow/condition-no -->
		<!-- wp:mailster-workflow/action {"id":"5b8177","action":"unsubscribe"} /-->
		<!-- /wp:mailster-workflow/condition-no -->
		<!-- /wp:mailster-workflow/conditions -->
		<!-- /wp:mailster-workflow/condition-no -->
		<!-- /wp:mailster-workflow/conditions -->',
	)
);



register_block_pattern(
	'mailster-workflow/pagevisit',
	array(
		'title'         => __( 'Workflow for page visits', 'mailster' ),
		'description'   => __( 'This workflow serves as a valuable tool for targeting subscribers who have visited specific pages. By leveraging this workflow, you can segment your audience based on their browsing behavior, enabling you to deliver personalized campaigns, recommend relevant content, and provide targeted offers.', 'mailster' ),
		'viewportWidth' => 600,
		'postTypes'     => array( 'mailster-workflow' ),
		'categories'    => array( 'mailster-custom-category' ),
		'content'       => '<!-- wp:mailster-workflow/triggers -->
		<!-- wp:mailster-workflow/trigger {"trigger":"page_visit","pages":["/hello-world"]} /-->
		<!-- /wp:mailster-workflow/triggers -->
		
		<!-- wp:mailster-workflow/delay {"amount":10,"unit":"minutes"} /-->
		
		<!-- wp:mailster-workflow/email {"name":"Email #1"} /--><!-- wp:mailster-workflow/action {"action":"add_tag","tags":["Visited"]} /-->',
	)
);


register_block_pattern(
	'mailster-workflow/win-back-subscribers',
	array(
		'title'         => __( 'Win back inactive subscribers', 'mailster' ),
		// 'article'       => 'https://example.com',
		'description'   => __( 'Re-engage subscribers who have shown a lack of engagement and remove inactive ones. You can initiate a campaign and update a custom field if they interact, or transfer them to the unsubscribed folder if they do not. This approach effectively purges your list, ensuring that your most active subscribers remain, leading to improved email deliverability.', 'mailster' ),
		'viewportWidth' => 600,
		'postTypes'     => array( 'mailster-workflow' ),
		'categories'    => array( 'mailster-custom-category' ),
		'content'       => '<!-- wp:mailster-workflow/triggers -->
		<!-- wp:mailster-workflow/trigger {"trigger":"updated_field","field":"-1"} /-->
		<!-- /wp:mailster-workflow/triggers -->

		<!-- wp:mailster-workflow/comment {"comment":"Whenever a field is updated  - either by the user or the admin - send a special offer."} /-->

		<!-- wp:mailster-workflow/email {"id":"9869d5","name":"Special Offer"} /-->

		<!-- wp:mailster-workflow/delay {"id":"1e5fca","amount":3,"unit":"days"} /-->

		<!-- wp:mailster-workflow/conditions {"id":"fc8a35","conditions":"conditions%5B0%5D%5B0%5D%5Bfield%5D=_click\u0026conditions%5B0%5D%5B0%5D%5Boperator%5D=is\u0026conditions%5B0%5D%5B0%5D%5Bvalue%5D=9869d5"} -->
		<!-- wp:mailster-workflow/condition-yes -->
		<!-- wp:mailster-workflow/comment {"comment":"if the user clicked in our \u0022Special Offer Campaign you could add a tag."} /-->

		<!-- wp:mailster-workflow/action {"id":"5eeabf","action":"add_tag"} /-->
		<!-- /wp:mailster-workflow/condition-yes -->

		<!-- wp:mailster-workflow/condition-no -->
		<!-- wp:mailster-workflow/comment {"comment":"Send another campaign if the user haven\'t clicked in our previous message."} /-->

		<!-- wp:mailster-workflow/email {"id":"921df7","name":"Final Offer"} /-->

		<!-- wp:mailster-workflow/delay {"id":"590a14","amount":3,"unit":"days"} /-->

		<!-- wp:mailster-workflow/conditions {"id":"c59f3d","conditions":"conditions%5B0%5D%5B0%5D%5Bfield%5D=_click\u0026conditions%5B0%5D%5B0%5D%5Boperator%5D=is\u0026conditions%5B0%5D%5B0%5D%5Bvalue%5D=921df7"} -->
		<!-- wp:mailster-workflow/condition-yes -->
		<!-- wp:mailster-workflow/action {"id":"3330a6","action":"add_tag"} /-->
		<!-- /wp:mailster-workflow/condition-yes -->

		<!-- wp:mailster-workflow/condition-no -->
		<!-- wp:mailster-workflow/action {"id":"66caf9","action":"unsubscribe"} /-->
		<!-- /wp:mailster-workflow/condition-no -->
		<!-- /wp:mailster-workflow/conditions -->
		<!-- /wp:mailster-workflow/condition-no -->
		<!-- /wp:mailster-workflow/conditions -->',
	)
);


register_block_pattern(
	'mailster-workflow/webinar-invitation',
	array(
		'title'         => __( 'Webinar invitation', 'mailster' ),
		'description'   => __( 'Utilize this workflow to extend invitations to subscribers from various signup sources for your webinar event. Following the event, send them a survey email to gather their feedback.', 'mailster' ),
		'viewportWidth' => 600,
		'postTypes'     => array( 'mailster-workflow' ),
		'categories'    => array( 'mailster-custom-category' ),
		'content'       => '<!-- wp:mailster-workflow/triggers -->
		<!-- wp:mailster-workflow/trigger {"trigger":"form_conversion","forms":[]} /-->
		<!-- /wp:mailster-workflow/triggers -->
		
		<!-- wp:mailster-workflow/comment {"comment":"Send an RSVP right after they sign up."} /-->
		
		<!-- wp:mailster-workflow/email {"id":"be676c","name":"RSVP Email"} /-->
		
		<!-- wp:mailster-workflow/comment {"comment":"Assuming your Webinar starts on the 10th every month it\'s a good practice to remind them one day upfront with a dedicate email. "} /-->
		
		<!-- wp:mailster-workflow/delay {"id":"6f7e6c","amount":1,"unit":"month","date":"2023-05-14T07:00:00.000Z","month":9} /-->
		
		<!-- wp:mailster-workflow/email {"id":"984580","name":"Webinar reminder"} /-->
		
		<!-- wp:mailster-workflow/comment {"comment":"Send the actual link to the webinar in this step."} /-->
		
		<!-- wp:mailster-workflow/delay {"id":"50c293","amount":1,"unit":"days"} /-->
		
		<!-- wp:mailster-workflow/email {"id":"2f62d1","name":"Email with CTA to Webinar"} /-->
		
		<!-- wp:mailster-workflow/comment {"comment":"Wait some time after the webinar has finished and send a feedback request."} /-->
		
		<!-- wp:mailster-workflow/delay {"id":"d57ad9","amount":3,"unit":"hours"} /-->
		
		<!-- wp:mailster-workflow/email {"id":"56ee4f","name":"Feedback Survey"} /-->
		
		<!-- wp:mailster-workflow/action {"id":"a7d5f7","action":"add_tag","tags":["Webinar Complete"]} /-->',
	)
);
register_block_pattern(
	'mailster-workflow/online-course',
	array(
		'title'         => __( 'Online course', 'mailster' ),
		'description'   => __( 'Commence your online course on a designated date and implement an automated system to deliver lessons to your learners every week. This straightforward workflow proves to be highly productive for your course participants.', 'mailster' ),
		'viewportWidth' => 600,
		'postTypes'     => array( 'mailster-workflow' ),
		'categories'    => array( 'mailster-custom-category' ),
		'content'       => '<!-- wp:mailster-workflow/triggers -->
		<!-- wp:mailster-workflow/trigger {"trigger":"list_add"} /-->
		
		<!-- wp:mailster-workflow/trigger {"trigger":"form_conversion","forms":[]} /-->
		<!-- /wp:mailster-workflow/triggers -->
		
		<!-- wp:mailster-workflow/comment {"comment":"Send a Welcome Email if user joins the list to your online course."} /-->
		
		<!-- wp:mailster-workflow/email {"id":"cb3c43","name":"Welcome Email"} /-->
		
		<!-- wp:mailster-workflow/comment {"comment":"Send the next email on the next Monday at 12:00"} /-->
		
		<!-- wp:mailster-workflow/delay {"id":"91d94d","amount":1,"unit":"week","date":"2023-05-14T10:00:00.000Z","weekdays":[0]} /-->
		
		<!-- wp:mailster-workflow/email {"id":"8c995f","name":"Lesson #1"} /-->
		
		<!-- wp:mailster-workflow/comment {"comment":"After one week send the email for the next lesson."} /-->
		
		<!-- wp:mailster-workflow/delay {"id":"ec85ae","amount":1,"unit":"weeks","date":"2023-05-14T10:00:00.000Z","weekdays":[0]} /-->
		
		<!-- wp:mailster-workflow/email {"id":"bbdc54","name":"Lesson #2"} /-->
		
		<!-- wp:mailster-workflow/comment {"comment":"Final email after another week. You can of course add additional emails after that."} /-->
		
		<!-- wp:mailster-workflow/delay {"id":"fa95d9","amount":1,"unit":"weeks","date":"2023-05-14T10:00:00.000Z","weekdays":[0]} /-->
		
		<!-- wp:mailster-workflow/email {"id":"e761b1","name":"Final Lesson"} /-->
		
		<!-- wp:mailster-workflow/comment {"comment":"Sometimes it\'s good to remove users from a list to keep your list clean."} /-->
		
		<!-- wp:mailster-workflow/action {"id":"4bec1c","action":"remove_list"} /-->
		
		<!-- wp:mailster-workflow/comment {"comment":"You can also add tags to the subscriber once the workflow is finished."} /-->
		
		<!-- wp:mailster-workflow/action {"id":"2159da","action":"add_tag","tags":["Course #1 finished"]} /-->',
	)
);

register_block_pattern(
	'mailster-workflow/birthday-wishes',
	array(
		'title'         => __( 'Celebrate customer birthdays', 'mailster' ),
		'description'   => __( 'Employ this workflow to pleasantly surprise your subscribers with birthday wishes on their special day!', 'mailster' ),
		'viewportWidth' => 600,
		'postTypes'     => array( 'mailster-workflow' ),
		'categories'    => array( 'mailster-custom-category' ),
		'content'       => '<!-- wp:mailster-workflow/triggers -->
		<!-- wp:mailster-workflow/trigger {"trigger":"anniversary","repeat":-1,"date":"2023-05-14T07:00:00.000Z","field":"birthday"} /-->
		<!-- /wp:mailster-workflow/triggers -->
		
		<!-- wp:mailster-workflow/comment {"comment":"Send Birthday wishes to your subscribers. It\'s a common practice to offer a special discount which is only valid for a certain time frame."} /-->
		
		<!-- wp:mailster-workflow/email {"id":"3e8c96","name":"Birthday Wishes"} /-->',
	)
);


register_block_pattern(
	'mailster-workflow/target-contacts',
	array(
		'title'         => __( 'Target engaged contacts', 'mailster' ),
		'description'   => __( 'Gain insights into the individuals who engage with the links in your emails. Utilize this information to send follow-up emails containing additional information tailored to their interests and actions. By understanding their interactions, you can deliver targeted and relevant content to further engage and nurture your audience.', 'mailster' ),
		'viewportWidth' => 600,
		'postTypes'     => array( 'mailster-workflow' ),
		'categories'    => array( 'mailster-custom-category' ),
		'content'       => '<!-- wp:mailster-workflow/triggers -->
		<!-- wp:mailster-workflow/trigger {"trigger":"link_click","links":["' . home_url() . '"]} /-->
		<!-- /wp:mailster-workflow/triggers -->
		
		<!-- wp:mailster-workflow/comment {"comment":"If the user clicks on one of the links defined in the trigger add a tag and send an email after 3 days."} /-->
		
		<!-- wp:mailster-workflow/action {"id":"27dca8","action":"add_tag","tags":["Clicked link"]} /-->
		
		<!-- wp:mailster-workflow/delay {"id":"f67641","amount":3,"unit":"days"} /-->
		
		<!-- wp:mailster-workflow/email {"id":"04c449","name":"Discover more"} /-->',
	)
);


register_block_pattern(
	'mailster-workflow/check-for-eu-member',
	array(
		'title'         => __( 'Check for EU Member', 'mailster' ),
		'description'   => __(
			'The following process checks whether the user is a member of the European Union, and then adds or removes a corresponding tag based on the result.',
			'mailster'
		),
		'viewportWidth' => 600,
		'postTypes'     => array( 'mailster-workflow' ),
		'categories'    => array( 'mailster-custom-category' ),
		'content'       => '<!-- wp:mailster-workflow/conditions {"conditions":"conditions%5B0%5D%5B0%5D%5Bfield%5D=geo\u0026conditions%5B0%5D%5B0%5D%5Boperator%5D=is\u0026conditions%5B0%5D%5B0%5D%5Bvalue%5D=_EN"} -->
		<!-- wp:mailster-workflow/condition-yes -->
		<!-- wp:mailster-workflow/action {"action":"add_tag","tags":["EU"]} /-->
		<!-- /wp:mailster-workflow/condition-yes -->
		
		<!-- wp:mailster-workflow/condition-no -->
		<!-- wp:mailster-workflow/action {"action":"remove_tag","tags":["EU"]} /-->
		<!-- /wp:mailster-workflow/condition-no -->
		<!-- /wp:mailster-workflow/conditions -->',
	)
);

