module.exports = {
  plugins: [
    require(`postcss-reporter`)({clearReportedMessages: true}),
    require(`postcss-import`),
    require(`postcss-cssnext`),
  ]
};
