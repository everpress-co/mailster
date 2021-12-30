/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';
import { useBlockProps, RichText } from '@wordpress/block-editor';
import { useEntityProp } from '@wordpress/core-data';

/**
 * Internal dependencies
 */

export default function save(props) {
	const { attributes, setAttributes, isSelected } = props;
	const { lists, dropdown } = attributes;
	const className = ['mailster-wrapper mailster-wrapper-_lists'];

	return (
		<div
			{...useBlockProps.save({
				className: className.join(' '),
			})}
		>
			{dropdown ? (
				<select name="_lists[]" className="input">
					{lists.map((list, i) => {
						return (
							<option key={i} value={list.ID}>
								{list.name}
							</option>
						);
					})}
				</select>
			) : (
				<>
					{lists.map((list, i) => {
						return (
							<div
								key={i}
								className="mailster-group mailster-group-checkbox"
							>
								<label>
									<input
										type="checkbox"
										name="_lists[]"
										value={list.id}
										checked={list.checked}
										aria-label={list.name}
									/>
									<RichText.Content
										tagName="span"
										value={list.name}
										className="mailster-label"
									/>
								</label>
							</div>
						);
					})}
				</>
			)}
		</div>
	);
}
