import { Autocomplete } from 'stimulus-autocomplete'

export default class extends Autocomplete
{
    jsonResult = {}

    replaceResults (content)
    {
        if (this.isJson(content))
        {
            let json = JSON.parse(content)
            this.jsonResult = json

            content = ''

            for(let ctn of json)
            {
                content = content + this.jsonItemTemplate(ctn)
            }
        }

        this.resultsTarget.innerHTML = content
        this.identifyOptions()

        if ( !! this.options)
        {
            this.open()
        }
        else
        {
            this.close()
        }
    }

    jsonItemTemplate (item)
    {
        return `<li role="option" data-autocomplete-value="${item.value}">${item.display}</li>`
    }

    isJson (item)
    {
        item = typeof item !== "string" ? JSON.stringify(item) : item

        try
        {
            item = JSON.parse(item)
        }
        catch (e)
        {
            return false;
        }

        if (typeof item === "object" && item !== null)
        {
            return true;
        }

        return false;
    }
}
