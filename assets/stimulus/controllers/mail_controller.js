import RemoteModal from './remote_modal_controller'

//-- Mixins
import { useLoadingBarTop, useButtonLoading, useSyncIcon } from '../mixins'

export default class extends RemoteModal
{
    static values = {
        urlStatus: String,
        urlWrite: String,
        urlReply: String,
        urlRead: String,
        urlUnread: String,
        urlDelete: String,
        urlDeleteBulk: String,
        transCheckAllInactive: String,
        transCheckAllActive: String
    }

    static targets = ['buttons']

    connect ()
    {
        super.connect()

        useLoadingBarTop(this)
        useButtonLoading(this)
        useSyncIcon(this, 'top', 'left', 'text-lotgd-green-600')

        setInterval(() => this.statusUpdate(), 15000)
    }

    buttonsTargetConnected(target)
    {
        document.addEventListener('limit_characters.is_over_characters', e =>
        {
            const btns = target.getElementsByTagName('button')
            for (let btn of btns)
            {
                btn.disabled = e.detail.isOverCharacters
            }
        })
    }

    async inbox (event)
    {
        const params = event.params

        let url = this.urlValue
        let content = null

        if (Object.keys(params).length > 0)
        {
            this.startLoadingBarTop()
            url = `${url}&sort_order=${params.sortOrder}&sort_direction=${params.sortDirection}`
        }
        else
        {
            this.startButtonLoading(event.target)
        }

        this.remoteContent = ''
        content = await this.fetch(url)

        if (Object.keys(params).length > 0)
        {
            this.stopLoadingBarTop()
        }

        if ( ! content) return

        this.containerTarget.innerHTML = ''

        this.containerTarget.appendChild(document.createRange().createContextualFragment(content))
    }

    async read (event)
    {
        event.preventDefault()

        const params = event.params

        let url = this.urlReadValue
        let content = null

        if (Object.keys(params).length > 0)
        {

            url = `${url}&message_id=${params.id}`
        }

        if (params.isButton === true)
        {
            this.startButtonLoading(event.target)
        }
        else
        {
            this.startLoadingBarTop()
        }

        this.remoteContent = ''
        content = await this.fetch(url)

        if (params.isButton === false || params.isButton === undefined)
        {
            this.stopLoadingBarTop()
        }

        if ( ! content) return

        this.containerTarget.innerHTML = ''

        this.containerTarget.appendChild(document.createRange().createContextualFragment(content))

    }

    async unread (event)
    {
        const params = event.params

        this.startButtonLoading(event.target)

        let content = ''

        this.remoteContent = ''
        content = await this.fetch(`${this.urlUnreadValue}&message_id=${params.id}`)

        this.stopButtonLoading(event.target)

        if ( ! content) return

        this.containerTarget.innerHTML = ''

        this.containerTarget.appendChild(document.createRange().createContextualFragment(content))
    }

    async delete (event)
    {
        this.startButtonLoading(event.target)

        const id = event.params.id

        this.remoteContent = ''
        let content = await this.fetch(`${this.urlDeleteValue}&id=${id}`)

        this.stopButtonLoading(event.target)

        if ( ! content) return

        this.containerTarget.innerHTML = ''

        this.containerTarget.appendChild(document.createRange().createContextualFragment(content))
    }

    async deleteBulk()
    {
        const btns = this.containerTarget.getElementsByTagName('button')

        for (let btn of btns)
        {
            this.startButtonLoading(btn)
        }

        const form = this.containerTarget.getElementsByTagName('form')[0]

        this.remoteContent = ''
        const content = await this.fetch(this.urlDeleteBulkValue, {
            method: 'POST',
            body: new FormData(form)
        })

        if ( ! content) return

        this.containerTarget.innerHTML = ''

        this.containerTarget.appendChild(document.createRange().createContextualFragment(content))
    }

    async statusUpdate ()
    {
        if ( ! this.hasUrlStatusValue || this.loading === true)
        {
            return
        }

        this.startSyncIcon(this.activatorTarget)

        const response = await fetch(this.urlStatusValue)
        const content = await response.text()

        this.stopSyncIcon(this.activatorTarget)

        this.activatorTarget.innerHTML = ''
        this.activatorTarget.appendChild(document.createRange().createContextualFragment(content))
    }

    async write(event)
    {
        this.#send(event, this.urlWriteValue)
    }

    async reply(event)
    {
        this.#send(event, `${this.urlReplyValue}&message_id=${event.params.id}`)
    }

    checkAll (event)
    {
        const checkAll = event.target.innerHTML === this.transCheckAllInactiveValue

        this.startButtonLoading(event.target)

        document.getElementsByName('msg[]').forEach(field =>
        {
            field.checked = checkAll
        })

        this.stopButtonLoading(event.target)

        event.target.innerHTML = checkAll ? this.transCheckAllActiveValue : this.transCheckAllInactiveValue
    }

    async #send(event, url)
    {
        this.startButtonLoading(event.target)

        this.remoteContent = ''

        const content = await this.fetch(url)

        this.stopButtonLoading(event.target)

        if ( ! content) return

        this.containerTarget.innerHTML = ''

        this.containerTarget.appendChild(document.createRange().createContextualFragment(content))
    }
}
