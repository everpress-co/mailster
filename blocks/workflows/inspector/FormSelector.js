/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import { Spinner, CheckboxControl, BaseControl } from '@wordpress/components';

import { useSelect } from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';
import * as Icons from '@wordpress/icons';

/**
 * Internal dependencies
 */

export default function FormSelector(props) {
	const {
		attributes,
		setAttributes,
		help,
		label = __('Forms', 'mailster'),
	} = props;
	const { forms = [] } = attributes;

	const allForms = useSelect((select) =>
		select('mailster/automation').getForms()
	);

	function setForm(id, add = true) {
		var newForms = [...forms];
		if (add) {
			newForms.push(id);
		} else {
			newForms = newForms.filter((el) => {
				return el != id;
			});
		}
		newForms = newForms.filter((el) => {
			return el != -1;
		});
		setAttributes({ forms: newForms.length ? newForms : undefined });
	}

	return (
		<BaseControl label={label} help={help}>
			{!allForms && <Spinner />}
			{allForms &&
				allForms.map((form, i) => {
					return (
						<CheckboxControl
							key={i}
							className="inspector-checkbox"
							value={form.ID}
							checked={forms.includes(form.ID)}
							aria-label={form.name}
							label={form.name}
							onChange={(add) => setForm(form.ID, add)}
						/>
					);
				})}
		</BaseControl>
	);
}
