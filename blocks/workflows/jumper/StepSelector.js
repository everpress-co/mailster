/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import {
	Panel,
	PanelRow,
	DropdownMenu,
	MenuGroup,
	MenuItem,
	BaseControl,
	Button,
} from '@wordpress/components';

import { useEffect, useState } from '@wordpress/element';
import { dispatch } from '@wordpress/data';
import { getBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import { searchBlocks, useSteps, useWindow } from '../../util';
import StepIcon from './Icon.js';

export default function StepId(props) {
	const { attributes, setAttributes, clientId, isSelected } = props;
	const { step } = attributes;

	const [currentStep, setCurrentStep] = useState();
	const [isFound, setFound] = useState(false);

	const window = useWindow();

	const { toggleBlockHighlight } = dispatch('core/block-editor');

	const stepBlocks = useSteps(isSelected);

	useEffect(() => {
		if (!stepBlocks.length) return;
		const s = stepBlocks.find((b) => b.attributes.id === step);
		if (s) {
			setCurrentStep(s);
		} else {
			setStep(undefined);
		}
	}, [step, stepBlocks]);

	const setStep = (step) => {
		setAttributes({ step: step });
	};

	const scrollBehavior = {
		block: 'center',
		inline: 'nearest',
	};

	const StepButtons = ({ onClose }) => {
		return stepBlocks.map((t, i) => {
			if (t.clientId === clientId) return null;
			const id = t.attributes.id;
			const type = getBlockType(t.name);
			return (
				<MenuGroup key={i}>
					<MenuItem
						icon={type.icon.src || type.icon}
						iconPosition={'left'}
						info={id}
						isSelected={id === step}
						onMouseOver={() => {
							toggleBlockHighlight(t.clientId, true);
							const e = window.document.getElementById('block-' + t.clientId);
							e && e.scrollIntoView(scrollBehavior);
						}}
						onClick={() => {
							setStep(id);
							onClose();
							const e = window.document.getElementById('block-' + clientId);
							e && e.scrollIntoView({ behavior: 'smooth' });
							toggleBlockHighlight(clientId, true);
						}}
					>
						{type.title}
					</MenuItem>
				</MenuGroup>
			);
		});
	};

	const label =
		(currentStep && getBlockType(currentStep.name).title) ||
		__('Select a step', 'mailster');
	const icon =
		(currentStep && getBlockType(currentStep.name).icon.src) || StepIcon;

	return (
		<BaseControl>
			<Panel>
				{stepBlocks.length < 1 && (
					<PanelRow>
						<p>{__('No valid steps found', 'mailster!')}</p>
					</PanelRow>
				)}
				{stepBlocks.length > 1 && (
					<BaseControl label={__('Jump to Step', 'mailster')}>
						<PanelRow>
							<DropdownMenu text={label} icon={icon}>
								{(props) => <StepButtons {...props} />}
							</DropdownMenu>
						</PanelRow>
						{step && (
							<PanelRow>
								<Button
									variant="secondary"
									isPressed={isFound}
									icon={isFound ? 'controls-back' : 'search'}
									onClick={() => {
										const id = isFound ? clientId : currentStep.clientId;
										toggleBlockHighlight(id, true);
										const e = window.document.getElementById('block-' + id);
										e && e.scrollIntoView(scrollBehavior);
										setFound(!isFound);
									}}
								>
									{isFound
										? __('Back to Jumper', 'mailster')
										: __('Find Step', 'mailster')}
								</Button>
							</PanelRow>
						)}
					</BaseControl>
				)}
			</Panel>
		</BaseControl>
	);
}
