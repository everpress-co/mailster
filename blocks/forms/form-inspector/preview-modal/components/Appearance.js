/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import {
	RangeControl,
	PanelBody,
	PanelRow,
	SelectControl,
	__experimentalBoxControl as BoxControl,
	__experimentalItemGroup as ItemGroup,
} from '@wordpress/components';

/**
 * Internal dependencies
 */

export default function PlacementSettings(props) {
	const { options, setOptions, placement } = props;
	const { type } = placement;

	return (
		<PanelBody title={__('Appearance', 'mailster')} initialOpen={false}>
			<PanelRow>
				<RangeControl
					className="widefat"
					label={__('Form Width', 'mailster')}
					help={__('Set the with of your form in %', 'mailster')}
					value={options.width}
					allowReset={true}
					onChange={(val) => setOptions({ width: val })}
					min={10}
					max={100}
					initialPosition={100}
				/>
			</PanelRow>
			<PanelRow>
				<BoxControl
					label={__('Form Padding', 'mailster')}
					values={options.padding}
					help={__('Set the padding of your form in %', 'mailster')}
					resetValues={{
						top: undefined,
						left: undefined,
						right: undefined,
						bottom: undefined,
					}}
					onChange={(val) => setOptions({ padding: val })}
				/>
			</PanelRow>
			{'content' != type && (
				<PanelRow>
					<ItemGroup isBordered={false} size="small">
						<SelectControl
							label={__('Animation', 'mailster')}
							value={options.animation}
							onChange={(val) => setOptions({ animation: val })}
						>
							<option value="">{__('None', 'mailster')}</option>
							<option value="fadein">
								{__('FadeIn', 'mailster')}
							</option>
							<option value="shake">
								{__('Shake', 'mailster')}
							</option>
							<option value="swing">
								{__('Swing', 'mailster')}
							</option>
							<option value="heartbeat">
								{__('Heart Beat', 'mailster')}
							</option>
							<option value="tada">
								{__('Tada', 'mailster')}
							</option>
							<option value="wobble">
								{__('Wobble', 'mailster')}
							</option>
						</SelectControl>
					</ItemGroup>
				</PanelRow>
			)}
		</PanelBody>
	);
}
