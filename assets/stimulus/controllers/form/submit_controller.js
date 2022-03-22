import { Controller } from '@hotwired/stimulus'

//-- Mixins
import { useLoading } from '../../mixins'

export default class extends Controller
{
    static targets = ['activator', 'loading']
    static values = { url: String }

    connect()
    {
        useLoading(this)
    }

    async send (event)
    {
        // Cancels the event's default behavior (e.g. following a link or submitting a form)
        event.preventDefault()

        if ( ! this.hasUrlValue)
        {
            return;
        }

        //-- Block activator button
        this.startLoading()

        const form = this.element.getElementsByTagName('form')[0]

        const content = await fetch(this.urlValue, {
            method: 'POST',
            body: new FormData(form)
        })

        this.element.innerHTML = await content.text()
    }
}
