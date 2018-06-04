<?php namespace Motorphp\SilexTools\NetteLibrary\ProviderAdapters;

use Motorphp\SilexTools\Components\Factory;
use Motorphp\SilexTools\Components\Provider;
use Motorphp\SilexTools\NetteLibrary\SourceCode\Fragment;
use Motorphp\SilexTools\NetteLibrary\SourceCode\FragmentWriter;

class FragmentWriterProvider
{
    /** @var FragmentWriter */
    private $writer;

    /** @var string */
    private $provider = '';

    /** @var string */
    private $custom = '';

    /** @var string */
    private $separator = '';

    private $nestingLevel = 1;

    static function newInstance() : FragmentWriterProvider
    {
        return new FragmentWriterProvider(new FragmentWriter());
    }

    function __construct(FragmentWriter $writer)
    {
        $this->writer = $writer;
    }

    function writeProvider(Provider $component) : FragmentWriterProvider
    {
        $this->provider = 'new ?()';
        $component->writeClass($this->writer);
        return $this;
    }

    function done() : Fragment
    {
        if (empty($this->custom)) {
            $template = sprintf('$container->register(%s);' . PHP_EOL , $this->provider);
        } else {
            $template = '$container->register('
                . PHP_EOL . $this->indent() . $this->provider
                . PHP_EOL . $this->indent(). sprintf(', [%s]', $this->custom . PHP_EOL)
                . PHP_EOL . ')' . ';'
                . PHP_EOL
            ;
        }

        $this->writer->writeTemplate($template);
        return $this->writer->done();
    }

    function writeEntry(Factory $component) : FragmentWriterProvider
    {
        $this->custom .= PHP_EOL . $this->indent() . $this->separator . '? => ?($container)';
        $this->separator = ',';

        $component->writeKey($this->writer);
        $component->writeCallback($this->writer);

        return $this;
    }

    function startList(string $key) : FragmentWriterProvider
    {
        $this->custom .= PHP_EOL . $this->indent() . $this->separator . '? => [';
        $this->separator = '';
        $this->nestingLevel++;

        $this->writer->writeString($key);

        return $this;
    }

    function endList() : FragmentWriterProvider
    {
        $this->custom .= PHP_EOL . $this->indent() .']';
        $this->nestingLevel--;
        return $this;
    }

    private function indent() : string
    {
        return str_repeat(' ', 2 * $this->nestingLevel );
    }
}
