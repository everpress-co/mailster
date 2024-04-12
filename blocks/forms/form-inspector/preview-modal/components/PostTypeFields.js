/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import {
	PanelRow,
	CheckboxControl,
	__experimentalItemGroup as ItemGroup,
	__experimentalItem as Item,
} from '@wordpress/components';

import { useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */

import PostTokenFields from './PostTokenFields';

export default function PostTypeFields(props) {
	const { options, setOptions } = props;

	const postTypes = useSelect((select) => {
		const result = select('core').getEntityRecords('root', 'postType');
		return !result
			? []
			: result.filter((type) => {
					return (
						type.viewable &&
						!['attachment', 'mailster-form', 'mailster-workflow'].includes(
							type.slug
						)
					);
			  });
	});

	const alls = options.all || [];

	function setAll(all, add) {
		var newAlls = [...alls];
		if (add) {
			newAlls.push(all);
		} else {
			newAlls = newAlls.filter((el) => {
				return el != all;
			});
		}
		setOptions({ all: newAlls });
	}

	return (
		<>
			{postTypes.map((postType) => {
				return (
					<PanelRow key={postType.slug}>
						<ItemGroup isBordered={true} className="widefat" size="medium">
							<Item>
								<CheckboxControl
									label={__('Display on all ' + postType.name, 'mailster')}
									checked={alls.includes(postType.slug)}
									onChange={(val) => {
										setAll(postType.slug, val);
									}}
								/>
							</Item>

							{!alls.includes(postType.slug) && (
								<PostTokenFields
									options={options}
									setOptions={setOptions}
									postType={postType}
								/>
							)}
						</ItemGroup>
					</PanelRow>
				);
			})}
		</>
	);
}
