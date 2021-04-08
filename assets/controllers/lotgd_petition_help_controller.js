import Popover from 'stimulus-popover'
import jQuery from 'jquery'
import toastr from '../lib/toastr'

export default class extends Popover
{
    static classes = ['loading', 'disabled']
    static targets = ['button']

    async show (event)
    {
        event.preventDefault()

        this.buttonTarget.classList.add(this.loadingClass, this.disabledClass)

        let content = await this.fetch()

        this.buttonTarget.classList.remove(this.loadingClass, this.disabledClass)

        if (!content) return

        const fragment = document.createRange().createContextualFragment(content).firstElementChild
        jQuery(fragment).modal({
            closable: false,
            onApprove: (element) =>
            {
                const modal = element.parent().parent()
                const form = modal.find('form')
                element.addClass('loading disabled')
                form.addClass('loading')

                jQuery.ajax({
                    url: form.prop('action'),
                    method: form.prop('method'),
                    data: form.serialize()
                })
                .done(response =>
                {
                    modal.html(jQuery(response).children())
                    jQuery('.ui.lotgd.dropdown').dropdown()
                })
                .fail(() =>
                {
                    modal.find('.content').addClass('error')
                    toastr.fire({
                        'icon': 'error'
                    })
                })

                return false
            }
        }).modal('show')
        jQuery('.ui.lotgd.dropdown').dropdown()
    }
}
