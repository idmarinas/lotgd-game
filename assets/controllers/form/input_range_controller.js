import { Controller } from 'stimulus';

export default class extends Controller
{
    static targets = ['range', 'value', 'min', 'max']

    connect()
    {
        this.valueTarget.innerHTML = this.rangeTarget.value

        if (this.hasMinTarget)
        {
            this.minTarget.innerHTML = this.rangeTarget.min
        }

        if (this.hasMaxTarget)
        {
            this.maxTarget.innerHTML = this.rangeTarget.max
        }
    }

    updateValue(evt)
    {
        this.valueTarget.innerHTML = evt.currentTarget.value;
    }
}
