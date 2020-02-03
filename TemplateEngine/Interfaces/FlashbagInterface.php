<?php

namespace UKMNorge\TemplateEngine\Interfaces;

interface FlashbagInterface {
    /**
     * Class constructor, where ID is used to differentiate this from other flashbags
     *
     * @param String $id
     */
    public function __construct( String $id );
    /**
     * Add error message
     *
     * @param String $message
     * @return self
     */
    public function error( String $message);

    /**
     * Add success message
     *
     * @param String $message
     * @return self
     */
    public function success( String $message);
    /**
     * Add info message
     *
     * @param String $message
     * @return void
     */
    public function info(String $message);
    /**
     * Fetch all messages (and mark as read)
     * 
     * Level == name of input function (error, success, info)
     *
     * @return Array<Array> String $level => String $message
     */
    public function getAll();
    /**
     * Does the flashbag contain any messages?
     *
     * @return void
     */
    public function needToSpeak();
}