<?php
// session_start() has to go right at the top, before any output!
?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>REST/OAuth Example</title>
</head>
<body>
<table border=\"1\"><tr><td>
            <br/>
<tt>
    <br/>
    First with the enterprise client
    {{$sessioninfo}}
    <br/>
    Results of query {{$query}}<br/><br/>\n
    @foreach($response->records as $userrecord)
                id: {{$userrecord->Id}}
                fn: {{$userrecord->FirstName}}
                ln: {{$userrecord->LastName}}
                @isset($userrecord->Phone)
                    phone: {{$userrecord->Phone}}
                @endisset
                @isset($userrecord->OwnerId)
                    owner: {{$userrecord->OwnerId}}
                @endisset
                @isset($userrecord->AccountId)
                    account: {{$userrecord->AccountId}}
                @endisset
    <br/>
    @endforeach

    <br/>Now, create some records<br/><br/>
    @foreach($create_response as $i => $result)

        @if($result->success == 1)
            {{$create_records[$i]->FirstName}} {{$create_records[$i]->LastName}} {{$create_records[$i]->Phone}} created with id {{$result->id}}.. <br/>
        @else
            Error: {{$result->errors->message}}
        @endif

    @endforeach

    <br/>Retrieve the newly created records:<br/><br/>

    @foreach($retrieve_response as $record)
        {{$record->Id}}: {{$record->FirstName}}  {{$record->LastName}} .. {{$record->Phone}}   @isset($record->OwnerId) o:  {{$record->OwnerId}} @endisset <br/>
    @endforeach

    <br/>Next, update the new records<br/><br/>

    @foreach($update_response as $result)
        @if($result->success == 1)
            {{$result->id}} updated <br/>
        @else
            Error: {{$result->errors->message}}
        @endif
    @endforeach

    <br/>Retrieve the updated records to check the update:<br/><br/>

    @foreach($check_response as $record)
        {{$record->Id}}: {{$record->FirstName}}  {{$record->LastName}} .. {{$record->Phone}} @isset($record->OwnerId) o:  {{$record->OwnerId}} @endisset <br/>
    @endforeach

    <br/>Let's remove those phone numbers<br/><br/>

    @foreach($remove_response as $result)
        @if($result->success == 1)
            {{$result->id}} updated <br/>
        @else
            Error: {{$result->errors->message}}
        @endif
    @endforeach

    <br/>Retrieve the updated records again to check the update:"<br/><br/>

    @foreach($check2_response as $record)
        {{$record->Id}}: {{$record->FirstName}}  {{$record->LastName}} ..
        @isset($record->Phone)
            phone: {{$record->Phone}}
        @else
            no phone number
        @endisset
            @isset($record->OwnerId) o:  {{$record->OwnerId}} @endisset
        <br/>
    @endforeach

     <br/>Finally, delete the records:<br/><br/>
{{--   @foreach($delete_response as $result)
            @if($result->success == 1)
                {{$result->id}} deleted <br/>
            @else
                Error: {{$result->errors->message}}
            @endif
        @endforeach --}}

</tt>
</body>
</html>
