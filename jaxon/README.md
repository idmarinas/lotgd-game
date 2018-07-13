In this directory you can include your custom classes to use with Jaxon.
Remember that all of these classes are available globally, which means you can use them at any time.

If you just want the functions to be available in a certain module or modules, add the classes using the global variable $lotgdJaxon

# Example of usage
```php
namespace Global\Ajax;

class Example
{
    public function exampleMethod()
    {
        $response = new \Jaxon\Response\Response();

        return $response;
    }
}
```
> Output code that generates Jaxon is like this:

```javascript
JaxonGlobal = {};
JaxonGlobal.Ajax = {};
JaxonGlobal.Ajax.Mail = {};
JaxonGlobal.Ajax.Mail.exampleMethod = function() {
    return jaxon.request(
        { jxncls: 'Global.Ajax.Mail', jxnmthd: 'exampleMethod' },
        { parameters: arguments }
    );
};

```

> To use that PHP function in your JavaScript you can do it with something similar to this:
```html
<button onclick="JaxonGlobal.Ajax.Mail.exampleMethod()">Clicky button</button>
```
