/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import {
	Button,
	PanelRow,
	CheckboxControl,
	TextControl,
} from '@wordpress/components';

import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { dispatch } from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';

/**
 * Internal dependencies
 */

import { searchBlock } from '../../util';

export default function Options(props) {
	const { meta, setMeta } = props;
	const { redirect, overwrite, gdpr, doubleoptin, prefill } = meta;

	const [title, setTitle] = useEntityProp('postType', 'mailster-form', 'title');

	const editMessages = () => {
		const mblock = searchBlock('mailster/messages');
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
				<CheckboxControl
					label={__('Allow Prefills', 'mailster')}
					help={__(
						'Allow to prefill this form with GET variables. Use the field name as key and the value as value.',
						'mailster'
					)}
					checked={!!prefill}
					onChange={() => setMeta({ prefill: !prefill })}
				/>
			</PanelRow>
			<PanelRow>
				<TextControl
					label={__('Redirect after submit', 'mailster')}
					help={__(
						'Redirect subscribers after they submit the form. Keep empty to stay on the same page and show a success message.',
						'mailster'
					)}
					placeholder="https://example.com/please-confirm"
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
