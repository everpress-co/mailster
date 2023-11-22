/**
 * External dependencies
 */

import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import { __, sprintf } from '@wordpress/i18n';

import {
	useBlockProps,
	InnerBlocks,
	InspectorControls,
} from '@wordpress/block-editor';
import {
	Tooltip,
	PanelBody,
	Panel,
	ToggleControl,
	Button,
	Modal,
} from '@wordpress/components';
import { useSelect, select, useDispatch, dispatch } from '@wordpress/data';
import { useEffect, useState } from '@wordpress/element';

/**
 * Internal dependencies
 */

import './editor.scss';
import { TABS } from './constants';
import HomepageInspectorControls from './inspector';
import HomepageBlockControls from './BlockControls';
import { HelpBeacon, searchBlocks } from '../util';

const BLOCK_TEMPLATE = [
	['mailster/homepage-context', { type: 'submission' }],
	['mailster/homepage-context', { type: 'profile' }],
	['mailster/homepage-context', { type: 'unsubscribe' }],
	['mailster/homepage-context', { type: 'subscribe' }],
];

export default function Edit(props) {
	const { attributes, setAttributes, isSelected } = props;

	const { showAll } = attributes;

	const className = ['mailster-homepage'];

	const [current, setCurrent] = useState();
	const [shortcodes, setShortCodes] = useState(false);
	const [shortcodesModal, setShortCodesModal] = useState(false);

	useEffect(() => {
		if (!isSelected) return;
		location.hash = '';
	}, [isSelected]);

	useEffect(() => {
		if (!current) return;

		history.replaceState(undefined, undefined, '#mailster-' + current);

		const block = searchBlocks('mailster/homepage-context').find((block) => {
			return block.attributes.type === current;
		});

		block && dispatch('core/block-editor').selectBlock(block.clientId);

		return () => {
			history.pushState(
				'',
				document.title,
				location.pathname + location.search
			);
		};
	}, [current]);

	useEffect(() => {
		const hash = location.hash.substring(10);
		hash && setCurrent(hash);
		window.addEventListener('hashchange', onHashChange);
		return () => {
			window.removeEventListener('hashchange', onHashChange);
		};
	}, []);
	useEffect(() => {
		const shortcodes = searchBlocks('core/shortcode').filter((block) => {
			return (
				block.attributes.text.match(/^\[newsletter_signup/) ||
				block.attributes.text.match(/^\[newsletter_confirm/) ||
				block.attributes.text.match(/^\[newsletter_unsubscribe/)
			);
		});
		if (shortcodes.length) {
			setShortCodes(shortcodes);
			setShortCodesModal(true);
		}
	}, []);

	//set other forms if only "submission" is set
	useEffect(() => {
		if (attributes.submission) {
			if (!attributes.profile)
				setAttributes({ profile: attributes.submission });
			if (!attributes.unsubscribe)
				setAttributes({ unsubscribe: attributes.submission });
		}
	}, [attributes]);

	const removeLegacyBlocks = () => {
		shortcodes.forEach((block) => {
			dispatch('core/block-editor').removeBlock(block.clientId);
		});
		setShortCodes(false);
		setShortCodesModal(false);
	};

	const onSelect = (type, index) => {
		location.hash = '#mailster-' + type;
		setCurrent(type);
	};

	const onHashChange = () => {
		setCurrent(location.hash.substring(10) ?? 'submission');
	};

	const currentTab = TABS.find((tab) => tab.id === current);

	className.push('tab-' + (current || 'submission'));

	if (showAll) className.push('show-all');

	const blockProps = useBlockProps({
		className: classnames({}, className),
	});

	return (
		<>
			<div {...blockProps}>
				{shortcodesModal && shortcodes && (
					<Modal
						title={__(
							'Mailster Legacy Shortcodes have been detected!',
							'mailster'
						)}
						onRequestClose={() => setShortCodesModal(false)}
					>
						<p>
							{sprintf(
								__(
									'If you use the legacy shortcodes (%s) you can remove them now.',
									'mailster'
								),
								'[newsletter_signup]'
							)}
						</p>
						<p>
							{__(
								'The new newsletter homepage block will replace them.',
								'mailster'
							)}
						</p>
						<Button
							onClick={removeLegacyBlocks}
							variant="secondary"
							isDestructive={true}
						>
							{__('Remove Legacy Shortcodes', 'mailster')}
						</Button>{' '}
						<Button
							onClick={() => setShortCodesModal(false)}
							variant="tertiary"
						>
							{__('Keep them', 'mailster')}
						</Button>
					</Modal>
				)}

				<InnerBlocks template={BLOCK_TEMPLATE} templateLock="all" />
			</div>
			<InspectorControls></InspectorControls>
			<HomepageInspectorControls
				current={current || 'submission'}
				onSelect={onSelect}
			/>
			<HomepageBlockControls
				{...props}
				current={current || 'submission'}
				onSelect={onSelect}
			/>
		</>
	);
}
