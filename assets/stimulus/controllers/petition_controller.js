import RemoteModal from './remote_modal_controller'

//-- Mixins
import { useLoadingBarTop, useButtonLoading } from '../mixins'

export default class extends RemoteModal
{
    static values = { urlFaq: String, urlPrimer: String }

    connect()
    {
        super.connect()

        useLoadingBarTop(this)
        useButtonLoading(this)
    }

    async faq(event)
    {
        event.preventDefault()

        this.#faqPrivate(this.urlFaqValue)
    }

    async faq1(event)
    {
        event.preventDefault()

        await this.#faqPrivate(`${this.urlFaqValue}&faq=1`)
    }

    async faq2(event)
    {
        event.preventDefault()

        await this.#faqPrivate(`${this.urlFaqValue}&faq=2`)
    }

    async faq3(event)
    {
        event.preventDefault()

        await this.#faqPrivate(`${this.urlFaqValue}&faq=3`)
    }

    async primer(event)
    {
        event.preventDefault()

        await this.#faqPrivate(this.urlPrimerValue)
    }

    async return(event)
    {
        this.startButtonLoading(event.target)

        let content = null

        this.remoteContent = ''

        content = await this.fetch(this.urlFaqValue)

        this.stopButtonLoading(event.target)

        if ( ! content) return

        this.containerTarget.innerHTML = ''

        this.containerTarget.appendChild(document.createRange().createContextualFragment(content))
    }

    async #faqPrivate(url)
    {
        this.startLoadingBarTop()

        let content = null

        this.remoteContent = ''

        content = await this.fetch(url)

        this.stopLoadingBarTop()

        if ( ! content) return

        this.containerTarget.innerHTML = ''

        this.containerTarget.appendChild(document.createRange().createContextualFragment(content))
    }
}
