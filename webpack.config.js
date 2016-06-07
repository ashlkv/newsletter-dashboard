var path = require('path');
var webpack = require('webpack');
var precss = require('precss');
var autoprefixer = require('autoprefixer');
var HtmlWebpackPlugin = require('html-webpack-plugin');

module.exports = function(options) {
    options = options || {};
    return {
        // Using devtool: eval in production build prevents UglifyJsPlugin from compressing the code.
        // Set devtool option to 'source-map' for production if source map is required.
        devtool: options.production ? '' : 'eval',
        entry: options.production ? './src/index' : [
            'webpack-dev-server/client?http://localhost:3000',
            'webpack/hot/only-dev-server',
            './src/index'
        ],
        output: {
            path: options.production ? path.join(__dirname, 'dist') : path.join(__dirname, 'build'),
            publicPath: options.production ? '' : 'http://localhost:3000/',
            filename: options.production ? 'app.[hash].js' : 'app.js'
        },
        node: {
            fs: "empty"
        },
        plugins: options.production ? [
            new webpack.optimize.UglifyJsPlugin({
                compress: {warnings: false}
            }),
            // React production build: smaller than dev
            new webpack.DefinePlugin({
                'process.env': {
                    'NODE_ENV': JSON.stringify('production')
                }
            }),
            new HtmlWebpackPlugin({
                template: './tmpl.html',
                filename: 'index.html',
                production: true
            }),
            // Include only necessary locales: decreases momentjs size four times.
            new webpack.ContextReplacementPlugin(/moment[\\\/]locale$/, /^\.\/(ru)$/)
        ] : [
            new webpack.HotModuleReplacementPlugin(),
            new webpack.DefinePlugin({
                'process.env': {
                    'NODE_ENV': JSON.stringify('dev')
                }
            }),
            new HtmlWebpackPlugin({
                template: './tmpl.html',
                filename: 'index.html'
            })
        ],
        module: {
            loaders: [
                {
                    test: /\.js$/,
                    loaders: options.production ? ['babel?presets[]=es2015,presets[]=stage-0,presets[]=react'] : ['react-hot', 'babel'],
                    exclude: /(node_modules)/,
                    include: path.join(__dirname, 'src')
                },
                {
                    test: /\.css$/,
                    loader: 'style!css!postcss'
                },
                {
                    test: /\.sass$/,
                    loaders: ["style", "css", "sass"]
                },
                {
                    test: /\.svg$/,
                    loader: 'url?limit=100000&mimetype=image/svg+xml'
                },
                {
                    test: /\.png$/,
                    loader: 'url-loader?mimetype=image/png'
                },
                {
                    test: /\.json$/,
                    loader: 'json-loader'
                },
                {test: /\.eot(\?v=\d+\.\d+\.\d+)?$/, loader: "file"},
                {test: /\.(woff|woff2)$/, loader: "url?prefix=font/&limit=5000"},
                {test: /\.ttf(\?v=\d+\.\d+\.\d+)?$/, loader: "url?limit=10000&mimetype=application/octet-stream"}
            ]
        },

        postcss: function() {
            return [precss, autoprefixer];
        },
        resolve: {
            alias: {
                moment: path.join(__dirname, "node_modules/moment/moment.js")
            }
        }
    }
};
