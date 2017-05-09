/**
 * Create lotgd.js
 */
var
	//-- Dependencies
	webpack = require('webpack'),
	ora = require('ora'),

	//-- Configuration
	configTasks = require('../../config/tasks'),

	log = configTasks.log,
	isProduction = configTasks.isProduction()
;

if (isProduction)
{
	var webpackConfig = require('../../config/webpack.prod.conf');
	var spinner = ora('building LOTGD JS App for production...');
}
else
{
	var webpackConfig = require('../../config/webpack.dev.conf');
	var spinner = ora('building LOTGD JS App for development...');
}

module.exports = function(callback)
{
	spinner.start();
	return webpack(webpackConfig, function (err, stats) {
		spinner.stop()
		if (err) throw err
		process.stdout.write(stats.toString({
			colors: true,
			modules: false,
			children: false,
			chunks: false,
			chunkModules: false
		}) + '\n')
	});
}
