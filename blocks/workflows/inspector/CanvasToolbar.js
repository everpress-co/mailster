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
import { useDebounce } from '@wordpress/compose';

/**
 * Internal dependencies
 */

import { useSessionStorage } from '../../util';

const MAX_ZOOM = 100;
const MIN_ZOOM = 40;

export default function CanvasToolbar() {
	const post_id = select('core/editor').getCurrentPostId();

	const [position, setPositionVar] = useSessionStorage('workflow-' + post_id);

	//check if editor is in iframe (Since WP 6.3)
	const iframed = document.querySelector('iframe[name="editor-canvas"]');
	const [loaded, isLoaded] = useState();
	const [frame, setFrame] = useState();
	const [pane, setPane] = useState();

	const onScroll = useDebounce(() => {
		let pos = getPanePosition();
		setOrigin(pos.x, pos.y);
		setPositionVar(pos);
	}, 400);

	useEffect(() => {
		if (iframed) {
			iframed.addEventListener('load', () => {
				setFrame(iframed.contentWindow);
				setPane(
					iframed.contentWindow.document.querySelector('.is-root-container')
				);
				isLoaded(true);
			});
		} else {
			wp.domReady(() => {
				setFrame(
					document.querySelector('.interface-interface-skeleton__content')
				);
				setPane(document.querySelector('.is-root-container'));
				isLoaded(true);
			});
		}
	}, []);

	useEffect(() => {
		if (!pane) return;

		var offsetX, offsetY, posX, posY;

		pane.addEventListener('mousedown', startDrag);
		frame.addEventListener('scroll', onScroll);

		if (!position) {
			resetPane(true);
		} else {
			setPanePosition(position.x, position.y, position.z);
		}
		pane.classList.add('loaded');

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

			pane.classList.add('dragging');
			pane.addEventListener('mouseup', stopDrag);
			pane.addEventListener('mouseleave', stopDrag);
			pane.addEventListener('mousemove', drag);
			frame.removeEventListener('scroll', onScroll);
		}

		function drag(e) {
			e.preventDefault();

			let pos = getPanePosition();
			let f = (100 - pos.z) * Math.exp(-4) + 1;
			f = 1;

			// calculate the new cursor position:
			offsetX = e.clientX - posX;
			offsetY = e.clientY - posY;
			posX = e.clientX;
			posY = e.clientY;

			let x = Math.max(0, pos.x - offsetX);
			let y = Math.max(0, pos.y - offsetY);

			// set the element's new position:
			setPanePosition(x, y);
		}

		function stopDrag(e) {
			// stop moving when mouse button is released:
			pane.classList.remove('dragging');
			pane.removeEventListener('mouseup', stopDrag);
			pane.removeEventListener('mouseleave', stopDrag);
			pane.removeEventListener('mousemove', drag);
			frame.addEventListener('scroll', onScroll);

			setPosition(getPanePosition());
		}
	}, [pane]);

	const setZoom = (zoom) => {
		setPanePosition(position?.x, position?.y, zoom);
		setPosition({ z: zoom });
	};

	const setPosition = (newPos) => {
		setPositionVar({
			...position,
			...newPos,
		});
	};

	const setPanePosition = (x, y, z) => {
		if (!pane) return;
		frame.scrollTo(x, y);
		if (z) pane.style.scale = `${z}%`;
		setOrigin(x, y);
	};

	const getPanePosition = () => {
		return {
			x: iframed ? frame.scrollX : frame.scrollLeft,
			y: iframed ? frame.scrollY : frame.scrollTop,
			z: (pane.style.scale || 1) * 100,
		};
	};

	const setOrigin = (x, y) => {
		// TODO make this work
		return;
		if (!pane) return;
		let i = iframed.getBoundingClientRect();
		let body = iframed.contentWindow.document.body;
		let w = body.offsetWidth - i.width;
		let h = body.offsetHeight - i.height;

		x = w ? x / w : 0.5;
		y = h ? y / h : 0.5;

		pane.style.transformOrigin = `${x * 100}% ${y * 100}%`;
	};

	useEffect(() => {
		if (!pane) return;

		position && setZoom(position.z);
	}, [pane]);

	useEffect(() => {
		const onResize = () => {
			console.warn('RESIZE');
		};
		onResize();
		window.addEventListener('resize', onResize);
		return () => {
			window.removeEventListener('resize', onResize);
		};
	}, []);

	const resetPane = () => {
		const triggers = pane.querySelector('.wp-block-mailster-workflow-triggers');

		if (triggers) {
			setTimeout(() => {
				triggers.scrollIntoView({
					inline: 'center',
					block: 'center',
					behavior: 'smooth',
				});
			}, 100);
			setZoom(100);
		} else {
			setPanePosition(0, 0, 100);
		}
	};

	const zoom = position ? position.z : 100;

	const zoomIn = () => {
		setZoom(Math.min(zoom + 10, MAX_ZOOM));
	};

	const zoomOut = () => {
		setZoom(Math.max(zoom - 10, MIN_ZOOM));
	};

	if (!loaded) return null;

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
				disabled={zoom === MIN_ZOOM}
				onClick={zoomOut}
				label={__('Zoom Out', 'mailster')}
			/>
			<RangeControl
				className="zoom-level"
				withInputField={false}
				value={zoom}
				onChange={(value) => setZoom(value)}
				min={MIN_ZOOM}
				max={MAX_ZOOM}
				showTooltip={false}
			/>
			<Button
				variant="link"
				icon={plus}
				disabled={zoom === MAX_ZOOM}
				onClick={zoomIn}
				label={__('Zoom In', 'mailster')}
			/>
		</>
	);
}
