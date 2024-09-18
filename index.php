<?php

$folder = createFolder();
createFile($folder);

header('Content-Type: application/json');
echo json_encode(['result' => true]);

function createFolder()
{
    $folder = 'responses/';

    @mkdir($folder);

    return $folder;
}

function createFile($folder)
{
    $file = sprintf(
        '%s/%s%s.json',
        $folder,
        $_SERVER['REQUEST_METHOD'],
        str_ireplace('/', '_', explode('?', $_SERVER['REQUEST_URI'])[0])
    );

    if (file_exists($file)) {
        file_put_contents($file, ',' . PHP_EOL, FILE_APPEND);
    }

    file_put_contents($file, json_encode(getContent(), JSON_PRETTY_PRINT), FILE_APPEND);
}

function getContent()
{
    $headersFunc = function () {
        $headers = [];

        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'HTTP_') !== false) {
                $headers[$key] = $value;
            }
        }

        return $headers;
    };

    return [
        'method' => $_SERVER['REQUEST_METHOD'],
        'files' => $_FILES,
        'query' => $_GET,
        'body' => $_POST,
        'json' => @json_decode(file_get_contents('php://input'), true),
        'header' => $headersFunc(),
    ];
}
