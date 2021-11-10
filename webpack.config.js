const defaultConfig = require('@wordpress/scripts/config/webpack.config');

const glob = require('glob');
const path = require('path');

const entry = glob.sync('./blocks/**/index.js').reduce((acc, path) => {
	const entry = path.replace('./blocks/', '').replace('/index.js', '');
	acc[entry] = path.replace('/index.js', '');
	return acc;
}, {});

entry['form-inspector'] = './blocks/form-inspector';

module.exports = {
	...defaultConfig,
	entry,
	output: {
		filename: './[name].js',
		path: path.resolve(__dirname) + '/build',
	},
};
