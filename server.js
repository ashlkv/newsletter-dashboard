require('dotenv').config({silent: true});

var webpack = require('webpack');
var WebpackDevServer = require('webpack-dev-server');
var config = require('./webpack.dev');

new WebpackDevServer(webpack(config), {
    publicPath: config.output.publicPath,
    hot: true,
    historyApiFallback: true,
    proxy: [{
        path: /\/backend(.*)/,
        target: process.env.PROXY_TARGET_BACKEND
    }]
}).listen(3000, 'localhost', function(err, result) {
        if (err) {
            return console.log(err);
        }

        console.log('Listening at http://localhost:3000/');
    });
