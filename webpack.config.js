const CleanWebpackPlugin = require('clean-webpack-plugin');
const ExtractTextPlugin = require("extract-text-webpack-plugin");
const merge = require('webpack-merge');
const MinifyPlugin = require("babel-minify-webpack-plugin");
const path = require('path');
const webpack = require('webpack');

const distDirectory = path.resolve(__dirname, 'web/profiles/chlovet/dist');
const extractLess = new ExtractTextPlugin({filename: "[name].css"});

const common = {
  plugins: [
//    new webpack.ProvidePlugin({
//      $: "jquery",
//      jQuery: "jquery",
//      'window.$': "jquery",
//      'window.jQuery': "jquery",
//    }),
    //new MinifyPlugin(),
    extractLess
  ],
  resolve: {
    extensions: [".ts", ".js", ".less"],
  },
  module: {
    rules: [{
      test: /\.tsx?$/,
      exclude: /node_modules/,
      use: [{
        loader: "babel-loader",
        options: {
          presets: ['env']
        }
      }, {
        loader: "ts-loader"
      }],
    },{
      test: /\.js$/,
      exclude: /node_modules|sites\/all/,
      use: [{
        loader: "babel-loader",
        options: {
          presets: ['env']
        }
      }]
    },{
      test: /\.less/,
      use: extractLess.extract({
        fallback: "style-loader",
        use: [{
          loader: "css-loader",
          options: {
            minimize: true
          }
        }, {
          loader: "less-loader"
        }]
      })
    },{
      test: /.(png|jpg|gif|ttf|otf|eot|svg|woff(2)?)(\?[a-z0-9]+)?$/,
      use: [{
        loader: 'file-loader',
        options: {
          name: 'asset/[name].[ext]',
          path: distDirectory,
          //publicPath: ''
        }
      }]
    }]
  }
};

const front = {
  entry: {
    'public': './resources/public.js',
    'style.public': './resources/less/public/style.less'
  },
  output: {
    filename: '[name].min.js',
    path: distDirectory
  },
  plugins: [
    // Only cleanup once, pending front building
    new CleanWebpackPlugin(['dist']),
  ],
  module: {
    rules: [{
      // Expose jQuery to everyone else, code that attempt to reach jQuery as
      // a global can't reach it otherwise.
      // By setting this only in the public JavaScript code we also ensure that
      // jQuery will not be loaded twice (one in public.min.js and the other one
      // in admin.min.js) - and that specific jQuery addons will all be on the
      // same global instance.
      // Counter-part for admin is using "jquery" dependency as an external
      // dependency, which will then not be included in the bundle.
      test: require.resolve('jquery'),
      use: [{
        loader: 'expose-loader',
        options: 'jQuery'
      },{
        loader: 'expose-loader',
        options: '$'
      }]
    },{
      // Rather ugly solution that forces every jquery* named package to inherit
      // from the imported $ or jQuery. Import loader does just append the right
      // $ = require("jquery") statement in one of matched packages.
      //   https://stackoverflow.com/a/31366600
      test:   /jquery\..*\.js/,
      loader: "imports-loader?$=jquery,jQuery=jquery,this=>window"
    }]
  }
};

const admin = {
  entry: {
    'admin': './resources/admin.js',
    'style.admin': './resources/less/admin/style.less',
  },
  output: {
    filename: '[name].min.js',
    path: distDirectory
  },
  externals: {
    jquery: 'jQuery'
  },
  module: {
    rules: [{
      // ULink is a legacy js script
      test:   /ulink\.js/,
      loader: "imports-loader?this=>window"
    }]
  }
};

module.exports = [merge(common, front), merge(common, admin)];
