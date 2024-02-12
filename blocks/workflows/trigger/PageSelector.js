/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import {
	Button,
	TextControl,
	BaseControl,
	Card,
	Flex,
	FlexItem,
	Tip,
} from '@wordpress/components';

import { useSelect } from '@wordpress/data';
import * as Icons from '@wordpress/icons';

/**
 * Internal dependencies
 */

export default function Selector(props) {
	const { attributes, setAttributes } = props;
	const { pages = [] } = attributes;

	const site = useSelect((select) => select('core').getSite());

	function add() {
		var newPages = [...pages];

		newPages.push('/');

		setAttributes({ pages: newPages.length ? newPages : undefined });
	}

	function update(id, val) {
		var newPages = [...pages];
		newPages[id] = val.replace(site.url, '').replace(/^\//, '');
		newPages[id] = '/' + newPages[id];
		setAttributes({ pages: newPages.length ? newPages : undefined });
	}

	function remove(id) {
		var newPages = [...pages];

		newPages = newPages.filter((el, i) => {
			return i != id;
		});

		setAttributes({ pages: newPages.length ? newPages : undefined });
	}

	return (
		<>
			<BaseControl>
				{pages.map((link, i) => {
					return (
						<Card key={i} className="mailster-trigger-link">
							<Flex>
								<FlexItem>
									<TextControl
										label={site.url}
										value={link}
										onChange={(val) => update(i, val)}
									/>
								</FlexItem>
							</Flex>
							<Button variant="link" onClick={() => remove(i)}>
								{__('remove', 'mailster')}
							</Button>
						</Card>
					);
				})}
			</BaseControl>
			<BaseControl>
				<Button
					variant="secondary"
					onClick={() => add()}
					help={__(
						'Select all lists where this workflow should get triggered',
						'mailster'
					)}
				>
					{__('Add Page', 'mailster')}
				</Button>
			</BaseControl>
			<BaseControl>
				<Tip>
					{__(
						'The user must be an active subscriber for this workflow to get triggered.',
						'mailster'
					)}
				</Tip>
			</BaseControl>
		</>
	);
}
