In this directory can add all your class for remplace core class.
Or add other class for your custom server.

# Example of usage
```php
namespace Lotgd\Local;

use Lotgd\Core\Class as LotgdClass;

//-- Can extends any class of Core
class Example extends LotgdClass
{
    public function exampleMethod()
    {
        //-- Your code here
    }
}
