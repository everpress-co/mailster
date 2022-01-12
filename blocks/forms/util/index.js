/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

import { useEffect, useRef } from '@wordpress/element';

/**
 * Internal dependencies
 */

export function useUpdateEffect(callback, dependencies) {
	const firstRenderRef = useRef(true);

	useEffect(() => {
		if (firstRenderRef.current) {
			firstRenderRef.current = false;
			return;
		}
		return callback();
	}, dependencies);
}
