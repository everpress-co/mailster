/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import {
	TextControl,
	BaseControl,
	SelectControl,
	DatePicker,
	__experimentalNumberControl as NumberControl,
} from '@wordpress/components';

import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { useSelect } from '@wordpress/data';
import { date } from '@wordpress/date';
import { useEntityProp } from '@wordpress/core-data';
import * as Icons from '@wordpress/icons';
import { useEffect, useState } from '@wordpress/element';
import { ButtonGroup } from '@wordpress/components';
import { Button } from '@wordpress/components';
import { PanelRow } from '@wordpress/components';

/**
 * Internal dependencies
 */

export default function FieldSelector(props) {
	const {
		attributes,
		setAttributes,
		help,
		label = __('Fields', 'mailster'),
	} = props;
	const { field, value = '' } = attributes;

	const allFields = useSelect((select) =>
		select('mailster/automation').getFields()
	);

	const currentField = allFields.filter((f) => f.id == field).pop();

	const getInitialDateType = () => {
		if (!isNaN(parseFloat(value)) && isFinite(value)) {
			if (value < 0) {
				return 'decrease';
			} else if (value > 0) {
				return 'increase';
			}
			return 'current';
		}
		return 'date';
	};
	const changeDateType = (newType) => {
		var newValue;
		switch (newType) {
			case 'date':
				newValue = '';
				break;
			case 'decrease':
				newValue = -1;
				break;
			case 'increase':
				newValue = 1;
				break;
			case 'current':
				newValue = 0;
				break;

			default:
				break;
		}
		setDateType(newType);
		setAttributes({ value: newValue.toString() });
	};
	const [dateType, setDateType] = useState(getInitialDateType());

	allFields &&
		!field &&
		allFields.unshift({
			id: 0,
			name: __('Select Custom Field', 'mailster'),
		});

	return (
		<BaseControl
			help={__('Add new custom fields on the settings page.', 'mailster')}
		>
			<SelectControl
				label={
					field
						? __('Set field', 'mailster')
						: __('Select Custom Field', 'mailster')
				}
				value={field}
				onChange={(val) => {
					setAttributes({ field: val ? val : undefined });
				}}
				options={allFields.map((field, i) => {
					return { value: field.id, label: field.name };
				})}
			/>
			{field && currentField && (
				<>
					{(() => {
						if (currentField.type == 'date') {
							return (
								<BaseControl label={__('to', 'mailster')}>
									<SelectControl
										value={dateType}
										onChange={(val) => changeDateType(val)}
										options={[
											{
												label: __('A specific date', 'mailster'),
												value: 'date',
											},
											{
												label: __('To the current date', 'mailster'),
												value: 'current',
											},
											{
												label: __('Increase by days', 'mailster'),
												value: 'increase',
											},
											{
												label: __('Decrease by days', 'mailster'),
												value: 'decrease',
											},
										]}
									/>
									{dateType == 'date' && (
										<DatePicker
											currentDate={value ? new Date(value) : new Date()}
											onChange={(val) =>
												setAttributes({
													value: val ? date('Y-m-d', val) : undefined,
												})
											}
											__nextRemoveHelpButton
											__nextRemoveResetButton
										/>
									)}
									{dateType == 'increase' && (
										<NumberControl
											help={__('Number of days', 'mailster')}
											onChange={(val) =>
												setAttributes({ value: parseInt(val, 10).toString() })
											}
											value={value || 1}
											min="1"
										/>
									)}
									{dateType == 'decrease' && (
										<NumberControl
											help={__('Number of days', 'mailster')}
											onChange={(val) =>
												setAttributes({
													value: (parseInt(val, 10) * -1).toString(),
												})
											}
											value={value * -1 || 1}
											min="1"
										/>
									)}
								</BaseControl>
							);
						} else if (
							currentField.type == 'dropdown' ||
							currentField.type == 'radio'
						) {
							return (
								<SelectControl
									label={__('to', 'mailster')}
									value={value}
									options={currentField.values.map((val, i) => {
										return { value: val, label: val };
									})}
									onChange={(val) =>
										setAttributes({ value: val ? val : undefined })
									}
								/>
							);
						} else if (currentField.type == 'checkbox') {
							return (
								<BaseControl label={__('to', 'mailster')}>
									<PanelRow>
										<Button
											variant="secondary"
											isPressed={value}
											disabled={value}
											onClick={() => setAttributes({ value: !value })}
										>
											{__('checked', 'mailster')}
										</Button>
										<Button
											variant="secondary"
											isPressed={!value}
											disabled={!value}
											onClick={() => setAttributes({ value: !value })}
										>
											{__('unchecked', 'mailster')}
										</Button>
									</PanelRow>
								</BaseControl>
							);
						} else {
							return (
								<TextControl
									label={__('to', 'mailster')}
									value={value}
									onChange={(val) =>
										setAttributes({ value: val ? val : undefined })
									}
								/>
							);
						}
					})()}
				</>
			)}
		</BaseControl>
	);
}
