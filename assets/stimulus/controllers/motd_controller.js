import RemoteModal from './remote_modal_controller'
import Swal from '../../external/swal'

//-- Mixins
import { useLoadingBarTop, useButtonLoading, useSyncIcon } from '../mixins'

export default class extends RemoteModal
{
    static values = { urlItem: String, urlPoll: String, urlDelete: String, urlVote: String }

    connect ()
    {
        super.connect()

        useButtonLoading(this)
    }

    async list (event)
    {
        event.preventDefault()

        this.startButtonLoading(event.target)

        const params = event.params

        let url = this.urlValue
        let content = null

        if (Object.keys(params).length > 0)
        {
            url = `${url}&page=${params.page}`
        }

        this.remoteContent = ''

        content = await this.fetch(url)

        this.stopButtonLoading(event.target)

        if ( ! content) return

        this.containerTarget.innerHTML = ''

        this.containerTarget.appendChild(document.createRange().createContextualFragment(content))
    }

    async delete (event)
    {
        event.preventDefault()

        this.startButtonLoading(event.target)

        const params = event.params
        let url = this.urlDeleteValue

        if (Object.keys(params).length > 0)
        {
            url = `${url}&id=${params.itemId}`
        }

        const _self = this
        const options = {
            icon: 'question',
            showCancelButton: true,
            title: params.title,
            text: params.text,
            preConfirm ()
            {
                _self.remoteContent = ''
                return _self.fetch(url)
            }
        }

        Swal.configChange(options)

        Swal.get().fire(options).then(response =>
        {
            _self.stopButtonLoading(event.target)

            if (response.isConfirmed !== true || ! response.value) return

            _self.containerTarget.innerHTML = ''

            _self.containerTarget.appendChild(document.createRange().createContextualFragment(response.value))
        })

        Swal.configRestart()
    }

    async item (event)
    {
        event.preventDefault()

        await this.#addEditPrivate(this.urlItemValue, event)
    }

    async poll (event)
    {
        event.preventDefault()

        await this.#addEditPrivate(this.urlPollValue, event)
    }

    async vote (event)
    {
        event.preventDefault()

        this.startButtonLoading(event.target)

        const params = event.params
        let url = `${this.urlVoteValue}&item_id=${params.itemId}&option_id=${params.optionId}`

        let content = null
        this.remoteContent = ''

        content = await this.fetch(url)

        this.stopButtonLoading(event.target)

        if ( ! content) return

        this.containerTarget.innerHTML = ''

        this.containerTarget.appendChild(document.createRange().createContextualFragment(content))
    }

    async #addEditPrivate (url, event)
    {
        this.startButtonLoading(event.target)

        const params = event.params

        if (Object.keys(params).length > 0)
        {
            url = `${url}&id=${params.itemId}`
        }

        let content = null
        this.remoteContent = ''

        content = await this.fetch(url)

        this.stopButtonLoading(event.target)

        if ( ! content) return

        this.containerTarget.innerHTML = ''

        this.containerTarget.appendChild(document.createRange().createContextualFragment(content))
    }
}
