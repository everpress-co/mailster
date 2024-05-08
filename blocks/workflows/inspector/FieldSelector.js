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
	TreeSelect,
} from '@wordpress/components';

import { useSelect } from '@wordpress/data';
import { date } from '@wordpress/date';
import { useState } from '@wordpress/element';
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
		var newValue = '';
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
		}
		setDateType(newType);
		setAttributes({ value: newValue.toString() });
	};
	const [dateType, setDateType] = useState(getInitialDateType());

	return (
		<BaseControl
			help={__('Add new custom fields on the settings page.', 'mailster')}
		>
			<TreeSelect
				label={label}
				noOptionLabel={__('Select Custom Field', 'mailster')}
				selectedId={field}
				onChange={(val) => {
					setAttributes({ field: val ? val : undefined });
				}}
				tree={
					allFields &&
					allFields.map((field, i) => {
						return {
							id: field.id,
							name: field.name,
						};
					})
				}
			/>
			{field && currentField && (
				<>
					{(() => {
						if (currentField.type == 'date') {
							return (
								<BaseControl label={__('to', 'mailster')}>
									<TreeSelect
										selectedId={dateType}
										noOptionLabel={__('Remove this value', 'mailster')}
										tree={[
											{
												name: __('A specific date', 'mailster'),
												id: 'date',
											},
											{
												name: __('To the current date', 'mailster'),
												id: 'current',
											},
											{
												name: __('Increase by days', 'mailster'),
												id: 'increase',
											},
											{
												name: __('Decrease by days', 'mailster'),
												id: 'decrease',
											},
										]}
										onChange={(val) => changeDateType(val)}
									/>
									{dateType == 'date' && (
										<DatePicker
											currentDate={
												isNaN(Date.parse(value)) ? new Date() : new Date(value)
											}
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
								<TreeSelect
									label={__('to', 'mailster')}
									selectedId={value}
									noOptionLabel={__('Remove this value', 'mailster')}
									tree={currentField.values.map((val, i) => {
										return { id: val, name: val };
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
