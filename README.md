# Debug

[![Build Status](https://travis-ci.org/PHPixie/Debug.svg?branch=master)](https://travis-ci.org/PHPixie/Debug)
[![Test Coverage](https://codeclimate.com/github/PHPixie/Debug/badges/coverage.svg)](https://codeclimate.com/github/PHPixie/Debug)
[![Code Climate](https://codeclimate.com/github/PHPixie/Debug/badges/gpa.svg)](https://codeclimate.com/github/PHPixie/Debug)
[![HHVM Status](https://img.shields.io/hhvm/phpixie/debug.svg?style=flat-square)](http://hhvm.h4cc.de/package/phpixie/debug)

[![Author](http://img.shields.io/badge/author-@dracony-blue.svg?style=flat-square)](https://twitter.com/dracony)
[![Source Code](http://img.shields.io/badge/source-phpixie/debug-blue.svg?style=flat-square)](https://github.com/phpixie/debug)
[![Software License](https://img.shields.io/badge/license-BSD-brightgreen.svg?style=flat-square)](https://github.com/phpixie/debug/blob/master/LICENSE)
[![Total Downloads](https://img.shields.io/packagist/dt/phpixie/debug.svg?style=flat-square)](https://packagist.org/packages/phpixie/debug)

PHPixie Debug was created to improve PHP development in any environment. Of course if you are already using a web framework debugging tools are already provided, but when developing a library, solving a programming puzzle or even using WordPress the lack of a debugging toolset is hindering. Even basic functionality like convertieng errors to exceptions requires registering a special handler. PHPixie Debug can bootstrap you with a convenient environment in just two lines of code.

**Exceptions and tracing**

The Debug library tries to achieve the same level of usage ina console environment as we already have in web applications. When writing libraries for PHPixie I often wanted to have exception traces that would include the part of code that the exception happened in. Another problem with traces in php is that calling _print\_r(debug\_backtrace())_ directly can quickly result in a wall of text if any argument in the backtrace was an object with some dependencies. Using _debug\_print\_backtrace()_ gives a better result, but still prints all array members and requires output buffering to assign the result to a variable. Let’s take a look at the PHPixie trace:

```php
<?php
require_once('vendor/autoload.php');
$debug = new \PHPixie\Debug();

try{
    throw new \Exception("test");
    
}catch(\Exception $e) {
    //Pretty print an exception
    $debug->exceptionMessage($e);
}

echo "\n-------\n";

//Automatic exception printing
//Will also display any logged messages
//(more on that later)
$debug->registerHandlers();

class Test
{
    public function a($string)
    {
        $array = array(1, 2);
        $this->b($string, $array);
    }
    
    public function b($string, $array)
    {
        $object = (object) array('t' => 1);
        $this->c($string, $array, $object);
    }
    
    public function c()
    {
        substr();
    }
}

$test = new Test();
$test->a("pixie");
```

Results in:

```
Exception: test                                                       
                                                                      
5                                  
6 try{                                                                 
> throw new \Exception("test");                                    
8                                                                      
9 }catch(\Exception $e) {                                              
                                                                      
#0 D:\debug\examples\exceptions.php:7                                
                                                                      
-------                                                               
                                                                                                                                       
ErrorException: substr() expects at least 2 parameters, 0 given       
                                                                      
36 public function c()                                             
37 {                                                               
>> substr();                                                   
39 }                                                               
40 }                                                                   
                                                                      
#0 D:\debug\examples\exceptions.php:38                               
#1 D:\debug\examples\exceptions.php:38                               
    substr()                                                          
#2 D:\debug\examples\exceptions.php:33                               
    Test->c('pixie', array[2], stdClass)                              
#3 D:\debug\examples\exceptions.php:27                               
    Test->b('pixie', array[2])                                        
#4 D:\debug\examples\exceptions.php:43                               
    Test->a('pixie')                                                  
                                                                      
Logged items:                                                         
```

Note that the trace doesn’t include the handler that converted a PHP error into an exception, a lot of similar libraries forget to hide that part thus littering your trace. PHPixie Debug hides any of its handles for the traces.

**Dumping variables**  
Dumping data can be done via a static _\PHPixie\Debug::dump()_, this is by the way the first PHPixie static method ever. The reason for such approach is that usually you delete such calls after you fix the issue, so the Debug library itself is never really a dependency of your application, thus massing it via DI is needless. But the static call will only work if the Debug library has been prior initialized, and it acts as a proxy to that instance. PHPixie will never have any actual static logic.

```php
<?php
require_once('vendor/autoload.php');

use PHPixie\Debug;
$debug = new Debug();

Debug::dump("Array dump:");
Debug::dump(array(1));

Debug::dump("Short array dump:");
//Short dump prints minimum information
//Which is useful to check array size
//or the class name of an object
Debug::dump(array(1), true);

$object = (object) array('t' => 1);
Debug::dump("Object dump:");
Debug::dump($object);

Debug::dump("Short object dump:");
Debug::dump($object, true);

Debug::dump("Dump trace with parameters");
class Test
{
    public function a($string)
    {
        $array = array(1, 2);
        $this->b($string, $array);
    }
    
    public function b($string, $array)
    {
        $object = (object) array('t' => 1);
        $this->c($string, $array, $object);
    }
    
    public function c()
    {
        Debug::trace();
    }
}

$t = new Test();
$t->a("test");
```

Result:

```
'Array dump:'

Array
(
    [0] => 1
)


'Short array dump:'

array[1]

'Object dump:'

stdClass Object
(
    [t] => 1
)


'Short object dump:'

stdClass

'Dump trace with parameters'

#0 D:\debug\examples\dumping.php:37
    PHPixie\Debug::trace()
#1 D:\debug\examples\dumping.php:32
    Test->c('test', array[2], stdClass)
#2 D:\debug\examples\dumping.php:26
    Test->b('test', array[2])
#3 D:\debug\examples\dumping.php:42
    Test->a('test')
```

**Logging**  
To separate actual program output from debugging output usually developers store messages in some sort of array that they print afterwards. The problem with that approach is that if an exception happens or _exit()_ is called those messages will not be printed. PHPixie debug always prints the log on exception and can register a handler to also do that whenever the script ends execution. Here are two examples:

```php
use PHPixie\Debug;
$debug = new Debug();

Debug::log("test");
Debug::log(array(3));

class Test
{
    public function a($string, $num)
    {
        Debug::logTrace();
    }
}
$t = new Test();
$t->a("test", 5);

//Displaying logged items
$debug->dumpLog();

```
Logged items:

[0] D:\debug\examples\logging.php:7
'test'

[1] D:\debug\examples\logging.php:8
Array
(
    [0] => 3
)


[2] D:\debug\examples\logging.php:16
#0 D:\debug\examples\logging.php:16
    PHPixie\Debug::logTrace()
#1 D:\debug\examples\logging.php:20
    Test->a('test', 5)
```

And with automatic logging:

```php
<?php

use PHPixie\Debug;
$debug = new Debug();

//By passing 'true' to registerHandlers()
//we are also enabling dumping logged items
//after the script finishes
$debug->registerHandlers(true);

Debug::log("test");

echo("Logged messages will be printed below");

```
Logged messages will be printed now

Logged items:

#0 D:\debug\examples\log_handler.php:13
'test'
```

**In conclusion**  
The main purpose of PHPixie Debug is not actually exception handling and tracing, it was designed to provide an OOP interface to PHP traces and variable dumping. This will in near future allow for the creation of a web debugged for PHPixie 3, all that is lacking is a nice web template for it. Its primary use as a standalone tool is to bootstrap your development environment in two lines of code and no additional dependencies. I hope next time when you’ll be solving a test puzzle for an interview or you’ll find yourself in need of some tracing in WordPress you’ll remember this little library and save some time of reading through _debug\_backtrace()_ output.

**Demo**  
To try out PHPixie debug all you need to do is this:

```php
git clone https://github.com/phpixie/debug
cd debug/examples
 
#If you don't have Composer yet
curl -sS https://getcomposer.org/installer | php
 
php composer.phar install
php exceptions.php
php logging.php
php log_handler.php
php dumping.php
```

## Framework integration

The Debug library is automatically initialized by the framework, so you can use it immediately.
You can quickly access the logger in any template by using the `$this->debugLogger()` method.
The easiest way to use it with an HTML page is:

```
<pre>
    <?=$_((string) $this->debugLogger()) ?>
</pre>
```

As with all the other PHPixie libraries the code is 100% unit tested and works with all versions of PHP 5.3+ (including nightly PHP7 and HHVM).
