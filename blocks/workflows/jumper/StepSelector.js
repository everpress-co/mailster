/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
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
import { searchBlocks, useWindow, whenEditorIsReady } from '../../util';
import StepIcon from './Icon.js';

export default function StepId(props) {
	const { attributes, setAttributes, clientId, isSelected } = props;
	const { id, step, conditions } = attributes;

	const [stepBlocks, setStepBlocks] = useState([]);

	const window = useWindow();

	const { getBlock } = select('core/block-editor');
	const { flashBlock, toggleBlockHighlight } = dispatch('core/block-editor');

	useEffect(() => {
		const blocks = searchBlocks(
			'^mailster-workflow/(conditions|action|email|delay)$'
		);
		setStepBlocks(blocks);
	}, [isSelected]);

	const setStep = (step) => {
		setAttributes({ step: step });
	};

	const StepButtons = ({ onClose }) => {
		return stepBlocks.map((t, i) => {
			const id = t.attributes.id;
			const type = getBlockType(t.name);
			const b = getBlock(t.clientId);
			return (
				<MenuGroup key={i}>
					<MenuItem
						icon={type.icon.src || type.icon}
						iconPosition={'left'}
						info={id}
						isSelected={id === step}
						onMouseOver={() => {
							toggleBlockHighlight(t.clientId, true);
							const element = window.document.getElementById(
								'block-' + t.clientId
							);
							element &&
								element.scrollIntoView({
									behavior: 'smooth',
									block: 'center',
									inline: 'nearest',
								});
						}}
						onMouseLeave={() => {
							toggleBlockHighlight(t.clientId, false);
						}}
						//disabled={t.disabled}
						onClick={() => {
							//toggleBlockHighlight(t.clientId, false);
							flashBlock(t.clientId);
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
		(currentStep && getBlockType(currentStep.name).icon.src) || StepIcon;
	const info =
		(currentStep && currentStep.id) || __('Select a Step', 'mailster');

	return (
		<BaseControl>
			<Panel>
				<PanelRow>
					<h3>{__('Jump to Step', 'mailster')}</h3>
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
