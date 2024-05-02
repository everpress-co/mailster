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
import { searchBlocks, useWindow } from '../../util';
import StepIcon from './Icon.js';

export default function StepId(props) {
	const { attributes, setAttributes, clientId, isSelected } = props;
	const { step } = attributes;

	const [stepBlocks, setStepBlocks] = useState([]);
	const [currentStep, setCurrentStep] = useState();
	const [isFound, setFound] = useState(false);

	const window = useWindow();

	const { toggleBlockHighlight } = dispatch('core/block-editor');

	useEffect(() => {
		const blocks = searchBlocks(
			'^mailster-workflow/(conditions|action|email|delay|stop|jumper)$'
		);
		if (stepBlocks !== blocks) setStepBlocks(blocks);
	}, [isSelected]);

	useEffect(() => {
		if (!stepBlocks.length) return;
		const s = stepBlocks.find((b) => b.attributes.id === step);
		if (s) {
			setCurrentStep(s);
		} else {
			setAttributes({ step: undefined });
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
				<MenuGroup key={id}>
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
	const info = currentStep || __('Select a Step', 'mailster');

	return (
		<BaseControl>
			<Panel>
				<PanelRow>
					<h3>{__('Jump to Step', 'mailster')}</h3>
				</PanelRow>
				{stepBlocks.length < 1 && (
					<PanelRow>
						<p>{__('No valid steps found', 'mailster!')}</p>
					</PanelRow>
				)}
				{stepBlocks.length > 1 && (
					<>
						<PanelRow>
							<DropdownMenu text={label} label={info} icon={icon}>
								{(props) => <StepButtons {...props} />}
							</DropdownMenu>
						</PanelRow>
						{step && (
							<PanelRow>
								{!isFound && (
									<Button
										variant="link"
										onClick={() => {
											toggleBlockHighlight(currentStep.clientId, true);
											const e = window.document.getElementById(
												'block-' + currentStep.clientId
											);
											e && e.scrollIntoView(scrollBehavior);
											setFound(!isFound);
										}}
									>
										{__('Find Step', 'mailster')}
									</Button>
								)}
								{isFound && (
									<Button
										variant="link"
										onClick={() => {
											toggleBlockHighlight(clientId, true);
											const e = window.document.getElementById(
												'block-' + clientId
											);
											e && e.scrollIntoView(scrollBehavior);
											setFound(!isFound);
										}}
									>
										{__('Back to Jumper', 'mailster')}
									</Button>
								)}
							</PanelRow>
						)}
					</>
				)}
			</Panel>
		</BaseControl>
	);
}
