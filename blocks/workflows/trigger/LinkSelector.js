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
	const { links = [] } = attributes;

	const site = useSelect((select) => select('core').getSite());

	function add() {
		var newLinks = [...links];
		newLinks.push('');

		setAttributes({ links: newLinks.length ? newLinks : undefined });
	}

	function update(id, val) {
		var newLinks = [...links];
		newLinks[id] = val;
		setAttributes({ links: newLinks.length ? newLinks : undefined });
	}

	function remove(id) {
		var newLinks = [...links];

		newLinks = newLinks.filter((el, i) => {
			return i != id;
		});

		setAttributes({ links: newLinks.length ? newLinks : undefined });
	}

	return (
		<>
			<BaseControl>
				{links.map((link, i) => {
					return (
						<Card key={i} className="mailster-trigger-link ">
							<Flex>
								<FlexItem>
									<TextControl
										value={link}
										className="widefat"
										placeholder="https://"
										type="url"
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
					{__('Add Link', 'mailster')}
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
