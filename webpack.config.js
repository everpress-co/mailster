const defaultConfig = require('@wordpress/scripts/config/webpack.config');

const RemoveEmptyScriptsPlugin = require('webpack-remove-empty-scripts');
const SoundsPlugin = require('sounds-webpack-plugin');

const glob = require('glob');
const path = require('path');
const cp = require('child_process');

let myErrors = [];

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
				myErrors = stats.compilation.errors;
				this.play('warning');
				const message = myErrors
					.join('')
					.split('\n')[1]
					.replace(path.resolve(__dirname), '');

				var ls = cp.spawnSync(
					'osascript',
					[
						'-e',
						'display notification "' +
							message +
							'" with title "Error while building" subtitle "More text"',
					],
					{
						encoding: 'utf8',
					}
				);
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
		new SoundsPlugin(soundPluginOptions),
		new RemoveEmptyScriptsPlugin(),
	],
};
