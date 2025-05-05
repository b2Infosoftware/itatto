<!DOCTYPE html>
<html lang="en" style="margin:0px;">
<head>
    <title>{{$form->name}}</title>
    
</head>
<style>
    *,*::before,*::after{
        padding: 0,
        margin: 0,
    }    

    body{
        padding: 1.5rem;
        font-family: sans-serif;
    }
  
    .consentPreview{
        display: flex; 
        overflow: auto; 
        padding-left: 1rem;
        padding-right: 1rem; 
        margin-top: 0.5rem; 
        flex-direction: column; 
        border-radius: 0.25rem; 
        height: 100vh; 
        color: #000000; 
        background-color: #ffffff; 
    }
    .form-title{
        font-size: 1.875rem;
        line-height: 2.25rem; 
        font-weight: bold; 
        text-align: center; 
    }
    .form-subtitle{
        display: block; 
        padding: .75rem 1rem; 
        margin-bottom: 2rem; 
        border-radius: 0.25rem; 
        font-size: 1rem;
        line-height: 1.25; 
        color: #ffffff; 
        background-color: #000000; 
    }
    .text-bold{
        font-weight: bold;
    }
    .parts{
        width: 100%;
        display: block; 
        margin: 2rem 0;        
        position: relative;
    }
   
    .opening-text,
    .closing-text {
        padding: 1rem 0;
    }

    .statements {
        display: flex; 
        margin-top: 1rem; 
        flex-direction: column; 
    }
    .statements > span {
        padding-left: 1.25rem; 
        border-left: 4px solid #CCC; 
        margin: .5rem 0;
        display: block;
    }

    .sign-title {
        font-weight: 700;
        margin-top: 2rem;
        margin-bottom: 4rem;
    }

    .signature {
        display: block;
        margin-top: 2rem;
        margin-bottom: 2rem;
        width: 100%; 

    }
    .signature span {
        display: block;
        float: left;
        width: 40%;
        margin: 0 5%;        
        border-bottom: 1px solid;
        font-style: italic; 
        font-weight: 500; 
        text-align: center; 
        padding-bottom: 1rem;
    }

    .signature div {
        display: block;
        float: left;
        width: 40%;
        margin: 0 5%;
        text-align: center; 
    }   
    .signature img {
        height: 4rem; 
        width: auto;
        margin-top: -1rem;
    }
    .mb-1{
        margin-bottom: 1rem;
    }
    
</style>
<body>
    <div class="consentPreview">

        <div class="form-title">
          {{ $form->title }}
        </div>
        <div class="parts">
          <div>
            <div class="text-bold mb-1">{{ trans('general.agency') }}</div>
            <div>Micromutazioni</div>
            <div>Via Broggia 14 80135 Napoli P.I.</div>
            <div>07515090632</div>
            <div>+390815443724</div>
          </div>
          <div style="float: right; position: absolute; top: 1rem">
            <div class="text-bold mb-1">{{ trans('general.client') }}</div>
            <div>{{ $client->first_name }} {{$client->last_name}}</div>
            <div>{{ $client->phone_number }}</div>
            <div>{{ $client->email  }}</div>
          </div>
        </div>
        <div class="form-subtitle">
          {{ $form->subtitle }}
        </div>
        <div class="opening-text" style="font-family: 'sans-serif'">
            {!! $form->opening_text !!}
        </div>
        <div class="statements">
            @foreach ($form->statements as $statement)                
            <span>              
              <p>
                {{ $statement }}
              </p>
            </span>
            @endforeach
        </div>
    
        <div class="closing-text">
            {!! $form->closing_text !!}
        </div>

        <div class="sign-title">
          {{ $form->sign_title }}
        </div>
    
        <div class="signature">
          <span>{{ $client->first_name }} {{$client->last_name}}</span>
          <div><img src="{{$signature}}" /></div>
        </div>
      </div>
</body>
</html>