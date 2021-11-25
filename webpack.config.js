const defaultConfig = require('@wordpress/scripts/config/webpack.config');

const RemoveEmptyScriptsPlugin = require('webpack-remove-empty-scripts');
const SoundsPlugin = require('sounds-webpack-plugin');

const glob = require('glob');
const path = require('path');

const entry = glob.sync('./blocks/**/index.js').reduce((acc, path) => {
	const entry = path.replace('./blocks/', '').replace('/index.js', '');
	acc[entry] = path.replace('/index.js', '');
	return acc;
}, {});

entry['form-inspector'] = './blocks/form-inspector';

const soundPluginOptions = {
	sounds: {
		warning: '/System/Library/Sounds/Basso.aiff',
	},
	notifications: {
		done(stats) {
			if (stats.hasErrors()) {
				this.play('warning');
			}
		},
	},
};

module.exports = {
	...defaultConfig,
	entry,
	output: {
		filename: './[name].js',
		path: path.resolve(__dirname) + '/build',
	},
	module: {
		...defaultConfig.module,
		rules: [
			...defaultConfig.module.rules,
			// Add additional rules as needed.
		],
	},
	plugins: [
		...defaultConfig.plugins,
		new RemoveEmptyScriptsPlugin(),
		new SoundsPlugin(soundPluginOptions),
	],
};
