/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { __, _n } from '@wordpress/i18n';
import ServerSideRender from '@wordpress/server-side-render';

import {
	Button,
	PanelRow,
	Spinner,
	Modal,
	BaseControl,
} from '@wordpress/components';

import { useSelect } from '@wordpress/data';
import { useEffect, useState } from '@wordpress/element';

/**
 * Internal dependencies
 */

export default function ConditionsModal(props) {
	const { attributes, setAttributes, help = '', label, title } = props;
	const { conditions } = attributes;

	const [isLoaded, setLoaded] = useState(false);
	const [isOpen, setOpen] = useState(false);

	const allEmails = useSelect((select) =>
		select('mailster/automation').getEmails()
	);

	useEffect(() => {
		const i = setInterval(function () {
			const el = document.querySelector('.mailster-conditions');
			if (el) {
				setLoaded(true);
				mailster.conditions.init(el);
				clearInterval(i);
			}
		}, 100);

		return () => {
			clearInterval(i);
		};
	});
	useEffect(() => {
		if (!conditions) return;

		//TODO: should be done serveer side
		setAttributes({ conditions: conditions.replace('&amp;', '&') });
	}, [conditions]);

	return (
		<>
			<BaseControl help={help}>
				<PanelRow>
					<h3>{label || __('Conditions', 'mailster')}</h3>
				</PanelRow>
				{conditions && (
					<PanelRow>
						<ServerSideRender
							block="mailster-workflow/conditions"
							className="conditions-preview"
							attributes={{
								...attributes,
								...{ render: true, plain: false, emails: allEmails },
							}}
							EmptyResponsePlaceholder={() => <Spinner />}
						/>
					</PanelRow>
				)}
				<PanelRow>
					<Button variant="secondary" onClick={() => setOpen(true)}>
						{conditions
							? __('Change Conditions', 'mailster')
							: __('Add Conditions', 'mailster')}
					</Button>
					{conditions && (
						<Button
							variant="link"
							isDestructive
							onClick={() => setAttributes({ conditions: undefined })}
						>
							{__('Clear', 'mailster')}
						</Button>
					)}
				</PanelRow>
			</BaseControl>
			{isOpen && (
				<Modal
					title={
						title || __('Define your conditions for this step', 'mailster')
					}
					className="mailster-conditions-modal"
					onRequestClose={() => setOpen(false)}
					shouldCloseOnClickOutside={true}
					__experimentalhideheader
				>
					<ServerSideRender
						block="mailster-workflow/conditions"
						attributes={{ ...attributes, ...{ emails: allEmails } }}
					/>
					<div className="modal-conditions-buttons">
						<Button
							variant="primary"
							disabled={!isLoaded}
							onClick={() => {
								setOpen(false);
								setAttributes({ conditions: mailster.conditions.serialize() });
							}}
						>
							Save
						</Button>
						<Button
							disabled={!isLoaded}
							variant="secondary"
							onClick={() => {
								setOpen(false);
							}}
						>
							Cancel
						</Button>
					</div>
				</Modal>
			)}
		</>
	);
}
