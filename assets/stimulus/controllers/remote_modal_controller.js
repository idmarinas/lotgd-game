import { Modal } from "tailwindcss-stimulus-components"

//-- Helpers
import { useLoading } from '../helpers'

export default class extends Modal
{
    remoteContent = ''

    static targets = ['activator', 'loading']
    static values = { url: String, reloadData: Boolean }

    connect ()
    {
        super.connect()

        useLoading(this)
    }

    async open (event)
    {
        event.preventDefault()

        this.startLoading()

        let content = null

        content = await this.fetch()

        this.stopLoading()

        if ( ! content) return

        this.containerTarget.appendChild(document.createRange().createContextualFragment(content))

        if ( ! this.containerTarget.style.zIndex || this.containerTarget.style.zIndex < 9999)
        {
            this.containerTarget.style.zIndex = 9999
        }

        super.open(event)
    }

    close (event)
    {
        if (event && this.preventDefaultActionClosing)
        {
            event.preventDefault()
        }

        //-- By default if not defined reset content
        if ( ! this.hasReloadDataValue || this.reloadDataValue === true)
        {
            //-- Reset content
            //-- This force to reload data other time
            this.containerTarget.innerHTML = ''
            this.remoteContent = ''
        }

        super.close(event)
    }

    async fetch ()
    {
        if ( ! this.remoteContent)
        {
            if ( ! this.hasUrlValue)
            {
                console.error('[stimulus-remote-modal] You need to pass an url to fetch the modal content.')

                return
            }

            const response = await fetch(this.urlValue)
            this.remoteContent = await response.text()
        }

        return this.remoteContent
    }
}
