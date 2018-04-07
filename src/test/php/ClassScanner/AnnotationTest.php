<?php namespace Motorphp\SilexTools\ClassScanner;

use Helstern\SMSkeleton\HttpApi\Test\HelloController;
use PHPUnit\Framework\TestCase;

class AnnotationTestTest extends TestCase
{

    public function IssueForUser()
    {
        $reflection = new \ReflectionClass(HelloController::class);
        $fileName = $reflection->getFileName();
        $tokens = token_get_all(file_get_contents($fileName));

//        print_r($tokens);

//        die(token_name(382));

        foreach ($tokens as $token) {

            if (!is_array($token)) {
                continue;
            }

            if (T_FUNCTION === $token[0]) {
                print_r($token);
            }


        }


        die('aaa');


        $this->assertTrue(true);
    }
}
