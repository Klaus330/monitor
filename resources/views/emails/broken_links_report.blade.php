@component('mail::message',)
# {{ config('app.name') }}

Our scans detected **{{ $brokenLinksCount }}** broken @if($brokenLinksCount === 1) link @else links @endif on [{{ $site->uri->getHost() }}]({{$site->url}}) that require your attention.
You can inspect @if($brokenLinksCount === 1) it @else them @endif in the attached spreadsheet file or in the detailed report linked below.

@if($brokenLinksCount > 5)
Only the first 5 links are shown. To view them all, you can subscribe to one of our plans.
@endif

@component('mail::button', ['url' => route('site.broken.routes', ['site' => $site->id])])
View broken links scan details
@endcomponent

Don't want to receive mails about found broken links anymore? Turn off the "Broken links" switch on
the notifications settings.


#### Thank you for using {{ config('app.name') }}.
@endcomponent