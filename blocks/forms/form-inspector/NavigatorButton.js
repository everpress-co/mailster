/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */

import { __ } from '@wordpress/i18n';

import { Button } from '@wordpress/components';

import { __experimentalUseNavigator as useNavigator } from '@wordpress/components';

export default function NavigatorButton({
	as: Tag = Button,
	path,
	isBack = false,
	disabled = false,
	...props
}) {
	const navigator = useNavigator();

	return (
		<Tag
			onClick={() => !disabled && navigator.push(path, { isBack })}
			{...props}
			disabled={disabled}
		/>
	);
}
