<?php

namespace Herisson\Service;


class Message
{

    /**
     * Array of the currently known error in the page.
     *
     * Errors are strings
     */
    protected $errors = [];

    /**
     * Array of the currently known successful items in the page.
     *
     * Successes are strings
     */
    protected $success = [];

    /**
     * Get the errors array
     *
     * @return array of error messages
     */
    public function getErrors() : array
    {
        return $this->errors;
    }

    /**
     * Add an error to the errors array
     *
     * @param string $message the error message
     *
     * @return void
     */
    public function addError(string $message)
    {
        array_push($this->errors, $message);
    }

    /**
     * Check if there is any error message
     *
     * @return bool
     */
    public function hasErrors() : bool
    {
        return sizeof($this->errors) > 0;
    }

    /**
     * Get the success array
     *
     * @return array of success messages
     */
    public function getSuccess() : array
    {
        return $this->success;
    }

    /**
     * Add an succes to the success array
     *
     * @param string $message the success message
     *
     * @return void
     */
    public function addSucces(string $message)
    {
        array_push($this->success, $message);
    }

    /**
     * Check if there is any succes message
     *
     * @return bool
     */
    public function hasSuccess() : bool
    {
        return sizeof($this->success) > 0;
    }



}


