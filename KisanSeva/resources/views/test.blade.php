<!DOCTYPE html>
<html>
    <head>
        <title>Laravel</title>
    </head>
    <body>
        <h1>All Users</h1>
        @if(empty($records))
            Nothing to show.
        @else
            @foreach($records as $record)
                <div>
                    {{ $record->getField('UserName_xt') }}
                </div>
            @endforeach

        @endif
        <h2>All Tips</h2>
        @if(empty($datas))
            Nothing to show.
        @else
            @foreach($datas as $data)
                <div>
                    {{ $data->getField('TipName_xt') }}
                </div>
            @endforeach

        @endif
    </body>
</html>