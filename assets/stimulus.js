import { startStimulusApp } from '@symfony/stimulus-bridge'
import TextareaAutogrow from 'stimulus-textarea-autogrow'
import CharacterCounter from 'stimulus-character-counter'

// Registers Stimulus controllers from controllers.json and in the controllers/ directory
const stimulus = startStimulusApp(require.context(
    '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
    true,
    /\.(j|t)sx?$/
))

stimulus.register('character-counter', CharacterCounter)
stimulus.register('textarea-autogrow', TextareaAutogrow)

export default stimulus
