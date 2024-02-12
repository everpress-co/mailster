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

export default function ListSelector(props) {
	const {
		attributes,
		setAttributes,
		help,
		label = __('Lists', 'mailster'),
	} = props;
	const { lists = [] } = attributes;

	const allLists = useSelect((select) =>
		select('mailster/automation').getLists()
	);

	function setList(id, add = true) {
		var newLists = [...lists];
		if (add) {
			newLists.push(id);
		} else {
			newLists = newLists.filter((el) => {
				return el != id;
			});
		}
		newLists = newLists.filter((el) => {
			return el != -1;
		});
		setAttributes({ lists: newLists.length ? newLists : undefined });
	}

	return (
		<BaseControl label={label} help={help}>
			{!allLists && <Spinner />}
			{allLists &&
				allLists.map((list, i) => {
					return (
						<CheckboxControl
							key={i}
							className="inspector-checkbox"
							value={list.ID}
							checked={lists.includes(list.ID)}
							aria-label={list.name}
							label={list.name}
							onChange={(add) => setList(list.ID, add)}
						/>
					);
				})}
		</BaseControl>
	);
}
