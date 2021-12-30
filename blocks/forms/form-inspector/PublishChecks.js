/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
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
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
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
