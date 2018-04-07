<?php

// without this step the annotations would not be found when imported
foreach (spl_autoload_functions() as $fn) {
    \Doctrine\Common\Annotations\AnnotationRegistry::registerLoader($fn);
}