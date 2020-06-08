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
        self::FlushNow('I did it', 'created');
    }

}
