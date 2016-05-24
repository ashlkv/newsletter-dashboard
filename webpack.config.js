var path = require('path');
var webpack = require('webpack');

module.exports = {
    devtool: 'eval',
    entry: [
        'webpack-dev-server/client?http://localhost:3000',
        'webpack/hot/only-dev-server',
        './src/index'
    ],
    output: {
        path: path.join(__dirname, 'dist'),
        filename: 'app.js',
        publicPath: '/build/'
    },
    plugins: [
        new webpack.HotModuleReplacementPlugin()
    ],
    module: {
        loaders: [
            {
                test: /\.js$/,
                loaders: ['react-hot', 'babel'],
                exclude: /(node_modules)/,
                include: path.join(__dirname, 'src')
            },
            {
                test: /\.css$/,
                loader: 'style!css'
            },
            {
                test: /\.sass$/,
                loaders: ["style", "css", "sass"]
            },
            {
                test: /\.svg$/,
                loader: 'url?limit=100000&mimetype=image/svg+xml'
            },
            {test: /\.eot(\?v=\d+\.\d+\.\d+)?$/, loader: "file"},
            {test: /\.(woff|woff2)$/, loader: "url?prefix=font/&limit=5000"},
            {test: /\.ttf(\?v=\d+\.\d+\.\d+)?$/, loader: "url?limit=10000&mimetype=application/octet-stream"}
        ]
    },
    resolve: {
        alias: {
            moment: path.join(__dirname, "node_modules/moment/moment.js")
        }
    }
};
