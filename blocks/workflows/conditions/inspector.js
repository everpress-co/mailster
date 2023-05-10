/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import { InspectorControls } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import {
	Panel,
	PanelRow,
	PanelBody,
	CheckboxControl,
	TextControl,
	RangeControl,
	SelectControl,
	__experimentalNumberControl as NumberControl,
	FlexItem,
	Flex,
	Spinner,
	__experimentalItemGroup as ItemGroup,
	DropdownMenu,
	MenuGroup,
	MenuItem,
	Button,
	Modal,
	Tip,
} from '@wordpress/components';

import { useEffect, useState } from '@wordpress/element';
import { useSelect } from '@wordpress/data';
import * as Icons from '@wordpress/icons';

/**
 * Internal dependencies
 */

import Conditions from '../inspector/Conditions.js';

export default function ConditionInspectorControls(props) {
	const { attributes, setAttributes } = props;
	const { weight } = attributes;

	return (
		<InspectorControls>
			<Panel>
				<PanelBody>
					<Conditions {...props} />
				</PanelBody>
			</Panel>
			{false && (
				<Panel>
					<PanelBody>
						<RangeControl
							label="weight"
							value={weight}
							min={0}
							max={1}
							step={0.01}
							withInputField={false}
							onChange={(value) => setAttributes({ weight: parseFloat(value) })}
						/>
					</PanelBody>
				</Panel>
			)}
		</InspectorControls>
	);
}
