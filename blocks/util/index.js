/**
 * External dependencies
 */
import { escape } from 'lodash';
import moment from 'moment';

/**
 * WordPress dependencies
 */
import { __, _n } from '@wordpress/i18n';
import {
	createInterpolateElement,
	useEffect,
	useMemo,
	useRef,
	useState,
	useCallback,
} from '@wordpress/element';
import {
	select,
	subscribe,
	useSelect,
	useDispatch,
	dispatch,
} from '@wordpress/data';

/**
 * Internal dependencies
 */

export function HelpBeacon({ id, align }) {
	const href = new URL('https://kb.mailster.co/' + id);
	href.searchParams.set('utm_campaign', 'plugin');
	href.searchParams.set('utm_medium', 'link');
	href.searchParams.set('utm_source', 'Mailster Plugin');
	href.searchParams.set('utm_term', 'workflow');

	var styles = {};

	if (align) {
		styles['float'] = align;
	}

	return (
		<a
			className="mailster-help"
			href={href.toString()}
			data-article={id}
			style={styles}
		></a>
	);
}

export function useUpdateEffectCustom(callback, dependencies) {
	const firstRenderRef = useRef(true);

	useEffect(() => {
		if (firstRenderRef.current) {
			firstRenderRef.current = false;
			return;
		}
		return callback();
	}, dependencies);
}

export function useUpdateEffect(effect, deps) {
	const mounted = useRef(false);

	useEffect(() => {
		if (mounted.current) {
			return effect();
		}
		mounted.current = true;
		return undefined;
	}, deps);
}

function getData(type) {
	switch (type) {
		case 'lists':
			return select('mailster/automation').getLists();
		case 'forms':
			return select('mailster/automation').getForms();
		case 'tags':
			return select('mailster/automation').getTags();
		case 'fields':
			return select('mailster/automation').getFields();
		case 'actions':
			return select('mailster/automation').getActions();
		case 'steps':
			return select('mailster/automation').getSteps();
	}

	return null;

	return useSelect(
		(select) => {
			switch (type) {
				case 'lists':
					return select('mailster/automation').getLists();
				case 'forms':
					return select('mailster/automation').getForms();
				case 'tags':
					return select('mailster/automation').getTags();
				case 'fields':
					return select('mailster/automation').getFields();
				case 'actions':
					return select('mailster/automation').getActions();
				case 'steps':
					return select('mailster/automation').getSteps();
			}
		},
		[type]
	);
}

export function formatLists(lists) {
	const allLists = getData('lists');

	if (!allLists) {
		return null;
	}

	if (!lists || !lists.length) {
		return <>{__('No List defined.', 'mailster')}</>;
	}

	const getList = (id) => {
		if (id == -1) {
			return <>{__('Any List', 'mailster')}</>;
		}
		if (!allLists) {
			return null;
		}
		const list = allLists.filter((list) => {
			return list.ID == id;
		});
		return list.length ? list[0].name : null;
	};

	return lists.map((list, i) => {
		const name = getList(list);

		return (
			<strong key={i} className="mailster-step-badge">
				{name || list}
			</strong>
		);
	});
}

export function formatForms(forms) {
	const allForms = getData('forms');

	if (!allForms) {
		return null;
	}

	if (!forms || !forms.length) {
		return <>{__('No Form defined.', 'mailster')}</>;
	}

	const getForm = (id) => {
		if (id == -1) {
			return <>{__('Any Form', 'mailster')}</>;
		}
		if (!allForms) {
			return null;
		}
		const form = allForms.filter((form) => {
			return form.ID == id;
		});
		return form.length ? form[0].name : null;
	};

	return forms.map((form, i) => {
		const name = getForm(form);

		return (
			<strong key={i} className="mailster-step-badge">
				{name || form}
			</strong>
		);
	});
}

export function formatTags(tags) {
	const allFields = getData('tags');

	if (!tags || !tags.length) {
		return <>{__('No Tags defined.', 'mailster')}</>;
	}

	return tags.map((tag, i) => {
		return (
			<strong key={i} className="mailster-step-badge">
				{tag}
			</strong>
		);
	});
}

export function formatField(field, value, string) {
	const allFields = getData('fields');

	if (!allFields || !field) {
		return <>{__('No Field defined.', 'mailster')}</>;
	}

	if (field == -1) {
		return (
			<strong className="mailster-step-badge">
				{__('Any Field', 'mailster')}
			</strong>
		);
	}

	const getField = (id) => {
		if (!allFields) {
			return null;
		}
		const field = allFields.filter((field) => {
			return field.id == id;
		});
		return field.length ? field[0] : null;
	};

	const name = getField(field)?.name;

	const nameStr = (
		<strong className="mailster-step-badge">{name ? name : field}</strong>
	);

	const currentField = allFields.filter((f) => f.id == field).pop();
	if (currentField && currentField.type == 'date') {
		if (!isNaN(parseFloat(value)) && isFinite(value)) {
			let info = '';
			let valueStr;
			if (value < 0) {
				info = sprintf(
					_n(
						'Decrease %s by %s day.',
						'Decrease %s by %s days.',
						parseFloat(value),
						'mailster'
					),
					'<field />',
					'<value />'
				);
				valueStr = (
					<strong className="mailster-step-badge">{escape(value * -1)}</strong>
				);
			} else if (value > 0) {
				info = sprintf(
					_n(
						'Increase %s by %s day.',
						'Increase %s by %s days.',
						parseFloat(value),
						'mailster'
					),
					'<field />',
					'<value />'
				);
				valueStr = (
					<strong className="mailster-step-badge">{escape(value)}</strong>
				);
			} else {
				info = sprintf(
					__('Set %s to the %s.', 'mailster'),
					'<field />',
					'<value />'
				);
				valueStr = (
					<strong className="mailster-step-badge">
						{__('current date', 'mailster')}
					</strong>
				);
			}

			return createInterpolateElement(info, {
				field: nameStr,
				value: valueStr,
			});
		}

		const date = new Date(value || new Date()).toLocaleDateString();
		if (date instanceof Date) {
			value = date;
		}
	}
	if (currentField?.type == 'checkbox') {
		if (value) {
			string = sprintf(__('Check %s.', 'mailster'), '<field />');
		} else {
			string = sprintf(__('Uncheck %s.', 'mailster'), '<field />');
		}

		return createInterpolateElement(string, { field: nameStr });
	}

	const valueStr = (
		<strong className="mailster-step-badge">{escape(value)}</strong>
	);

	if (value === false) {
		string = __('Field %s.', 'mailster');
	} else if (!value) {
		string = __('Remove field %s.', 'mailster');
	}
	const s = sprintf(
		string || __('Update field %s with value %s.', 'mailster'),
		'<field />',
		'<value />'
	);

	return createInterpolateElement(s, {
		field: nameStr,
		value: valueStr,
	});
}

export function formatOffset(offset) {
	if (!offset) {
		return <></>;
	}

	const now = +new Date();
	const dateMoment = moment(now + offset * 1000);

	const s = sprintf(
		offset < 0
			? __('but %s before the date.', 'mailster')
			: __('but %s after the date.', 'mailster'),
		'<offset />'
	);

	return createInterpolateElement(s, {
		offset: (
			<strong className="mailster-step-badge">
				{dateMoment.fromNow(true)}
			</strong>
		),
	});
}

export function formatPages(pages) {
	if (!pages || !pages.length) {
		return <>{__('No Pages defined.', 'mailster')}</>;
	}

	return pages.map((page, i) => {
		return (
			<strong key={i} className="mailster-step-badge">
				{page}
			</strong>
		);
	});
}

export function formatLinks(links) {
	if (!links || !links.length) {
		return <>{__('No Links defined.', 'mailster')}</>;
	}

	return links.map((link, i) => {
		return (
			<strong key={i} className="mailster-step-badge">
				{link}
			</strong>
		);
	});
}

export function whenEditorIsReady() {
	return new Promise((resolve) => {
		const unsubscribe = subscribe(() => {
			if (
				select('core/editor').isCleanNewPost() ||
				select('core/block-editor').getBlockCount() > 0
			) {
				// check if we have an iframe (6.3+) and store the window object
				const iframe = document.querySelector('iframe[name="editor-canvas"]');
				const winObj = iframe ? iframe.contentWindow : window;

				const resolver = () => {
					winObj.removeEventListener('load', resolver);
					unsubscribe();
					resolve(winObj);
				};
				// search for the root container which indicates the content is loaded
				if (winObj.document.querySelector('.is-root-container')) {
					resolver();
				} else {
					// document is not ready yet, so add an event listener
					winObj.addEventListener('load', resolver);
				}
			}
		});
	});
}

export function useWindow(callback) {
	const [win, setWindow] = useState();

	useEffect(() => {
		whenEditorIsReady().then((w) => {
			setWindow(w);
			callback && callback(w);
		});
	}, []);

	return win;
}

export function useDocument(callback) {
	const [doc, setDocument] = useState();

	useEffect(() => {
		whenEditorIsReady().then((w) => {
			setDocument(w.document);
			callback && callback(w.document);
		});
	}, []);

	return doc;
}

export function useBlockChange(callback) {
	let useBlockChangeBlockCount = null;
	whenEditorIsReady().then((w) => {
		subscribe(() => {
			const blocks = w.document.querySelectorAll('.wp-block');

			// Get new blocks client ids
			const newBlockList = blocks.length;

			if (newBlockList === useBlockChangeBlockCount) {
				return;
			}

			// Update current block list with the new blocks for further comparison
			useBlockChangeBlockCount = newBlockList;

			callback(useBlockChangeBlockCount);
		});
	});
}

export function useLocalStorage(key, initialValue) {
	key = 'mailster_' + key;
	// State to store our value
	// Pass initial state function to useState so logic is only executed once
	const [storedValue, setStoredValue] = useState(() => {
		if (typeof window === 'undefined') {
			return initialValue;
		}
		try {
			// Get from local storage by key
			const item = window.localStorage.getItem(key);
			// Parse stored json or if none return initialValue
			return item ? JSON.parse(item) : initialValue;
		} catch (error) {
			// If error also return initialValue
			console.error(error);
			return initialValue;
		}
	});
	// Return a wrapped version of useState's setter function that ...
	// ... persists the new value to localStorage.
	const setValue = (value) => {
		try {
			// Allow value to be a function so we have same API as useState
			const valueToStore =
				value instanceof Function ? value(storedValue) : value;
			// Save state
			setStoredValue(valueToStore);
			// Save to local storage
			if (typeof window !== 'undefined') {
				window.localStorage.setItem(key, JSON.stringify(valueToStore));
			}
		} catch (error) {
			// A more advanced implementation would handle the error case
			console.error(error);
		}
	};
	return [storedValue, setValue];
}

export function useSessionStorage(key, initialValue) {
	key = 'mailster_' + key;
	// State to store our value
	// Pass initial state function to useState so logic is only executed once
	const [storedValue, setStoredValue] = useState(() => {
		if (typeof window === 'undefined') {
			return initialValue;
		}
		try {
			// Get from local storage by key
			const item = window.sessionStorage.getItem(key);
			// Parse stored json or if none return initialValue
			return item ? JSON.parse(item) : initialValue;
		} catch (error) {
			// If error also return initialValue
			console.error(error);
			return initialValue;
		}
	});
	// Return a wrapped version of useState's setter function that ...
	// ... persists the new value to sessionStorage.
	const setValue = (value) => {
		try {
			// Allow value to be a function so we have same API as useState
			const valueToStore =
				value instanceof Function ? value(storedValue) : value;
			// Save state
			setStoredValue(valueToStore);
			// Save to local storage
			if (typeof window !== 'undefined') {
				window.sessionStorage.setItem(key, JSON.stringify(valueToStore));
			}
		} catch (error) {
			// A more advanced implementation would handle the error case
			console.error(error);
		}
	};
	return [storedValue, setValue];
}
export const usePostTypes = () => {
	const postTypes = useSelect((select) => {
		const { getPostTypes } = select('core');
		const excludedPostTypes = ['attachment', 'newsletter'];
		const filteredPostTypes = getPostTypes({ per_page: -1 })?.filter(
			({ viewable, slug }) => viewable && !excludedPostTypes.includes(slug)
		);
		return filteredPostTypes;
	}, []);
	const postTypesTaxonomiesMap = useMemo(() => {
		if (!postTypes?.length) return;
		return postTypes.reduce((accumulator, type) => {
			accumulator[type.slug] = type.taxonomies;
			return accumulator;
		}, {});
	}, [postTypes]);
	const postTypesSelectOptions = useMemo(
		() =>
			(postTypes || []).map(({ labels, slug }) => ({
				label: labels.singular_name,
				singular: labels.singular_name,
				plural: labels.name,
				value: slug,
			})),
		[postTypes]
	);
	return { postTypesTaxonomiesMap, postTypesSelectOptions };
};
export const useTaxonomies = (postType) => {
	const taxonomies = useSelect(
		(select) => {
			const { getTaxonomies } = select('core');
			const filteredTaxonomies = getTaxonomies({
				type: postType,
				per_page: -1,
				context: 'view',
			});
			return filteredTaxonomies;
		},
		[postType]
	);
	return taxonomies;
};
export const getEntitiesInfo = (entities) => {
	const mapping = entities?.reduce(
		(accumulator, entity) => {
			const { mapById, mapByName, names } = accumulator;
			mapById[entity.id] = entity;
			mapByName[entity.name] = entity;
			names.push(entity.name);
			return accumulator;
		},
		{ mapById: {}, mapByName: {}, names: [] }
	);
	return {
		entities,
		...mapping,
	};
};

export function useEventListener(eventType, callback, element = window) {
	const callbackRef = useRef(callback);

	useEffect(() => {
		callbackRef.current = callback;
	}, [callback]);

	useEffect(() => {
		if (element == null) return;
		const handler = (e) => callbackRef.current(e);
		element.addEventListener(eventType, handler);

		return () => element.removeEventListener(eventType, handler);
	}, [eventType, element]);
}

export function useBlockAttributes(clientId) {
	const { updateBlockAttributes } = useDispatch('core/block-editor');

	const attributes = useSelect(
		(select) => {
			const { getBlockAttributes } = select('core/block-editor');
			const _attributes = getBlockAttributes(clientId) || {};

			return _attributes;
		},
		[clientId]
	);

	const setAttributes = useCallback(
		(newAttributes) => {
			updateBlockAttributes(clientId, newAttributes);
		},
		[clientId]
	);

	return [attributes, setAttributes];
}

export function useEmailSteps() {
	const steps = searchBlocks('mailster/email');

	const { updateBlockAttributes } = useDispatch('core/block-editor');

	const attributes = useSelect(
		(select) => {
			const { getBlockAttributes } = select('core/block-editor');
			const _attributes = getBlockAttributes(clientId) || {};

			return _attributes;
		},
		[clientId]
	);

	const setAttributes = useCallback(
		(newAttributes) => {
			updateBlockAttributes(clientId, newAttributes);
		},
		[clientId]
	);

	return [attributes, setAttributes];
}

export function searchBlock(blockName, clientId, innerBlocks = true) {
	const blocks = searchBlocks(blockName, clientId, innerBlocks);

	if (blocks.length) {
		return blocks[0];
	}

	return false;
}

export function searchBlocks(pattern, clientId = null, innerBlocks = true) {
	const { getBlocks, getBlockRootClientId } = select('core/block-editor');
	const allBlocks = getBlocks(clientId);
	let matchingBlocks = [];

	function _s(blocks) {
		blocks.forEach((block) => {
			// Check if the block matches the pattern. This example checks the block's content.
			if (block.name && new RegExp(pattern, 'g').test(block.name)) {
				//store the root client ID as well
				block.rootClientId = getBlockRootClientId(block.clientId);
				matchingBlocks.push(block);
			}

			// If the block has inner blocks, search them as well
			if (innerBlocks && block.innerBlocks.length > 0) {
				_s(block.innerBlocks);
			}
		});
	}

	_s(allBlocks);
	return matchingBlocks;
}

export function clearData(selector, store) {
	dispatch(store).invalidateResolutionForStoreSelector(selector);
}

export function useInterval(callback, delay, instant = false) {
	const interval = useRef(null);
	const cb = useRef(callback);
	useEffect(() => {
		cb.current = callback;
	}, [callback]);
	useEffect(() => {
		const tick = () => cb.current();
		if (instant && cb.current) {
			tick();
		}
		if (typeof delay === 'number') {
			interval.current = window.setInterval(tick, delay);
			return () => window.clearInterval(interval.current);
		}
	}, [delay]);
	return interval;
}

export function useFocus(element) {
	const ref = useRef(element);
	// set to true if element is window
	const [isFocused, setIsFocused] = useState(element === window);
	const focus = useCallback(() => {
		setIsFocused(true);
	}, [isFocused]);
	const blur = useCallback(() => {
		setIsFocused(false);
	}, [isFocused]);

	useEffect(() => {
		const el = ref.current;

		el?.addEventListener('focus', focus);
		el?.addEventListener('blur', blur);

		return () => {
			el?.removeEventListener('focus', focus);
			el?.removeEventListener('blur', blur);
		};
	});

	return [ref, isFocused];
}

export function useSteps(dependencies) {
	const [stepBlocks, setStepBlocks] = useState([]);

	useEffect(() => {
		const blocks = searchBlocks(
			'^mailster-workflow/(conditions|action|email|delay|stop|jumper|notification)$'
		);
		if (stepBlocks !== blocks) {
			setStepBlocks(blocks);
		}
	}, [dependencies]);

	return stepBlocks;
}
