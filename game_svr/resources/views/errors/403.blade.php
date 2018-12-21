<html>
    <head>
        <title>{{ config('app.name') }} - Error</title>
    </head>
    <body>
        {{ $exception->getMessage() }}
    </body>
</html>