/**
 * External dependencies
 */

import classnames from 'classnames';

/**
 * WordPress dependencies
 */

import { __, sprintf } from '@wordpress/i18n';
import { cleanForSlug } from '@wordpress/url';
import { format } from '@wordpress/date';

/**
 * Internal dependencies
 */

export default function FormElement(props) {
	const {
		attributes,
		setAttributes,
		isSelected,
		isEditor,
		clientId,
		borderProps,
		colorProps,
		spacingProps,
		innerStyle,
		autoComplete,
	} = props;
	const {
		label,
		name,
		selected,
		type,
		inline,
		required,
		native,
		style = {},
		values,
		pattern,
	} = attributes;

	const elem = classnames(colorProps.className, borderProps.className);

	const id = attributes.id;

	const inputStyle = {
		...borderProps.style,
		...colorProps.style,
		...spacingProps.style,
		...innerStyle,
		...{
			color: style.inputColor,
			backgroundColor: style.backgroundColor,
			borderColor: style.borderColor,
			borderWidth: style.borderWidth,
			borderStyle: style.borderStyle,
			borderRadius: style.borderRadius,
		},
	};

	switch (type) {
		case 'radio':
			return (
				<fieldset>
					<legend>{label}</legend>
					{values.map((value, i) => {
						const fieldid = isEditor
							? null
							: attributes.id + (i ? '-' + i : '');
						return (
							<div
								className="mailster-group mailster-group-radio"
								style={inputStyle}
								key={i}
							>
								<input
									name={name}
									id={fieldid}
									aria-required={required}
									aria-label={label}
									required={required}
									type="radio"
									checked={selected == value}
									value={value}
									onChange={(event) =>
										setAttributes({
											selected: event.target.value,
										})
									}
								/>
								<label className="mailster-label" htmlFor={fieldid}>
									{value}
								</label>
							</div>
						);
					})}
				</fieldset>
			);
		case 'checkbox':
			const fieldid = isEditor ? null : attributes.id;
			return (
				<fieldset
					className="mailster-group mailster-group-checkbox"
					style={inputStyle}
				>
					<legend>{label}</legend>
					<input
						name={name}
						id={fieldid}
						aria-required={required}
						aria-label={label}
						spellCheck={false}
						required={required}
						type="checkbox"
						autoComplete={autoComplete}
						defaultChecked={false}
					/>
					<label className="mailster-label" htmlFor={fieldid}>
						{label}
					</label>
				</fieldset>
			);
		case 'dropdown':
			return (
				<select
					name={name}
					id={id}
					className="input"
					aria-required={required}
					aria-label={label}
					required={required}
					value={selected ?? ''}
					style={inputStyle}
					autoComplete={autoComplete}
					onChange={(event) =>
						setAttributes({
							selected: event.target.value,
						})
					}
				>
					{values.map((value, i) => {
						return (
							<option key={i} value={value}>
								{value}
							</option>
						);
					})}
				</select>
			);

		case 'submit':
			return (
				<input
					name={name}
					id={id}
					type="submit"
					style={inputStyle}
					value={label}
					className="wp-block-button__link submit-button"
				/>
			);
		case 'textarea':
			return (
				<textarea
					name={name}
					id={id}
					aria-required={required}
					aria-label={label}
					spellCheck={false}
					required={required}
					rows={style.height}
					value={
						isSelected && !inline
							? sprintf(__('Sample text for %s','mailster'), label)
							: ''
					}
					onChange={() => {}}
					className="input"
					autoComplete={autoComplete}
					style={inputStyle}
					placeholder=" "
				/>
			);
		default:
			const sample =
				'date' == type
					? format('Y-m-d', new Date())
					: sprintf(__('Sample text for %s','mailster'), label);

			return (
				<input
					name={name}
					id={id}
					type={native ? type : 'text'}
					aria-required={required}
					aria-label={label}
					spellCheck={false}
					required={required}
					value={isSelected && !inline ? sample : ''}
					onChange={() => {}}
					className="input"
					autoComplete={autoComplete}
					style={inputStyle}
					placeholder=" "
				/>
			);
	}
}
