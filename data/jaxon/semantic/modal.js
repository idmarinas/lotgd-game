/* global jaxon $ */
/* eslint-disable no-new-func */

jaxon.dialogs.semantic = {
    modal: '',
    id: 0,
    modalId: ''
}

jaxon.command.handler.register('semantic.show', function (args)
{
    let modalId = jaxon.dialogs.semantic.id
    if (args.data.id)
    {
        modalId = args.data.id
    }
    jaxon.dialogs.semantic.modalId = modalId

    const options = $.extend({}, args.data.options, {
        onHidden ()
        {
            $(`#modal-${modalId}`).remove()
        }
    })
    const funcs = ['onShow', 'onVisible', 'onHide', 'onApprove', 'onDeny']

    for (const funcId in funcs)
    {
        const func = funcs[funcId]

        if (options[func])
        {
            options[func] = new Function('element', options[func])
        }
    }

    //-- Create new modal
    jaxon.dialogs.semantic.id++

    //-- Remove old modal if exist
    $('#modal-' + modalId).remove()

    let modal = '<div id="modal-' + modalId + '" class="ui ' + (options.classModal || '') + ' modal"><i class="close icon"></i>'

    if (args.data.title)
    {
        modal = modal + '<div class="ui header">' + args.data.title + '</div>'
    }

    const classContent = (args.data.isScrollable ? 'scrolling ' : '') + ' content'
    modal = modal + '<div class="' + classContent + '">' + args.data.content + '</div>'

    if (args.data.buttons.length)
    {
        modal = modal + '<div class="actions">'

        for (const buttonId in args.data.buttons)
        {
            const button = args.data.buttons[buttonId]

            modal = modal + '<a class="' + button.class + '">' + button.title + '</a>'
        }
        modal = modal + '</div>'
    }

    modal = modal + '</div>'

    jaxon.dialogs.semantic.modal = modal

    // Open modal
    $(jaxon.dialogs.semantic.modal).modal(options).modal('show')
})

jaxon.command.handler.register('semantic.hide', function (_args)
{
    if (jaxon.dialogs.semantic.modal != null)
    {
        // Close an destroy modal
        $(jaxon.dialogs.semantic.modal).modal('hide')
        $('#modal-' + jaxon.dialogs.semantic.id).remove()
        $('#modal-' + jaxon.dialogs.semantic.modalId).remove()
        delete (jaxon.dialogs.semantic.modal)
    }
})
