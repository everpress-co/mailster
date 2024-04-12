/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import { useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */

import PostTokenField from './PostTokenField';

export default function PostTokenFields(props) {
	const { postType, options, setOptions } = props;

	const taxonomies = useSelect((select) => {
		return select('core').getEntityRecords('root', 'taxonomy');
	});

	return (
		<>
			<PostTokenField
				postType={postType}
				options={options}
				setOptions={setOptions}
			/>
			{taxonomies &&
				taxonomies
					.filter((taxonomy) => {
						return postType.taxonomies.includes(taxonomy.slug);
					})
					.map((taxonomy) => {
						return (
							<PostTokenField
								key={taxonomy.slug}
								postType={postType}
								taxonomy={taxonomy}
								options={options}
								setOptions={setOptions}
							/>
						);
					})}
		</>
	);
}
