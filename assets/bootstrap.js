import { startStimulusApp } from '@symfony/stimulus-bridge'

// Import TailwindCSS Components
import { Tabs, Modal } from 'tailwindcss-stimulus-components'

//-- Components
import Notification from 'stimulus-notification'

// Registers Stimulus controllers from controllers.json and in the stimulus/controllers/ directory
export const app = startStimulusApp(require.context(
    '@symfony/stimulus-bridge/lazy-controller-loader!./stimulus/controllers',
    true,
    /\.[jt]sx?$/
));

// Register TailwindCSS Components
app.register('tabs', Tabs)
app.register('modal', Modal)
app.register('notification', Notification)
