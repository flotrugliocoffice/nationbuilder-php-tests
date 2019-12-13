@extends('layout')
@section('page_title','NationBuilder tester')

@section('content')
    <div class="cover-container d-flex h-100 p-3 mx-auto flex-column">
        <header class="masthead mb-auto">
            <div class="inner text-center">
                <h3 class="masthead-brand">Coffice</h3>

            </div>
        </header>

        <main role="main" class="inner cover">
            <h1 class="cover-heading text-center">Test Nation Builder api</h1>

            <p class="lead">
                Authenticated on NationBuilder current test Token: {{$auth->getToken()}}
            </p>

            <div class="container">
                <div class="row">
                    <div class="col-4">
                        <a class="btn btn-link" data-toggle="collapse" href="#people" role="button"
                           aria-expanded="false" aria-controls="collapseExample">
                            Test People Api
                        </a>
                    </div>
                    <div class="col-4">
                        <a class="btn btn-link" data-toggle="collapse" href="#events" role="button"
                           aria-expanded="false" aria-controls="collapseExample">
                            Test Event Api
                        </a>
                    </div>
                    <div class="col-4">
                        <a class="btn btn-link" data-toggle="collapse" href="#contacts" role="button"
                           aria-expanded="false" aria-controls="collapseExample">
                            Test Contact Api
                        </a>

                    </div>
                </div>
            </div>
        @php

            $showPeople="";
            $showEvent="";
            $showContact="";
            if(isset($_REQUEST["edit"])) {
                $showPeople ="show";
            }
            if(isset($_REQUEST["contact"])||isset($_REQUEST["listcontact"])) {
                $showContact ="show";
            }
            if(isset($_REQUEST["event"])) {
                $showEvent ="show";
            }
        @endphp

        <!-- start People section -->
            <div class="acco container collapse {{$showPeople}}" id="people">
                <div class="row">
                    <div class="col-12">
                        <p class="lead"><b>People tests</b></p>
                        @php
                            if(isset($_POST) && isset($_POST["action"])  && in_array($_POST["action"],["edit","create"])) {
                                $res = $auth->consumePersonAction($_REQUEST["edit"]);
                                $_REQUEST["edit"] = false;
                                if(!empty($res) && isset($res["person"])) {
                                    echo "<h4 class='success'>Person updated successfully</h4>";

                                    if($_POST["action"]!='delete') {
                                        $_REQUEST["edit"] = $res["person"]["id"];
                                    }
                                }
                            }
                        @endphp
                    </div>
                    <div class="col-6">
                        <h3>List people</h3>
                        @php
                            $people = $auth->getPeople();
                        @endphp
                        @if($people)
                            <div class="container">
                                <div class="row">
                                    @foreach($people as $person)
                                        <div class="col-sm-4">
                                            <div class="card">
                                                @if($person["profile_image_url_ssl"])
                                                    <img src="{{$person["profile_image_url_ssl"]}}" class="card-img-top"
                                                         alt="{{$person["first_name"]}}">
                                                @endif
                                                <div class="card-body">
                                                    <h5 class="card-title">{{$person["first_name"]}} {{$person["last_name"]}}</h5>
                                                    <p class="card-text">{{isset($person["bio"])?$person["bio"]:''}}
                                                        <br/>{{$person["email"]}}</p>
                                                    <form method="post" action="/">
                                                        <input type="hidden" name="action" value="delete"/>
                                                        <input type="hidden" name="_id" value="{{$person["id"]}}"/>
                                                        <input type="hidden" name="edit" value="{{$person["id"]}}"/>
                                                        <button type="submit" class="btn btn-danger"
                                                                value="save">Delete
                                                        </button>
                                                    </form>

                                                    <a class="btn btn-primary"
                                                       href="/?edit={{$person["id"]}}">Edit {{$person["first_name"]}}</a>
                                                </div>
                                            </div>
                                        </div>

                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="col-6">
                        <form method="post" action="/">
                            <input type="hidden" name="action" value="{{isset($_REQUEST["edit"])?'edit':'create'}}"/>
                            <input type="hidden" name="_id" value="{{isset($_REQUEST["edit"])?$_REQUEST["edit"]:''}}"/>
                            <input type="hidden" name="edit" value="{{isset($_REQUEST["edit"])?$_REQUEST["edit"]:''}}"/>

                            @if(isset($_REQUEST["edit"]) &&!empty($_REQUEST["edit"]))
                                <h3>Edit person ({{$_REQUEST["edit"]}})</h3>
                                @php
                                    $person = $auth->getPerson($_REQUEST["edit"]);
                                @endphp
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Email address</label>
                                    <input type="email" class="form-control" id="exampleInputEmail1"
                                           aria-describedby="emailHelp" name="email" placeholder="Enter email"
                                           value="{{$person["email"]}}"
                                    >
                                    <small id="emailHelp" class="form-text text-muted">We'll never share your email with
                                        anyone else.
                                    </small>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputName">First name</label>
                                    <input type="text" class="form-control" name="first_name" id="exampleInputName"
                                           placeholder="Enter first name"
                                           value="{{$person["first_name"]}}"
                                    >

                                </div>
                                <div class="form-group">
                                    <label for="exampleInputLName">Last name</label>
                                    <input type="text" class="form-control" name="last_name" id="exampleInputLName"
                                           placeholder="Enter last name"
                                           value="{{$person["last_name"]}}">

                                </div>


                            @else
                                <h3>Create new person</h3>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Email address</label>
                                    <input type="email" class="form-control" id="exampleInputEmail1"
                                           aria-describedby="emailHelp" name="email" placeholder="Enter email"
                                           value="{{isset($_REQUEST["email"])?$_REQUEST["email"]:''}}"
                                    >
                                    <small id="emailHelp" class="form-text text-muted">We'll never share your email with
                                        anyone else.
                                    </small>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputName">First name</label>
                                    <input type="text" class="form-control" name="first_name" id="exampleInputName"
                                           placeholder="Enter first name"
                                           value="{{isset($_REQUEST["first_name"])?$_REQUEST["first_name"]:''}}"
                                    >

                                </div>
                                <div class="form-group">
                                    <label for="exampleInputLName">Last name</label>
                                    <input type="text" class="form-control" name="last_name" id="exampleInputLName"
                                           placeholder="Enter last name"
                                           value="{{isset($_REQUEST["last_name"])?$_REQUEST["last_name"]:''}}">

                                </div>
                            @endif
                            <button type="submit" class="btn-primary"
                                    value="save">{{isset($_REQUEST["edit"])?'Update person':'Create person'}}</button>
                            @if(isset($_REQUEST["edit"]))
                                <a href="/">Create a new person (clear form)</a>
                            @endif

                        </form>
                    </div>

                </div>
            </div>


            <!-- start event section -->
            <div class="acco container collapse {{$showEvent}}" id="events">
                <div class="row">
                    <div class="col-12">
                        <p class="lead"><b>Event tests</b></p>
                        @php
                            /*
                            "id": 1,
            "slug": "event_test",
            "path": "/event_test",
            "status": "published",
            "site_slug": "cofficegroupdev",
            "name": "Event test",
            "headline": "Event test",
            "title": "Event test - Coffice Test",
                            */

                            if(isset($_POST) && isset($_POST["event"])) {
                                $res = $auth->consumeEventAction($_REQUEST["event"]);
                                if(!empty($res) && isset($res["event"])) {
                                    echo "<h4 class='success'>Event updated successfully</h4>";
                                }
                            }

                        @endphp
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        Test Event (constant id:1)
                        <pre>
                       @php
                           $eventID = 1;
                           $event = $auth->getEvent($eventID);
                           var_dump($event);
                       @endphp
                    </pre>
                    </div>
                    <div class="col-6">
                        <h3>Edit Event (constant id:1)</h3>
                        <form method="post" action="/">
                            <input type="hidden" name="event" value="edit"/>
                            <input type="hidden" name="_id" value="{{$eventID}}"/>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Event Name</label>
                                <input type="text" class="form-control" id="exampleInputEmail1"
                                       aria-describedby="emailHelp" name="name" placeholder="Enter event name"
                                       value="{{isset($event["name"])?$event["name"]:''}}">

                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Event headline</label>
                                <input type="text" class="form-control" id="exampleInputEmail1"
                                       aria-describedby="emailHelp" name="headline" placeholder="Enter event headline"
                                       value="{{isset($event["headline"])?$event["headline"]:''}}">

                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Event title</label>
                                <input type="text" class="form-control" id="exampleInputEmail1"
                                       aria-describedby="emailHelp" name="title" placeholder="Enter event title"
                                       value="{{isset($event["title"])?$event["title"]:''}}">

                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Event start time</label>
                                <input type="datetime-local" class="form-control" id="exampleInputEmail1"
                                       aria-describedby="emailHelp" name="start_time"
                                       placeholder="Enter event start time"
                                       value="{{isset($event["start_time"])?$auth->formatDate($event["start_time"],'Y-m-d\TH:i:s'):''}}">

                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Event end time</label>
                                <input type="datetime-local" class="form-control" id="exampleInputEmail1"
                                       aria-describedby="emailHelp" name="end_time" placeholder="Enter event end time"
                                       value="{{isset($event["end_time"])?$auth->formatDate($event["end_time"],'Y-m-d\TH:i:s'):''}}">

                            </div>

                            <div class="form-group">
                                <label for="exampleInputLName">Excerpt</label>
                                <textarea name="excerpt" placeholder="write your excerpt"></textarea>
                            </div>
                            <button type="submit" class="btn-primary" value="save">Send Event</button>
                        </form>
                    </div>
                </div>
            </div>
            <!-- start contacts section -->
            <div class="acco container collapse {{$showContact}}" id="contacts">
                <div class="row">
                    <div class="col-12">
                        <p class="lead"><b>Contacts tests</b></p>
                        @php
                            if(isset($_POST) && isset($_POST["action"]) && in_array($_POST["action"],["contact","listcontact"])) {
                                $res = $auth->consumePersonContactAction($_REQUEST["contact"],$_REQUEST["action"]);
                                $_REQUEST["edit"] = false;
                                $_REQUEST["contact"] = false;
                                if(!empty($res) && isset($res["contact"])) {
                                    echo "<h4 class='success'>Person contacted successfully</h4>";
                                    $_REQUEST["contact"] = $res["contact"]["person_id"];
                                    if($_POST["action"]==='contact') {
                                        $_REQUEST["action"] = 'listcontact';
                                    }
                                }
                            }
                        @endphp
                    </div>
                    <div class="col-6">
                        <h3>List Contact</h3>
                        @php
                            $people = $auth->getPeople();
                        @endphp
                        @if($people)
                            <div class="container">
                                <div class="row">
                                    @foreach($people as $person)
                                        <div class="col-sm-6">
                                            <div class="card">
                                                @if($person["profile_image_url_ssl"])
                                                    <img src="{{$person["profile_image_url_ssl"]}}" class="card-img-top"
                                                         alt="{{$person["first_name"]}}">
                                                @endif
                                                <div class="card-body">
                                                    <h5 class="card-title">{{$person["first_name"]}} {{$person["last_name"]}}</h5>
                                                    <p class="card-text">{{isset($person["bio"])?$person["bio"]:''}}
                                                        <br/>{{$person["email"]}}</p>
                                                    <a class="btn btn-link"
                                                       href="/?contact={{$person["id"]}}&action=contact">Contact {{$person["first_name"]}}</a><br/>
                                                    <a class="btn btn-info"
                                                       href="/?contact={{$person["id"]}}&action=listcontact">List {{$person["first_name"]}}
                                                        's Contacts</a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                    @if(isset($_REQUEST["action"]))
                        <div class="col-6">

                            <form method="post" action="/">
                                <input type="hidden" name="action"
                                       value="{{isset($_REQUEST["action"])?$_REQUEST["action"]:''}}"/>
                                <input type="hidden" name="_id"
                                       value="{{isset($_REQUEST["contact"])?$_REQUEST["contact"]:''}}"/>
                                <input type="hidden" name="contact"
                                       value="{{isset($_REQUEST["contact"])?$_REQUEST["contact"]:''}}"/>
                                @php
                                    $person = $auth->getPerson($_REQUEST["contact"]);

                                @endphp
                                @if(isset($_REQUEST["action"]) &&$_REQUEST["action"]==='listcontact')
                                    <h3>All {{$person["first_name"]}}'s contacts</h3>
                                    @php
                                        $contacts = $auth->getPersonContacts($_REQUEST["contact"]);
                                    @endphp
                                    @if(is_array($contacts) && count($contacts)===0)
                                        <h5>No messages for {{$person["first_name"]}}</h5>
                                    @else
                                        <div class="container">
                                            @foreach($contacts as $contact)
                                                <div class="row">
                                                    <div class="alert alert-info" role="alert">
                                                        Contact date <b>{{$auth->formatDate($contact["created_at"],"d M Y H:i:s")}}</b><br/>
                                                        Message:<br/>
                                                        <p style="display: block; background: #FFF; margin: 10px auto;">{{$contact["note"]}}</p>

                                                        Type:<b>{{$contact["status"]}}</b>

                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                @else
                                    <h3>Contact {{$person["first_name"]}}</h3>
                                    <div class="form-group">
                                        <label for="exampleInputName">Contact method</label>
                                        <select name="method">
                                            <option value="door_knock">door knock</option>
                                            <option value="email">Email</option>
                                        </select>

                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputLName">Note/message</label>
                                        <textarea name="note" placeholder="write your note"></textarea>
                                    </div>
                                    <button type="submit" class="btn-primary" value="save">Send contact</button>
                                @endif


                            </form>
                        </div>
                    @endif
                </div>
            </div>


        </main>

        <footer class="mastfoot mt-auto">
            <div class="inner">
                <p>Nation builder <a href="https://cofficegroupdev.nationbuilder.com/">coffice api tester</a>, by <a
                            href="https://www.cofficegroup.com">@Coffice</a>.</p>
            </div>
        </footer>
    </div>
@endsection

@section('scripts')

    <script>
        jQuery(document).ready(function () {
            jQuery('.acco').on('show.bs.collapse', function () {
                $('.acco').removeClass('show');
            });
        });
    </script>
@endsection
