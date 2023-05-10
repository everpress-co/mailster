/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import { Spinner, CheckboxControl, BaseControl } from '@wordpress/components';

import { useSelect, select } from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';
import { ButtonGroup, Button, RangeControl } from '@wordpress/components';
import { plus, reset, home } from '@wordpress/icons';
import { useEffect, useState } from '@wordpress/element';

/**
 * Internal dependencies
 */

import { useSessionStorage } from '../../util';

export default function CanvasToolbar() {
	const post_id = select('core/editor').getCurrentPostId();

	const [zoom, setZoomVar] = useSessionStorage(
		'workflow-' + post_id + '-zoom',
		100
	);
	const [position, setPositionVar] = useSessionStorage(
		'workflow-' + post_id + '-position',
		{
			x: 0,
			y: 0,
		}
	);

	const setZoom = (zoom) => {
		document.querySelector('.is-root-container').style.scale = `${zoom}%`;
		setZoomVar(zoom);
	};

	const setPosition = (position) => {
		const frame = document.querySelector(
			'.interface-interface-skeleton__content'
		);
		if (position.x) frame.scrollLeft = position.x;
		if (position.y) frame.scrollTop = position.y;

		setPositionVar(position);
	};

	useEffect(() => {
		var offsetX, offsetY, posX, posY, frame, pane, root;

		wp.domReady(() => {
			frame = document.querySelector('.interface-interface-skeleton__content');
			pane = document.querySelector('.edit-post-visual-editor');
			root = document.querySelector('.is-root-container');
			pane.addEventListener('mousedown', startDrag);

			if (position.x) frame.scrollLeft = position.x;
			if (position.y) frame.scrollTop = position.y;
		});

		function startDrag(e) {
			e = e || window.event;

			if (
				!e.target.classList.contains('is-root-container') &&
				!e.target.classList.contains('editor-styles-wrapper') &&
				!e.target.classList.contains('canvas-handle')
			) {
				return;
			}
			// get the mouse cursor position at startup:
			posX = e.clientX || e.changedTouches[0].clientX;
			posY = e.clientY || e.changedTouches[0].clientY;

			pane.classList.add('dragging');
			document.addEventListener('mouseup', stopDrag);
			document.addEventListener('mousemove', drag);
		}

		function drag(e) {
			e = e || window.event;
			e.preventDefault();
			// calculate the new cursor position:
			offsetX = e.clientX - posX;
			offsetY = e.clientY - posY;
			posX = e.clientX;
			posY = e.clientY;

			// set the element's new position:
			frame.scrollLeft = Math.max(0, frame.scrollLeft - offsetX);
			frame.scrollTop = Math.max(0, frame.scrollTop - offsetY);
		}

		function stopDrag() {
			// stop moving when mouse button is released:

			pane.classList.remove('dragging');
			document.removeEventListener('mouseup', stopDrag);
			document.removeEventListener('mousemove', drag);

			setPosition({
				x: frame.scrollLeft,
				y: frame.scrollTop,
			});
		}
	}, []);

	const MAX = 100;
	const MIN = 20;

	useEffect(() => {
		document.querySelector('.is-root-container').style.scale = `${zoom}%`;
	}, [zoom]);

	const resetPane = () => {
		const triggers = document.querySelector(
			'.wp-block-mailster-workflow-triggers'
		);
		triggers &&
			triggers.scrollIntoView({
				inline: 'center',
				block: 'center',
				behavior: 'smooth',
			});
		setZoom(100);
	};

	const zoomIn = () => {
		setZoom(Math.min(zoom + 10, MAX));
	};

	const zoomOut = () => {
		setZoom(Math.max(zoom - 10, MIN));
	};

	return (
		<>
			<Button
				variant="tertiary"
				icon={home}
				onClick={resetPane}
				label={__('Reset View', 'mailster')}
			/>
			<Button
				variant="link"
				icon={reset}
				disabled={zoom === MIN}
				onClick={zoomOut}
				label={__('Zoom Out', 'mailster')}
			/>
			<RangeControl
				className="zoom-level"
				withInputField={false}
				value={zoom}
				onChange={(value) => setZoom(value)}
				min={MIN}
				max={MAX}
				showTooltip={false}
			/>
			<Button
				variant="link"
				icon={plus}
				disabled={zoom === MAX}
				onClick={zoomIn}
				label={__('Zoom In', 'mailster')}
			/>
		</>
	);

	return (
		<ButtonGroup>
			{zoom}
			<Button
				variant="secondary"
				icon={plus}
				onClick={zoomIn}
				shortcut={__('asda', 'asdas')}
			/>
			<Button variant="secondary" icon={reset} onClick={zoomOut} />
		</ButtonGroup>
	);
}
