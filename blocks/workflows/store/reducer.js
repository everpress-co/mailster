const DEFAULT_STATE = {
	triggers: null,
	numbers: null,
	actions: null,
	lists: [],
	tags: [],
	fields: [],
	campaigns: [],
	stats: [],
};

const reducer = (state = DEFAULT_STATE, action) => {
	switch (action.type) {
		case 'SET_TRIGGERS':
			return {
				...state,
				triggers: action.triggers,
			};

		case 'SET_NUMBERS':
			return {
				...state,
				numbers: action.numbers,
			};

		case 'CLEAR_NUMBERS':
			return {
				...state,
				numbers: DEFAULT_STATE.numbers,
			};

		case 'SET_ACTIONS':
			return {
				...state,
				actions: action.actions,
			};

		case 'SET_LISTS':
			return {
				...state,
				lists: action.lists,
			};

		case 'SET_TAGS':
			return {
				...state,
				tags: action.tags,
			};

		case 'SET_FIELDS':
			return {
				...state,
				fields: action.fields,
			};

		case 'SET_CAMPAIGNS':
			return {
				...state,
				campaigns: action.campaigns,
			};

		case 'SET_CAMPAIGN_STATS':
			let obj = {
				...state,
			};
			obj['stats'][action.campaign] = action.stats;
			return obj;

		case 'SET_FORMS':
			return {
				...state,
				forms: action.forms,
			};

		case 'FAIL_RESOLUTION':
			console.error(action);
			return state;

		default:
			return state;
	}
};

export default reducer;
