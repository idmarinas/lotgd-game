import { Modal } from "tailwindcss-stimulus-components"

export default class extends Modal
{
    remoteContent = ''

    static targets = ['container', 'activator', 'loading']
    static values = { url: String, reloadData: Boolean }

    async open (event)
    {
        event.preventDefault()

        //-- Disable button
        this.activatorTarget.disabled = true

        //-- Show loading
        if (this.hasLoadingTarget) this.loadingTarget.classList.remove('hidden')

        let content = null

        content = await this.fetch()

        //-- Enable button
        this.activatorTarget.disabled = false

        //-- Hidde loading
        if (this.hasLoadingTarget) this.loadingTarget.classList.add('hidden')

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
