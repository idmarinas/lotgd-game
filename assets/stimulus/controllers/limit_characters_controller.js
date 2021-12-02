import CharacterCounter from 'stimulus-character-counter'

export default class extends CharacterCounter
{
    isOverCharacters = undefined

    static values = { limit: Number, textCharsLeft: String, textCharsOver: String }

    update ()
    {
        let msg = ''

        //-- Not have characters left to write
        if (this.count > this.limitValue)
        {
            this.updateIsOverCharacters(true)

            msg = this.textCharsOverValue.replace('{size}', this.limitValue).replace('{over}', (this.count - this.limitValue))
        }
        else
        {
            this.updateIsOverCharacters(false)

            msg = this.textCharsLeftValue.replace('{size}', this.limitValue).replace('{left}', (this.limitValue - this.count))
        }

        this.counterTarget.innerHTML = msg
    }

    updateIsOverCharacters(value)
    {
        if (this.isOverCharacters !== value)
        {
            this.isOverCharacters = value

            document.dispatchEvent(new CustomEvent('limit_characters.is_over_characters', {
                bubbles: true,
                cancelable: true,
                detail: {
                    isOverCharacters: this.isOverCharacters
                }
            }))
        }
    }
}
