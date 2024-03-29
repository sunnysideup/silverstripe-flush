# what does it do?

Sends a message directly to the command line / screen akin to `DB::alteration_message`.

# how to use it?
```php
use Sunnysideup\Flush\FlushNow;
class MyClass
{
    use FlushNow;
    
    protected function doSomething()
    {
        self::flushNowLine();
        self::flushNow('I did it', 'created');
        self::flushNowLine();
    }

}

```

and then you can also do: 

```php

use MyClass;

class AnotherClass
{    
    protected function doSomething()
    {
        MyClass::flushNow('I did it', 'created');
    }

}
```

_note that we make use of FlushNow Trait added to MyClass_

Type options are:
 -  created  
 -  good  
 -  changed  
 -  info  
 -  obsolete  
 -  repaired  
 -  deleted  
 -  error  
 -  bad  
 -  heading  
