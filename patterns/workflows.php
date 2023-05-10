<?php
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
	'mailster-workflow/welcome01',
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
	'mailster-workflow/welcome02',
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
		
		<!-- wp:mailster-workflow/email {" /-->
		
		<!-- wp:mailster-workflow/delay {"amount":3,"unit":"days"} /-->
		
		<!-- wp:mailster-workflow/email {" /-->
		
		<!-- wp:mailster-workflow/delay {"amount":1,"unit":"days"} /-->
		
		<!-- wp:mailster-workflow/conditions {"conditions":"conditions%5B0%5D%5B0%5D%5Bfield%5D=_open\u0026conditions%5B0%5D%5B0%5D%5Boperator%5D=is\u0026conditions%5B0%5D%5B0%5D%5Bvalue%5D=_last_5"} -->
		<!-- wp:mailster-workflow/condition {"fulfilled":true} -->
		<!-- wp:mailster-workflow/action {"action":"nothing"} /-->
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
	'mailster-workflow/3my-awesome-pattern',
	array(
		'title'         => __( 'Two buttons', 'mailster' ),
		'description'   => __( 'XXX', 'mailster' ),
		'viewportWidth' => 600,
		'postTypes'     => array( 'mailster-workflow' ),
		'categories'    => array( 'mailster-custom-category' ),
		'content'       => '<!-- wp:mailster-workflow/triggers -->
		<!-- wp:mailster-workflow/trigger {"trigger":"list_add","lists":[-1]} /-->
		<!-- /wp:mailster-workflow/triggers -->
		
		<!-- wp:mailster-workflow/action {"action":"add_tag"} /-->
		
		<!-- wp:mailster-workflow/action {"action":"update_field"} /-->
		
		<!-- wp:mailster-workflow/conditions {"conditions":"conditions%5B0%5D%5B0%5D%5Bfield%5D=email\u0026conditions%5B0%5D%5B0%5D%5Boperator%5D=is\u0026conditions%5B0%5D%5B0%5D%5Bvalue%5D=abc"} -->
		<!-- wp:mailster-workflow/condition {"fulfilled":true} -->
		<!-- wp:mailster-workflow/delay {"amount":1,"unit":"hours"} /-->
		<!-- /wp:mailster-workflow/condition -->
		
		<!-- wp:mailster-workflow/condition {"fulfilled":false} -->
		<!-- wp:mailster-workflow/delay {"amount":1,"unit":"weeks"} /-->
		<!-- /wp:mailster-workflow/condition -->
		<!-- /wp:mailster-workflow/conditions -->',
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
	'mailster-workflow/5my-awesome-pattern',
	array(
		'title'         => __( 'Check for EU Member', 'mailster' ),
		'description'   => __(
			'The following process verifies whether the user is a member of the European Union, and then adds or removes a corresponding tag based on the result.',
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
