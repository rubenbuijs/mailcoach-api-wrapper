<?php

return [

    /*
     * The base url for your Mailcoach application.
     * E.g. https://yourdomain.com/api/
     * Ends with a slash: /
     */
    'baseUrl' => env('MAILCOACH_API_BASE_URL', null),

    /*
     * The API Token that you retrieved from Mailcoach Settings -> API Tokens
     */
    'apiToken' => env('MAILCOACH_API_TOKEN', null),

    /*
     * The ID of the list that you want to interact with using this API.
     * You can find the ID by opening the list in your browser /email-lists/<LISTID>/summary
     */
    'listId' => env('MAILCOACH_LIST_ID', null),

    /*
     * If you're having trouble with https connections, set this to false.
     */
    'ssl' =>  env('MAILCOACH_SSL', true),

];
