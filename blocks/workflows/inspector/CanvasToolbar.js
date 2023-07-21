/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __ } from '@wordpress/i18n';

import { select } from '@wordpress/data';
import { Button, RangeControl } from '@wordpress/components';
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

	const [root, setRoot] = useState(null);

	const setZoom = (zoom) => {
		if (root) root.style.scale = `${zoom}%`;
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
		var offsetX, offsetY, posX, posY, frame, canvasRoot;

		const editorCanvas = document.querySelector('iframe[name="editor-canvas"]');

		wp.domReady(() => {
			frame = document.querySelector('.interface-interface-skeleton__content');
			const int = setInterval(() => {
				canvasRoot = editorCanvas
					? editorCanvas.contentWindow.document.querySelector(
							'.is-root-container'
					  )
					: document.querySelector('.is-root-container');
				if (canvasRoot) {
					clearInterval(int);
					setRoot(canvasRoot);

					canvasRoot.addEventListener('mousedown', startDrag);
				}
			}, 10);

			if (position.x) frame.scrollLeft = position.x;
			if (position.y) frame.scrollTop = position.y;
		});

		function startDrag(e) {
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

			canvasRoot.classList.add('dragging');
			canvasRoot.addEventListener('mouseup', stopDrag);
			canvasRoot.addEventListener('mousemove', drag);
		}

		function drag(e) {
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

		function stopDrag(e) {
			// stop moving when mouse button is released:

			canvasRoot.classList.remove('dragging');
			canvasRoot.removeEventListener('mouseup', stopDrag);
			canvasRoot.removeEventListener('mousemove', drag);

			setPosition({
				x: frame.scrollLeft,
				y: frame.scrollTop,
			});
		}
	}, []);

	const MAX = 100;
	const MIN = 20;

	useEffect(() => {
		if (root) root.style.scale = `${zoom}%`;
	}, [zoom, root]);

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
		setPosition({
			x: 0,
			y: 0,
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
}
