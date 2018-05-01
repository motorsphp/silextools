<?php namespace Motorphp\SilexTools\ClassScanner;

class ClassFileParser
{
    public function parseString($location, $file): ?ClassFile
    {
        $tokens = token_get_all($file);
        return $this->parse($location, $tokens);
    }

    public function parseSplFile(\SplFileInfo $file): ?ClassFile
    {
        $path = $file->getRealPath();
        $contents = file_get_contents($path);
        return $this->parseString($path, $contents);
    }

    /**
     * @param $file
     * @return ClassFile
     */
    public function parseFile($file): ?ClassFile
    {
        $contents = file_get_contents($file);
        return $this->parseString($file, $contents);
    }

    public function parse($file, array $tokens): ?ClassFile
    {
        $namespace = null;
        $classname = null;
        $tokenCount = count($tokens);
        for ($offset=0; $offset < $tokenCount; $offset++) {
            if (!is_array($tokens[$offset])) {
                continue;
            }

            if (T_NAMESPACE === $tokens[$offset][0]) {
                $namespace = $this->parseNamespace($tokens, $offset);
                continue;
            }

            if (T_CLASS === $tokens[$offset][0]) {
                $classname = $this->parseClassname($tokens, $offset);
                break;
            }
        }

        if (empty($classname)) {
            return null;
        }

        return new ClassFile($classname, $namespace, $file);
    }
    /**
     * Extracts the namespace from tokenized file
     * @param array $tokens
     * @param integer $offset
     * @return string
     */
    private function parseClassname($tokens, $offset)
    {
        $offset++; // the next token is a whitespace

        $offset++;
        if (isset($tokens[$offset][0]) && T_STRING === $tokens[$offset][0]) {
            return $tokens[$offset][1];
        }

        return null;
    }

    /**
     * Extracts the namespace from tokenized file
     * @param array $tokens
     * @param integer $offset
     * @return string
     */
    private function parseNamespace($tokens, $offset)
    {
        $offset++; // the next token is a whitespace

        $namespace = '';
        $tokenCount = count($tokens);
        for ($offset++; $offset < $tokenCount; $offset++) {
            // expecting T_STRING
            if (!is_array($tokens[$offset])) {
                break;
            }

            if (isset($tokens[$offset][0]) && T_STRING === $tokens[$offset][0]) {
                $namespace .= $tokens[$offset][1];
            } else {
                break;
            }

            // expecting T_NS_SEPARATOR
            $offset++;
            if (!is_array($tokens[$offset])) {
                continue;
            }

            if (isset($tokens[$offset][0]) && T_NS_SEPARATOR === $tokens[$offset][0]) {
                $namespace .= $tokens[$offset][1];
            } else {
                break;
            }
        }

        return $namespace;
    }
}
