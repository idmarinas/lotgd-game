import { Controller } from '@hotwired/stimulus';

export default class extends Controller
{
    loading = false

    static values = { url: String }

    connect ()
    {
        setInterval(() => this.statusCheck(), 15000)
    }

    async statusCheck ()
    {
        if ( ! this.hasUrlValue || this.loading === true)
        {
            this.loading = false

            return
        }

        this.loading = true

        const response = await fetch(this.urlValue)
        const content = await response.text()

        this.loading = false

        this.element.innerHTML = ''

        if ( ! content || content === '')
        {
            return;
        }

        this.element.appendChild(document.createRange().createContextualFragment(content))
    }
}
