<?php

class MailsterConditions {

	public function __construct( $conditions = array(), $operator = 'AND' ) {

	}


	public function __get( $name ) {

		if ( ! isset( $this->$name ) ) {
			$this->{$name} = $this->{'get_' . $name}();
		}

		return $this->{$name};

	}


	public function view( $conditions = array(), $operator = 'AND' ) {

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style( 'mailster-conditions', MAILSTER_URI . 'assets/css/conditions-style' . $suffix . '.css', array( 'mailster-select2' ), MAILSTER_VERSION );
		wp_enqueue_script( 'mailster-conditions', MAILSTER_URI . 'assets/js/conditions-script' . $suffix . '.js', array( 'jquery', 'mailster-select2' ), MAILSTER_VERSION, true );
		wp_localize_script( 'mailster-conditions', 'mailsterL10n', array() );

		if ( empty( $conditions ) ) {
			$conditions = array();
		}

		include MAILSTER_DIR . 'views/conditions/conditions.php';

	}

	public function render( $conditions = array(), $operator = 'AND' ) {

		if ( empty( $conditions ) ) {
			$conditions = array();
		}
		include MAILSTER_DIR . 'views/conditions/render.php';

	}

	public function fielddropdown() {
		include MAILSTER_DIR . 'views/conditions/fielddropdown.php';
	}
	public function operatordropdown() {
		include MAILSTER_DIR . 'views/conditions/operatordropdown.php';
	}

	private function get_custom_fields() {
		$custom_fields = mailster()->get_custom_fields( );
		$custom_fields = wp_parse_args( array( 'firstname' => array( 'name' => mailster_text( 'firstname' ) ), 'lastname' => array( 'name' => mailster_text( 'lastname' ) ) ), (array) $custom_fields );

		return $custom_fields;
	}

	private function get_custom_date_fields() {
		$custom_date_fields = mailster()->get_custom_date_fields( true );

		return $custom_date_fields;
	}

	private function get_fields() {
		$fields = array(
			'id' => __( 'ID', 'mailster' ),
			'hash' => __( 'Hash', 'mailster' ),
			'email' => __( 'Email', 'mailster' ),
			'wp_id' => __( 'WordPress User ID', 'mailster' ),
			'status' => __( 'Status', 'mailster' ),
			'added' => __( 'Added', 'mailster' ),
			'updated' => __( 'Updated', 'mailster' ),
			'signup' => __( 'Signup', 'mailster' ),
			'confirm' => __( 'Confirm', 'mailster' ),
			'ip_signup' => __( 'IP on Signup', 'mailster' ),
			'ip_confirm' => __( 'IP on confirmation', 'mailster' ),
			'rating' => __( 'Rating', 'mailster' ),
		);

		return $fields;
	}

	private function get_time_fields() {
		$time_fields = array( 'added', 'updated', 'signup', 'confirm' );

		return $time_fields;
	}

	private function get_meta_fields() {
		$meta_fields = array( 'form', 'referer', 'client', 'clienttype', 'coords', 'geo', 'lang', 'timeoffset', 'lat', 'lng' );

		return $meta_fields;
	}

	private function get_wp_user_meta() {
		$wp_user_meta = wp_parse_args( array( 'wp_user_level', 'wp_capabilities' ), mailster( 'helper' )->get_wpuser_meta_fields() );
		// removing custom fields from wp user meta to prevent conflicts
		$wp_user_meta = array_diff( $wp_user_meta, array_merge( array( 'email' ), $this->custom_fields ) );

		return $wp_user_meta;
	}

	private function get_operators() {
		return array(
			'is' => __( 'is', 'mailster' ),
			'is_not' => __( 'is not', 'mailster' ),
			'contains' => __( 'contains', 'mailster' ),
			'contains_not' => __( 'contains not', 'mailster' ),
			'begin_with' => __( 'begins with', 'mailster' ),
			'end_with' => __( 'ends with', 'mailster' ),
			'is_greater' => __( 'is greater', 'mailster' ),
			'is_smaller' => __( 'is smaller', 'mailster' ),
			'is_greater_equal' => __( 'is greater or equal', 'mailster' ),
			'is_smaller_equal' => __( 'is smaller or equal', 'mailster' ),
			'pattern' => __( 'match regex pattern', 'mailster' ),
			'not_pattern' => __( 'does not match regex pattern', 'mailster' ),
		);

	}


	private function get_field_operator( $operator ) {
		$operator = esc_sql( stripslashes( $operator ) );

		switch ( $operator ) {
			case '=':
				return 'is';
			case '!=':
				return 'is_not';
			case '<>':
				return 'contains';
			case '!<>':
				return 'contains_not';
			case '^':
				return 'begin_with';
			case '$':
				return 'end_with';
			case '>=':
				return 'is_greater_equal';
			case '<=':
				return 'is_smaller_equal';
			case '>':
				return 'is_greater';
			case '<':
				return 'is_smaller';
			case '%':
				return 'pattern';
			case '!%':
				return 'not_pattern';
		}

		return $operator;

	}


	private function nice_name( $string, $type = null ) {

		switch ( $type ) {
			case 'field':
				if ( isset( $this->fields[ $string ] ) ) {
					return $this->fields[ $string ];
				}
				if ( isset( $this->custom_fields[ $string ] ) ) {
					return $this->custom_fields[ $string ]['name'];
				}
				break;
			case 'operator':
				if ( isset( $this->operators[ $string ] ) ) {
					return $this->operators[ $string ];
				}
				if ( 'AND' == $string ) {
					return __( 'and', 'mailster' );
				}
				if ( 'OR' == $string ) {
					return __( 'or', 'mailster' );
				}
				break;
			case 'value':
				break;

		}

		return $string;

	}

}
