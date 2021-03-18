<?php

namespace Herisson;


class Shell
{

    /**
     * Run a shell exec call
     *
     * @param string $binary  the binary to call
     * @param string $options the options to pass to binary
     *
     * @return string the shell output
     */
    public function shellExec($binary, $options) : string
    {

        if (preg_match("#/#", $binary)) {
            $fullBinary = $binary;
        } else {
            $fullBinary = $this->getPath($binary);
        }
        // echo "$binary -> $fullBinary<br>";
        if (file_exists($fullBinary) && is_executable($fullBinary)) {
            /*
            $herissonOptions = get_option('HerissonOptions');
            if ($herissonOptions['debugMode']) {
                Message::i()->addSucces($fullBinary." ".$options);
            }
            */
            exec($fullBinary." ".$options, $output);
            return implode("\n", $output);
        }
        return false;
    }

    /**
     * Get the path of the given binary
     *
     * This methods uses `which` to get the full path of the binary
     *
     * @param string $binary the binary to get to path to
     *
     * @return string the full path of the binary
     */
    public function getPath($binary)
    {
        exec("which $binary", $output);
        return implode("\n", $output);
    }

}

