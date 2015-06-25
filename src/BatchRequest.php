<?php

namespace Optimus\BatchRequest;

use Optimus\BatchRequest\Database\TransactionInterface;
use Optimus\BatchRequest\ResponseFormatter\ResponseFormatterInterface;
use Optimus\BatchRequest\ResultFormatter\ResultFormatterInterface;
use Optimus\BatchRequest\Router\RouterInterface;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class BatchRequest {

    private $router;

    private $responseFormatter;

    private $resultFormatter;

    private $config;

    private $db;

    public function __construct(
        RouterInterface $router,
        array $config,
        ResultFormatterInterface $resultFormatter,
        ResponseFormatterInterface $responseFormatter,
        TransactionInterface $databaseManager)
    {
        $this->config = $config;
 
        $this->setRouter($router);
        $this->setDatabaseManager($databaseManager);
        $this->setResultFormatter($resultFormatter);
        $this->setResponseFormatter($responseFormatter);
    }

    /**
     * Handle an array of actions to be performed. 
     * The format:
     * [
     *     {
     *         action: "settings/1", // will be run as /settings/1
     *         method: "PUT",
     *         key: "settings", // a key used to identify the response,
     *         data: [], // key, value array of data to send with the request
     *         headers: [] // key, value array of headers to send with the request
     *     }
     * ]
     * 
     * @param  array $actions 
     * @return void          
     */
    public function request(array $actions) 
    {
        // We begin a database transaction, so the we can rollback
        // if there are errors in one or more of the actions.
        $this->beginDatabaseTransaction();

        $responses = $this->router->batch($actions);

        return $this->prepareResults($responses);
    }

    /**
     * Format the results into an api response.
     * If the results contained a special error, e.g. 401, 412 etc.
     * we just return the given JsonResponse with the proper status code.
     *
     * If an action was a resource (i.e. a GET request) we return the data
     * If an action was an action (i.e. POST/PUT/DELETE request) we return 
     * a normal API response
     * On PUT requests, we add the etag so it can be used client side.
     * 
     * @return array|\Illuminate\Http\Response 
     */
    public function prepareResults($results)
    {
        // Array of all error responses
        $errors = [];
        // Array of all successfull responses
        $successes = [];

        // Was there an error in the result collection?
        $errorneous = false;
        foreach($results as $key => $response) {
            if (!$response->isSuccessful()) {
                $errorneous = true;
                $errors[$key] = $this->generateResult($response);
            } else {
                $successes[$key] = $this->generateResult($response);
            }
        }

        // The result collection was errorneous. Rollback the database
        // and return all the error messages
        if ($errorneous === true) {
            if ($this->config['rollback_db_transactions_on_error']) {
                $this->rollbackDatabaseTransaction();
            }

            return $this->generateResponse(
                "error",
                $successes,
                $errors
            );
        }

        // Everything ran smoothely. Commit all database transactions and 
        // return all the success responses.
        $this->commitDatabaseTransaction();

        return $this->generateResponse(
            "success",
            $successes,
            $errors
        );
    }

    public function setRouter(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function setDatabaseManager(TransactionInterface $databaseManager)
    {
        $this->db = $databaseManager;
    }

    public function setResponseFormatter(ResponseFormatterInterface $responseFormatter)
    {
        $this->responseFormatter = $responseFormatter;
    }

    public function setResultFormatter(ResultFormatterInterface $resultFormatter)
    {
        $this->resultFormatter = $resultFormatter;
    }

    /**
     * @return void
     */
    private function beginDatabaseTransaction()
    {
        $this->db->beginTransaction();
    }

    /**
     * @return void
     */
    private function rollbackDatabaseTransaction()
    {
        $this->db->rollback();
    }

    /**
     * @return void
     */
    private function commitDatabaseTransaction()
    {
        $this->db->commit();
    }

    private function generateResponse($status, array $successes, array $errors)
    {
        return $this->responseFormatter->formatResponse($status, $successes, $errors);
    }

    private function generateResult(SymfonyResponse $result)
    {
        return $this->resultFormatter->formatResult($result);
    }

}