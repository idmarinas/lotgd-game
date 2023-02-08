import { Modal } from "tailwindcss-stimulus-components"

//-- Mixins
import { useLoading } from '../mixins'

export default class extends Modal
{
    remoteContent = ''

    static targets = ['activator']
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

        content = await this.fetch(this.queryParameters(event.params))

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

    queryParameters(params)
    {
        let url = this.hasUrlValue ? this.urlValue : null
        let query = url.includes('?') ? '&' : '?'

        let keys = Object.keys(params)

        if (keys.length > 0)
        {
            keys.forEach(item =>
            {
                if (params[item] !== '')
                {
                    query =  `${query}${item}=${params[item]}`
                }
            })

            return `${url}${query}`
        }

        return url
    }

    async fetch (fetchUrl, options = {})
    {
        let url = this.hasUrlValue ? this.urlValue : null

        if (fetchUrl !== undefined && fetchUrl !== null && fetchUrl !== '')
        {
            url = fetchUrl
        }

        if ( ! this.remoteContent)
        {
            if (url === undefined || url === null || url === '')
            {
                console.error('[stimulus-remote-modal] You need to pass an url to fetch the modal content.')

                return
            }

            const response = await fetch(url, options)
            this.remoteContent = await response.text()
        }

        return this.remoteContent
    }
}
