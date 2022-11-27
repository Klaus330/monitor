@component('mail::message',)
# {{ config('app.name') }}

{{ $fixedBrokenRoutesCount }} of the broken links we found have been fixed.



@component('mail::table')
| Route        | Status        |
| :----------------: | :-------------: |
@foreach($fixedBrokenRoutes as $brokenLink)
| {{$brokenLink->route}} | {{$brokenLink->http_code}} |
@endforeach
@endcomponent

You can only see 5 of the broken links. If you want to see the full report please join our premium plan.

@component('mail::button', ['url' => route('site.broken.routes', ['site' => $site->id])])
View broken links scan details
@endcomponent

Don't want to receive mails about found broken links anymore? Turn off the "Broken links" switch on
the notifications settings.


#### Thank you for using {{ config('app.name') }}.
@endcomponent