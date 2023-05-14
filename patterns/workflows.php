<?php


register_block_pattern(
	'mailster-workflow/webhook',
	array(
		'title'         => __( 'Web hook', 'mailster' ),
		'description'   => __( 'Start with your own workflow.', 'mailster' ),
		'viewportWidth' => 400,
		'postTypes'     => array( 'mailster-workflow' ),
		'categories'    => array( 'mailster-custom-category' ),
		'content'       => '<!-- wp:mailster-workflow/triggers --><!-- wp:mailster-workflow/trigger  /--><!-- /wp:mailster-workflow/triggers --><!-- wp:mailster-workflow/action {"action":"webhook","webhook":"https://webhook.site/45ef2bb3-5213-419b-9bb0-d959f844fcc4"} /-->',
	)
);

register_block_pattern(
	'mailster-workflow/scratch',
	array(
		'title'         => __( 'Start from scratch', 'mailster' ),
		'description'   => __( 'Start with your own workflow.', 'mailster' ),
		'viewportWidth' => 400,
		'postTypes'     => array( 'mailster-workflow' ),
		'categories'    => array( 'mailster-custom-category' ),
		'content'       => '<!-- wp:mailster-workflow/triggers --><!-- wp:mailster-workflow/trigger /--><!-- /wp:mailster-workflow/triggers --><!-- wp:mailster-workflow/action /-->',
	)
);

register_block_pattern(
	'mailster-workflow/simple-welcome',
	array(
		'title'         => __( 'Simple Welcome email', 'mailster' ),
		'postTypes'     => array( 'mailster-workflow' ),
		'description'   => __(
			'This simple and efficient workflow is perfect for welcoming new subscribers. It is easy to set up and produces great outcomes. Once an individual subscribes to your form and is added to a list, the workflow will automatically send them a welcome email.',
			'mailster'
		),
		'viewportWidth' => 600,
		'categories'    => array( 'mailster-custom-category' ),
		'content'       => '<!-- wp:mailster-workflow/triggers --><!-- wp:mailster-workflow/trigger {"trigger":"list_add","lists":[-1]} /--><!-- /wp:mailster-workflow/triggers --><!-- wp:mailster-workflow/email /-->',
	)
);


register_block_pattern(
	'mailster-workflow/improved-welcome',
	array(
		'title'         => __( 'Improved Welcome email series', 'mailster' ),
		'postTypes'     => array( 'mailster-workflow' ),
		'description'   => __(
			'Employ various channels such as sign up forms or landing pages to activate the identical workflow for your new subscribers. All new subscribers will be sent the 
		same welcome campaign. Additionally, you can modify a custom field for the engaged subscribers and shift the unresponsive ones to a designated group for future targeting.',
			'mailster'
		),
		'viewportWidth' => 600,
		'categories'    => array( 'mailster-custom-category' ),
		'content'       => '<!-- wp:mailster-workflow/triggers -->
		<!-- wp:mailster-workflow/trigger {"trigger":"list_add","lists":[-1]} /-->
		<!-- /wp:mailster-workflow/triggers -->
		
		<!-- wp:mailster-workflow/email /-->
		
		<!-- wp:mailster-workflow/delay {,"amount":2,"unit":"days"} /-->
		
		<!-- wp:mailster-workflow/email /-->
		
		<!-- wp:mailster-workflow/conditions {,"conditions":"conditions%5B0%5D%5B0%5D%5Bfield%5D=_open\u0026conditions%5B0%5D%5B0%5D%5Boperator%5D=is\u0026conditions%5B0%5D%5B0%5D%5Bvalue%5D=_last_5"} -->
		<!-- wp:mailster-workflow/condition {"fulfilled":true} -->
		<!-- wp:mailster-workflow/action {,"action":"nothing"} /-->
		<!-- /wp:mailster-workflow/condition -->
		
		<!-- wp:mailster-workflow/condition {"fulfilled":false} -->
		<!-- wp:mailster-workflow/stop /-->
		<!-- /wp:mailster-workflow/condition -->
		<!-- /wp:mailster-workflow/conditions -->',
	)
);



register_block_pattern(
	'mailster-workflow/pagevisit',
	array(
		'title'         => __( 'Workflow for page visits', 'mailster' ),
		'description'   => __( 'If you want to focus on subscribers who have visited specific pages, then this workflow will come in handy.', 'mailster' ),
		'viewportWidth' => 600,
		'postTypes'     => array( 'mailster-workflow' ),
		'categories'    => array( 'mailster-custom-category' ),
		'content'       => '<!-- wp:mailster-workflow/triggers -->
		<!-- wp:mailster-workflow/trigger {"trigger":"page_visit","pages":["/hello-world"]} /-->
		<!-- /wp:mailster-workflow/triggers -->
		
		<!-- wp:mailster-workflow/delay {"amount":10,"unit":"minutes"} /-->
		
		<!-- wp:mailster-workflow/email /--><!-- wp:mailster-workflow/action {"action":"add_tag","tags":["Visited"]} /-->',
	)
);


register_block_pattern(
	'mailster-workflow/win-back-subscribers',
	array(
		'title'         => __( 'Win back inactive subscribers', 'mailster' ),
		'description'   => __( 'Re-engage subscribers who have shown a lack of engagement and remove inactive ones. You can initiate a campaign and update a custom field if they interact, or transfer them to the unsubscribed folder if they do not. This approach effectively purges your list, ensuring that your most active subscribers remain, leading to improved email deliverability.', 'mailster' ),
		'viewportWidth' => 600,
		'postTypes'     => array( 'mailster-workflow' ),
		'categories'    => array( 'mailster-custom-category' ),
		'content'       => '<!-- wp:mailster-workflow/triggers -->
		<!-- wp:mailster-workflow/trigger /-->
		<!-- /wp:mailster-workflow/triggers -->
		
		<!-- wp:mailster-workflow/email {,"comment":"Send an initial offer"} /-->
		
		<!-- wp:mailster-workflow/delay {,"amount":3,"unit":"days"} /-->
		
		<!-- wp:mailster-workflow/conditions {,"conditions":"conditions%5B0%5D%5B0%5D%5Bfield%5D=_click\u0026conditions%5B0%5D%5B0%5D%5Boperator%5D=is\u0026conditions%5B0%5D%5B0%5D%5Bvalue%5D=_last_5"} -->
		<!-- wp:mailster-workflow/condition {"fulfilled":true} -->
		<!-- wp:mailster-workflow/action {,"action":"add_tag"} /-->
		<!-- /wp:mailster-workflow/condition -->
		
		<!-- wp:mailster-workflow/condition {"fulfilled":false} -->
		<!-- wp:mailster-workflow/email {,"comment":"Send final offer"} /-->
		
		<!-- wp:mailster-workflow/delay {,"amount":3,"unit":"days"} /-->
		
		<!-- wp:mailster-workflow/conditions {,"conditions":"conditions%5B0%5D%5B0%5D%5Bfield%5D=_click\u0026conditions%5B0%5D%5B0%5D%5Boperator%5D=is\u0026conditions%5B0%5D%5B0%5D%5Bvalue%5D=_last_5"} -->
		<!-- wp:mailster-workflow/condition {"fulfilled":true} /-->
		
		<!-- wp:mailster-workflow/condition {"fulfilled":false} -->
		<!-- wp:mailster-workflow/action {,"action":"unsubscribe"} /-->
		<!-- /wp:mailster-workflow/condition -->
		<!-- /wp:mailster-workflow/conditions -->
		<!-- /wp:mailster-workflow/condition -->
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
		
		<!-- wp:mailster-workflow/email {"name":"RSVP Email"} /-->
		
		<!-- wp:mailster-workflow/comment {"comment":"Assuming your Webinar starts on the 10th every month it\'s a good practice to remind them one day upfront with a dedicate email. "} /-->
		
		<!-- wp:mailster-workflow/delay {"amount":1,"unit":"month","date":"2023-05-14T07:00:00.000Z","month":9} /-->
		
		<!-- wp:mailster-workflow/email {"name":"Webinar reminder"} /-->
		
		<!-- wp:mailster-workflow/comment {"comment":"Send the actual link to the webinar in this step."} /-->
		
		<!-- wp:mailster-workflow/delay {"amount":1,"unit":"days"} /-->
		
		<!-- wp:mailster-workflow/email {"name":"Email with CTA to Webinar"} /-->
		
		<!-- wp:mailster-workflow/comment {"comment":"Wait some time after the webinar has finished and send a feedback request."} /-->
		
		<!-- wp:mailster-workflow/delay {"amount":3,"unit":"hours"} /-->
		
		<!-- wp:mailster-workflow/email {"name":"Feedback Survey"} /-->
		
		<!-- wp:mailster-workflow/action /-->',
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
		<!-- /wp:mailster-workflow/triggers -->
		
		<!-- wp:mailster-workflow/email {,"comment":"Send a Welcome Email"} /-->
		
		<!-- wp:mailster-workflow/delay {,"amount":1,"unit":"week","date":"2023-05-14T10:00:00.000Z","weekdays":[0]} /-->
		
		<!-- wp:mailster-workflow/email {,"comment":"Lesson 1"} /-->
		
		<!-- wp:mailster-workflow/delay {,"amount":1,"unit":"weeks","date":"2023-05-14T10:00:00.000Z","weekdays":[0]} /-->
		
		<!-- wp:mailster-workflow/email {,"comment":"Lesson 2"} /-->
		
		<!-- wp:mailster-workflow/delay {,"amount":1,"unit":"weeks","date":"2023-05-14T10:00:00.000Z","weekdays":[0]} /-->
		
		<!-- wp:mailster-workflow/email {,"comment":"Final Lesson"} /-->
		
		<!-- wp:mailster-workflow/action {,"action":"remove_list","comment":"Remove user from the list again"} /-->
		
		<!-- wp:mailster-workflow/action {,"action":"add_tag","tags":["Course #1 finished"]} /-->',
	)
);
register_block_pattern(
	'mailster-workflow/4my-awesome-pattern',
	array(
		'title'         => __( 'Birthday greetings', 'mailster' ),
		'description'   => __( 'Employ this workflow to pleasantly surprise your subscribers with birthday wishes on their special day! Additionally, you can send birthday reminders a few days in advance to ensure they don\'t miss the celebration.', 'mailster' ),
		'viewportWidth' => 600,
		'postTypes'     => array( 'mailster-workflow' ),
		'categories'    => array( 'mailster-custom-category' ),
		'content'       => '<!-- wp:mailster-workflow/triggers -->
		<!-- wp:mailster-workflow/trigger {"trigger":"anniversary","repeat":-1,"date":"2023-05-14T07:00:39.000Z","field":"birthday"} /-->
		<!-- /wp:mailster-workflow/triggers -->
		
		<!-- wp:mailster-workflow/comment {"comment":"Send Birthday wishes to your subscribers. It\'s a common practice to offer a special discount which is only valid for a certain time frame."} /-->
		
		<!-- wp:mailster-workflow/email /-->',
	)
);
register_block_pattern(
	'mailster-workflow/4my-awesome-pattern',
	array(
		'title'         => __( 'Two buttons', 'mailster' ),
		'description'   => __( 'XXX', 'mailster' ),
		'viewportWidth' => 600,
		'postTypes'     => array( 'mailster-workflow' ),
		'categories'    => array( 'mailster-custom-category' ),
		'content'       => '<!-- wp:mailster-workflow/triggers --><!-- wp:mailster-workflow/trigger {"trigger":"list_add","lists":[-1]} /--><!-- /wp:mailster-workflow/triggers --><!-- wp:mailster-workflow/action {,"action":"add_tag","tags":["ABC"]} /--><!-- wp:mailster-workflow/action  /-->',
	)
);


register_block_pattern(
	'mailster-workflow/chec-for-eu-member',
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
		<!-- wp:mailster-workflow/condition {"fulfilled":true} -->
		<!-- wp:mailster-workflow/action {"action":"add_tag","tags":["EU"]} /-->
		<!-- /wp:mailster-workflow/condition -->
		
		<!-- wp:mailster-workflow/condition {"fulfilled":false} -->
		<!-- wp:mailster-workflow/action {"action":"remove_tag","tags":["EU"]} /-->
		<!-- /wp:mailster-workflow/condition -->
		<!-- /wp:mailster-workflow/conditions -->',
	)
);
