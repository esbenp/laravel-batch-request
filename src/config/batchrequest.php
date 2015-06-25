<?php

return [

    /**
     * Should urls given to the endpoint be prefixed?
     * E.g. /url would become /api/url if url_prefix => /api
     */
    "url_prefix" => false,

    /**
     * The actions array sent from the client should be prefixed with a 
     * key. That key is given here.
     * e.g. 
     * {
     *     "actions": [
     *         {Action1},
     *         {Action2},
     *         etc.. 
     *     ]
     * }
     */
    "actions_key" => "actions",

    /**
     * The chosen class to format the response of the batch request
     */
    "response_formatter" => Optimus\BatchRequest\ResponseFormatter\OptimusResponseFormatter::class,

    /**
     * The chosen class to format each individual response in the batch request
     */
    "result_formatter" => Optimus\BatchRequest\ResultFormatter\OptimusResultFormatter::class,

    "wrap_in_database_transaction" => true,

    /**
     * Batch requests are wrapped in a database transaction. If set to true 
     * the database will rollback if any given response was errorneous 
     * (non-2xx status) 
     */
    "rollback_db_transactions_on_error" => true

];