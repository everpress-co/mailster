const defaultConfig = require('@wordpress/scripts/config/webpack.config');

const RemoveEmptyScriptsPlugin = require('webpack-remove-empty-scripts');
const SoundsPlugin = require('sounds-webpack-plugin');

const glob = require('glob');
const path = require('path');
const cp = require('child_process');

const entry = glob.sync('./blocks/**/index.js').reduce((acc, p) => {
	const entry = p.replace('./blocks/', '').replace('/index.js', '');
	acc[entry] = p.replace('/index.js', '');
	return acc;
}, {});

const publicentry = glob.sync('./blocks/**/public.js').reduce((acc, p) => {
	const entry = p.replace('./blocks/', '').replace('/public.js', '');
	acc[entry + '/public'] = p;
	return acc;
}, {});

console.warn(publicentry);

const soundPluginOptions = {
	sounds: {
		warning: '/System/Library/Sounds/Basso.aiff',
	},
	notifications: {
		done(stats) {
			if (stats.hasErrors()) {
				this.play('warning');
				let message = stats.compilation.errors
					.join('')
					.split('\n')
					.slice(-1)
					.join('');

				if (message) {
					message = message.replace(path.resolve(__dirname), '');
				}

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
			if (stats.hasWarnings()) {
				this.play('warning');

				let message = stats.compilation.warnings
					.join('')
					.split('\n')
					.slice(-1)
					.join('');

				if (message) {
					message = message.replace(path.resolve(__dirname), '');
				}

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

	entry: { ...entry, ...publicentry },
	output: {
		filename: '[name]/index.js',
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
