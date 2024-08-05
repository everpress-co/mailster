/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import {
	PanelRow,
	BaseControl,
	Tip,
	SelectControl,
} from '@wordpress/components';

import { useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */

export default function Selector(props) {
	const { attributes, setAttributes } = props;
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
						{allFields.length === 0 && (
							<option value="loading" disabled>
								{__('Loading...', 'mailster')}
							</option>
						)}
						{allFields.length > 0 && (
							<optgroup label={__('User fields', 'mailster')}>
								{allFields.map((field, i) => {
									return (
										<option key={i} value={field.id}>
											{field.name}
										</option>
									);
								})}
							</optgroup>
						)}
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
