/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import {
	useBlockProps,
	InspectorControls,
	RichText,
	PlainText,
} from '@wordpress/block-editor';
import {
	Button,
	Panel,
	PanelBody,
	PanelRow,
	CheckboxControl,
	TextControl,
	TextareaControl,
} from '@wordpress/components';

import { Fragment, Component, useState, useEffect } from '@wordpress/element';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { select, dispatch, useSelect, useDispatch } from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';

import { more } from '@wordpress/icons';

/**
 * Internal dependencies
 */

export default function Options(props) {
	const { meta, setMeta } = props;
	const { redirect, overwrite, gdpr, doubleoptin } = meta;

	const [title, setTitle] = useEntityProp(
		'postType',
		'newsletter_form',
		'title'
	);

	const editMessages = () => {
		const mblock = select('core/block-editor')
			.getBlocks()
			.find((block) => {
				return block.name == 'mailster/form-wrapper';
			})
			?.innerBlocks.find((block) => {
				return block.name == 'mailster/messages';
			});

		if (mblock) dispatch('core/block-editor').selectBlock(mblock.clientId);
	};

	return (
		<PluginDocumentSettingPanel
			name="options"
			title={__('Options', 'mailster')}
		>
			<PanelRow>
				<TextControl
					label={__('Form Name', 'mailster')}
					value={title}
					onChange={(value) => setTitle(value)}
					help={__('Define a name for your form.', 'mailster')}
					placeholder={__('Add title', 'mailster')}
				/>
			</PanelRow>
			<PanelRow>
				<CheckboxControl
					label={__('Enable double opt in', 'mailster')}
					checked={!!doubleoptin}
					onChange={() => setMeta({ doubleoptin: !doubleoptin })}
					help={__(
						'New subscribers must confirm their subscription.',
						'mailster'
					)}
				/>
			</PanelRow>
			<PanelRow>
				<CheckboxControl
					label={__('GDPR compliant', 'mailster')}
					help={__(
						'Users must check a checkbox to submit the form',
						'mailster'
					)}
					checked={!!gdpr}
					onChange={() => setMeta({ gdpr: !gdpr })}
				/>
			</PanelRow>
			<PanelRow>
				<CheckboxControl
					label={__('Merge Data', 'mailster')}
					help={__(
						'Allow users to update their data with this form. Data like tags and lists will get merged together.',
						'mailster'
					)}
					checked={!!overwrite}
					onChange={() => setMeta({ overwrite: !overwrite })}
				/>
			</PanelRow>
			<PanelRow>
				<TextControl
					label={__('Redirect after submit', 'mailster')}
					help={__(
						'Redirect subscribers after they submit the form',
						'mailster'
					)}
					value={redirect}
					onChange={(value) => setMeta({ redirect: value })}
					type="url"
				/>
			</PanelRow>
			<PanelRow>
				<Button variant="secondary" onClick={editMessages}>
					{__('Edit Error/Success Messages', 'mailster')}
				</Button>
			</PanelRow>
		</PluginDocumentSettingPanel>
	);
}
