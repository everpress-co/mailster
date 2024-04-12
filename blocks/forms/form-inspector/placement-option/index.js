/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import {
	CheckboxControl,
	CardMedia,
	Card,
	CardHeader,
	CardFooter,
	Button,
	Icon,
	Flex,
} from '@wordpress/components';

import { settings } from '@wordpress/icons';

/**
 * Internal dependencies
 */

export default function PlacementOption(props) {
	const { meta, setMeta, setOpen, placement, setPlacements } = props;
	const { type, image, title } = placement;

	const className = ['placement-option'];

	meta.placements.includes(type) && className.push('enabled');

	const enabled = 'other' == type || meta.placements.includes(type);

	return (
		<Card size="small" className={className.join(' ')}>
			<CardHeader>
				<Flex align="center">
					{'other' != type && (
						<CheckboxControl
							value={type}
							checked={enabled}
							onChange={(val) => {
								setPlacements(type, val);
							}}
						/>
					)}

					<Button
						variant="link"
						onClick={() => setOpen(type)}
						icon={<Icon icon={settings} />}
						isSmall={true}
					/>
				</Flex>
			</CardHeader>
			<CardMedia disabled={!enabled} onClick={() => setOpen(type)}>
				{image}
			</CardMedia>
			<CardFooter>{title}</CardFooter>
		</Card>
	);
}
