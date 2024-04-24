/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import { InspectorControls } from '@wordpress/block-editor';
import {
	Panel,
	PanelRow,
	PanelBody,
	DropdownMenu,
	MenuGroup,
	MenuItem,
	BaseControl,
} from '@wordpress/components';

import { useEffect, useState } from '@wordpress/element';
import { useSelect, select, dispatch } from '@wordpress/data';
import { getBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import { searchBlocks } from '../../util';

export default function StepId(props) {
	const { attributes, setAttributes, clientId, isSelected } = props;
	const { id, step } = attributes;

	const [stepBlocks, setStepBlocks] = useState([]);

	// useEffect(() => {
	// 	!amount && setAttributes({ amount: 1 });
	// 	!unit && setAttributes({ unit: 'hours' });
	// 	!month && setAttributes({ month: 1 });
	// 	!date && setAttributes({ date: new Date() });
	// });

	const { getBlockIndex, getBlock, getBlocks, getBlockAttributes } =
		select('core/block-editor');

	const { selectBlock, toggleBlockHighlight } = dispatch('core/block-editor');

	useEffect(() => {
		const blocks = searchBlocks(
			'^mailster-workflow/(conditions|action|email|delay)$'
		);
		setStepBlocks(blocks);
	}, [isSelected]);

	const setStep = (step) => {
		console.log(step);
		setAttributes({ step: step });
	};

	const StepButtons = ({ onClose }) => {
		return stepBlocks.map((t, i) => {
			const id = t.attributes.id;
			const type = getBlockType(t.name);
			const b = getBlock(t.clientId);
			console.log(t, b);
			return (
				<MenuGroup key={i}>
					<MenuItem
						icon={type.icon.src || type.icon}
						iconPosition={'left'}
						info={id}
						isSelected={id === step}
						onMouseOver={() => {
							toggleBlockHighlight(t.clientId, true);
						}}
						onMouseLeave={() => {
							toggleBlockHighlight(t.clientId, false);
						}}
						//disabled={t.disabled}
						onClick={() => {
							setStep(id);
							onClose();
						}}
					>
						{type.title}
					</MenuItem>
				</MenuGroup>
			);
		});
	};

	const currentStep = stepBlocks.find((b) => b.attributes.id === step);

	const label =
		(currentStep && getBlockType(currentStep.name).title) ||
		__('Select a step', 'mailster');
	const icon =
		(currentStep && getBlockType(currentStep.name).icon.src) ||
		__('Select a step', 'mailster');
	const info =
		(currentStep && currentStep.id) || __('Select a step', 'mailster');

	return (
		<BaseControl>
			<Panel>
				<PanelRow>
					<h3>{__('Step', 'mailster')}</h3>
				</PanelRow>

				<PanelRow>
					<DropdownMenu text={label} info={info} icon={icon}>
						{(props) => <StepButtons {...props} />}
					</DropdownMenu>
				</PanelRow>
			</Panel>
		</BaseControl>
	);
}
