/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __, sprintf } from '@wordpress/i18n';

import {
	Panel,
	PanelBody,
	PanelRow,
	CheckboxControl,
	__experimentalItemGroup as ItemGroup,
	__experimentalItem as Item,
} from '@wordpress/components';
import { useState, useEffect } from '@wordpress/element';
import { useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import DisplayOptions from './DisplayOptions';
import Triggers from './Triggers';
import Schedule from './Schedule';
import Appearance from './Appearance';

export default function PlacementSettings(props) {
	const { meta, placement, setPlacements, useThemeStyle, setUseThemeStyle } =
		props;
	const { type, title } = placement;

	const postID = useSelect(
		(select) => select('core/editor').getCurrentPostId(),
		[]
	);

	const [isEnabled, setIsEnabled] = useState(meta.placements.includes(type));
	useEffect(() => {
		meta.placements && setIsEnabled(meta.placements.includes(type));
	}, [meta.placements]);

	return (
		<Panel>
			{'other' == type ? (
				<PanelRow>
					<ItemGroup className="widefat" isBordered={false} size="medium">
						<Item>
							<h3>PHP</h3>
						</Item>
						<Item>
							<pre>
								<code id={'form-php-' + postID}>
									{'<?php echo mailster_form( ' + postID + ' ); ?>'}
								</code>
							</pre>
						</Item>
						<Item>
							<code id="form-php-2">
								{'echo mailster_form( ' + postID + ' );'}
							</code>
						</Item>
						<Item>
							<code id="form-php-3">
								{'<?php $form_html = mailster_form( ' + postID + ' ); ?>'}
							</code>
						</Item>
						<Item>
							<CheckboxControl
								label={__('useThemeStyle', 'mailster')}
								checked={useThemeStyle}
								onChange={(val) => setUseThemeStyle(!useThemeStyle)}
							/>
						</Item>
					</ItemGroup>
				</PanelRow>
			) : (
				<>
					<PanelBody opened={true}>
						<PanelRow>
							<CheckboxControl
								label={sprintf(
									__('Enabled this form for %s.', 'mailster'),
									title
								)}
								value={type}
								checked={isEnabled}
								onChange={(val) => setPlacements(type, val)}
							/>
						</PanelRow>
					</PanelBody>

					{isEnabled && (
						<>
							<DisplayOptions {...props} />
							<Triggers {...props} />
							<Schedule {...props} />
							<Appearance {...props} />
						</>
					)}
				</>
			)}
		</Panel>
	);
}
