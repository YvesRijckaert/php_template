const path = require(`path`),
  merge = require(`webpack-merge`),
  parts = require(`./webpack.parts`),
  webpack = require(`webpack`);

const ImageminPlugin = require(`imagemin-webpack-plugin`).default;
const ImageminJpegRecompress = require(`imagemin-jpeg-recompress`);

const PATHS = {
  src: path.join(__dirname, `src`),
  dist: path.join(__dirname, `dist`)
};

const commonConfig = {
  entry: [
    path.join(PATHS.src, `js/script.js`),
    path.join(PATHS.src, `css/style.css`),
  ],
  output: {
    path: PATHS.dist,
    filename: `js/script.js`,
  },
  module: {
    rules: [
      {
        test: /\.(js)$/,
        exclude: /node_modules/,
        loader: [`babel-loader`, `eslint-loader`]
      },
      {
        test: /\.(jpe?g|png|gif|webp|svg)$/,
        use: [
          {
            loader: `file-loader`,
            options: {
              limit: 1000,
              context: `./src`,
              name: `[path][name].[ext]`,
            },
          }, {
            loader: `image-webpack-loader`,
            options: {
              mozjpeg: {
                progressive: true,
                quality: 65,
              },
              optipng: {
                enabled: false,
              },
              pngquant: {
                quality: `65-90`,
                speed: 4,
              },
              gifsicle: {
                interlaced: false,
              },
              webp: {
                quality: 75,
              },
            },
          },
        ],
      }, 
    ]
  },
  plugins: [
    new webpack.ProvidePlugin({
      Promise: `es6-promise`,
      fetch: `imports-loader?this=>global!exports-loader?global.fetch!whatwg-fetch`
    })
  ]
};

const productionConfig = merge([
  parts.extractCSS(),
  {
    plugins: [
      new ImageminPlugin({
        test: /\.(jpe?g)$/i,
        plugins: [
          ImageminJpegRecompress({}),
        ],
      })
    ],
  },
]);

const developmentConfig = merge([
  {
    devServer: {
      overlay: true,
      contentBase: PATHS.src
    },
  },
  parts.loadCSS(),
]);

module.exports = env => {
  if (process.env.NODE_ENV === `production`) {
    console.log(`building production`);
    return merge(commonConfig, productionConfig);
  }
  return merge(commonConfig, developmentConfig);
};
