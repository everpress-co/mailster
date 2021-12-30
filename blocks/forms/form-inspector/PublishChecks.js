/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import {
	Panel,
	PanelBody,
	PanelRow,
	CheckboxControl,
	TextControl,
	TextareaControl,
	MenuGroup,
	MenuItem,
} from '@wordpress/components';

import { Fragment, Component, useState, useEffect } from '@wordpress/element';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';

import { more, warning } from '@wordpress/icons';
import {
	useSelect,
	select,
	useDispatch,
	dispatch,
	subscribe,
} from '@wordpress/data';

/**
 * Internal dependencies
 */

let locked = false;

export default function PublishChecks(props) {
	const { meta, setMeta } = props;
	const { gdpr, lists } = meta;

	let errors = [];
	let warnings = [];

	if (lists.length < 1) {
		errors.push({
			msg: 'Please select a list',
			onClick: () => console.warn('Error'),
		});
	}
	if (!gdpr) {
		warnings.push({
			msg: 'You have no GDPR field in place',
			onClick: () => console.warn('Warning'),
		});
	}

	// if (errors.length > 0) {
	// 	locked = true;
	// 	dispatch('core/editor').lockPostSaving('title-lock');
	// } else {
	// 	locked = false;
	// 	dispatch('core/editor').unlockPostSaving('title-lock');
	// }

	return (
		<MenuGroup className="widefat">
			{errors.map((obj, i) => (
				<MenuItem
					key={i}
					className="is-warning"
					icon={warning}
					onClick={obj.onClick}
					isTertiary
					iconPosition="left"
				>
					{obj.msg}
				</MenuItem>
			))}
			{warnings.map((obj, i) => (
				<MenuItem
					key={i}
					className="is-warning"
					icon={warning}
					onClick={obj.onClick}
					isTertiary
					iconPosition="left"
				>
					{obj.msg}
				</MenuItem>
			))}
		</MenuGroup>
	);
}
