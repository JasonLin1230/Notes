const path = require("path");
const webpack = require("webpack");
const HTMLPlugin = require("html-webpack-plugin");
const ExtractPlugin = require("extract-text-webpack-plugin");

const isDev = process.env.NODE_ENV === "development";

const config = {
  target: "web",
  entry: path.join(__dirname, "src/index.js"),
  output: {
    filename: "bundle.js",
    path: path.join(__dirname, "dist")
  },
  module: {
    rules: [
      {
        test: /.vue$/,
        loader: "vue-loader"
      },
      {
        test: /.jsx$/,
        loader: "babel-loader"
      },
      {
        test: /\.(gif|jpg|jpeg|png|svg)$/,
        use: [
          {
            loader: "url-loader",
            options: {
              limit: 1024,
              name: "[name].[ext]"
            }
          }
        ]
      }
    ]
  },
  plugins: [
    new webpack.DefinePlugin({
      "process.env": {
        NODE_ENV: isDev ? '"development"' : '"prodution"'
      }
    }),
    new HTMLPlugin()
  ]
};

if (isDev) {
  config.module.rules.push({
    test: /\.styl/,
    use: [
      "style-loader",
      "css-loader",
      {
        loader: "postcss-loader",
        options: {
          sourceMap: true
        }
      },
      "stylus-loader"
    ]
  });
  config.devtool = "#cheap-module-eval-source-map";
  config.devServer = {
    port: 8000,
    host: "0.0.0.0",
    overlay: {
      errors: true
    },
    //open: true
    hot: true
  };
  config.plugins.push(
    new webpack.HotModuleReplacementPlugin(),
    new webpack.NoEmitOnErrorsPlugin()
  );
} else {
  // 生产环境
  // 类库文件与业务逻辑文件分离
  config.entry = {
    app: path.join(__dirname, "src/index.js"),
    vendor: ["vue"]
  };
  // js文件名chunkhash
  config.output.filename = "[name].[chunkhash:8].js";
  // css单独抽离
  config.module.rules.push({
    test: /\.styl/,
    use: ExtractPlugin.extract({
      fallback: "style-loader",
      use: [
        "css-loader",
        {
          loader: "postcss-loader",
          options: {
            sourceMap: true
          }
        },
        "stylus-loader"
      ]
    })
  });
  config.plugins.push(
    new ExtractPlugin("styles.[contentHash:8].css"),
    // 类库代码抽离
    new webpack.optimize.CommonsChunkPlugin({
      name: "vendor"
    }),
    // webpack配置抽离
    new webpack.optimize.CommonsChunkPlugin({
      name: "runtime"
    })
  );
}

module.exports = config;
