/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __, sprintf } from '@wordpress/i18n';

import {
	Button,
	PanelRow,
	DropdownMenu,
	TextControl,
	MenuGroup,
	MenuItem,
	Spinner,
	PanelBody,
	__experimentalItemGroup as ItemGroup,
	BaseControl,
	Card,
	Flex,
	FlexBlock,
	FlexItem,
	Tip,
	DateTimePicker,
	SelectControl,
	Popover,
	DatePicker,
	TimePicker,
} from '@wordpress/components';

import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { useSelect } from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';
import { useEffect, useState } from '@wordpress/element';

import * as Icons from '@wordpress/icons';
import {
	useBlockProps,
	__experimentalLinkControl as LinkControl,
} from '@wordpress/block-editor';
import { dateI18n, gmdateI18n } from '@wordpress/date';

/**
 * Internal dependencies
 */
import {
	DELAY_OPTIONS,
	IS_12_HOUR,
	TIME_FORMAT,
	DATE_FORMAT,
} from './constants';

export default function Selector(props) {
	const { attributes, setAttributes, isAnniversary = false } = props;
	const { field } = attributes;

	const allFields = useSelect((select) =>
		select('mailster/automation').getFields()
	);

	return (
		<>
			<PanelRow>
				<BaseControl className="widefat">
					<SelectControl
						label={
							field
								? __('Whenever', 'mailster')
								: __('Select Custom Field', 'mailster')
						}
						help={
							field
								? __('is updated', 'mailster')
								: __(
										'Choose the field you like to use to trigger this workflow.',
										'mailster'
								  )
						}
						value={field}
						onChange={(val) => {
							setAttributes({ field: val ? val : undefined });
						}}
					>
						<option value="">{__('Choose', 'mailster')}</option>
						<option value={-1}>{__('Any field', 'mailster')}</option>
						<optgroup label={__('User fields', 'mailster')}>
							{allFields.map((field, i) => {
								return (
									<option key={i} value={field.id}>
										{field.name}
									</option>
								);
							})}
						</optgroup>
					</SelectControl>
				</BaseControl>
			</PanelRow>
			{field && (
				<PanelRow>
					<Tip>
						{__(
							'Use conditions below to trigger this workflow only if the field matches the condition.',
							'mailster'
						)}
					</Tip>
				</PanelRow>
			)}
		</>
	);
}
