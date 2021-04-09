import { startStimulusApp } from '@symfony/stimulus-bridge'

// Registers Stimulus controllers from controllers.json and in the controllers/ directory
const stimulus = startStimulusApp(require.context(
    '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
    true,
    /\.(j|t)sx?$/
))

export default stimulus
