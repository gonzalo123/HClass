<?php
require_once __DIR__ . '/../lib/HClass/HClass.php';
use HClass\HClass;

class HclassTest extends PHPUnit_Framework_TestCase
{
    function testSimpleUsage()
    {
        $human = HClass::define()
            ->fct(HClass::__construct, function($self, $name) {$self->name = $name;})
            ->fct('hello', function($self) {
                          return "{$self->name} says hello";
                      });

        $gonzalo = $human->create('Gonzalo');
        $peter = $human->create('Peter');

        $this->assertEquals("Gonzalo says hello", $gonzalo->hello());
        $this->assertEquals("Peter says hello", $peter->hello());
    }

    function testCallingUndefinedFunctions()
    {
        $human = HClass::define()
            ->fct(HClass::__construct, function($self, $name) {$self->name = $name;})
            ->fct('hello', function($self) {
                          return "{$self->name} says hello";
                      });

        $gonzalo = $human->create('Gonzalo');
        $this->setExpectedException('Exception', "ERROR Method 'goodbye' does not exits");
        $gonzalo->goodbye();
    }

    function testInheritance()
    {
        $human = HClass::define()
            ->fct(HClass::__construct, function($self, $name) {$self->name = $name;})
            ->fct('hello', function($self) {
                          return "{$self->name} says hello";
                      });

        $shyHuman = HClass::define($human)
            ->fct('hello', function($self) {
                          return "{$self->name} is shy and don't says hello";
                      });

        $gonzalo = $human->create('Gonzalo');
        $peter = $shyHuman->create('Peter');

        $this->assertEquals("Gonzalo says hello", $gonzalo->hello());
        $this->assertEquals("Peter is shy and don't says hello", $peter->hello());
    }

    function testDinamicallyFunctionCreation()
    {
        $human = HClass::define()
            ->fct(HClass::__construct, function($self, $name) {$self->name = $name;})
            ->fct('hello', function($self) {
                          return "{$self->name} says hello";
                      });

        $gonzalo = $human->create('Gonzalo');
        $this->assertEquals("Gonzalo says hello", $gonzalo->hello());

        try {
            $gonzalo->goodbye();
        } catch (Exception $e) {
            $this->assertEquals("ERROR Method 'goodbye' does not exits", $e->getMessage());
        }

        $human->fct('goodbye', function($self) {
                            return "{$self->name} says goodbye";
                        });

        $this->assertEquals("Gonzalo says goodbye", $gonzalo->goodbye());
    }

    function testFizzBuzz()
    {
        $fizzBuzz = HClass::define()
            ->fct('run', function($self, $elems = 100) {
                          list($fizz, $buzz) = array('Fizz', 'Buzz');
                          return array_map(function ($element) use ($fizz, $buzz) {
                                  $out = array();
                                  if ($element % 3 == 0 || strpos((string) $element, '3') !== false ) {
                                      $out[] = $fizz;
                                  }
                                  if ($element % 5 == 0 || strpos((string) $element, '5') !== false ) {
                                      $out[] = $buzz;
                                  }
                                  return (count($out) > 0) ? implode('', $out) : $element;
                              }, range(0, $elems-1));
                      });
        $fizzBuzz = $fizzBuzz->create();
        $arr = $fizzBuzz->run();

        $this->assertEquals(count($arr), 100);
        $this->assertEquals($arr[1],  1);
        $this->assertEquals($arr[3],  'Fizz');
        $this->assertEquals($arr[4],  4);
        $this->assertEquals($arr[5],  'Buzz');
        $this->assertEquals($arr[6],  'Fizz');
        $this->assertEquals($arr[20], 'Buzz');
        $this->assertEquals($arr[13], 'Fizz');
        $this->assertEquals($arr[15], 'FizzBuzz');
        $this->assertEquals($arr[53], 'FizzBuzz');
    }

    function testAnotherFizzBuzzImplementationWithDependencyInjection()
    {
        $fizzBuzz = HClass::define();
        $fizzBuzz->fct(HClass::__construct, function($self, $fizzBuzzElement) {
                               $self->fizzBuzzElement = $fizzBuzzElement;
                           });
        $fizzBuzz->fct('run', function($self, $elems = 100) {
                               $out = array();
                               foreach (range(1, $elems) as $elem) {
                                   $out[$elem] =  $self->fizzBuzzElement->render($elem);
                               }
                               return $out;
                           });
        
        $fizzBuzzElement = HClass::define()
            ->fct('render', function($self, $element) {
                          list($fizz, $buzz) = array('Fizz', 'Buzz');
                          $out = array();
                          if ($element % 3 == 0 || strpos((string) $element, '3') !== false ) {
                              $out[] = $fizz;
                          }

                          if ($element % 5 == 0 || strpos((string) $element, '5') !== false ) {
                              $out[] = $buzz;
                          }
                          return (count($out) > 0) ? implode('', $out) : $element;
            });

        $fbe = $fizzBuzzElement->create();

        $this->assertEquals($fbe->render(1),  1);

        $this->assertEquals($fbe->render(2),  2);
        $this->assertEquals($fbe->render(3),  'Fizz');
        $this->assertEquals($fbe->render(4),  4);
        $this->assertEquals($fbe->render(5),  'Buzz');
        $this->assertEquals($fbe->render(6),  'Fizz');
        $this->assertEquals($fbe->render(20), 'Buzz');
        $this->assertEquals($fbe->render(13), 'Fizz');
        $this->assertEquals($fbe->render(15), 'FizzBuzz');
        $this->assertEquals($fbe->render(53), 'FizzBuzz');

        $fb = $fizzBuzz->create($fbe);
        $arr = $fb->run();

        $this->assertEquals(count($arr), 100);
        $this->assertEquals($arr[1],  1);
        $this->assertEquals($arr[3],  'Fizz');
        $this->assertEquals($arr[4],  4);
        $this->assertEquals($arr[5],  'Buzz');
        $this->assertEquals($arr[6],  'Fizz');
        $this->assertEquals($arr[20], 'Buzz');
        $this->assertEquals($arr[13], 'Fizz');
        $this->assertEquals($arr[15], 'FizzBuzz');
        $this->assertEquals($arr[53], 'FizzBuzz');
    }
}