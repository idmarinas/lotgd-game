import Autocomplete from './autocomplete_controller'

export default class extends Autocomplete
{
    static targets = ['superuserMessage']

    jsonItemTemplate (item)
    {
        return `<li role="option" class="p-2 hover:bg-white hover:bg-opacity-20" data-autocomplete-value="${item.value}"><i class="${item.icon}"></i> ${item.display}</li>`
    }

    isSuperuser (value)
    {
        const user = this.jsonResult.find(record =>
        {
            return record.value === value
        })

        return user instanceof Object
    }

    superuserMessageTargetConnected (target)
    {
        document.addEventListener('autocomplete.change', event =>
        {
            if (this.isSuperuser(event.detail.value))
            {
                target.classList.toggle('hidden')
            }
        })
    }
}
