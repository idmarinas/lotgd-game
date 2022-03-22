import { Controller } from '@hotwired/stimulus'

export default class extends Controller
{
    collectionHolder = null
    addOptButton = document.createElement('button')
    removeOptButton = null
    prototype = null

    connect ()
    {
        this.collectionHolder = this.element
        this.addOptButton.type = 'button'
        this.addOptButton.classList.add('w-auto')
        this.addOptButton.classList.add('input-green', 'text-xs')

        this.removeOptButton = this.addOptButton.cloneNode()
        this.removeOptButton.classList.remove('input-green')
        this.removeOptButton.classList.add('input-red', 'float-left')

        this.addOptButton.innerHTML = '<i class="fas fa-plus"></i>'
        this.removeOptButton.innerHTML = '<i class="fas fa-minus"></i>'

        this.addOptButton.addEventListener('click', () => { this.addPollOpt() })

        for (let child of this.collectionHolder.childNodes)
        {
            this.pollOptDeleteLink(child)
        }

        this.collectionHolder.prepend(this.addOptButton)

        this.collectionHolder.dataset.index = this.collectionHolder.getElementsByTagName('input').length

        this.prototype = this.collectionHolder.dataset.prototype
    }

    addPollOpt ()
    {
        // get the new index
        const index = this.collectionHolder.dataset.index

        let newForm = this.prototype
        // You need this only if you didn't set 'label' => false in your tags field in TaskType
        // Replace '__name__label__' in the prototype's HTML to
        // instead be a number based on how many items we have
        // newForm = newForm.replace(/__name__label__/g, index);

        // Replace '__name__' in the prototype's HTML to
        // instead be a number based on how many items we have
        newForm = document.createRange().createContextualFragment(newForm.replace(/__name__/g, index)).firstChild

        // increase the index with one for the next item
        this.collectionHolder.dataset.index = index + 1

        newForm = this.pollOptDeleteLink(newForm)
        this.collectionHolder.append(newForm)
    }

    pollOptDeleteLink (element)
    {
        const btn = this.removeOptButton.cloneNode(true)

        btn.addEventListener('click', () => { element.remove() })

        element.firstChild.firstChild.prepend(btn)

        return element
    }
}
