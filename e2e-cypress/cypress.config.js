const { defineConfig } = require("cypress");

module.exports = defineConfig({
  e2e: {
    setupNodeEvents(on, config) {
      on('before:browser:launch', (browser = {}, launchOptions) => {
        // Set the desired screen resolution for headless mode
        const width = 1920;
        const height = 1080;

        // Apply window-size arguments based on browser
        if (browser.isHeadless) {
          if (browser.name === 'electron') {
            launchOptions.preferences.width = 1920;
            launchOptions.preferences.height = 1080;
          }
          if (browser.name === 'chrome') {
            launchOptions.args.push(`--window-size=${width},${height}`);
          }
          if (browser.name === 'firefox') {
            launchOptions.args.push(`--width=${width}`);
            launchOptions.args.push(`--height=${height}`);
          }
        }

        return launchOptions;
      });
    },
    viewportWidth: 1920,
    viewportHeight: 1080,
    video: true,
  },
});